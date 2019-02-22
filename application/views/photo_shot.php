<!DOCTYPE html>
<html>
<head>
	<title>Photo maker</title>
	<link rel="icon" type="image/png" href="/application/views/favicon.png"/>
	<link rel="stylesheet" href="/application/views/css/photo_shot.css">
</head>
<body>
	<?php
		include 'header.php';
	?>
	<br>
	<div>
	<div class="common_container">
		<div class="photo_screen">
			<div class="video_container">
				<video id="video" width="640" height="480" autoplay></video>
				<img id="video_sticker" src="/custom_stickers/groot-2.png">
				<img id="video_image" width="640" height="480" src="">
			</div>
			<br>
			<div class="buttons-container">
				<input class="upload-button" name="upload_photo" type="file">
				<i class="material-icons photo" title="Take a snap" id="shot">camera</i>
				<i class="material-icons photo" title="Clear screen" id="clear_video">photo_filter</i>
				<i class="material-icons photo" title="Save photo to gallery" id="save_photo">save</i>
			</div>
			<br>
			<canvas id="canvas" width="640" height="480"></canvas>
			<br>
			
		</div>
		<div id="stickers_id" class="stickers_container">
			<img class="stickers" src="/custom_stickers/groot-1.png">
			<img class="stickers" src="/custom_stickers/groot-2.png">
			<img class="stickers" src="/custom_stickers/groot-3.png">
			<img class="stickers" src="/custom_stickers/rocket-2.png">
			<img class="stickers" src="/custom_stickers/teen-groot.png">
			<img class="stickers" src="/custom_stickers/groot-big.png">
			<img class="stickers" src="/custom_stickers/minion.png">
			<img class="stickers" src="/custom_stickers/minion1.png">
			<img class="stickers" src="/custom_stickers/batman.png">
			<img class="stickers" src="/custom_stickers/Captain.png">
			<img class="stickers" src="/custom_stickers/Peter_Griffin.png">
			<img class="stickers" src="/custom_stickers/Brian.png">
			<img class="stickers" src="/custom_stickers/stewie.png">
			<img class="stickers" src="/custom_stickers/deadpool2.png">
		</div>
	</div>
	<?php
		include 'footer.php';
	?>
	<script src="/application/views/js/photo_shot.js"></script>
</body>
</html>