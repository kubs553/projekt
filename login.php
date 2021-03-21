<?php
require_once('includes/config.php');
if( $user->is_logged_in() ){ header('Location: index.php'); exit(); }

if(isset($_POST['submit'])){
	if (!isset($_POST['username'])) {
    	$error[] = "Wypełnij wszystkie pola";
	}
	if (!isset($_POST['password'])) {
    	$error[] = "Wypełnij wszystkie pola";
	}
	$username = $_POST['username'];
	if ($user->isValidUsername($username)){
		if (!isset($_POST['password'])){
			$error[] = 'Wpisz hasło';
		}
		$password = $_POST['password'];
		if ($user->login($username, $password)){
			$_SESSION['username'] = $username;
			header('Location: logged_in.php');
			exit;
		} else {
			$error[] = 'Zła nazwa uzytkownika lub hasło, bądź konto nie jest aktywowane.';
		}
	}else{
		$error[] = 'Za krótki login';
	}
}

$title = 'Logowanie';
require('header.php'); 
?>
<div class="container">
	<div class="row">
	    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<form role="form" method="post" action="" autocomplete="off">
				<h2>Logowanie</h2>
				<p><a href='./'>Wróć na stronę główną.</a></p>
				<hr>
				<?php
				if (isset($error)){
					foreach ($error as $error){
						echo '<p class="bg-danger">'.$error.'</p>';
					}
				}
				if (isset($_GET['action'])){
					switch ($_GET['action']) {
						case 'active':
							echo "<h2 class='bg-success'>Twoje konto zostało aktywowane, mozesz się zalogować.</h2>";
							break;
						case 'reset':
							echo "<h2 class='bg-success'>Sprawdź e-mail, gdzie przyszedł link do resetu hasła.</h2>";
							break;
						case 'resetAccount':
							echo "<h2 class='bg-success'>Hasło zostało zmienione, mozesz się zalogować.</h2>";
							break;
					}

				}
				?>
				<div class="form-group">
					<input type="text" name="username" id="username" class="form-control input-lg" placeholder="Login" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['username'], ENT_QUOTES); } ?>" tabindex="1">
				</div>
				<div class="form-group">
					<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Hasło" tabindex="3">
				</div>
				<div class="row">
					<div class="col-xs-9 col-sm-9 col-md-9">
						 <a href='reset.php'>Zapomniałeś hasła?</a>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Zaloguj się" class="btn btn-primary btn-block btn-lg" tabindex="5"></div>
				</div>
			</form>
		</div>
	</div>
</div>