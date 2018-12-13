<?php
	if(is_logged() == 0) {
        header('Location:index.php?page=home');
    }
	
	if(!isset($_GET['runner'])) {
		header('Location:index.php?page=runners');
	}
	if(!empty($_GET['runner'])) {
		$runner_id = $_GET['runner'];
		
		if(does_runner_exist($runner_id)) {
			$runner = get_runner($runner_id);
		}
		else {
			header("Location:index.php?page=runners");
		}
	}
	
	if(!isset($_GET['race'])) {
		header('Location:index.php?page=runners');
	}
	if(!empty($_GET['race'])) {
		$race_id = $_GET['race'];
		
		if(does_race_exist($race_id)) {
			$race = get_race($race_id);
		}
		else {
			header("Location:index.php?page=runners");
		}
	}
	
	if(!isset($_GET['timestamp'])) {
		header('Location:index.php?page=runners');
	}
	if(!empty($_GET['timestamp'])) {
		$timestamp_time = $_GET['timestamp'];
		
		if(does_timestamp_exist($timestamp_time, $runner_id, $race_id)) {
			$timestamp = get_timestamp($timestamp_time, $runner_id);
		}
		else {
			header("Location:index.php?page=runners");
		}
	}
	
	if(isset($_POST['submit'])) {
		$new_datetime = "'" . $_POST['date'] . " " . $_POST['time'] . "'";
		$station = explode(" - ", $_POST['station']);
		
		if(!empty($_GET['timestamp'])) {
			edit_timestamp($timestamp_time, $runner_id, $race_id, $new_datetime, $station[0]);
			//set_laps($runner_id, $race_id);
			//header('Location:index.php?page=runner&runner='.$runner_id.'&race='.$race_id.'&timestamp-modified=1');
		}
		else {
			add_timestamp($runner_id, $race_id, $new_datetime, $station[0]);
			header('Location:index.php?page=runner&runner='.$runner_id.'&race='.$race_id.'&timestamp-added=1');
		}
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<h2 class="page-title">
			<?php
				if(!empty($_GET['timestamp'])) {
					?>
						Edit Timestamp
					<?php
				}
				
				else {
					?>
						Add Timestamp
					<?php
				}
			?>
		</h2>
		
		<!-- max='<?=$race->EndDate?>' -->
		<form method="post" class="form-horizontal form-add-edit">
			<div class="form-group">
				<label for="date" class="col-lg-3 d-inline-block  control-label">Timestamp Date: </label>
				<input id="date" name="date" type="date" min='<?=$race->Date?>' class="col-lg-9 d-inline-block form-control h-100" required autofocus 
					<?php
						if(!empty($_GET['timestamp'])) {
							?>
								value='<?=$timestamp->Date?>'
							<?php
						}
					?>
				>
			</div>
			<div class="form-group">
				<label for="time" class="col-lg-3 d-inline-block  control-label">Timestamp Time: </label>
				<input id="time" name="time" type="time" min="00:00:00" max="23:59:59" step="1" class="col-lg-9 d-inline-block form-control h-100" required autofocus 
					<?php
						if(!empty($_GET['timestamp'])) {
							?>
								value='<?=$timestamp->Time?>'
							<?php
						}
					?>
				>
			</div>
			<div class="form-group">
				<label for="station" class="col-lg-3 d-inline-block  control-label">Station : </label>
				<select id="station" name="station" class="col-lg-9 d-inline-block form-control h-100" required>
				<?php
					foreach(get_stations() as $station) {
				?>	
						<option
							<?php
								if(!empty($_GET['timestamp']) && $timestamp->Station == $station->ID)
								{
							?>
									selected="selected"
							<?php
								}
							?>
						>
							<?=$station->ID." - ".$station->Name?>
						</option>					
				<?php
					}
				?>
				</select>
			</div>
			
			<div class="form-group">
				<div class="col-lg-3 d-inline-block"></div>
				<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=runner&runner=<?=$runner_id?>&race=<?=$race_id?>'" />
				<button class="col-lg-3	pull-right btn btn-default" type="submit" name="submit">Submit</button>
			</div>
		</form>
	</div>
</main>
