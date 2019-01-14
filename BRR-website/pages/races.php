<?php
	$_SESSION['dashboard'] = 0;
	
	if(	(isset($_GET['remove']) && $_GET['remove'] == 1) &&
		(isset($_GET['race']) && !empty($_GET['race']))) {
		$race_id = $_GET['race'];
		
		$delete_race_error = "";
		
		if(!does_race_exist($race_id)) {
			$delete_race_error = "This race does not exist.";
		}
		
		else if(!is_race_empty($race_id)) {
			$delete_race_error = "This race can not be deleted because it has some data.";
		}
		
		else {
			delete_race($race_id);
			
			if($_SESSION['dashboard'] == 1) {
				header('Location:index.php?page=dashboard&list=races&race-deleted=1');
			}
			
			else {
				header('Location:index.php?page=races&race-deleted=1');
			}
		}
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<?php
			require 'body/race-list.php';
		?>
	</div>
</main>	