<?php

include_once(ROOT.'/application/models/Main.php');

class MainController extends Main
{

	public function indexAction()
	{
		if (isset($_SESSION['loggued_in_user'])) {
			$photoStuff = $this->getAllPhotoData();
			$commentStuff = $this->getAllCommentsData();
			$likeStuff = $this->getAllLikesData();
			
			include_once(ROOT.'/application/views/gallery.php');
		} else {
			$galleryStuff = $this->getAllPhotoData();
			require_once(ROOT . '/application/views/index.php');
		}
	}


}