<!DOCTYPE html>
<html>
<head>
	<title>User settings</title>
	<link rel="icon" type="image/png" href="/application/views/favicon.png"/>
	<link rel="stylesheet" href="/application/views/css/user_page.css">
</head>
<body>
	<?php
		include 'header.php';
	?>
	<div class="main-container">
		<form name="user_changes"  method="post" >
			<br>
			<span style="font-size: 25px;">You can change your data here</span>
			<br>
			<br>
			<br>
			<div class="grid-wrapper">
				<div>New username:</div>
				<div>
					<input type="text" name="new_username" autocomplete="username" placeholder=<?php echo $user_data['login'];?>>
				</div>
				<br>
				<div>New email:</div>
				<div>
					<input type="text" name="new_email" autocomplete="email" placeholder=<?php echo $user_data['email'] ?>>
				</div>
				<br>
				<div>New password:</div>
				<div>
					<input type="password" name="new_passwd1" autocomplete="new-password">
				</div>
				<br>
				<div>Confirm new password:</div>
				<div>
					<input type="password" name="new_passwd2" autocomplete="new-password">
				</div>
			</div>
			<br>
			<div>Notifications:
				<input type="radio" name="notification" id="yes" value="yes" <?php if ($user_data['notification'] === '1') {echo 'checked';} ?>>Yes
				<input type="radio" name="notification" id="no" value="no" <?php if ($user_data['notification'] === '0') {echo 'checked';} ?>>No
			</div>
			<br>
			<div style="font-weight: bold;">
				<input type="submit" name="save_changes" value="Save changes"/>
			</div>
			</form>
		</div>
	</div>
	<?php include 'footer.php';?>
</body>
</html>