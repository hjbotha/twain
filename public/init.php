<?php

include '../config.php';
include '../include/functions.php';


$tables = ['users(username STRING, password STRING, plaintext BOOLEAN, email STRING)',
            'dbversion (version INTEGER)',
            'authorised_url_patterns (url_pattern STRING)',
            'authorised_urls (url STRING)',
			'tokens (token STRING, user STRING, authorised BOOLEAN, secret STRING, expiry_date DATETIME)',
			'authorised_networks (address STRING)',];



function create_table($table) {
	return $GLOBALS['db']->query('CREATE TABLE IF NOT EXISTS ' . $table . ';');
}

foreach ($tables as $table) {
	if (create_table($table) === false) {
		echo "Something went wrong. Please check your web server logs.";
		die;
	}
}

$current_version = pdo_select_all($db,'dbversion');
if ($current_version[0]['version'] === '1') {
	header("HTTP/1.1 404 Not Found");
	exit;
}

$db->query("INSERT INTO dbversion (version) VALUES (1)");
echo "Successfully created database.";

?>
