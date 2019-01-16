<?php
	$category = "";
	
	if(is_logged() == 0) {
        header('Location:index.php?page=home');
    }
	
	if(!isset($_GET['category'])) {
		header('Location:index.php?page=dashboard&list=categories');
	}
	
	if(!empty($_GET['category'])) {
		$category_id = $_GET['category'];
		
		if(does_category_exist($category_id)) {
			$category = get_category($category_id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(isset($_POST['submit'])) {
		$gender = $_POST['gender'];
		$distance = $_POST['distance'];
		
		if(!empty($_GET['category'])) {
			edit_category($category_id, $gender, $distance);
			header('Location:index.php?page=dashboard&list=categories&category-modified=1');
		}
		
		else {
			add_category($gender, $distance);
			header('Location:index.php?page=dashboard&list=categories&category-added=1');
		}
	}
?>

<h2 class="page-title">
	<?php
		if(!empty($_GET['category']))
		{
			?>
				Edit Category
			<?php
		}
		
		else 
		{
			?>
				Add Category
			<?php
		}
	?>
</h2>

<form method="post" class="form-horizontal form-add-edit">
	<div class="form-group">
		<label for="gender" class="col-lg-3 d-inline-block control-label">Gender : </label>
		<select id="gender" name="gender" class="col-lg-9 d-inline-block form-control h-100" required>
			<option 
				<?php
					if(!empty($_GET['category']) && ($category->Gender == "Man")) { 
						?>
							selected="selected"
						<?php
					}
				?>
			>
				Man
			</option>
			
			<option	
				<?php
					if(!empty($_GET['category']) && ($category->Gender == "Woman")) {
						?>
							selected="selected"
						<?php
					}
				?>
			>
				Woman
			</option>
		</select>
	</div>	
	
	<div class="form-group">
		<label for="distance" class="col-lg-3 d-inline-block  control-label">Distance : </label>
		<input id="distance" name="distance" type="number" class="col-lg-9 d-inline-block form-control h-100" required <?php
			if(!empty($_GET['category']))
			{
				?>
					value='<?=$category->Distance ?>'
				<?php
			}
		?>>
	</div>	
	
	<div class="form-group">
		<div class="col-lg-3 d-inline-block"></div>
		<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=dashboard&list=categories'" />
		<button class="col-lg-3	pull-right btn btn-default" type="submit" name="submit">Submit</button>
	</div>
</form>