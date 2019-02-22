<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="/application/views/css/header.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<title></title>
</head>
<body>
	<div>
		<?php
			if (isset($_SESSION['loggued_in_user']) && ($_SESSION['loggued_in_user'])) {
				echo '	<div class="header-container">
							
							<span>What\'s up, '.$_SESSION['loggued_in_user'].'?</span>
							<a href="/logout" title="Log Out"><i class="material-icons">directions_run</i></a>
							<a href="/gallery" title="Your gallery"><i class="material-icons">photo_library</i></a>
							<a href="/user_page" title="Settings"><i class="material-icons">settings</i></a>
							<a href="/photo" title="Take a photo"><i class="material-icons">party_mode</i></a>
							<a href="/index" title="Main page"><i class="material-icons">home</i></a>
						</div>';
			} else {
				echo '	<div class="header-container">
							<span>What\'s up, geek? Please login or join us! </span>
							<a href="/create" title="Create account"><i class="material-icons">person_add</i></a>
							<a href="/signin" title="Sign In"><i class="material-icons">person_outline</i></a>
						</div>';
			}
			if (isset($_SESSION['tempor_alert'])) {
				echo "<script>alert(\"".$_SESSION['tempor_alert']."\");</script>";
				unset($_SESSION['tempor_alert']);
			}
		?>
	</div>
</body>
</html>