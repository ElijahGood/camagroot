<?php

require ROOT.'/application/models/Gallery.php';

class GalleryController extends Gallery
{
	public function indexAction() {
		if (isset($_SESSION['loggued_in_user'])) {
			$photoStuff = $this->getPhotoData();
			$commentStuff = $this->getCommentsData();
			$likeStuff = $this->getLikesData();

			include_once(ROOT.'/application/views/gallery.php');
		} else {
			header("Location: /index");
		}
	}

	public function saveImageAction() {
		if (isset($_SESSION['loggued_in_user'])) {
			$user_data = User::getUserByLogin($_SESSION['loggued_in_user']);
			$new_photo_name = $user_data['id'].':'.uniqid().'.png';
			if (isset($_POST['img'])) {
				$image = $_POST['img'];
				$image = str_replace('data:image/png;base64,', '', $image);
				$image = str_replace(' ', '+', $image);

				unset($_POST['img']);
				if (!is_dir(ROOT.'/images')) {
					mkdir(ROOT.'/images');
				}

				$user_id = intval($user_data['id']);
				$date_time = date("Y-m-d H:i:s");
				$link_path = '/images/'.$new_photo_name;

				$decoded_image = base64_decode($image);
				$file = fopen(ROOT.$link_path, "wr");
				fwrite($file, $decoded_image);
				fclose($file);

				//update database
				$stmt = "INSERT INTO photos (link_path, user_id, date_time) VALUES ('$link_path', '$user_id', '$date_time')";
				$res = $this->database->database->prepare($stmt);
				$res->bindParam(':link_path', $link_path, PDO::PARAM_STR);
				$res->bindParam(':user_id', $user_id, PDO::PARAM_INT);
				$res->execute();
			}
		}
	}

	public function saveCommentAction() {
		if (isset($_SESSION['loggued_in_user'])) {
			if (isset($_POST['comment_text']) && isset($_POST['photo_id'])) {
				$user_data = User::getUserByLogin($_SESSION['loggued_in_user']);
				//data for each cell
				$comment_text = $_POST['comment_text'];
				$photo_id = $_POST['photo_id'];
				$user_id = intval($user_data['id']);
				$user_name = $user_data['login'];
				if (isset($comment_text) && isset($photo_id) && isset($user_id) && isset($user_name)) {
					//update database
					$stmt = "INSERT INTO comments (comment, user_id, photo_id) VALUES (:comment_text, :user_id, :photo_id)";
					$res = $this->database->database->prepare($stmt);
					$res->bindParam(':comment_text', $comment_text, PDO::PARAM_STR);
					$res->bindParam(':user_id', $user_id, PDO::PARAM_INT);
					$res->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
					$res->execute();
					
					if ($user_data['notification'] === '1') {
						$this->sendNotificationMail($photo_id, $user_id);
					}
					echo json_encode(array('comment_text' => $comment_text, 'photo_id' => $photo_id, 'user_id' => $user_id, 'username' => $user_name));
					
				} else {
					echo json_encode(array('fuckedup'));
				}
				unset($_POST['photo_id']);
				unset($_POST['comment_text']);
			}
		}
	}

	public function deleteCommentAction() {
		if (isset($_SESSION['loggued_in_user'])) {
			if (isset($_POST['comment_id'])) {
				$comment_id = intval($_POST['comment_id']);
				if (isset($comment_id)) {
					//update database
					$stmt = "DELETE FROM comments WHERE id = :comment_id";
					$res = $this->database->database->prepare($stmt);
					$res->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
					$res->execute();

					unset($_POST['comment_id']);
				}
			}
		}
	}

	public function likesAction() {
		if (isset($_SESSION['loggued_in_user'])) {
			$user_name = $_SESSION['loggued_in_user'];
			$user_data = User::getUserByLogin($user_name);
			$user_id = intval($user_data['id']);
			if (isset($_POST['photo_id'])) {
				$photo_id = intval($_POST['photo_id']);
				if ($this->database->isUniqueLike($user_id, $photo_id)) {
					$stmt = "INSERT INTO likes (`user_id`, `photo_id`) VALUES (:user_id, :photo_id)";
					$sql = $this->database->database->prepare($stmt);
					$sql->bindParam(':user_id', $user_id, PDO::PARAM_INT);
					$sql->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
					$sql->execute();
				}
				unset($_POST['photo_id']);
			}
		}
	}

	public function unlikesAction() {
		if (isset($_SESSION['loggued_in_user'])) {
			$user_name = $_SESSION['loggued_in_user'];
			$user_data = User::getUserByLogin($user_name);
			$user_id = intval($user_data['id']);
			if (isset($_POST['photo_id'])) {
				$photo_id = intval($_POST['photo_id']);
				if (!$this->database->isUniqueLike($user_id, $photo_id)) {
					$stmt = "DELETE FROM likes WHERE `user_id` = :user_id AND `photo_id` = :photo_id";
					$sql = $this->database->database->prepare($stmt);
					$sql->bindParam(':user_id', $user_id, PDO::PARAM_INT);
					$sql->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
					$sql->execute();
				}
				unset($_POST['photo_id']);
			}
		}
	}

	public function deleteImageAction() {
		if (isset($_SESSION['loggued_in_user'])) {
			$user_name = $_SESSION['loggued_in_user'];
			if (isset($_POST['photo_id'])) {
				$photo_id = intval($_POST['photo_id']);
				$this->deleteLikes($photo_id);
				$this->deleteComments($photo_id);
				$this->deletePhoto($photo_id);
				unset($_POST['photo_id']);
			}
		}
	}
}