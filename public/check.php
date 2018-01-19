<?php

include '../config.php';
include '../include/functions.php';
include '../include/html.php';

$token = $_GET['token'];
$user = $_GET['user'];
$source = get_source($_GET["source"]);
$check = $_GET['check'];

if ($check == 1) {
	if (check_token_valid($db,$token,$user)) {
		echo 'yes';
		exit;
	} else {
		echo 'no';
		exit;
	}
}

if (check_token_valid($db,$token,$user)) {
	setcookie('token', $token, 0, '/', "." . $domain);
	setcookie('user', $user, 0, '/', "." . $domain);
	header('Location: ' . $source, true, 301);
	exit;
}

header("HTTP/1.1 401 Unauthorized");
?>