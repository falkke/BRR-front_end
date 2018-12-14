<?php
	$error = "";

	if(isset($_POST['submit'])) {
		$username = htmlspecialchars(trim($_POST['username']));
		$password = htmlspecialchars(trim($_POST['password']));
		
		if(does_admin_exist($username, $password) == 1) {
			$_SESSION['admin'] = $username;
			$_SESSION['dashboard'] = 0;
			
			header('Location:index.php?page=admin');
		}
		
		else {
			$error = "The username and password you entered do not match those in our database. Please check and try again.";
		}
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<h2 class="page-title">Login</h2>
		
		<?php
			if(isset($error) && !empty($error)) {
				?>
					<p class="alert alert-danger" role="alert"><?=$error?></p>
				<?php
			}
		?>
		
		<form method="post" class="form-login">
			<label for="username" class="sr-only">Username</label>
			<input type="username" id="username" name="username" class="form-control" placeholder="Username" required autofocus>
			<label for="password" class="sr-only">Password</label>
			<input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
			
			<button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Login</button>
		</form>
	</div>
</main>