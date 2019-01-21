<?php
	if(is_logged() == 0) {
        header('Location:index.php?page=home');
    }
	
	if(!isset($_GET['runner'])) {
		if($_SESSION['dashboard'] == 1) {
			header('Location:index.php?page=dashboard&list=runners');
		}
		
		else {
			header('Location:index.php?page=runners');
		}
	}
	
	if(!empty($_GET['runner'])) {
		$runner_id = $_GET['runner'];
		
		if(does_runner_exist($runner_id)) {
			$runner = get_runner($runner_id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(isset($_POST['submit'])) {
		$first_name = htmlspecialchars(trim($_POST['first_name']));
		$last_name = htmlspecialchars(trim($_POST['last_name']));
		$birth_date = $_POST['birth_date'];
		
		if(!empty($_GET['runner'])) {
			$runner = get_runner($runner_id);
			edit_runner($runner_id, $first_name, $last_name, $birth_date, $runner->Gender);
			
			if($_SESSION['dashboard'] == 1) {
				header('Location:index.php?page=dashboard&list=runners&runner-modified=1');
			}
			
			else {
				header('Location:index.php?page=runners&runner-modified=1');
			}
		}
		
		else {
			$gender=$_POST['gender'];
			
			add_runner($first_name, $last_name, $birth_date, $gender);
			
			if($_SESSION['dashboard'] == 1) {
				header('Location:index.php?page=dashboard&list=runners&runner-added=1');
			}
			
			else {
				header('Location:index.php?page=runners&runner-added=1');
			}
		}
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<h2 class="page-title">
			<?php
				if(!empty($_GET['runner'])) {
					?>
						Edit Runner
					<?php
				}
				
				else {
					?>
						Add Runner
					<?php
				}
			?>
		</h2>
		
		<form method="post" class="form-horizontal form-add-edit">
			<div class="form-group">
				<label for="first_name" class="col-lg-3 d-inline-block  control-label">First Name : </label>
				<input id="first_name" name="first_name" type="text" class="col-lg-9 d-inline-block form-control h-100" required autofocus 
					<?php
						if(!empty($_GET['runner'])) {
							?>
								value='<?=$runner->FirstName?>'
							<?php
						}
					?>
				>
			</div>	
			
			<div class="form-group">
				<label for="last_name" class="col-lg-3 d-inline-block  control-label">Last Name : </label>
				<input id="last_name" name="last_name" type="text" class="col-lg-9 d-inline-block form-control h-100" required 
					<?php
						if(!empty($_GET['runner'])) {
							?>
								value='<?=$runner->LastName?>'
							<?php
						}
					?>
				>
			</div>	
			
			<div class="form-group">
				<label for="birth_date" class="col-lg-3 d-inline-block  control-label">Birth Date : </label>
				<input id="birth_date" name="birth_date" type="date" class="col-lg-9 d-inline-block form-control h-100" required 
					<?php
						if(!empty($_GET['runner'])) {
							?>
								value='<?=$runner->DateOfBirth?>'
							<?php
						}
					?>
				>
			</div>	
			
			<div class="form-group">
				<label for="gender" class="col-lg-3 d-inline-block control-label">Gender : </label>
				<select id="gender" name="gender" class="col-lg-9 d-inline-block form-control h-100" required 
					<?php
						if(!empty($_GET['runner'])) {
							?>
								disabled
							<?php
						}
					?>
				>
					<option 
						<?php
							if(!empty($_GET['runner']) && ($runner->Gender == "Man")) { 
								?>
									selected="selected"
								<?php
							}
						?>
					>
						Man
					</option>
					
					<option	
						<?php
							if(!empty($_GET['runner']) && ($runner->Gender == "Woman")) {
								?>
									selected="selected"
								<?php
							}
						?>
					>
						Woman
					</option>
				</select>
			</div>	
			
			<div class="form-group">
				<div class="col-lg-3 d-inline-block"></div>
				<?php
					if($_SESSION['dashboard'] == 1) {
						?>
							<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=dashboard&list=runners'" />
						<?php
					}
					
					else {
						?>
							<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=runners'" />
						<?php
					}
				?>
				<button class="col-lg-3	pull-right btn btn-default" type="submit" name="submit">Submit</button>
			</div>
		</form>
	</div>
</main>
