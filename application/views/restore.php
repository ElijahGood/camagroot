<html>
<head>
	<title>Restore request</title>
	<link rel="icon" type="image/png" href="/application/views/favicon.png"/>
</head>
<body>
	<?php include 'header.php';?>
	<div style="text-align: center;height: 86vh;">
		<form name="login"  method="post" >
			<br>
			<span style="font-size:23px;">Send password restoring message on your email</span>
			<br>
			<br>
			<div>Enter your email:
			<input type="text" name="restore_email"></div>
			<br>
			<div style="font-weight: bold;">
				<input type="submit" name="restore_msg_submit" value="Send restore message"/></div>
		</form>
	</div>
	<?php include 'footer.php';?>
</body>
</html>