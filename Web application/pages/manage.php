<?php
	if(is_logged() == 0) {
        header('Location:index.php?page=home');
    }
?>

<main role="main" class="container">
	<div class="starter-template">
		<?php
			if(isset($_GET['timestamp'])) {
				require 'body/timestamp-form.php';
			}
			else if(isset($_GET['race'])) {
				require 'body/race-form.php';
			}
			
			else if(isset($_GET['runner'])) {
				require 'body/runner-form.php';
			}
			
			else if(isset($_GET['team'])) {
				require 'body/team-form.php';
			}
			
			else if(isset($_GET['si-unit'])) {
				require 'body/si-unit-form.php';
			}
			
			else if(isset($_GET['station'])) {
				require 'body/station-form.php';
			}

			else if(isset($_GET['category'])) {
				require 'body/category-form.php';
			}
		?>
	</div>
</main>
