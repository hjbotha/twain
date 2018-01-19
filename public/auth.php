<?php

include '../config.php';
include '../include/functions.php';
include '../include/html.php';

$token = $_GET['token'];
$provided_secret = $_GET['secret'];
$user = $_GET['user'];

if (is_secret_correct($db,$token,$user,$provided_secret)) {
	pdo_authorise_session_token($db,$token);
	send_start_to_meta();
	send_head();
	send_title_to_div($title);
	echo '<h2>Successfully authorised</h2>';
	send_tail();
	exit;
}

header("HTTP/1.1 401 Unauthorized");
send_start_to_meta();
send_head();
send_title_to_div($title);
echo "<H2>Invalid token/secret</H2>";
send_tail();
?>
