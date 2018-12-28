<?php
	if(!isset($_GET['race'])) {
		if($_SESSION['dashboard'] == 1) {
			header('Location:index.php?page=dashboard&list=races');
		}
		
		else {
			header('Location:index.php?page=races');
		}
	}	
	
	if(!empty($_GET['race'])) {
		$race_id = $_GET['race'];
		
		if(does_race_exist($race_id)) {
			$race = get_race($race_id);
			$end_date_time = explode(" ", $race->EndTime);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(isset($_POST['submit'])) {
		$name = htmlspecialchars(trim($_POST['name']));
		$start_date = $_POST['start-date'];
		$end_date = $_POST['end-date'];
		$end_time = $_POST['end-time'];
		
		if($start_date <= $end_date) {
			if(!empty($_GET['race'])) {
				
				edit_race($race_id, $name, $start_date, $end_date." ".$end_time);
				
				if(!is_displayed($race_id) && isset($_POST['display'])){
					display_race($race_id);
				}
				
				else if(is_displayed($race_id)) {
					do_not_display_race();
				}
				
				if($_SESSION['dashboard'] == 1) {
					header('Location:index.php?page=dashboard&list=races&race-modified=1');
				}
				
				else {
					header('Location:index.php?page=races&race-modified=1');
				}
			}
			
			else {
				add_race($name, $start_date, $end_date." ".$end_time);
				
				if($_SESSION['dashboard'] == 1) {
					header('Location:index.php?page=dashboard&list=races&race-added=1');
				}
				
				else {
					header('Location:index.php?page=races&race-added=1');
				}
			}
		}
		
		else {
			$error = "The start date must be before the end date.";
		}
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<h2 class="page-title">
			<?php
				if(!empty($_GET['race']))
				{
					?>
						Edit Race
					<?php
				}
				
				else 
				{
					?>
						Add Race
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
		
		<form method="post" class="form-horizontal form-add-edit">
			<div class="form-group">
				<label for="name" class="col-lg-3 d-inline-block control-label h-100">Name : </label>
				<input type="text" id="name" name="name" class="col-lg-9 d-inline-block form-control h-100" placeholder="Name" required autofocus
					<?php
						if(!empty($_GET['race'])) {
							?>
								value='<?=$race->Name ?>'
							<?php
						}
					?>
				/>
			</div>
			
			<div class="form-group">
				<label for="start-date" class="col-lg-3 d-inline-block control-label h-100">Start Date : </label>
				<input name="start-date" type="date" class="col-lg-9 d-inline-block form-control h-100" required 
					<?php
						if(!empty($_GET['race'])) {
							?>
								value='<?=$race->Date?>'
							<?php
						}
					?>
				/>
			</div>
			
            <div class="form-group">
				<label for="end-date" class="col-lg-3 d-inline-block control-label h-100">End Date : </label>
				<input name="end-date" type='date' class="col-lg-9 d-inline-block form-control h-100" required
					<?php
						if(!empty($_GET['race'])) {
							?>
								value='<?=$end_date_time[0]?>'
							<?php
						}
					?>
				/>
            </div>
			
			
            <div class="form-group">
				<label for="end-time" class="col-lg-3 d-inline-block control-label h-100">End Time : </label>
				<input name='end-time' type='time' class="col-lg-9 d-inline-block form-control h-100" required
					<?php
						if(!empty($_GET['race'])) {
							?>
								value='<?=$end_date_time[1]?>'
							<?php
						}
					?>
				/>
            </div>
			
			<?php
				if(!empty($_GET['race']) && (is_current_race($race_id) || is_planned_race($race_id))) {
					?>
						<div class="form-group">
							<label for="display" class="col-lg-3 d-inline-block control-label h-100">Display On Main Page : </label>
							<input name="display" type='checkbox' class="col-lg-9 d-inline-block form-control h-100"
								<?php
									if(is_displayed($race_id)) {
										?>
											checked
										<?php
									}
								?>
							/>
						</div>
					<?php
				}
			?>
			
			<div class="form-group">
				<div class="col-lg-3 d-inline-block"></div>
					<?php
						if($_SESSION['dashboard'] == 1) {
							?>
								<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=dashboard&list=races'" />
							<?php
						}
						
						else {
							?>
								<input class="col-lg-3 pull-right btn btn-default" type="button" value="Cancel" onclick="location.href='index.php?page=races'" />
							<?php
						}
					?>
				<button class="col-lg-3	pull-right btn btn-default" type="submit" name="submit">Submit</button>
			</div>
		</form>
		
		<?php
			require 'body/runner-race-form.php';
		?>
	</div>
</main>