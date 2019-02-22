<?php

require ROOT.'/application/models/User.php';

class Gallery extends User
{
    protected $database;
    
    function __construct() {
		$this->database = new Database;
    }

	public function sendNotificationMail($photo_id, $user_id) {
		$photo_owner_data = $this->getUserByPhotoId($photo_id);
		$photo_commentor_data = $this->getUserById($user_id);
		$email = $photo_owner_data['email'];
		$commentor_login = $photo_commentor_data['login'];

		$encoding = "utf-8";
		$preferences = array(
			"input-charset" => $encoding,
			"output-charset" => $encoding,
			"line-length" => 76,
			"line-break-chars" => "\r\n"
		);
		$to = $email;
		$subject = "Some activity with your post on Camagroot";
		$message = '<!DOCTYPE html>
					<html>
					<head>
						<title>Activity on your post</title>
					</head>
					<body>
						<div>
							<p>Hey there! One of your photos has been commented by '.$commentor_login.'. 
							Check it on our website: <a href="http://localhost:8085/index">Camagroot</a></p>
						</div>
					</body>
					</html>';
		$additional_header = "Content-type: text/html; charset=".$encoding." \r\n";
		$additional_header .= "From: Camagroot LLC <no-reply@camagroot.com> \r\n";
		$additional_header .= "MIME-Version: 1.0 \r\n";
		$additional_header .= "Content-Transfer-Encoding: 8bit \r\n";
		$additional_header .= "Date: ".date("r (T)")." \r\n";
		$additional_header .= iconv_mime_encode("Subject", $subject, $preferences);
		mail($to, $subject, $message, $additional_header);
	}

    public function getPhotoData() {
		$user_data = User::getUserByLogin($_SESSION['loggued_in_user']);
		$loged_id = intval($user_data['id']);
		$stmt = "SELECT * FROM photos WHERE user_id = :loged_id ORDER BY `date_time` DESC";
		$res = $this->database->database->prepare($stmt);
		$res->bindParam(':loged_id', $loged_id, PDO::PARAM_INT);
		$res->execute();
		$result = $res->fetchAll();
		$dataToReturn = array();
		foreach ($result as $item) {
			$user_data = $this->getUserById($item['user_id']);
			$creator_login = $user_data['login'];
			array_push($dataToReturn, array('id' => $item['id'], 'username' => $creator_login, 'link_path' => $item['link_path'], 'date_time' => $item['date_time']));
		}
    	return $dataToReturn;
    }
  
    public function getCommentsData() {
		$stmt = "SELECT * FROM comments";
		$res = $this->database->database->prepare($stmt);
		$res->execute();
		$dataToReturn = $res->fetchAll();

		//get all photo object
		$stmt = "SELECT * FROM photos";
		$res = $this->database->database->prepare($stmt);
		$res->execute();
		$dataPhoto = $res->fetchAll();
		
		//creating two-dimensional array with photo_id`s as keys to arrays of comments 
		$keys_for_array = array();
		foreach ($dataPhoto as $value) {
			array_push($keys_for_array, $value['id']);
		}
		$commentsData = array_fill_keys($keys_for_array, array());

		//make array with photo_id as keys and array of comments as value
		foreach ($dataToReturn as $value) {
			$photo_id = $value['photo_id'];
			$commentor_data = $this->getUserById($value['user_id']);
			$comments_name = $commentor_data['login'];
			array_push($commentsData[$photo_id], array('comment' => $value['comment'], 'name' => $comments_name, 'user_id' => $value['user_id'], 'comment_id' => $value['id']));
		}
		return $commentsData;
	}

	public function getLikesData() {
		//get all photo object
		$stmt = "SELECT * FROM photos";
		$res = $this->database->database->prepare($stmt);
		$res->execute();
		$dataPhoto = $res->fetchAll();
		//creating two-dimensional array with photo_id`s as keys to arrays of comments 
		$keys_for_array = array();
		$values_for_array = array();

		if (isset($_SESSION['loggued_in_user'])) {
			$user_data = $this->getUserByLogin($_SESSION['loggued_in_user']);
			$user_id = $user_data['id'];
		}
		foreach ($dataPhoto as $value) {
			array_push($keys_for_array, $value['id']);
			$likes_num = $this->getLikesNumber($value['id']);
			//getting bolean value = whether user already liked precise photo or not
			if (isset($user_id)) {
				$user_likes_bool = $this->userLikedPhotoBool($user_id, $value['id']);
			} else {
				$user_likes_bool = 0;
			}
			array_push($values_for_array, array('likes_num' => intval($likes_num), 'liked_bool' => intval($user_likes_bool)));
		}
		$likesData = array_combine($keys_for_array, $values_for_array);
		return $likesData;
	}
		
	public function getLikesNumber($photo_id) {
		$stmt = "SELECT COUNT(*) FROM likes WHERE photo_id = :photo_id";
		$res = $this->database->database->prepare($stmt);
		$res->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
		$res->execute();
		$likesData = $res->fetch();
		$likes_number = $likesData[0];
		return ($likes_number);
	}

	public function userLikedPhotoBool($id_user, $id_photo) {
		$stmt = "SELECT COUNT(*) FROM likes WHERE `photo_id` = :id_photo AND `user_id` = :id_user";
		$res = $this->database->database->prepare($stmt);
		$res->bindParam(':id_photo', $id_photo, PDO::PARAM_INT);
		$res->bindParam(':id_user', $id_user, PDO::PARAM_INT);
		$res->execute();
		$return_data = $res->fetch();
		$return_bool = $return_data[0];
		return ($return_bool);
	}

	public function getUserByLogin($login) {
		$stmt = "SELECT * FROM users WHERE login = :login";
		$sql = $this->database->database->prepare($stmt);
		$sql->bindParam(':login', $login, PDO::PARAM_STR);
		$sql->setFetchMode(PDO::FETCH_ASSOC);
		$sql->execute();
		$result = $sql->fetch();
		return $result;
	}

	public function getUserById($user_id) {
		$stmt = "SELECT * FROM users WHERE id = :user_id";
		$sql = $this->database->database->prepare($stmt);
		$sql->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$sql->setFetchMode(PDO::FETCH_ASSOC);
		$sql->execute();
		$result = $sql->fetch();
		return $result;
	}

	public function getUserByPhotoId($photo_id) {
		$stmt = "SELECT * FROM photos WHERE id = :photo_id";
		$sql = $this->database->database->prepare($stmt);
		$sql->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
		$sql->setFetchMode(PDO::FETCH_ASSOC);
		$sql->execute();
		$data = $sql->fetch();
		$user_id = $data['user_id'];
		
		$result = $this->getUserById($user_id);
		return $result;
	}

	public function deleteLikes($photo_id) {
		$stmt = "DELETE FROM likes WHERE `photo_id` = :photo_id";
		$sql = $this->database->database->prepare($stmt);
		$sql->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
		$sql->execute();
	}

	public function deleteComments($photo_id) {
		$stmt = "DELETE FROM comments WHERE `photo_id` = :photo_id";
		$sql = $this->database->database->prepare($stmt);
		$sql->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
		$sql->execute();
	}

	public function deletePhoto($photo_id) {
		//deleting from server folder
		$stmt1 = "SELECT * FROM photos WHERE `id` = :photo_id";
		$res = $this->database->database->prepare($stmt1);
		$res->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
		$res->execute();
		$result = $res->fetch();
		$cmd = "rm ".ROOT.$result['link_path'];
		exec($cmd);

		//delete from database
		$stmt = "DELETE FROM photos WHERE `id` = :photo_id";
		$sql = $this->database->database->prepare($stmt);
		$sql->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
		$sql->execute();
	}
} 