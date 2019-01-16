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

	if(	(isset($_GET['race-instance']) && !empty($_GET['race-instance']))
	&&	(isset($_GET['remove']) && !empty($_GET['remove']) && $_GET['remove'] == 1)){
		$race_instance_id = $_GET['race-instance'];
		
		delete_race_instance($race_instance_id);
		
		header('Location:index.php?page=manage&race='.$race->ID.'&class-deleted=1');
	}
	
	if(	(isset($_GET['race-runner']) && !empty($_GET['race-runner']))
	&&	(isset($_GET['remove']) && !empty($_GET['remove']) && $_GET['remove'] == 1)){
		$runner_id = $_GET['race-runner'];

		if(!has_si_unit($race->ID, $runner_id)) {
			delete_race_runner($race->ID, $runner_id);
		}
		
		header('Location:index.php?page=manage&race='.$race->ID.'&runner-deleted=1');
	}

	if(	(isset($_GET['runner']) && !empty($_GET['runner']))
	&&	(isset($_GET['si-unit']) && ((!empty($_GET['si-unit']) || $_GET['si-unit']) == 0))
	&&	(isset($_GET['remove']) && !empty($_GET['remove']) && $_GET['remove'] == 1)){
		$runner_id = $_GET['runner'];
		$si_unit_id = $_GET['si-unit'];
		$race_id = $race->ID;

		delete_runner_si_unit($runner_id, $si_unit_id, $race_id);
		edit_si_unit($si_unit_id, "Returned");
		header('Location:index.php?page=manage&race='.$race->ID.'&unit-deleted=1');
	}

	if(isset($_POST['add_runner'])) {
		$runner_id = explode(" - ", $_POST['runner']);
		$runner = get_runner($runner_id[0]);
		$category = explode(" - ", $_POST['category']);
		$category_distance = explode(" ", $category[1]);
		$team_id = explode(" - ", $_POST['team']);
		$bib = $_POST['bib'];
		$race_instance = get_race_instance($race->ID, $runner->Gender, $category_distance[1]);
		
		if($race_instance != NULL && $runner->Gender == $category_distance[0]){
			if($team_id[0] != "") {
				add_race_runner($runner_id[0], $bib, $team_id[0], $race_instance->ID);
				header('Location:index.php?page=manage&race='.$race->ID.'&runner-added=1');
			}
			else {
				add_race_runner_no_team($runner_id[0], $bib, $race_instance->ID);
				header('Location:index.php?page=manage&race='.$race->ID.'&runner-added=1');
			}
		}
		else {
			$runner_error = NULL;
			$unit_error = NULL;
			$class_error = NULL;
			$runner_error = "This Runner does not match the race Category.";
		}
	}	
	
	if(isset($_POST['add_si_unit_runner'])) {
		$runner_id = explode(" - ", $_POST['runner-si-unit']);
		$si_unit_id = $_POST['si-unit'];
		
		if(does_si_unit_exist($si_unit_id)) {
			add_race_si_unit_runner($race->ID, $runner_id[0], $si_unit_id);
			header('Location:index.php?page=manage&race='.$race->ID.'&unit-added=1');
		}
		else {
			$runner_error = NULL;
			$unit_error = NULL;
			$class_error = NULL;
			$unit_error = "This SI Unit is not available to be used, check SI unit tab to know who used it in last.";
		}
	}
	
	if(isset($_POST['add_instance'])) {
		$category_id = explode(" - ", $_POST['category']);
		$category = get_category($category_id[0]);
		$start_time = $_POST['start-time'];
		
		if(!does_race_instance_exist($race->ID, $category->Gender, $category->Distance)) {
			add_race_instance($race->ID, $category_id[0], $start_time);
			header('Location:index.php?page=manage&race='.$race->ID.'&class-added=1');
		}
		else {
			$runner_error = NULL;
			$unit_error = NULL;
			$class_error = NULL;
			$class_error = "This Category is already defined for this race.";
		}
	}
	
	if(isset($_POST['import_file'])) {
		$import_error_message = "";
		$import_success_message = "";
		
		if($_FILES['file']['tmp_name'] != "") {
			if(($handle = fopen($_FILES['file']['tmp_name'], "r")) !== FALSE) {
				$data = [];
				$row = 0;
				while (($data[$row] = fgetcsv($handle, 1000, ";")) !== FALSE) {
					$row = $row + 1;
				}
				$import_error = [];
				$import_error = verify_import($data, $row);
				if($import_error[0] == 0 && $import_error[1] == 0) {
					$import_success_message = "The import has been succefully done.";
					import_data($data, $row, $race->ID);
				}
				else if($import_error[0] != 0 && $import_error[1] == 0) {
					$import_error_message = "The import has not been done due to incorect file data. 
											(line ".$import_error[0].")";
				}
				else {
					$import_error_message = "The import has not been done due to incorect file data. 
											(line ".$import_error[0].", column ".$import_error[1].")<br>";	
					if($import_error[1] == 1) {
						$import_error_message = $import_error_message . "NR-lapp must be a number.";	
					}
					else if($import_error[1] == 2) {
						$import_error_message = $import_error_message . "SI-NR identifier must be a number.";	
					}
					else if($import_error[1] == 3) {
						$import_error_message = $import_error_message . "Klass must follow this format 'GENDER DISTANCE Miles'<br>
												With GENDER = Man, Woman, Herrar, Damer 
												and DISTANCE = 20, 50, 100.";
					}
					else if($import_error[1] == 4) {
						$import_error_message = $import_error_message . "Starttid must follow this format 'HH:MM:SS'";	
					}
					else if($import_error[1] == 5) {
						$import_error_message = $import_error_message . "Namn must follow this format 'FIRSTNAME LASTNAME'";	
					}
					else if($import_error[1] == 7) {
						$import_error_message = $import_error_message . "Personnummer must follow this format 'YYMMDD'";	
					}
				}
				fclose($handle);
			}
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
			
			if(isset($import_error_message) && ($import_error_message != "")) {
				?>
					<p class="alert alert-danger" role="alert"><?=$import_error_message?></p>
				<?php
			}
			else if(isset($import_success_message) && ($import_success_message != "")) {				
				?>
					<p class="alert alert-success" role="alert"><?=$import_success_message?></p>
				<?php
			}
			
			if(isset($runner_error)) {
				?>
					<p class="alert alert-danger" role="alert"><?=$runner_error?></p>
				<?php
			}
			else if(isset($_GET['runner-deleted']) && !empty($_GET['runner-deleted']) && ($_GET['runner-deleted'] == 1)) {
				?>
					<p class="alert alert-success" role="alert">The Runner has been succefully removed from the race.</p>
				<?php
			}
			else if(isset($_GET['runner-added']) && !empty($_GET['runner-added']) && ($_GET['runner-added'] == 1)) {
				?>
					<p class="alert alert-success" role="alert">The Runner has been succefully added to the race.</p>
				<?php
			}
			
			if(isset($class_error)) {
				?>
					<p class="alert alert-danger" role="alert"><?=$class_error?></p>
				<?php
			}
			else if(isset($_GET['class-deleted']) && !empty($_GET['class-deleted']) && ($_GET['class-deleted'] == 1)) {
				?>
					<p class="alert alert-success" role="alert">The Category has been succefully removed from the race.</p>
				<?php
			}
			else if(isset($_GET['class-added']) && !empty($_GET['class-added']) && ($_GET['class-added'] == 1)) {
				?>
					<p class="alert alert-success" role="alert">The Category has been succefully added to the race.</p>
				<?php
			}
			
			if(isset($unit_error)) {
				?>
					<p class="alert alert-danger" role="alert"><?=$unit_error?></p>
				<?php
			}
			else if(isset($_GET['unit-deleted']) && !empty($_GET['unit-deleted']) && ($_GET['unit-deleted'] == 1)) {
				?>
					<p class="alert alert-success" role="alert">The link between a SI Unit and a Runner has been succefully revoked.</p>
				<?php
			}
			else if(isset($_GET['unit-added']) && !empty($_GET['unit-added']) && ($_GET['unit-added'] == 1)) {
				?>
					<p class="alert alert-success" role="alert">The SI Unit has been succefully linked to a Runner.</p>
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
			if(!empty($_GET['race']) && (is_planned_race($_GET['race']) || is_current_race($_GET['race']))) {
				?>
					<form method="post" class="form-horizontal form-add-edit" enctype="multipart/form-data">
						<input id="file" name="file" type="file" class="col-lg-6 d-inline-block form-control h-100">
						<button class="bg-primary text-white table-button" type="submit" name="import_file">↑</button>
					</form>
				
					<form method="post" class="form-horizontal form-add-edit border mb-4">
						<div class="form-group">
							<div class="table-scroll-y-20">
								<table class="table table-bordered table-striped table-condensed">           
									<thead>
										<tr>
											<th>Gender</th>
											<th>Distance</th>
											<th>Start Time</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											foreach(get_race_instances($race->ID) as $race_instance) {	
												$race_category_id = $race_instance->Class;
												$race_category = get_category($race_category_id);
												?>	
													<tr>
														<td><?=$race_category->Gender?></td>
														<td><?=$race_category->Distance?></td>
														<td><?=$race_instance->StartTime?></td>
														<td><a class="bg-danger text-white table-button" href=<?="index.php?page=manage&race=".$race->ID."&race-instance=".$race_instance->ID."&remove=1"?>>X</a></td>
													</tr>						
												<?php
											}
										?>
									</tbody>
								</table>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-3 mb-4">
								<label for="category" class="control-label">Category</label>
								<select name="category" class="form-control" required>
									<?php
										foreach(get_categories() as $category) {
											?>	
												<option><?=$category->ID." - ".$category->Gender." ".$category->Distance?></option>					
											<?php
										}
									?>
								</select>
							</div>
							
							<div class="col-md-3 mb-4">
								<label for="start-time" class="control-label">Start Time</label>
								<input name='start-time' type='time' class="form-control" required>
							</div>
						
							<div class="col-md-3 mb-4"></div>
							
							<div class="col-md-3 mb-4">
								<div class="line"></div>
								<button class="btn btn-default w-100" type="submit" name="add_instance">Add</button>
							</div>	
						</div>
					</form>	
				
					<form method="post" class="form-horizontal form-add-edit border mb-4">
						<div class="form-group">
							<div class="table-scroll-y-20">
								<table class="table table-bordered table-striped table-condensed">           
									<thead>
										<tr>
											<th>Bib</th>
											<th>Name</th>
											<th>Team</th>
											<th>Category</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											foreach(get_race_runners($race->ID, '') as $race_runner) {	
												$class = get_race_runner_class($race_runner->Runner, $race->ID) ;		
												$runner = get_runner($race_runner->Runner);
												$team = get_race_runner_team($race_runner->Runner, $race->ID);
												?>	
													<tr>
														<td><?=$race_runner->Bib?></td>
														<td><?=$runner->FirstName." ".$runner->LastName?></td>
														<td><?=$team->Name?></td>
														<td><?=$class->Gender." ".$class->Distance?></td>
														<td><a class="bg-danger text-white table-button" href=<?="index.php?page=manage&race=".$race->ID."&race-runner=".$runner->ID."&remove=1"?>>X</a></td>
													</tr>						
												<?php
											}
										?>
									</tbody>
								</table>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-3 mb-4">
								<label for="runner" class="control-label">Runner</label>
								<select id="runner" name="runner" class="form-control" required>
									<?php
										foreach(get_race_not_runners($race->ID) as $race_runner) {
											?>	
												<option><?=$race_runner->ID." - ".$race_runner->FirstName." ".$race_runner->LastName?></option>					
											<?php
										}
									?>
								</select>
							</div>
							
							<div class="col-md-3 mb-4">
								<label for="category" class="control-label">Category</label>
								<select id="category" name="category" class="form-control" required>
									<?php
									
										foreach(get_instances($race->ID) as $category) {
											?>	
												<option><?=$category->ID." - ".$category->Gender." ".$category->Distance?></option>					
											<?php
										}
									?>
								</select>
							</div>
							
							<div class="col-md-3 mb-4">
								<label for="team" class="control-label">Team</label>
								<select id="team" name="team" class="form-control">
									<option selected value> - </option>
									<?php
										foreach(get_teams() as $team) {
											?>	
												<option><?=$team->ID." - ".$team->Name?></option>					
											<?php
										}
									?>
								</select>
							</div>
						</div>
						
						<div class="form-row">
							<label for="bib" class="col-md-2 mb-4 control-label">Bib</label>
							<input id="bib" name="bib" type="number" class="col-md-5 mb-4 form-control" required>
							<div class="col-md-2 mb-4"></div>
							<button class="col-md-3 mb-4 btn btn-default" type="submit" name="add_runner">Add</button>
						</div>	
					</form>	

					<form method="post" class="form-horizontal form-add-edit border">
						<div class="form-group">
							<div class="table-scroll-y-20">
								<table class="table table-bordered table-striped table-condensed">           
									<thead>
										<tr>
											<th>ID</th>
											<th>Status</th>
											<th>Holder</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											foreach(get_race_runner_units($race->ID) as $si_unit) {	
												?>	
													<tr>
														<td><?=$si_unit->ID?></td>
														<td><?=$si_unit->Status?></td>
														<td><?=$si_unit->FirstName." ".$si_unit->LastName?></td>
														<td><a class="bg-danger text-white table-button" href=<?="index.php?page=manage&race=".$race->ID."&runner=".$si_unit->Runner."&si-unit=".$si_unit->ID."&remove=1"?>>X</a></td>
													</tr>						
												<?php
											}
										?>
									</tbody>
								</table>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-3 mb-4">
								<label for="runner-si-unit" class="control-label">Runner</label>
								<select id="runner-si-unit" name="runner-si-unit" class="form-control" required>
									<?php
										foreach(get_race_runners($race->ID, "") as $race_runner) {			
											$runner = get_runner($race_runner->Runner);
											?>	
												<option><?=$runner->ID." - ".$runner->FirstName." ".$runner->LastName?></option>					
											<?php
										}
									?>
								</select>
							</div>

							<div class="col-md-3 mb-4">
								<label for="si-unit" class="control-label">SI-Unit</label>
								<input id="si-unit" name="si-unit" type="number" class="form-control" required>
							</div>
							
							<div class="col-md-3 mb-4"></div>
							
							<div class="col-md-3 mb-4">
								<div class="line"></div>
								<button class="btn btn-default w-100" type="submit" name="add_si_unit_runner">Add</button>
							</div>	
						</div>
					</form>				
				<?php
			}
		?>
	</div>
</main>