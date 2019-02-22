<!DOCTYPE html>
<html>
<head>
	<title>User creation</title>
	<link rel="icon" type="image/png" href="/application/views/favicon.png"/>
</head>
<body>
	<?php include 'header.php';?>
	<div style="text-align: center;height: 86vh;">
		<form name="login"  method="post" >
			<br>
			<span style="font-size: 23px;">Create new user</span>
			<br>
			<br>
			<div>Username:<br>
			<input type="text" name="login"></div>

			<div>Email:<br>
			<input type="text" name="email"></div>

			<div>Password:<br>
			<input type="password" name="passwd1"></div>

			<div>Password confirmation:<br>
			<input type="password" name="passwd2"></div>

			<br/>
			<div>
				<input type="submit" name="user_create_submit" value="Create account"/>
			</div>
		</form>
	</div>
	<?php include 'footer.php';?>
</body>
</html>