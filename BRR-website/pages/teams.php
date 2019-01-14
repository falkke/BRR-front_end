<?php
	$_SESSION['dashboard'] = 0;
	
	if(	(isset($_GET['remove']) && $_GET['remove'] == 1) &&
		(isset($_GET['team']) && !empty($_GET['team']))) {
		$team_id = $_GET['team'];
			
		$delete_team_error = "";
		
		if(!does_team_exist($team_id)) {
			$delete_team_error = "This team does not exist.";
		}
		
		else if(!is_team_empty($team_id)) {
			$delete_team_error = "This team can not be deleted because it has some data.";
		}
		
		else {
			delete_team($team_id);
			
			if($_SESSION['dashboard'] == 1) {
				header('Location:index.php?page=dashboard&list=teams&team-deleted=1');
			}
			
			else {
				header('Location:index.php?page=teams&team-deleted=1');
			}
		}
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<?php
			require 'body/team-list.php';
		?>
	</div>
</main>	