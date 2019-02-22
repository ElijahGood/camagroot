<!DOCTYPE html>
<html>
<head>
	<title>Index page</title>
	<link rel="icon" type="image/png" href="/application/views/favicon.png"/>
	<link rel="stylesheet" type="text/css" href="/application/views/css/index.php">
</head>
<body>
	<?php
		include 'header.php';

		//get total number of posts
		$num_of_items = count($galleryStuff);
		//show background image if needed
		if ($num_of_items === 0) {
			echo "<div class='no-content-container' style='height: 84vh;'>";
		} else {
			echo "<div class='main-container'>";
		}
		//pagination handle
		if ($num_of_items  > 5) {
			
			echo "
			<br>
			<div class='center'>
				<div class='pagination'>
					<a class='left_page'>&laquo;</a>";
					$page_counter = 1;
					while ($num_of_items > 0) {
						echo "<a class='page_number'>".$page_counter."</a>";
						$num_of_items -= 5;
						$page_counter++;
					}
				echo "
					<a class='right_page'>&raquo;</a>
				</div>
			</div>";
		}

		//all photos goes here
		$page_num = 1;
		foreach ($galleryStuff as $item) {

			echo "
			<div class='photo_container' number_id=".$page_num.">
			<br>
				<img src='".$item["link_path"]."'></img>
				<div>
					<span>This masterpiece was created at ".$item["date_time"]." by ".$item["username"]."</span>
				</div>
			<br>
			</div>
			";
			echo "";
			$page_num++;
		}

		//pagination handle
		$num_of_items = count($galleryStuff);
		if ($num_of_items  > 5) {
			echo "
			<div class='center'>
				<div class='pagination'>
					<a class='left_page'>&laquo;</a>";
					$page_counter = 1;
					while ($num_of_items > 0) {
						echo "<a class='page_number'>".$page_counter."</a>";
						$num_of_items -= 5;
						$page_counter++;
					}
				echo "
					<a class='right_page'>&raquo;</a>
				</div>
			</div>
			<br>";
		}
		echo "</div>";

		include 'footer.php';
	?>
	<script src="/application/views/js/index.js"></script>
</body>
</html>