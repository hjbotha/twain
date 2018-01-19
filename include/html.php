<?php
function send_js($source,$checkurl) {
	echo '
<script>
var start = new Date() / 1000;
var end = start + 600;
var source = "'.$source.'";
var checkurl = "'.$checkurl.'"

function redirect(url) {
	window.location.replace(url);
}

function check_auth() {
	var refreshIntervalId = setInterval(function() {
		var now = new Date() / 1000;
		if (now < end) {
			var request = new XMLHttpRequest();
			request.open("GET", "'.$checkurl.'&check=1", true);
			request.send();
			request.onload = function() {
				if (request.responseText.toString().includes("yes")){
					// Success!
					redirect(checkurl + "&source=" + source);
				}
			}
		} else {
			redirect("/timeout.php")
		}
	}, 2000);
};
</script>
';
}

function send_start_to_meta() {
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
';
}

function send_head() {
	echo '
<head>
<link rel="stylesheet" href="'.$GLOBALS['auth_server_url'].'/styles.css" />
';
}

function send_title_to_div($title, $check_auth = false) {
	echo '
<title>' . $title.'</title>
</head>
';
if ($check_auth == true) {
	echo '<body onload="check_auth()">';
} else {
	echo '<body>';
}
echo '
<div class="log-form">
';
}

function send_form_head() {
	echo '<h2>Log in to Mosli</h2>';
}
function send_form($source,$post_target = "https://auth.home.mosli.net/login.php",$message = 'form') {
	echo '
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