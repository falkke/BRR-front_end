<?php
	if(isset($_POST['add'])) {
		$runner_id = explode(" - ", $_POST['runner']);
		$si_unit_id = $_POST['si-unit'];
		$category_distance = $_POST['category'];
		$team_id = explode(" - ", $_POST['team']);
		$bib = $_POST['bib'];
		
		echo $runner_id[0]." ".$si_unit_id." ".$category_distance." ".$team_id[0]." ".$bib;
		add_race_runner($_GET['race'], $category_distance, $runner_id[0], $bib, $team_id[0]);
		add_race_si_unit_runner($race_id, $runner_id[0], $si_unit_id);
		
		/*if(!empty($_GET['race'])) {
			edit_race($race_id, $name, $date);
			
			if($_SESSION['dashboard'] == 1) {
				header('Location:index.php?page=dashboard&list=races&race-modified=1');
			}
			
			else {
				header('Location:index.php?page=races&race-modified=1');
			}
		}
		
		else {
			add_race($name, $date);
			
			if($_SESSION['dashboard'] == 1) {
				header('Location:index.php?page=dashboard&list=races&race-added=1');
			}
			
			else {
				header('Location:index.php?page=races&race-added=1');
			}
		}*/
	}


	if(!empty($_GET['race']) && is_planned_race($_GET['race'])) {
		?>
			<form method="post" class="form-horizontal form-add-edit border">
				<div class="form-group">
					<div class="table-scroll-y-20">
						<table class="table table-bordered table-striped table-condensed">           
							<thead>
								<tr>
									<th>Bib</th>
									<th>Name</th>
									<th>Team</th>
									<th>Category</th>
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
												SIUNIT
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
									//$class = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
									//$runner = get_runner($race_runner->Runner);
									//$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
									?>	
										<option><?=$race_runner->ID." - ".$race_runner->FirstName." ".$race_runner->LastName?></option>					
									<?php
								}
							?>
						</select>
					</div>
					
					<div class="col-md-3 mb-4">
						<label for="si-unit" class="control-label">SI-Unit</label>
						<select id="si-unit" name="si-unit" class="form-control" required>
							<?php
								foreach(get_race_not_si_unit($race->ID) as $race_si_unit) {
									?>	
										<option><?=$race_si_unit->ID?></option>					
									<?php
								}
							?>
						</select>
					</div>
					
					<div class="col-md-3 mb-4">
						<label for="category" class="control-label">Category</label>
						<select id="category" name="category" class="form-control" required>
							<?php
								foreach(get_categories_distances() as $category) {
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
					<button class="col-md-3 mb-4 btn btn-default" type="submit" name="add">Add</button>
				</div>	
			</form>				
		<?php
	}
?>
