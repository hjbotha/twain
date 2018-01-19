<?php

function delete_old_files($path,$age) {
	$files = glob($path . "/*");
	$now = time();
	foreach ($files as $file) {
		if (is_file($file)) {
			if ($now - filemtime($file) >= $age) {
				unlink($file);
			}
		}
	}
}

function is_authed_url($requrl) {
	$db = $GLOBALS['db'];
	$authed_url_patterns = pdo_select_all($db,'authorised_url_patterns','url_pattern');
	foreach ($authed_url_patterns as $cururl) {
		if (strpos($requrl,$cururl['url_pattern']) === 0) {
			return true;
		}
	}
	
	$authed_exact_urls = pdo_select_all($db,'authorised_urls','url');
	foreach ($authed_exact_urls as $cururl) {
		if (strpos($requrl,$cururl['url']) === 0) {
			return true;
		}
	}
	if (strpos($requrl,$GLOBALS['auth_server_url']) === 0) {
		return true;
	}
	return false;
}

function get_user_property($user,$property) {
	$result = pdo_select($GLOBALS['db'],'users',$property,'username',$user)[0][$property];
	return is_null($result) ? null : $result;

}

function send_auth_email($user,$sourceip,$authlink) {
	$address = get_user_property($user,'email');
	$subject = "Login to Mosli";
	$body = "We have received a request to log you in from $sourceip. To authorise this request, click on the following link.\n\n$authlink";
	mail($address, $subject, $body);
}

function send_email($to, $subject, $body) {
	$mailserver = $GLOBALS['mailserver'];
	$mailuser = $GLOBALS['mailuser'];
	$mailpass = $GLOBALS['mailpass'];
	$mailfrom = $GLOBALS['mailfrom'];
	$mailfromname = $GLOBALS['mailfromname'];
	echo "your email has not been sent";
}

function getGUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }
    else {
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
    }
}

function send_html($head,$form,$tail) {
	echo $head;
	echo $form;
	echo $tail;
}

function printExecutionTime($start,$time_execution = false) {
	if ($time_execution == true) {
		$execution_time = microtime(true) - $start;
		echo "Execution time: " . $execution_time;
	}
}

function get_source($preferred_source) {
	if ($preferred_source) {
		return $preferred_source;
	}
	return $GLOBALS['default_site'];
}

function cidr_match($ip, $range) {
    list ($subnet, $bits) = explode('/', $range);
    $ip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
    return ($ip & $mask) == $subnet;
}


function check_credentials($user, $provided_password) {
	$actual_password = get_user_property($user,'password');
	if ($actual_password === $provided_password) {
			return true;
	}
	return false;
}

function check_token_valid($db, $token, $user) {
	$token = pdo_retrieve_token($db,$token);
	if (($token[0]['expiry_date'] > time()) && ($token[0]['authorised'] === '1' ) && ($token[0]['user'] === $user)) {
		return true;
	} else {
		pdo_delete_token($db,$token);
	}
	return false;
}

function is_secret_correct($db,$token,$user,$provided_secret) {
	$token_entry = pdo_select($db,'tokens','*','token',$token);
	if (($token_entry[0]['authorised'] === '0') && ($token_entry[0]['secret'] === $provided_secret) &&($token_entry[0]['user'] === $user)) {
		return true;
	}
	return false;
}

function pdo_select($db,$table,$returncolumns,$searchcolumn,$searchvalue) {
	$test = $db->prepare("SELECT COUNT(*) FROM $table WHERE $searchcolumn=:searchvalue");
	$test->bindparam(':searchvalue', $searchvalue);
	$test->execute();
	$count = $test->fetchColumn();
	if ($count > 0) {
		$stmt = $db->prepare("SELECT $returncolumns FROM $table WHERE $searchcolumn" . "=" . ":searchvalue");
		$stmt->bindparam(':searchvalue', $searchvalue);
		$stmt->execute();
		$result = $stmt->fetchAll();
		return $result;
	}
	return null;
}

function pdo_select_all($db,$table) {
	$test = $db->prepare("SELECT COUNT(*) FROM $table");
	$test->execute();
	$count = $test->fetchColumn();
	if ($count > 0) {
		$stmt = $db->prepare("SELECT * FROM $table");
		$stmt->execute();
		return $stmt->fetchAll();
	}
	return null;
}

function pdo_delete_token($db,$token) {
	return pdo_delete($db,'tokens','token',$token);
}

function pdo_delete($db,$table,$searchcolumn,$searchvalue) {
    $stmt = $db->prepare("DELETE FROM $table WHERE $searchcolumn=:searchvalue");
    $stmt->bindparam(':searchvalue', $searchvalue);
    $stmt->execute();
    return $stmt->fetchAll();
}

function pdo_create_persistent_token($db,$token,$user) {
	$expiry_date = time() + 60 * 60 * 24 * 365;
	$stmt = $db->prepare("INSERT INTO tokens (token,expiry_date,authorised,user) VALUES (:token,$expiry_date,1,:user)");
	$stmt->bindparam(':token', $token);
	$stmt->bindparam(':user', $user);
	return $stmt->execute();
}

function pdo_create_auth_token($db,$token,$user,$secret) {
	$expiry_date = time() + 60 * 10;
	$stmt = $db->prepare("INSERT INTO tokens (token, secret, expiry_date,authorised,user) VALUES (:token, :secret, $expiry_date,0,:user)");
	$stmt->bindparam(':secret', $secret);
	$stmt->bindparam(':token', $token);
	$stmt->bindparam(':user', $user);
	return $stmt->execute();
}

function generate_secret() {
	$characters = 'QWERTYUIOPLKJHGFDSAZXCVBNMabcdefghijklmnopqrstuvwxyz0123456789';
	$secret = '';
	$max = strlen($characters) - 1;
	for ($i = 0; $i < 12; $i++) {
		$secret .= $characters[mt_rand(0, $max)];
	}
	return $secret;
	
}

function pdo_authorise_session_token($db,$token) {
	$expiry_date = time() + 60 * 10;
    $stmt = $db->prepare("UPDATE tokens SET authorised = 1, secret = '', expiry_date = $expiry_date WHERE token = :token");
    $stmt->bindparam(':token', $token);
    return $stmt->execute();
}

function pdo_reset_token_expiry($db,$token,$length) {
	$expiry_date = time() + $length;
    $stmt = $db->prepare("UPDATE tokens SET expiry_date = $expiry_date WHERE token = :token");
    $stmt->bindparam(':token', $token);
    return $stmt->execute();
}

function pdo_retrieve_token($db, $token) {
	$result = pdo_select($db,"tokens","secret,authorised,expiry_date,user","token",$token);
	return $result;
}

?>
