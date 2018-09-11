<?php

include '../config.php';
include '../include/functions.php';


$tables = ['users(username STRING, password STRING, plaintext BOOLEAN, email STRING)',
            'dbversion (version INTEGER)',
			'tokens (token STRING, user STRING, authorised BOOLEAN, secret STRING, expiry_date DATETIME, authmethod STRING, status BOOLEAN)',
			'authorised_networks (address STRING)',
			'uris (networks STRING, users STRING, networks STRING, anonymous BOOLEAN)'];

function create_table($table) {
	return $GLOBALS['db']->query('CREATE TABLE IF NOT EXISTS ' . $table . ';');
}

$current_version = pdo_select_all($db,'dbversion');

if ($current_version[0]['version'] === '1') {
	$db->query("ALTER TABLE tokens ADD authmethod STRING");
	$db->query("ALTER TABLE tokens ADD status BOOLEAN");
	$db->query("UPDATE dbversion SET version = 2");
	echo "Successfully updated database to version 2.";
	exit;
}

if ($current_version[0]['version'] === '2') {
	$db->query("CREATE TABLE uris (networks STRING, users STRING, networks STRING, anonymous BOOLEAN)");
	$db->query("UPDATE dbversion SET version = 3");
	echo "Successfully updated database to version 3.";
	echo "NOTE: authorised_url_patterns and authorised_urls tables are no longer used.";
	echo "NOTE: These tables need to be migrated manually to the newly created uris table.";
	echo "NOTE: Delete the redundant tables when done.";
	exit;
}

if ($current_version[0]['version'] === '3' ) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

foreach ($tables as $table) {
	if (create_table($table) === false) {
		echo 'Creating table' . $table;
		echo "Something went wrong. Please check your web server logs.";
		die;
	}
}

$db->query("INSERT INTO dbversion (version) VALUES (3)");
echo "Successfully created database.";

?>
