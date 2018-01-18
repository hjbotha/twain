<?php
function send_head($title) {
	echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
<link rel="stylesheet" href="'.$GLOBALS['auth_server_url'].'/styles.css" />
<title>' . $title.'</title>
</head>
<body>
<div class="log-form">
';
}

function send_form($source,$post_target = "https://auth.home.mosli.net/login.php",$message = 'form') {
	echo '
	<h2>Log in to Mosli</h2>
	<form method="post" action="'.$post_target.'">
		<p><input type="text" name="username" placeholder="username" /></p>
		<p><input type="password" name="password" placeholder="password" /></p>
		<input type="hidden" name="source" value="'.$source.'" />
		<p><button type="submit" class="btn">Login</button></p>
	</form>
';
}

function send_tail() {
	echo '
</div></body></html>';
}

?>
