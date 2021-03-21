<?php require('includes/config.php'); 
if ($user->is_logged_in() ){ 
	header('Location: logged_in.php'); 
	exit(); 
}

$resetToken = $_GET['key'];
$stmt = $db->prepare('SELECT resetToken, resetComplete FROM members WHERE resetToken = :token');
$stmt->execute(array(':token' => $resetToken));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($row['resetToken'])){
	$stop = 'Zły token, spróbuj jeszcze raz.';
} elseif($row['resetComplete'] == 'Yes') {
	$stop = 'Hasło zostało juz zmienione.';
}

if (isset($_POST['submit'])){
	if (!isset($_POST['password']) || ! isset($_POST['passwordConfirm'])) {
		$error[] = 'Podaj oba hasła.';
	}
	if (strlen($_POST['password']) < 3){
		$error[] = 'Hasło jest za krótkie.';
	}
	if (strlen($_POST['passwordConfirm']) < 3){
		$error[] = 'Potwierdź hasło jest za krótkie.';
	}
	if ($_POST['password'] != $_POST['passwordConfirm']){
		$error[] = 'Hasła się nie zgadzają.';
	}
	if (!isset($error)){
		$hashedpassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
		try {
			$stmt = $db->prepare("UPDATE members SET password = :hashedpassword, resetComplete = 'Yes'  WHERE resetToken = :token");
			$stmt->execute(array(
				':hashedpassword' => $hashedpassword,
				':token' => $row['resetToken']
			));
			header('Location: login.php?action=resetAccount');
			exit;
		} catch(PDOException $e) {
		    $error[] = $e->getMessage();
		}
	}
}
$title = 'Zmiana hasła';
require('header.php'); 
?>
<div class="container">
	<div class="row">
	    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
	    	<?php if (isset($stop)){
	    		echo "<p class='bg-danger'>$stop</p>";
	    	} else { ?>
				<form role="form" method="post" action="" autocomplete="off">
					<h2>Zmień hasło</h2>
					<hr>
					<?php
					if (isset($error)){
						foreach($error as $error){
							echo '<p class="bg-danger">'.$error.'</p>';
						}
					}
					if (isset($_GET['action'])) {
						switch ($_GET['action']) {
							case 'active':
								echo "<h2 class='bg-success'>Twoje konto jest aktywne, mozesz się zalogować.</h2>";
								break;
							case 'reset':
								echo "<h2 class='bg-success'>Sprawdź e-mail.</h2>";
								break;
						}
					}
					?>
					<div class="row">
						<div class="col-xs-6 col-sm-6 col-md-6">
							<div class="form-group">
								<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Hasło" tabindex="1">
							</div>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6">
							<div class="form-group">
								<input type="password" name="passwordConfirm" id="passwordConfirm" class="form-control input-lg" placeholder="Potwierdź hasło" tabindex="1">
							</div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Zmień hasło" class="btn btn-primary btn-block btn-lg" tabindex="3"></div>
					</div>
				</form>
			<?php } ?>
		</div>
	</div>
</div>
