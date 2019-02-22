<html>
<head>
	<title>User gallery</title>
	<link rel="icon" type="image/png" href="/application/views/favicon.png"/>
	<link rel="stylesheet" href="/application/views/css/gallery.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
	<?php
		include 'header.php';
	?>
	<br> 
	<div class="common-container">
		<?php

			//pagination handle
			$num_of_items = count($photoStuff);
			//show background image if needed
			if ($num_of_items === 0) {
				echo "<div class='no-content-container' style='height: 84vh;'>";
			} else {
				echo "<div class='main-container'>";
			}

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

			//photos, comment and etc.
			$page_num = 1;
			foreach ($photoStuff as $item) {
				echo "
				<div class='post-container' number_id=".$page_num.">
					<img src='".$item["link_path"]."'></img>
					<div>
						<span>This masterpiece was created at ".$item["date_time"]." by ".$item["username"]."</span>";
						if ($item['username'] === $_SESSION['loggued_in_user']) {
							echo "<i class='material-icons photo-delete'>delete_forever</i>";
						}
				echo "</div>
					
					<div class='likes_comments_container'>
						
						<div class='like_container'>";
							if ($likeStuff[$item['id']]['liked_bool'] === 1) {
								echo "<i class='material-icons md-red' style='font-size:27px;'>favorite</i>";
							} else {
								echo "<i class='material-icons md-red'style='font-size:default'>favorite_border</i>";
							}
							echo "<i class='likes_number' photo_id='".$item['id']." '>".$likeStuff[$item['id']]['likes_num']."</i>
							<i class='material-icons show_all_comments'>comment</i>
						</div>
						<div class='comments_container'>
							<div class='prev_comments' title='Show comments'>";
								foreach ($commentStuff[$item['id']] as $value) {
								echo "
								<div class='span-container'>
									<span ><i><ins>".$value['name']."</i></ins>: ". $value['comment']."</span>";
									if ($value['name'] === $_SESSION['loggued_in_user']) {
										echo "<i class='material-icons del-comment' comment_id=".$value['comment_id']." title='Delete this photo'>highlight_off</i>";
									}
								echo "
								</div>";
								}
							echo "
							</div>
							<br>
							<div class='comment_text_container' photo_id=".$item["id"].">
								<textarea class='comment_text' placeholder = 'Write your comment'></textarea>
								<i class='material-icons comment-button'>send</i>
							</div>
						</div>
					</div>
				</div>
				";
				$page_num++;
			}

			//pagination handle
			$num_of_items = count($photoStuff);
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
		?>
	</div>
	<br>
	<?php
		include 'footer.php';
	?>
	<script src="/application/views/js/gallery.js"></script>
</body>
</html>