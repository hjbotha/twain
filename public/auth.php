<?php

include '../config.php';
include '../include/functions.php';
include '../include/html.php';

$token = $_GET['token'];
$provided_secret = $_GET['secret'];
$user = $_GET['user'];
$source = $_GET["source"];

if (is_secret_correct($db,$token,$user,$provided_secret)) {
	pdo_authorise_session_token($db,$token);
	setcookie('token', $token, 0, '/', "." . $domain);
	setcookie('user', $user, 0, '/', "." . $domain);
	header('Location: ' . $source, true, 301);
	send_head($title);
	echo "<H1>Success! Attempting to redirect you to " . $source . "</H1>";
	send_tail();
	exit;
}

header("HTTP/1.1 401 Unauthorized");
send_head($title);
echo "<H2>Invalid token/secret</H2>";
send_tail();

?>
