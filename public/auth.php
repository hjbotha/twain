<?php

include '../config.php';
include '../include/functions.php';
include '../include/html.php';

$token = $_GET['token'];
$provided_secret = $_GET['secret'];
$user = $_GET['user'];
/*
if (isset($token)) {
	$token_file = $token_dir . "/" . $_GET['token'];
	if (file_exists($token_file)) {
	    $token_file_descriptor = fopen($token_file, "r");
		$actual_secret = fread($token_file_descriptor,filesize($token_file));
		fclose($token_file_descriptor);
		$provided_secret = $_GET['secret'];
		if ($provided_secret === $actual_secret) {
			$token_file_descriptor = fopen($token_file, "w");
			fwrite($token_file_descriptor,"authorised");
			fclose($token_file_descriptor);
			echo 'Successfully authorised';
	        exit;
		} else {
			fclose($token_file_descriptor);
			// invalidate token to prevent brute force
			unlink($token_file);
		}
	}
}
*/

if (is_secret_correct($db,$token,$user,$provided_secret)) {
	pdo_authorise_session_token($db,$token);
	send_head($title);
	echo '<h1>Successfully authorised</h1>';
	send_tail();
	exit;
}

header("HTTP/1.1 401 Unauthorized");
send_head($title);
echo "<H1>Invalid token/secret</H1>";
send_tail();

?>
