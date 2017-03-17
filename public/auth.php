<?php
error_log(json_encode($_GET));
if(!array_key_exists('pass', $_GET) || $_GET['pass'] != "becarefulhowyoulogin") {
	echo "invalid credentials";
	header('HTTP/1.0 403 Forbidden');
	exit(1);
}
