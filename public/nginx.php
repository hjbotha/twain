<?php

include 'include/config.php';
include 'include/functions.php';
include 'include/html.php';

$user = getValueFrom('username');
$hashed_pass = getValueFrom('password');
$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
$source =  get_source($_GET["from"]);

$requrl = $_SERVER["HTTP_X_FORWARDED_PROTO"] . "://" . $_SERVER["HTTP_X_FORWARDED_HOST"] . "/" . $_SERVER["HTTP_X_REQUEST_URI"];
foreach ($authed_urls as $cururl) {
	if (strpos($cururl,$requrl)) {
		exit;
	}
}

if (check_ip($ip,$authed_subnets)) {
	exit;
}

if (in_array($_SERVER["HTTP_HOST"],$authed_backend_servers)) {
	exit;
}

if (isset($_SERVER["Authorization"])) {
	$http_auth_string = $explode(" ", $_SERVER["Authorization"])[1];
	foreach ($authed_users as $user) {
		if (base64_encode($user) == $http_auth_string) {
			exit;
		}
	}
}	

if (check_credentials($user, $hashed_pass, $authed_users)) {
	/* Set cookie to last 1 year */
	setcookie('username', $user, time()+60*60*24*365, '/', '.home.mosli.net');
	setcookie('password', $hashed_pass, time()+60*60*24*365, '/', '.home.mosli.net');
	exit;
}

header("HTTP/1.1 401 Unauthorized");




?>
