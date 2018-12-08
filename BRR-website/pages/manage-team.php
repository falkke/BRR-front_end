<?php
	if(is_logged() == 0) {
        header('Location:index.php?page=home');
    }
	
	if(isset($_GET['team']) && !empty($_GET['team'])) {
		$team_id = $_GET['team'];
		
		if(does_team_exist($team_id)) {
			$team = get_team($team_id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(isset($_POST['submit'])) {
		$team_name = htmlspecialchars(trim($_POST['team_name']));
		
		if(isset($_GET['team'])) {
			edit_team($team_id, $team_name);
			header('Location:index.php?page=team-list&team-modified=1');
		}
		
		else {
			add_team($team_name);
			header('Location:index.php?page=team-list&team-added=1');
		}
		
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<h2 class="page-title">
		<?php
			if(isset($_GET['team']))
			{
		?>
			Edit Team
		<?php
			}
			
			else 
			{
		?>
			Add Team
		<?php
			}
		?>
		</h2>
		
		<form method="post" class="form-horizontal form-add-edit">
			<div class="form-group">
				<label for="team_name" class="col-lg-3 d-inline-block  control-label">Name : </label>
				<input id="team_name" name="team_name" type="text" class="col-lg-9 d-inline-block form-control h-100" required autofocus <?php
					if(isset($_GET['team']))
					{
				?>
					value='<?= $team->Name ?>'
				<?php
					}
				?>>
			</div>	
		
			<div class="form-group">
				<div class="col-lg-3 d-inline-block"></div>
				<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=team-list'" />
				<button class="col-lg-3	pull-right btn btn-default" type="submit" name="submit">Submit</button>
			</div>
		</form>
	</div>
</main>
