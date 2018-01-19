<?php

include '../config.php';
include '../include/functions.php';
include '../include/html.php';

$token = $_GET['token'];
$user = $_GET['user'];
$source = get_source($_GET["source"]);

if (check_token_valid($db,$token,$user)) {
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
echo "<H1>Not authorised</H1><p>Click the link in the email to authorise this login and then click <a class=\"normal\" href=\"check.php?user=" . $user . "&token=" . $token . "&source=" . $source . "\">here</a> to try again.</p>";
send_tail();

?>