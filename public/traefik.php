<?php

include '../config.php';
include '../include/functions.php';
include '../include/html.php';
include '../include/init.php';

if (!rand(0,999)) {
	delete_expired_tokens($db);
}

$basic_user = $_SERVER["PHP_AUTH_USER"];
$basic_pass = $_SERVER["PHP_AUTH_PW"];
$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
$requrl = $_SERVER["HTTP_X_FORWARDED_PROTO"] . "://" . $_SERVER["HTTP_X_FORWARDED_HOST"] . $_SERVER["HTTP_X_FORWARDED_URI"];
$source = get_source($requrl);
$cookie_user = $_COOKIE['user'];
$token = $_COOKIE['token'];

// Allow the request if it comes from an IP address matching a network present in the authed_networks table
$authed_subnets = pdo_select_all($db,'authorised_networks','address');
if (isset($authed_subnets)) {
	foreach ($authed_subnets as $address) {
		if (cidr_match($ip,$address['address']) == true) {
        printExecutionTime($start, $time_execution);
	    exit;
		}
	}
}

$matching_uris = get_uris_from_db($requrl);
if ($matching_uris) { 												// if the request is for an address in the uris table
	foreach ($matching_uris as $matching_uri) { 					// for each matching uri
		if ($matching_uri['anonymous'] == TRUE) {					// if anonymous access is allowed to this uri
			printExecutionTime($start, $time_execution);
			exit;													// allow the request
		}
		$networks = explode(',',$matching_uri['networks']); 		// explode the comma-separate list of networks in that row into an array
		foreach ($networks as $network) { 							// for each network
			if (cidr_match($ip,$network) == true) { 				// if the client's IP matches
				printExecutionTime($start, $time_execution);
				exit; 												// allow the request
			}
		}
		if (check_token_valid($db, $token,$cookie_user)) {			// if the requesting user has a valid token
			if (get_token_authmethod($token) === "password") {		// if that token was for a password (as opposed to email) authentication
				pdo_update_auth_token($db,$token);					// reset the expiry time of that token
			}
			$users = explode(',',$matching_uri['users']);			// explode the comma-separated list of users from the matching entry in the uris table
			if ((in_array($cookie_user,$users)) || (in_array('*',$users))) { 
																	// if the requesting user is allowed to access the requested resource
				printExecutionTime($start, $time_execution);
				exit;												// allow the request
			}
		}
		if ((isset($basic_user)) && (isset($basic_pass)) && (check_credentials($basic_user,$basic_pass))) {
			printExecutionTime($start, $time_execution);
			exit;
		}
	}
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
