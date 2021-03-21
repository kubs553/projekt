<?php
require('includes/config.php');

$userID = trim($_GET['x']);
$active = trim($_GET['y']);

if (is_numeric($userID) && !empty($active)) {
	$stmt = $db->prepare("UPDATE members SET active = 'Yes' WHERE memberID = :memberID AND active = :active");
	$stmt->execute(array(
		':memberID' => $userID,
		':active' => $active
	));

	if ($stmt->rowCount() == 1){
		header('Location: login.php?action=active');
		exit;

	} else {
		echo "Twoje konto nie moze zostać aktywowane."; 
	}
}
?>