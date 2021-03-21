<?php
ob_start();
session_start();
date_default_timezone_set('Europe/London');
define('DBHOST','localhost');
define('DBUSER','root');
define('DBPASS','');
define('DBNAME','uzytkownicy');

define('SITEEMAIL','email@email.pl');

try {
	$db = new PDO("mysql:host=".DBHOST.";charset=utf8mb4;dbname=".DBNAME, DBUSER, DBPASS);
} catch(PDOException $e) {
    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
    exit;
}

include('classes/user.php');
include('classes/phpmailer/mail.php');
$user = new User($db);
?>
