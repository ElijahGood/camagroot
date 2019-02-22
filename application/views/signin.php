<!DOCTYPE html>
<html>
<head>
	<title>Sign in</title>
	<link rel="icon" type="image/png" href="/application/views/favicon.png"/>
</head>
<body>
	<?php include 'header.php';?>
	<?php 
	if (isset($_SESSION['loggued_in_user'])) {
		header("Location: /index");
	} else {
		echo '
		
		<div class="main-container" style="text-align: center;height: 86vh;">
			<br>
			<span style="font-size:23px;">Log in action</span>
			<br>
			<br>
			<form name="login"  method="post" >
				<div style="text-align: center;">Username:
				<input type="text" name="username"></div>

				<div style="text-align: center;">Password:
				<input type="password" name="passwd"></div>
				<br>
				<div style="text-align: center;  font-weight: bold;">
					<input type="submit" name="signin_try" value="Sign in"/></div>
					<br>
				<div style="text-align: center;">
					<input type="submit" name="forgot_pass" value="Forgot my password"/>
				</div>
			</form>
				<div style="text-align: center;">
				<a href="/create"><button>Register</button></a>
				</div>
		</div>';
	}
	?>
	<?php include 'footer.php';?>
</body>
</html>