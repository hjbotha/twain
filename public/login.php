<?php

include '../config.php';
include '../include/functions.php';
include '../include/html.php';

$user = strtolower($_POST['username']);
$pass = $_POST['password'];
$source = get_source($_POST["source"]);
$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

if ($user != '') {
	if ($pass != '') {
		if (check_credentials($user, $pass)) {
			$token = getGUID();
			pdo_create_persistent_token($db,$token,$user);
			setcookie('token', $token,        time()+60*60*24*365, '/', "." . $domain);
			setcookie('user', $user,        time()+60*60*24*365, '/', "." . $domain);
			header('Location: ' . $source, true, 301);
			echo "<H2>Redirecting</H2><p>Attempting to redirect you to " . $source . "</p>";
			exit;
		}
	} else {
		if (check_user($user)) {
		error_log("checking user");
			$auth_token = getGUID();
			$secret = generate_secret();
			pdo_create_auth_token($db,$auth_token,$user,$secret);
			$authurl = "https://auth.home.mosli.net/auth.php?user=$user&token=$auth_token&secret=$secret";
			error_log(send_auth_email($user,$ip,$authurl));
		}
		$checkurl = "$auth_server_url/check.php?token=$auth_token&user=$user";
		send_start_to_meta();
		send_head();
		send_js($source,$checkurl);
		send_title_to_div($title, true);
		echo "<h2>Email sent</h2><p>Click the link in the email to authorise this login.</p>";
		send_tail();
		exit;
	}
}

header("HTTP/1.1 401 Unauthorized");
send_start_to_meta();
send_head();
send_title_to_div($title);
if (($user != '') || ($pass != '')) {
	echo "<h2>Wrong username/password</h2><p>Please try again.</p>";
} else {
	send_form_head();
}

send_form($source);
send_tail();
exit;
?>
