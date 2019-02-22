<?php

require ROOT.'/application/func/DBconnect.php';

abstract class Model {

	public $database;
	
	public function __construct() {
		$this->database = new Database;
	}

	public function isLoguedIn() {
		if (isset($_SESSION['loggued_in_user'])) { 
			if ($_SESSION['loggued_in_user']) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function endSession() {
		unset($_SESSION['loggued_in_user']);
	}

}