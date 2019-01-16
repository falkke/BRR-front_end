<?php
	$error = "";

	if(isset($_POST['submit'])) {
		$username = $_SESSION['admin'];
		$password = htmlspecialchars(trim($_POST['password']));
		$password1 = htmlspecialchars(trim($_POST['password1']));
		$password2 = htmlspecialchars(trim($_POST['password2']));
		
		if(does_admin_exist($username, $password) == 1) {
			if($password1 == $password2) {
				$new_password = password_hash($password1, PASSWORD_BCRYPT);
				edit_admin($username, $new_password);
				
				header('Location:index.php?page=settings&password-modified=1');
			}
			
			else {
				$error = "The passwords entered are not the same.";
			}
		}
		else {
			$error = "The current password is not correct.";
		}
	}
?>

<h2 class="page-title">Edit Password</h2>

<?php
	if(isset($error) && !empty($error)) {
		?>
			<p class="alert alert-danger" role="alert"><?=$error?></p>
		<?php
	}
	
	else if(isset($_GET['password-modified']) && !empty($_GET['password-modified']) && ($_GET['password-modified'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The password has been succefully modified.</p>
		<?php
	}
?>

<form method="post" class="form-horizontal form-add-edit">
	<div class="form-group">
		<label for="password" class="col-lg-3 d-inline-block  control-label">Password : </label>
		<input id="password" name="password" type="password" class="col-lg-9 d-inline-block form-control h-100" required autofocus>
	</div>	
	
	<div class="form-group">
		<label for="password1" class="col-lg-3 d-inline-block  control-label">New Password : </label>
		<input id="password1" name="password1" type="password" class="col-lg-9 d-inline-block form-control h-100" required>
	</div>	
	
	<div class="form-group">
		<label for="password2" class="col-lg-3 d-inline-block  control-label">New Password Again : </label>
		<input id="password2" name="password2" type="password" class="col-lg-9 d-inline-block form-control h-100" required>
	</div>	
	
	
	<div class="form-group">
		<div class="col-lg-3 d-inline-block"></div>
		<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=home'" />
		<button class="col-lg-3	pull-right btn btn-default" type="submit" name="submit">Submit</button>
	</div>
</form>