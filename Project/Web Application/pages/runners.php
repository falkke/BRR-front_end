<?php
	$_SESSION['dashboard'] = 0;
	
	if(	(isset($_GET['remove']) && $_GET['remove'] == 1) &&
		(isset($_GET['runner']) && !empty($_GET['runner']))) {
		$runner_id = $_GET['runner'];
			
		$delete_runner_error = "";

		if(!does_runner_exist($runner_id)) {
			$delete_runner_error = "This runner does not exist.";
		}
		
		else if(!is_runner_empty($runner_id)) {
			$delete_runner_error = "This runner can not be deleted because he/she has some data.";
		}
		
		else {
			delete_runner($runner_id);
			
			if($_SESSION['dashboard'] == 1) {
				header('Location:index.php?page=dashboard&list=runners&runner-deleted=1');
			}
			
			else {
				header('Location:index.php?page=runners&runner-deleted=1');
			}
		}
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<?php
			require 'body/runner-list.php';
		?>
	</div>
</main>	