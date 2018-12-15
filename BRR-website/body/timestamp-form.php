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
	
	else {
		header("Location:index.php?page=runners");
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
		
		if(does_timestamp_exist($timestamp_time, $runner_id)) {
			$timestamp = get_timestamp($timestamp_time, $runner_id);
		}
		else {
			header("Location:index.php?page=runners");
		}
	}
	
	if(isset($_POST['submit'])) {
		$new_datetime = $_POST['date'] . " " . $_POST['time'];
		$station_id = explode(" - ", $_POST['station']);
		
		if(!empty($_GET['timestamp'])) {
			if((!does_timestamp_exist($new_datetime, $runner_id)) || ($new_datetime == $timestamp_time)) {
				edit_timestamp($timestamp_time, $runner_id, $race_id, $new_datetime, $station_id[0]);
				
				$race_runner = get_race_runner($runner_id, $race->ID);
				
				$last_timestamp = get_last_timestamp($runner_id, $race_runner->RaceInstance);
				
				$lap = get_number_laps($runner_id, $race_id, $last_timestamp->Timestamp, $last_timestamp->Station);
				
				$race_instance = get_race_instance_by_id($race_runner->RaceInstance);
				$race_category = get_category($race_instance->Class);
				$station = get_station($last_timestamp->Station);
				
				if(((($lap - 1) * 10) + $station->LengthFromStart) <= $race_category->Distance) {
					header('Location:index.php?page=runner&runner='.$runner_id.'&race='.$race_id.'&timestamp-modified=1');
				}
				
				else {
					edit_timestamp($new_datetime, $runner_id, $race_id, $timestamp_time, $station_id[0]);
					$error = "This timestamp can not be added to the system because the distance will exceed the distance of the race.";
				}
			}
									
			else {
				$error = "This timestamp already exists.";
			}
		}
		
		else {
			if(!does_timestamp_exist($new_datetime, $runner_id)) {
				add_timestamp($runner_id, $race->ID, $new_datetime, $station_id[0]);
				
				$race_runner = get_race_runner($runner_id, $race->ID);
				
				$last_timestamp = get_last_timestamp($runner_id, $race_runner->RaceInstance);
				
				$lap = get_number_laps($runner_id, $race_id, $last_timestamp->Timestamp, $last_timestamp->Station);
				
				$race_instance = get_race_instance_by_id($race_runner->RaceInstance);
				$race_category = get_category($race_instance->Class);
				$station = get_station($last_timestamp->Station);
				
				if(((($lap - 1) * 10) + $station->LengthFromStart) <= $race_category->Distance) {
					header('Location:index.php?page=runner&runner='.$runner_id.'&race='.$race_id.'&timestamp-added=1');
				}
				
				else {
					delete_timestamp($runner_id, $race->ID, $new_datetime);
					$error = "This timestamp can not be added to the system because the distance will exceed the distance of the race.";
				}
			}
									
			else {
				$error = "This timestamp already exists.";
			}
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
		
		<?php
			if(isset($error) && !empty($error)) {
				?>
					<p class="alert alert-danger" role="alert"><?=$error?></p>
				<?php
			}
		?>
		
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
