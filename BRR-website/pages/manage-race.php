<?php
	if(is_logged() == 0) {
        header('Location:index.php?page=home');
    }
	
	if(isset($_GET['race']) && !empty($_GET['race'])) {
		$id = $_GET['race'];
		
		if(race_exists($id)) {
			$race = get_race($id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(isset($_POST['submit'])) {
		$name = htmlspecialchars(trim($_POST['name']));
		$date = $_POST['date'];
		
		if(isset($_GET['race'])) {
			edit_race($id, $name, $date);
		}
		
		else {
			add_race($name, $date);
		}
		
        header('Location:index.php?page=race-list');
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<h2 class="page-title">
		<?php
			if(isset($_GET['race']))
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
					if(isset($_GET['race']))
					{
				?>
					value='<?= $race->Name ?>'
				<?php
					}
				?>>
			</div>
			
			<div class="form-group">
				<label for="date" class="col-lg-3 d-inline-block control-label h-100">Date : </label>
				<input id="date" type="date" name="date" class="col-lg-9 d-inline-block form-control h-100" required 
				<?php
					if(isset($_GET['race']))
					{
				?>
					value='<?= $race->Date ?>'
				<?php
					}
				?>>
			</div>
			
			<div class="form-group">
				<div class="col-lg-3 d-inline-block"></div>
				<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=race-list'" />
				<button class="col-lg-3	pull-right btn btn-default" type="submit" name="submit">Submit</button>
			</div>
		</form>
	</div>
</main>