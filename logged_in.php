<?php require('includes/config.php'); 

if (!$user->is_logged_in()){
    header('Location: login.php'); 
    exit(); 
}

$title = 'Strona logowania';
require('header.php'); 
?>

<div class="container">
	<div class="row">
	    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
				<h2>Witaj, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES); ?></h2>
				<p><a href='logout.php'>Wyloguj się</a></p>
				<hr>
		</div>
	</div>
</div>

