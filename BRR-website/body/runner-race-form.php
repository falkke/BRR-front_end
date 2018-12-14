<?php
	if(	(isset($_GET['race-instance']) && !empty($_GET['race-instance']))
	&&	(isset($_GET['remove']) && !empty($_GET['remove']) && $_GET['remove'] == 1)){
		$race_instance_id = $_GET['race-instance'];
		
		delete_race_instance($race_instance_id);
		
		header('Location:index.php?page=manage&race='.$race->ID);
	}
	
	if(	(isset($_GET['race-runner']) && !empty($_GET['race-runner']))
	&&	(isset($_GET['remove']) && !empty($_GET['remove']) && $_GET['remove'] == 1)){
		$runner_id = $_GET['race-runner'];

		if(!has_si_unit($race->ID, $runner_id)) {
			delete_race_runner($race->ID, $runner_id);
		}
		
		header('Location:index.php?page=manage&race='.$race->ID);
	}

	if(	(isset($_GET['runner']) && !empty($_GET['runner']))
	&&	(isset($_GET['si-unit']) && !empty($_GET['si-unit']))
	&&	(isset($_GET['remove']) && !empty($_GET['remove']) && $_GET['remove'] == 1)){
		$runner_id = $_GET['runner'];
		$si_unit_id = $_GET['si-unit'];
		$race_id = $race->ID;

		delete_runner_si_unit($runner_id, $si_unit_id, $race_id);
		edit_si_unit($si_unit_id, "Returned");
		header('Location:index.php?page=manage&race='.$race->ID);
	}

	if(isset($_POST['add_runner'])) {
		$runner_id = explode(" - ", $_POST['runner']);
		$runner = get_runner($runner_id[0]);
		$category_distance = $_POST['category'];
		$team_id = explode(" - ", $_POST['team']);
		$bib = $_POST['bib'];
		$race_instance = get_race_instance($race->ID, $runner->Gender, $category_distance);
		
		if($race_instance != NULL){
			add_race_runner($race->ID, $category_distance, $runner_id[0], $bib, $team_id[0], $race_instance->ID);
		}
	}	
	
	if(isset($_POST['add_si_unit_runner'])) {
		$runner_id = explode(" - ", $_POST['runner-si-unit']);
		$si_unit_id = $_POST['si-unit'];
		
		if(does_si_unit_exist($si_unit_id)) {
			add_race_si_unit_runner($race->ID, $runner_id[0], $si_unit_id);
		}
	}
	
	if(isset($_POST['add_instance'])) {
		$category_id = explode(" - ", $_POST['category']);
		$category = get_category($category_id[0]);
		$start_time = $_POST['start-time'];
		
		if(!does_race_instance_exist($race->ID, $category->Gender, $category->Distance)) {
			add_race_instance($race->ID, $category_id[0], $start_time);
		}
	}

	if(!empty($_GET['race']) && is_planned_race($_GET['race'])) {
		?>
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
										$class = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
										$runner = get_runner($race_runner->Runner);
										$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
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
								foreach(get_instances_distances($race->ID) as $category) {
									?>	
										<option><?=$category?></option>					
									<?php
								}
							?>
						</select>
					</div>
					
					<div class="col-md-3 mb-4">
						<label for="team" class="control-label">Team</label>
						<select id="team" name="team" class="form-control" required>
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
								foreach(get_race_runners($race->ID) as $race_runner) {			
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
