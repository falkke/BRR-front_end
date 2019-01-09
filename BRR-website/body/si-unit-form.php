<?php
	$si_unit = "";

	if(is_logged() == 0) {
        header('Location:index.php?page=home');
    }
	
	if(!isset($_GET['si-unit'])) {
		header('Location:index.php?page=dashboard&list=si-units');
	}
	
	if(!empty($_GET['si-unit'])) {
		$si_unit_id = $_GET['si-unit'];
		
		if(does_si_unit_exist($si_unit_id)) {
			$si_unit = get_si_unit($si_unit_id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(isset($_POST['submit'])) {
		$status = "";
		$si_unit_id = $_POST['id'];
		
		if(empty($_GET['si-unit'])) {
			add_si_unit($si_unit_id, "Returned");
			header('Location:index.php?page=dashboard&list=si-units&si-unit-added=1');
		}
	}
?>

<h2 class="page-title">
	<?php
		if(!empty($_GET['si-unit'])) {
			?>
				Edit SI-Unit
			<?php
		}
		
		else 
		{
			?>
				Add SI-Unit
			<?php
		}
	?>
</h2>

<form method="post" class="form-horizontal form-add-edit">
	<div class="form-group">
		<label for="id" class="col-lg-3 d-inline-block control-label">Number : </label>
		<input id="id" name="id" type="text" class="col-lg-9 d-inline-block form-control h-100" required autofocus 
					<?php
						if(!empty($_GET['si-unit'])) {
					?>
							value='<?=$si_unit_id?>'
					<?php
						}
					?>
				>
	</div>
	
	<div class="form-group">
		<div class="col-lg-3 d-inline-block"></div>
		<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=dashboard&list=si-units'" />
		<button class="col-lg-3	pull-right btn btn-default" type="submit" name="submit">Submit</button>
	</div>
</form>