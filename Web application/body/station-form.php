<?php
	if(is_logged() == 0) {
        header('Location:index.php?page=home');
    }
	
	if(!isset($_GET['station'])) {
		header('Location:index.php?page=dashboard&list=stations');
	}
	
	if(!empty($_GET['station'])) {
		$station_id = $_GET['station'];
		
		if(does_station_exist($station_id)) {
			$station = get_station($station_id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(isset($_POST['submit'])) {
		if(!empty($_GET['station'])) {
			$identifier = $station_id;
		}
		else {
			$identifier = htmlspecialchars(trim($_POST['identifier']));
		}
		$name = htmlspecialchars(trim($_POST['name']));
		$code = $_POST['code'];
		$length_from_start = $_POST['length_from_start'];
		
		if(!empty($_GET['station'])) {
			edit_station($identifier, $name, $code, $length_from_start);
			header('Location:index.php?page=dashboard&list=stations&station-modified=1');
		}
		
		else {
			add_station($identifier, $name, $code, $length_from_start);
			header('Location:index.php?page=dashboard&list=stations&station-added=1');
		}
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<h2 class="page-title">
			<?php
				if(!empty($_GET['station'])) {
					?>
						Edit Station
					<?php
				}
				
				else {
					?>
						Add Station
					<?php
				}
			?>
		</h2>
		
		<form method="post" class="form-horizontal form-add-edit">			
			<div class="form-group">
				<label for="identifier" class="col-lg-3 d-inline-block  control-label">Identifier : </label>
				<input id="identifier" name="identifier" type="text" class="col-lg-9 d-inline-block form-control h-100" required autofocus 
					<?php
					if(!empty($_GET['station'])) {
							?>
								disabled value='<?=$station->ID?>'
							<?php
						}
					?>
				>
			</div>	
			
			<div class="form-group">
				<label for="name" class="col-lg-3 d-inline-block  control-label">Name : </label>
				<input id="name" name="name" type="text" class="col-lg-9 d-inline-block form-control h-100" required autofocus 
					<?php
						if(!empty($_GET['station'])) {
							?>
								value='<?=$station->Name?>'
							<?php
						}
					?>
				>
			</div>	
			
			<div class="form-group">
				<label for="code" class="col-lg-3 d-inline-block  control-label">Code : </label>
				<input id="code" name="code" type="number" class="col-lg-9 d-inline-block form-control h-100" required 
					<?php
						if(!empty($_GET['station'])) {
							?>
								value='<?=$station->Code?>'
							<?php
						}
					?>
				>
			</div>	
			
			<div class="form-group">
				<label for="length_from_start" class="col-lg-3 d-inline-block  control-label">Length From Start : </label>
				<input id="length_from_start" name="length_from_start" type="number" class="col-lg-9 d-inline-block form-control h-100" required 
					<?php
						if(!empty($_GET['station'])) {
							?>
								value='<?=$station->LengthFromStart?>'
							<?php
						}
					?>
				>
			</div>	
			
			<div class="form-group">
				<div class="col-lg-3 d-inline-block"></div>
				<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=dashboard&list=stations'" />
				<button class="col-lg-3	pull-right btn btn-default" type="submit" name="submit">Submit</button>
			</div>
		</form>
	</div>
</main>
