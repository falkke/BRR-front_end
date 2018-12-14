<?php
	if(is_logged() == 0) {
        header('Location:index.php?page=home');
    }
?>

<main role="main" class="container">
	<div class="starter-template">
		<?php
			require 'body/password-form.php';
		?>
	</div>
</main>
