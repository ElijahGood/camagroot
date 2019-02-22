<?php

include_once(ROOT.'/application/classes/Model.php');

class Main extends Model
{
	public function test_Database()
	{

		if ($this->isLoguedIn()) {
			require_once(ROOT . '/application/views/gallery.php');
		} else {
			require_once(ROOT . '/application/views/index.php');
		}
	}

	public function getAllPhotoData() {
		$stmt = "SELECT * FROM photos ORDER BY `date_time` DESC";
		$res = $this->database->database->prepare($stmt);
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
  
    public function getAllCommentsData() {
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

	public function getAllLikesData() {
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
}