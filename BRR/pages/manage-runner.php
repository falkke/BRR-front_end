<?php
	if(is_logged() == 0) {
        header('Location:index.php?page=home');
    }
	
	if(isset($_GET['runner']) && !empty($_GET['runner'])) {
		$id = $_GET['runner'];
		
		if(runner_exists($id)) {
			$runner = get_runner($id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(isset($_POST['submit'])) {
		//$ssn = htmlspecialchars(trim($_POST['ssn']));
		$first_name = htmlspecialchars(trim($_POST['first_name']));
		$last_name = htmlspecialchars(trim($_POST['last_name']));
		$birth_date = $_POST['birth_date'];
		$gender = $_POST['gender'];
		
		if(isset($_GET['runner'])) {
			edit_runner($id, $first_name, $last_name, $birth_date, $gender);
		}
		
		else {
			add_runner($first_name, $last_name, $birth_date, $gender);
		}
		
        header('Location:index.php?page=runner-list');
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<h2 class="page-title">
		<?php
			if(isset($_GET['runner']))
			{
		?>
			Edit Runner
		<?php
			}
			
			else 
			{
		?>
			Add Runner
		<?php
			}
		?>
		</h2>
		
		<form method="post" class="form-horizontal form-add-edit">
			<!--
			<div class="form-group">
				<label for="ssn" class="col-lg-3 d-inline-block control-label h-100">Social Security Number : </label>
				<input id="ssn" type="text" class="col-lg-9 d-inline-block form-control h-100">
			</div>
		
			<div class="form-group">
				<label for="bib" class="col-lg-3 d-inline-block control-label h-100">Bib : </label>
				<input id="bib" type="text" class="col-lg-9 d-inline-block form-control h-100">
			</div>
			-->
			
			<div class="form-group">
				<label for="first_name" class="col-lg-3 d-inline-block  control-label">First Name : </label>
				<input id="first_name" name="first_name" type="text" class="col-lg-9 d-inline-block form-control h-100" required autofocus <?php
					if(isset($_GET['runner']))
					{
				?>
					value='<?= $runner->FirstName ?>'
				<?php
					}
				?>>
			</div>	
			
			<div class="form-group">
				<label for="last_name" class="col-lg-3 d-inline-block  control-label">Last Name : </label>
				<input id="last_name" name="last_name" type="text" class="col-lg-9 d-inline-block form-control h-100" required <?php
					if(isset($_GET['runner']))
					{
				?>
					value='<?= $runner->LastName ?>'
				<?php
					}
				?>>
			</div>	
			
			<div class="form-group">
				<label for="birth_date" class="col-lg-3 d-inline-block  control-label">Birth Date : </label>
				<input id="birth_date" name="birth_date" type="date" class="col-lg-9 d-inline-block form-control h-100" required <?php
					if(isset($_GET['runner']))
					{
				?>
					value='<?= $runner->DateOfBirth ?>'
				<?php
					}
				?>>
			</div>	
			
			<div class="form-group">
				<label for="gender" class="col-lg-3 d-inline-block control-label">Gender : </label>
				<select id="gender" name="gender" class="col-lg-9 d-inline-block form-control h-100" required <?php
					if(isset($_GET['runner']))
					{
				?>
					disabled
				<?php
					}
				?>>
					<option <?php
						if(isset($_GET['runner']) && ($runner->Gender == "Man"))
						{ 
					?>
						selected="selected"
					<?php
						}
					?>
					>Man</option>
					<option	<?php
						if(isset($_GET['runner']) && ($runner->Gender == "Woman"))
						{
					?>
						selected="selected"
					<?php
						}
					?>
					>Woman</option>
				</select>
			</div>	
			
			<!--
			<div class="form-group">
				<label for="race-type" class="col-lg-3 d-inline-block control-label">Race Type : </label>
				<select id="race-type" class="col-lg-9 d-inline-block form-control h-100">
					<option>100 miles</option>
					<option>50 miles</option>
					<option>20 miles</option>
				</select>
			</div>
			
			<div class="form-group">
				<label for="team" class="col-lg-3 d-inline-block  control-label">Team : </label>
				<input id="team" type="text" class="col-lg-9 d-inline-block  form-control h-100">
			</div>
			-->
			
			<div class="form-group">
				<div class="col-lg-3 d-inline-block"></div>
				<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=runner-list'" />
				<button class="col-lg-3	pull-right btn btn-default" type="submit" name="submit">Submit</button>
			</div>
		</form>
	</div>
</main>
