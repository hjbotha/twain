<?php

include '../config.php';
include '../include/functions.php';
include '../include/html.php';

$user = $_POST['username'];
$pass = $_POST['password'];
$source = get_source($_POST["source"]);
$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

if ($user != '') {
	if ($pass != '') {
		echo 'checking creds';
		if (check_credentials($user, $pass)) {
/*
			$permanent_token = getGUID();
			$permanent_token_file = $permanent_token_dir . "/" . $permanent_token;
			$permanent_token_file_descriptor = fopen($permanent_token_file, 'w') or die('Cannot open file: '.$permanent_token_file); //implicitly creates file
			fclose($permanent_token_file_descriptor);
			setcookie('permanent_token', $permanent_token,        time()+60*60*24*365, '/', "." . $domain);
*/
			var_dump($user);
			$token = getGUID();
			pdo_create_persistent_token($db,$token,$user);
			header('Location: ' . $source, true, 301);
			setcookie('token', $token,        time()+60*60*24*365, '/', "." . $domain);
			setcookie('user', $user,        time()+60*60*24*365, '/', "." . $domain);
			echo "<H1>Attempting to redirect you to " . $source . "</H1>";
			exit;
		} else {
			var_dump($user);
			header("HTTP/1.1 401 Unauthorized");
			send_head($title);
			echo "<p>Wrong username/password. Please try again.</p>";
			send_form($source);
			send_tail();
			exit;

		}
	} else {
/*
		$auth_token = getGUID();
		$auth_token_file = $auth_token_dir . "/" . $auth_token;
		$auth_token_file_descriptor = fopen($auth_token_file, 'w') or die('Cannot open file: '.$auth_token_file); //implicitly creates file
		fwrite($auth_token_file_descriptor,$secret);
		fclose($auth_token_file_descriptor);
		$authurl = "https://auth.home.mosli.net/auth.php?token=$auth_token&secret=$secret";
		send_auth_email($user,$ip,$authurl);
		// send email containing token and secret
		// deliver JS which
		//	loops until check.php?token=$token returns 200
		//	redirects to auth.php
		header("HTTP/1.1 401 Unauthorized");
		send_head($title);
		echo "<H1>Email sent.</H1><p>Click the link in the email to authorise this login and then click <a class=\"normal\" href=\"check.php?token=" . $auth_token . "&source=" . $source . "\">here</a> to log in.</p>";
		send_tail();
		exit;
*/
		$auth_token = getGUID();
		$secret = generate_secret();
		pdo_create_auth_token($db,$auth_token,$user,$secret);
		$authurl = "https://auth.home.mosli.net/auth.php?user=$user&token=$auth_token&secret=$secret";
//		echo $authurl;
		send_auth_email($user,$ip,$authurl);
		send_head($title);
		echo "<h2>Email sent</h2><p>Click the link in the email to authorise this login and then click <a class=\"normal\" href=\"check.php?user=$user&token=$auth_token&source=$source\">here</a> to log in.</p>";
		send_tail();
		exit;
	}
}

header("HTTP/1.1 401 Unauthorized");
send_head($title);
send_form($source);
send_tail();
exit;
?>
