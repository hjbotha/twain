<?php
$time_execution = false;

$start = microtime(true);

include '../config.php';
include '../include/functions.php';
include '../include/html.php';

$basic_user = $_SERVER["PHP_AUTH_USER"];
$basic_pass = $_SERVER["PHP_AUTH_PW"];
$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
$requrl = $_SERVER["HTTP_X_FORWARDED_PROTO"] . "://" . $_SERVER["HTTP_X_FORWARDED_HOST"] . $_SERVER["HTTP_X_FORWARDED_URI"];
$source = get_source($requrl);
$cookie_user = $_COOKIE['user'];
$token = $_COOKIE['token'];

// Allow the request if it's for the auth site (defined in the config file)
// or it matches an exact allowed URL (authed_urls table)
// or starts with an allowed URL (authed_url_patterns table)
if (is_authed_url($requrl)) {
	exit;
}

// Allow the request if it comes from an IP address matching a network present in the authed_networks table
// allow_if_request_from_authed_network($);

// Allow if the request includes basic authentication credentials matching a user in the users table.
// allow_if_basic_authentication($);

// Allow if the request include a cookie with a valid user and token.
// allow_if_valid_token($user,$token);

$authed_subnets = pdo_select_all($db,'authorised_networks','address');
if (isset($authed_subnets)) {
	foreach ($authed_subnets as $address) {
		if (cidr_match($ip,$address['address']) == true) {
			return true; 
		}
	}
}

// handle basic authentication
if ((isset($basic_user)) && (isset($basic_pass))) {
	if (check_credentials($basic_user,$basic_pass)) {
		printExecutionTime($start, $time_execution);
		exit;
	}
}

if (check_token_valid($db, $token,$cookie_user)) {
	if (get_token_authmethod($token) === "password") {
		pdo_update_auth_token($db,$token);
	}
    printExecutionTime($start, $time_execution);
	exit;
}


header("HTTP/1.1 401 Unauthorized");
send_start_to_meta();
send_head();
send_title_to_div($title);
send_form_head();
send_form($source);
send_tail();
exit;

?>
