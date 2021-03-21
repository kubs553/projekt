<?php 
require('includes/config.php');

if ($user->is_logged_in() ){ 
	header('Location: logged_in.php'); 
	exit(); 
}

if(isset($_POST['submit'])){
    if (!isset($_POST['username'])) {
    	$error[] = "Wypełnij wszystkie pola";
    }

    if (!isset($_POST['email'])) {
    	$error[] = "Wypełnij wszystkie pola";
    }

    if (!isset($_POST['password'])) {
    	$error[] = "Wypełnij wszystkie pola";
    }

	$username = $_POST['username'];

	if (!$user->isValidUsername($username)){
		$error[] = 'Za krótki login.';
	} else {
		$stmt = $db->prepare('SELECT username FROM members WHERE username = :username');
		$stmt->execute(array(':username' => $username));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!empty($row['username'])){
			$error[] = 'Taki login juz istnieje.';
		}
	}

	if (strlen($_POST['password']) < 3){
		$error[] = 'Hasło jest za krótkie.';
	}

	if (strlen($_POST['passwordConfirm']) < 3){
		$error[] = 'Hasło jest za krótkie';
	}

	if ($_POST['password'] != $_POST['passwordConfirm']){
		$error[] = 'Hasła się nie zgadzają.';
	}

	$email = htmlspecialchars_decode($_POST['email'], ENT_QUOTES);
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
	    $error[] = 'Podaj poprawny adres e-mail.';
	} else {
		$stmt = $db->prepare('SELECT email FROM members WHERE email = :email');
		$stmt->execute(array(':email' => $email));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!empty($row['email'])){
			$error[] = 'Taki e-mail juz istnieje.';
		}
	}


	if (!isset($error)){
		$hashedpassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
		$activasion = md5(uniqid(rand(),true));

		try {
			$stmt = $db->prepare('INSERT INTO members (username,password,email,active) VALUES (:username, :password, :email, :active)');
			$stmt->execute(array(
				':username' => $username,
				':password' => $hashedpassword,
				':email' => $email,
				':active' => $activasion
			));
			$id = $db->lastInsertId('memberID');

			$to = $_POST['email'];
			$subject = "Potwierdzenie rejestracji";
			$body = "<p>Dziekujemy za rejestracje.</p>
			<p>Aby aktywowac konto, kliknij w podany link: <a href='http://192.168.64.2/projekt/activate.php?x=$id&y=$activasion'>http://192.168.64.2/projekt/activate.php?x=$id&y=$activasion</a></p>
			<p>Pozdrawiamy</p>";

			$mail = new Mail();
			$mail->setFrom(SITEEMAIL);
			$mail->addAddress($to);
			$mail->subject($subject);
			$mail->body($body);
			$mail->send();

			header('Location: index.php?action=joined');
			exit;

		} catch(PDOException $e) {
		    $error[] = $e->getMessage();
		}
	}
}
$title = 'Strona główna';
require('header.php');
?>

<div class="container">
	<div class="row">
	    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<form role="form" method="post" action="" autocomplete="off">
				<h2>Rejestracja</h2>
				<p>Posiadasz juz konto? <a href='login.php'>Zaloguj się</a></p>
				<hr>
				<?php
				if(isset($error)){
					foreach($error as $error){
						echo '<p class="bg-danger">'.$error.'</p>';
					}
				}
				if(isset($_GET['action']) && $_GET['action'] == 'joined'){
					echo "<h2 class='bg-success'>Rejestracja udana, otwórz skrzynkę pocztową i aktywuj konto.</h2>";
				}
				?>
				<div class="form-group">
					<input type="text" name="username" id="username" class="form-control input-lg" placeholder="Login" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['username'], ENT_QUOTES); } ?>" tabindex="1">
				</div>
				<div class="form-group">
					<input type="email" name="email" id="email" class="form-control input-lg" placeholder="Adres e-mail" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['email'], ENT_QUOTES); } ?>" tabindex="2">
				</div>
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="form-group">
							<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Hasło" tabindex="3">
						</div>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="form-group">
							<input type="password" name="passwordConfirm" id="passwordConfirm" class="form-control input-lg" placeholder="Potwierdź hasło" tabindex="4">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Zarejestruj się" class="btn btn-primary btn-block btn-lg" tabindex="5"></div>
				</div>
			</form>
		</div>
	</div>
</div>