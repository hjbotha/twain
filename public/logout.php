<?php

include '../config.php';
include '../include/functions.php';
include '../include/html.php';

$token = $_COOKIE['token'];
$user = $_COOKIE['user'];

pdo_delete_token($db,$token);

setcookie('token',"",        time()+60*60*24*14, '/', "." . $domain);
setcookie('user',"",        time()+60*60*24*14, '/', "." . $domain);
send_head($title);
echo "<H2>Logged out</H2>";
echo "You have successfully logged out.";
send_form("");
send_tail();
exit;
?>
