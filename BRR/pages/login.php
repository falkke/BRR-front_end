<?php
	if(is_logged() == 1) {
        header('Location:index.php?page=home');
    }

	if(isset($_POST['submit'])) {
		$username = htmlspecialchars(trim($_POST['username']));
		$password = htmlspecialchars(trim($_POST['password']));
		
		if(user_exist($username, $password) == 1) {
			$_SESSION['brr'] = $username;
			header('Location:index.php?page=admin');
		}
		else {
			$error_user_not_found = "User not found...";
		}
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<h2 class="page-title">Login</h2>
		<form method="post" class="form-login">
			<label for="username" class="sr-only">Username</label>
			<input type="username" id="username" name="username" class="form-control" placeholder="Username" required autofocus>
			<label for="password" class="sr-only">Password</label>
			<input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
			<button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Login</button>
		</form>
	</div>
</main>
