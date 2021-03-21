<?php require('includes/config.php');

if ($user->is_logged_in()){ 
	header('Location: logged_in.php'); 
	exit(); 
}

if (isset($_POST['submit'])){
	if (!isset($_POST['email'])) {
    	$error[] = "Wypełnij wszystkie pola";
	}

	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
	    $error[] = 'Podaj poprawny e-mail';
	} else {
		$stmt = $db->prepare('SELECT email FROM members WHERE email = :email');
		$stmt->execute(array(':email' => $_POST['email']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (empty($row['email'])){
			$error[] = 'Taki e-mail nie istnieje.';
		}
	}

	if (!isset($error)){
		$token = md5(uniqid(rand(),true));
		try {
			$stmt = $db->prepare("UPDATE members SET resetToken = :token, resetComplete='No' WHERE email = :email");
			$stmt->execute(array(
				':email' => $row['email'],
				':token' => $token
			));

			$to = $row['email'];
			$subject = "Reset hasla";
			$body = "<p>Link do resetu hasla: <a href='http://192.168.64.2/projekt/resetPassword.php?key=$token'>http://192.168.64.2/projekt/resetPassword.php?key=$token</a></p>";

			$mail = new Mail();
			$mail->setFrom(SITEEMAIL);
			$mail->addAddress($to);
			$mail->subject($subject);
			$mail->body($body);
			$mail->send();

			header('Location: login.php?action=reset');
			exit;

		} catch(PDOException $e) {
		    $error[] = $e->getMessage();
		}
	}
}

$title = 'Zresetuj konto';
require('layout/header.php');
?>

<div class="container">
	<div class="row">
	    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<form role="form" method="post" action="" autocomplete="off">
				<h2>Resetuj hasło</h2>
				<p><a href='login.php'>Wróć na stronę logowania.</a></p>
				<hr>
				<?php
				if (isset($error)){
					foreach($error as $error){
						echo '<p class="bg-danger">'.$error.'</p>';
					}
				}
				if (isset($_GET['action'])){
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
				<div class="form-group">
					<input type="email" name="email" id="email" class="form-control input-lg" placeholder="E-mail" value="" tabindex="1">
				</div>
				<hr>
				<div class="row">
					<div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Wyślij link" class="btn btn-primary btn-block btn-lg" tabindex="2"></div>
				</div>
			</form>
		</div>
	</div>
</div>