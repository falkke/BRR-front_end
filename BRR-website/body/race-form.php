<?php
	if(is_logged() == 0) {
        header('Location:index.php?page=home');
    }
	
	if(!isset($_GET['race'])) {
		if($_SESSION['dashboard'] == 1) {
			header('Location:index.php?page=dashboard&list=races');
		}
		
		else {
			header('Location:index.php?page=races');
		}
	}	
	
	if(!empty($_GET['race'])) {
		$race_id = $_GET['race'];
		
		if(does_race_exist($race_id)) {
			$race = get_race($race_id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(isset($_POST['submit'])) {
		$name = htmlspecialchars(trim($_POST['name']));
		$date = $_POST['date'];
		
		if(!empty($_GET['race'])) {
			edit_race($race_id, $name, $date);
			
			if($_SESSION['dashboard'] == 1) {
				header('Location:index.php?page=dashboard&list=races&race-modified=1');
			}
			
			else {
				header('Location:index.php?page=races&race-modified=1');
			}
		}
		
		else {
			add_race($name, $date);
			
			if($_SESSION['dashboard'] == 1) {
				header('Location:index.php?page=dashboard&list=races&race-added=1');
			}
			
			else {
				header('Location:index.php?page=races&race-added=1');
			}
		}
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<h2 class="page-title">
			<?php
				if(!empty($_GET['race']))
				{
					?>
						Edit Race
					<?php
				}
				
				else 
				{
					?>
						Add Race
					<?php
				}
			?>
		</h2>
		
		<form method="post" class="form-horizontal form-add-edit">
			<div class="form-group">
				<label for="name" class="col-lg-3 d-inline-block control-label h-100">Name : </label>
				<input type="name" id="name" name="name" class="col-lg-9 d-inline-block form-control h-100" placeholder="Name" required autofocus
					<?php
						if(!empty($_GET['race'])) {
							?>
								value='<?=$race->Name ?>'
							<?php
						}
					?>
				>
			</div>
			
			<div class="form-group">
				<label for="date" class="col-lg-3 d-inline-block control-label h-100">Date : </label>
				<input id="date" type="date" name="date" class="col-lg-9 d-inline-block form-control h-100" required 
					<?php
						if(!empty($_GET['race'])) {
							?>
								value='<?=$race->Date?>'
							<?php
						}
					?>
				>
			</div>
			
			<div class="form-group">
				<div class="col-lg-3 d-inline-block"></div>
				<?php
					if($_SESSION['dashboard'] == 1) {
						?>
							<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=dashboard&list=races'" />
						<?php
					}
					
					else {
						?>
							<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=races'" />
						<?php
					}
				?>
				<button class="col-lg-3	pull-right btn btn-default" type="submit" name="submit">Submit</button>
			</div>
		</form>
		
		<?php
			require 'body/runner-race-form.php';
		?>
	</div>
</main>