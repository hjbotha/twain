<?php

include '../config.php';
include '../include/functions.php';
include '../include/html.php';

$token = $_GET['token'];
$user = $_GET['user'];
$source = get_source($_POST["source"]);

header("HTTP/1.1 401 Unauthorized");
send_start_to_meta();
send_head();
send_title_to_div($title);
echo "<H2>Timed out</H2><p>Your login attempt timed out.</p>";
send_form($source);
send_tail();
?>