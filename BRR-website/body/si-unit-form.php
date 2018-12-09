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
		$status = $_POST['status'];
		
		if(!empty($_GET['si-unit'])) {
			edit_si_unit($si_unit_id, $status);
			header('Location:index.php?page=dashboard&list=si-units&si-unit-modified=1');
		}
		
		else {
			add_si_unit($status);
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
		<label for="status" class="col-lg-3 d-inline-block control-label">Status : </label>
		<select id="status" name="status" class="col-lg-9 d-inline-block form-control h-100" required>
			<?php
				foreach(get_status() as $status) {
					?>	
						<option 
							<?php
								if((!empty($_GET['si-unit'])) && (($status->Status) == ($si_unit->Status))) {
									?>
										selected="selected"
									<?php
								}
							?>
						>
							<?=$status->Status?>
						</option>					
					<?php
				}
			?>
		</select>
	</div>	
	
	<div class="form-group">
		<div class="col-lg-3 d-inline-block"></div>
		<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=dashboard&list=si-units'" />
		<button class="col-lg-3	pull-right btn btn-default" type="submit" name="submit">Submit</button>
	</div>
</form>