<html>
<head>
	<title>Restore password</title>
	<link rel="icon" type="image/png" href="/application/views/favicon.png"/>
</head>
<body>
	<?php include 'header.php';?>
	<div style="text-align: center;height: 86vh;">
		<form name="login"  method="post" >
			<br>
			<span style="font-size:23px;">Please change your password</span>
			<br>
			<br>
			<div>Enter new password:
			<input type="password" name="new_pass"></div>
			<br>
			<div>Confirm new password:
			<input type="password" name="new_pass_confirm"></div>
			<br>
			<div style="font-weight: bold;">
				<input type="submit" name="save_new_password" value="Save new password"/></div>
		</form>
	</div>
	<?php include 'footer.php';?>
</body>
</html>