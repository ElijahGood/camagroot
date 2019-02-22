<?php

require ROOT.'/application/func/DBconnect.php';

class User
{
	protected $database;

	function __construct() {
		$this->database = new Database;
	}
	
	public function sendVerificationMail($email, $login, $token) {
		$encoding = "utf-8";
		$preferences = array(
			"input-charset" => $encoding,
			"output-charset" => $encoding,
			"line-length" => 76,
			"line-break-chars" => "\r\n"
		);
		$to = $email;
		$subject = "Sign-up email verification";
		$message = '<!DOCTYPE html>
					<html>
					<head>
						<title>Verification</title>
					</head>
					<body>
						<div>
							<p>Hey there! New user has been created on Camagroot. To activate this account use link below </p>
							<a href="http://localhost:8085/verify?login='.$login.'&token='.$token.'">Verification link</a>
						</div>
					</body>
					</html>';
		$additional_header = "Content-type: text/html; charset=".$encoding." \r\n";
		$additional_header .= "From: Camagroot LLC <no-reply@camagroot.com> \r\n";
		$additional_header .= "MIME-Version: 1.0 \r\n";
		$additional_header .= "Content-Transfer-Encoding: 8bit \r\n";
		$additional_header .= "Date: ".date("r (T)")." \r\n";
		$additional_header .= iconv_mime_encode("Subject", $subject, $preferences);
		if (mail($to, $subject, $message, $additional_header)) {
			$_SESSION['tempor_alert'] = "Check your email, we have send a message to your =)";
			header("Location: /index");
		} else {
			$_SESSION['tempor_alert'] = "Something went wrong with registration. Try again or contact our service.";
			header("Location: /index");
		}
	}

		public function sendRestorePassMail($email, $login) {
		$new_token = $this->updateToken($login);

		$encoding = "utf-8";
		$preferences = array(
			"input-charset" => $encoding,
			"output-charset" => $encoding,
			"line-length" => 76,
			"line-break-chars" => "\r\n"
		);
		$to = $email;
		$subject = "Restore password on Camagroot";
		$message = '<!DOCTYPE html>
					<html>
					<head>
						<title>'.$login.' Password Restore</title>
					</head>
					<body>
						<div>
							<p>Hey there! New user has been created on Camagroot. To activate this account use link below </p>
							<a href="http://localhost:8085/passrestore?login='.$login.'&token='.$new_token.'">Restore password link</a>
						</div>
					</body>
					</html>';
		$additional_header = "Content-type: text/html; charset=".$encoding." \r\n";
		$additional_header .= "From: Camagroot LLC <no-reply@camagroot.com> \r\n";
		$additional_header .= "MIME-Version: 1.0 \r\n";
		$additional_header .= "Content-Transfer-Encoding: 8bit \r\n";
		$additional_header .= "Date: ".date("r (T)")." \r\n";
		$additional_header .= iconv_mime_encode("Subject", $subject, $preferences);
		if (mail($to, $subject, $message, $additional_header)) {
			$_SESSION['tempor_alert'] = "Check your email, we have send a message to your =)";
			header("Location: /index");
		} else {
			$_SESSION['tempor_alert'] = "Something went wrong with restoring password. Try again or contact our service.";
			header("Location: /index");
		}
	}

	public function updateToken($login) {
		$new_token = bin2hex(random_bytes(16));
		$stmt = "UPDATE users SET token = '$new_token' WHERE login = '$login'";
		$res = $this->database->safeQuery($stmt);
		return $new_token;
	}

	public function unsetToken($login) {
		$stmt = "UPDATE users SET token = '' WHERE login = '$login'";
		$res = $this->database->safeQuery($stmt);
	}

	public function getUserDataByEmail($email) {
		$stmt = "SELECT * FROM users WHERE email = :email";
		$sql = $this->database->database->prepare($stmt);
		$sql->bindParam(':email', $email, PDO::PARAM_STR);
		$sql->setFetchMode(PDO::FETCH_ASSOC);
		$sql->execute();
		$result = $sql->fetch();
		return $result;
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

	public function changeNotificationStatus($check, $user_id){
		if ($check === 'yes') {
			$stmt = "UPDATE users SET `notification` = 1 WHERE `id` = :user_id";
		} else {
			$stmt = "UPDATE users SET `notification` = 0 WHERE `id` = :user_id";
		}
		$res = $this->database->database->prepare($stmt);
		$res->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$res->execute();
	}

	public function changeUserName($new_name, $user_id) {
		$stmt = "UPDATE users SET `login` = :new_name WHERE `id` = :user_id";
		$res = $this->database->database->prepare($stmt);
		$res->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$res->bindParam(":new_name", $new_name, PDO::PARAM_STR);
		$res->execute();
		if (isset($_SESSION['loggued_in_user'])) {
			$_SESSION['loggued_in_user'] = $new_name;
		}
	}

	public function changeUserMail($new_email, $user_id) {
		$stmt = "UPDATE users SET `email` = :new_email WHERE `id` = :user_id";
		$res = $this->database->database->prepare($stmt);
		$res->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$res->bindParam(":new_email", $new_email, PDO::PARAM_STR);
		$res->execute();
	}

	public function changeUserPassword($new_pass, $user_id) {
		$stmt = "UPDATE users SET `password` = :new_pass WHERE `id` = :user_id";
		$res = $this->database->database->prepare($stmt);
		$res->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$res->bindParam(":new_pass", $new_pass, PDO::PARAM_STR);
		$res->execute();
	}
}