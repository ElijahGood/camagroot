<?php

require ROOT.'/application/models/User.php';

class UserController extends User
{
	public function signinAction() {
		require_once(ROOT.'/application/views/signin.php');
		if (isset($_POST['signin_try'])) {
			$username = $_POST['username'];
			$password = $_POST['passwd'];
			$check = $this->database->safeQuery("SELECT * FROM users WHERE login = '$username'");
			$user_data = $check->fetch();
			if ($user_data) {
				if (password_verify($password, $user_data['password']) && $user_data['confirmation'] == 1) {
					$_SESSION['loggued_in_user'] = $username;
					$tmp = $this->getUserByLogin($username);
					$_SESSION['loggued_in_id'] = $tmp['id'];
					header("Location: /index");
				} else {
					echo "<script>alert(\"Invalid password. Try again.\");</script>";
				}				
			} else {
				echo "<script>alert(\"Invalid username. Try again.\");</script>";
			}
		} else if (isset($_POST['forgot_pass'])) {
			header("Location: /restore");
		}
	}

	public function createAction() {
		require_once(ROOT.'/application/views/user_create.php');
		if (isset($_POST['user_create_submit'])) {
			if ($_POST['login'] && $_POST['email'] && $_POST['passwd1'] && $_POST['passwd2']) {		
				$username = $_POST['login'];
				$email = $_POST['email'];
				$passwd1 = $_POST['passwd1'];
				$passwd2 = $_POST['passwd2'];

				if ($this->database->isNotInDatabase($username, 'login')) {
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						echo "<script>alert(\"Invalid email. Try again.\");</script>";
					} else if ($this->database->isNotInDatabase($email, 'email')) {
						if (strcmp($passwd1, $passwd2)) {
							echo "<script>alert(\"Passwords doesnt match. Try again.\");</script>";
						} else if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,20}$/', $passwd1)) {
							echo "<script>alert(\"Password should be between 8 and 20 characters long. Contain at least one number, one letter and one special char.\");</script>";
						} else {
							$token = bin2hex(random_bytes(16));
							$hash_pass = password_hash($passwd1, PASSWORD_DEFAULT);
							$stmt = "INSERT INTO users (login, email, password, confirmation, notification, token) VALUES ('$username', '$email', '$hash_pass', 0, 1, '$token')";
							
							$res = $this->database->safeQuery($stmt);
							$this->sendVerificationMail($email, $username, $token);
							unset($res);
						}
					} else {
						echo "<script>alert(\"Username with that email is already exist.\");</script>";
					}
				} else {
					echo "<script>alert(\"This username is already taken. Use another one.\");</script>";
				}
			} else {
				echo "<script>alert(\"All fields should be submitted.\");</script>";
			}
		}
	}

	public function verifyAction() {
		$verify_login = $_GET['login'];
		$verify_token = $_GET['token'];		
		$query = $this->database->safeQuery("SELECT * FROM users WHERE login = '$verify_login'");
		$user_data = $query->fetch();
		$user_token = $user_data['token'];
		if ($user_token === $verify_token) {
			$stmt = "UPDATE users SET confirmation = 1 WHERE login = '$verify_login'";
			$sql = $this->database->safeQuery($stmt);
			$this->unsetToken($verify_login);
			$_SESSION['tempor_alert'] = "Your account has been successfully verified. Now you can log in.";
			header("Location: /signin");
		} else {
			$_SESSION['tempor_alert'] = "Something went wrong. Please contact our service.";
			header("Location: /index");
		}
	}

	public function restoreAction() {
		require_once(ROOT.'/application/views/restore.php');
		if (isset($_POST['restore_msg_submit'])) {
			if (isset($_POST['restore_email'])) {
				$email = $_POST['restore_email'];
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					echo "<script>alert(\"Invalid email. Try again.\");</script>";
				} else {
					if ($this->database->isNotInDatabase($email, 'email')) {
						echo "<script>alert(\"There is no such email in database. Try again.\");</script>";
					} else {
						$user_data = User::getUserDataByEmail($email);
						User::sendRestorePassMail($email, $user_data['login']);
					}
				}
			} else {
				echo "<script>alert(\"Please enter your email.\");</script>";
			}
		}
	}

	public function passrestoreAction() {
		$restore_login = $_GET['login'];
		$restore_token = $_GET['token'];
		$user_data = User::getUserByLogin($restore_login);
		if ($user_data['token'] === $restore_token) {
			include_once(ROOT.'/application/views/restorepass.php');
			if (isset($_POST['save_new_password'])) {
				if (isset($_POST['new_pass']) && isset($_POST['new_pass_confirm'])) {
					$passwd1 = $_POST['new_pass'];
					$passwd2 = $_POST['new_pass_confirm'];
					if (strcmp($passwd1, $passwd2)) {
							echo "<script>alert(\"Passwords doesnt match. Try again.\");</script>";
						} else if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,20}$/', $passwd1)) {
							echo "<script>alert(\"Password should be between 8 and 20 characters long. Contain at least one number, one letter and one special char.\");</script>";
						} else {
							$this->unsetToken($restore_login);
							$hash_pass = password_hash($passwd1, PASSWORD_DEFAULT);
							$stmt = "UPDATE users SET password = :hash_pass WHERE login = :restore_login";
							$res = $this->database->database->prepare($stmt);
							$res->bindParam(":restore_login", $restore_login, PDO::PARAM_STR);
							$res->bindParam(":hash_pass", $hash_pass, PDO::PARAM_STR);
							$res->execute();
							echo "<script>alert(\"Your password has been successfully changed. please login with new data.\");</script>";
							header("Location: /signin");
						}
				} else {
					echo "<script>alert(\"Both fields should be filled.\");</script>";
				}				
			}
		} else {
			include_once(ROOT.'/application/views/error404.php');
		}
	}

	public function logoutAction() {
		unset($_SESSION['loggued_in_user']);
		unset($_SESSION['tempor_alert']);
		header("Location: /index");
	}

	public function photoAction() {
		if (isset($_SESSION['loggued_in_user'])) {
			include_once(ROOT.'/application/views/photo_shot.php');
		} else {
			header("Location: /index");
		}
	}

	public function userPageAction() {
		if (isset($_SESSION['loggued_in_user'])) {
			$user_data = $this->getUserByLogin($_SESSION['loggued_in_user']);
			include_once(ROOT.'/application/views/user_page.php');
			$user_name = $user_data['login'];
			$user_id = $user_data['id'];
			if (isset($_POST['save_changes'])) {
				if (isset($_POST['notification'])) {
					$this->changeNotificationStatus($_POST['notification'], $user_id);
				}
				if (isset($_POST['new_username']) && $_POST['new_username'] !== "") {		
					$new_login = $_POST['new_username'];
					if ($this->database->isNotInDatabase($new_login, 'login')) {
						$this->changeUserName($new_login, $user_id);
					} else {
						echo "<script>alert(\"This username is already taken. Use another one.\");</script>";
					}
				}
				if (isset($_POST['new_email']) && $_POST['new_email'] !== "") {
					$new_email = $_POST['new_email'];
					if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
						echo "<script>alert(\"Invalid email. Try again.\");</script>";
					} else if ($this->database->isNotInDatabase($new_email, 'email')) {
						$this->changeUserMail($new_email, $user_id);
					} else {
						echo "<script>alert(\"Username with that email is already exist.\");</script>";
					}
				}
				if ((isset($_POST['new_passwd1']) && isset($_POST['new_passwd2']) &&
					($_POST['new_passwd2'] !== "" && $_POST['new_passwd1'] !== ""))) {
					$passwd1 = $_POST['new_passwd1'];
					$passwd2 = $_POST['new_passwd2'];
					if (strcmp($passwd1, $passwd2)) {
						echo "<script>alert(\"New passwords doesnt match. Try again.\");</script>";
					} else if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,20}$/', $passwd1)) {
						echo "<script>alert(\"Password should be between 8 and 20 characters long. Contain at least one number, one letter and one special char.\");</script>";
					} else {
						$hash_pass = password_hash($passwd1, PASSWORD_DEFAULT);
						$this->changeUserPassword($hash_pass, $user_id);
					}
				}
				header("Location: /user_page");
			}
		} else {
			header("Location: /index");
		}
		
	}
}