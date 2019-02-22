<?php

class Database
{
	public $database;

	public function __construct() {
		$sql_config = require ROOT.'/application/config/database.php';
		$this->database = new PDO('mysql:host='.$sql_config['host'].';dbname='.$sql_config['name'].'', $sql_config['user'], $sql_config['password']);
		$this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function safeQuery($sql) {
		$stmt = $this->database->prepare($sql);
		$stmt->execute();
		return $stmt;

	}

	public function isNotInDatabase($name, $param) {
		$stmt = "SELECT * FROM users WHERE :param = :name";
		$sql = $this->database->prepare($stmt);
		$sql->bindParam(':param', $param, PDO::PARAM_STR);
		$sql->bindParam(':name', $name, PDO::PARAM_STR);
		$sql->execute();
		$row = $sql->fetch();
		if ($row) {
			return false;
		} else {
			return true;
		}
	}

	public function isUniqueLike($user, $photo) {
		$user_id = intval($user);
		$photo_id = intval($photo);

		$stmt = "SELECT * FROM likes WHERE `user_id` = :user_id AND `photo_id` = :photo_id";
		$sql = $this->database->prepare($stmt);
		$sql->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$sql->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
		$sql->execute();
		$row = $sql->fetch();
		if ($row) {
			return false;
		} else {
			return true;
		}

	}
}