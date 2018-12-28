<?php
	$race = get_display_race();
	
	if($race != null) {
		$race_id = $race->Race;
		
		$search = "";
		
		if(isset($_GET['gender']) && !empty($_GET['gender'])) {
			$gender = $_GET['gender'];
		}
		
		if(isset($_GET['distance']) && !empty($_GET['distance'])) {
			if($_GET['distance'] == "100") {
				$distance = "100 miles";
			}
			
			else if($_GET['distance'] == "50") {
				$distance = "50 miles";
			}
			
			else {
				$distance = "20 miles";
			}
		}
		
		if (!empty($race) && !empty($gender) && !empty($distance)) {	
			$instance = get_race_instance($race_id, $gender, $_GET['distance']);
		}
		
		if(isset($_POST['submit'])) {
			$search = htmlspecialchars(trim($_POST['search']));
		}
		
		?>
			<main role="main" class="container no-gutters">
				<div class="row">
					<div class="nav-side-menu">
						<div class="brand">Category</div>
						<div class="menu-list">
							<ul id="menu-content" class="menu-content collapse out">
							<?php
								foreach(get_race_class_genders($race_id) as $class_gender) {
									?>							
										<li data-toggle="collapse" data-target="#<?=$class_gender ?>" class="collapsed">
											<a href="#"><?=$class_gender ?></a>
										</li>
										<ul class="sub-menu collapse" id="<?=$class_gender ?>">
											<?php
												foreach(get_race_class_gender_distances($race_id, $class_gender) as $class_gender_distance) {
													?>	
														<li><a href="index.php?page=race&race=<?= $race_id ?>&gender=<?=$class_gender ?>&distance=<?=$class_gender_distance ?>"><?=$class_gender_distance ?> miles</a></li>				
													<?php
												}
											?>	
										</ul>
								
									<?php
								}
							?>
							</ul>
						</div>
					</div>
				</div>
					
				<div class="section-template">
				<?php 
					if(
						(isset($_GET['gender']) && !empty($_GET['gender'])) &&
						(isset($_GET['distance']) && !empty($_GET['distance'])))
					{
				?>	
					<h2 class="page-title followed-title"><?= $race->Name ?></h2>
					<h3 class="page-subtitle"><?= "Results " . $gender . " - " . $distance ?></h3>
					
					<form method="post" class="form-inline my-2">
						<div class="input-group">
							<input class="form-control" type="text" style="text-align:right" placeholder="Search" name="search" id="search" aria-label="Search" <?php
								if(isset($_POST['submit'])) {
									?>
										value="<?= $_POST['search']?>"
									<?php
								}
							?>>    
							<span class="input-group-btn">
								<button class="btn  btn-default" type="submit" id="submit" name="submit">Search</button>
							</span>
						</div>
					</form>
					
					<?php 
					if(exist_race_runners($instance, $search, "Running") || exist_race_runners($instance, $search, NULL)) {
						?>
							<table class="table table-bordered table-striped table-condensed">           
								<thead>
									<tr>
										<th>Place</th>
										<th>Distance</th>
										<th>Bib</th>
										<th>Name</th>
										<th>Team</th>
										<th>Elaspsed Time</th>
										<th>Date & Time</th>
										<th>Status</th>
										<?php
											if(is_logged() == 1) {
												?>
													<th>
														<a class="bg-success text-white table-button" href="index.php?page=manage-runner-race&race=<?=$id?>">+</a>
													</th>
												<?php
											}
										?>
									</tr>
								</thead>
								
								<tbody>
									<?php
										foreach(get_race_runners_by_status($race->ID, $search, "Running") as $race_runner) {	
											$class = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
										
											if(($class->Gender == $_GET['gender']) && ($class->Distance == $_GET['distance'])) 
											{
												$runner = get_runner($race_runner->Runner);
												$timestamp = get_last_timestamp($race_runner->Runner, $race_runner->Race);
												$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
												$elapsed = get_total_elapsed_time($race_runner->Runner, $race_runner->Race);
												?>	
													<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
														<td><?=$race_runner->Place?></td>
														<td><?=$race_runner->Distance?></td>
														<td><?=$race_runner->Bib?></td>
														<td><?=$runner->FirstName." ".$runner->LastName?></td>
														<td><?=$team->Name?></td>
														<td>
															<?php
																if($elapsed != null)
																{
																	echo $elapsed;
																}
																else
																{
																	echo "-";
																}
															?>
														</td>
														<td>
															<?php
																if($timestamp != null)
																{
																	echo $timestamp->Timestamp;
																}
																else
																{
																	echo "-";
																}
															?>
														</td>
														<td><?=$race_runner->Status?></td>				
														<?php
															if(is_logged() == 1)
															{
														?>
															<td class="no-change">
																<a class="bg-primary text-white table-button" href="index.php?page=manage&runner=<?=$race_runner->Runner?>">...</a>
																<a class="bg-danger text-white table-button" href="index.php?page=home&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>&remove=1">X</a>
															</td>
														<?php
															}
														?>
													</tr>						
												<?php
											}
										}
									?>
								</tbody>
							</table>
						<?php
					}
					if(exist_race_runners($instance, $search, "Finished")) {
						?>
							<h3 class="text-left"> Finished </h3>
							<table class="table table-bordered table-striped table-condensed">           
								<thead>
									<tr>
										<th>Place</th>
										<th>Bib</th>
										<th>Name</th>
										<th>Team</th>
										<th>Elaspsed Time</th>
										<th>Behind</th>
										<th>Date & Time</th>
										<th>Status</th>
										<?php
											if(is_logged() == 1)
											{
										?>
											<th>
												<a class="bg-success text-white table-button" href="index.php?page=manage-runner-race&race=<?=$id?>">+</a>
											</th>
										<?php
											}
										?>
									</tr>
								</thead>
								
								<tbody>
									<?php
										foreach(get_race_runners_by_status($race->ID, $search, "Finished") as $race_runner) {	
											$class = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
										
											if(($class->Gender == $_GET['gender']) && ($class->Distance == $_GET['distance'])) 
											{
												$runner = get_runner($race_runner->Runner);
												$timestamp = get_last_timestamp($race_runner->Runner, $race_runner->Race);
												$behind = get_time_behind($race_runner);
												$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
												$elapsed = get_total_elapsed_time($race_runner->Runner, $race_runner->Race);
												?>	
													<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
														<td><?=$race_runner->Place?></td>
														<td><?=$race_runner->Bib?></td>
														<td><?=$runner->FirstName." ".$runner->LastName?></td>
														<td><?=$team->Name?></td>
														<td><?=$elapsed?></td>
														<td>
															<?php
																if($behind == "00:00:00")
																{
																	echo "-";
																}
																else
																{
																	echo "+" . $behind;
																}
															?>
														</td>
														<td>
															<?php
																if($timestamp != null)
																{
																	echo $timestamp->Timestamp;
																}
																else
																{
																	echo "-";
																}
															?>
														</td>
														<td><?=$race_runner->Status?></td>				
														<?php
															if(is_logged() == 1)
															{
														?>
															<td class="no-change">
																<a class="bg-primary text-white table-button" href="index.php?page=manage-runner-race">...</a>
																<a class="bg-danger text-white table-button" href="index.php?page=home&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>&remove=1">X</a>
															</td>
														<?php
															}
														?>
													</tr>						
												<?php
											}
										}
									?>
								</tbody>
							</table>
						<?php
					}
					if(exist_race_runners($instance, $search, "DNF")) {
						?>
							<h3 class="text-left"> Do Not Finish </h3>
							<table class="table table-bordered table-striped table-condensed">           
								<thead>
									<tr>
										<th>Place</th>
										<th>Distance</th>
										<th>Bib</th>
										<th>Name</th>
										<th>Team</th>
										<th>Elaspsed Time</th>
										<th>Date & Time</th>
										<th>Status</th>
										<?php
											if(is_logged() == 1)
											{
										?>
											<th>
												<a class="bg-success text-white table-button" href="index.php?page=manage-runner-race&race=<?=$id?>">+</a>
											</th>
										<?php
											}
										?>
									</tr>
								</thead>
								
								<tbody>
									<?php
										foreach(get_race_runners_by_status($race->ID, $search, "DNF") as $race_runner) {	
											$class = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
										
											if(($class->Gender == $_GET['gender']) && ($class->Distance == $_GET['distance'])) 
											{
												$runner = get_runner($race_runner->Runner);
												$timestamp = get_last_timestamp($race_runner->Runner, $race_runner->Race);
												$behind = get_time_behind($race_runner);
												$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
												$elapsed = get_total_elapsed_time($race_runner->Runner, $race_runner->Race);
												?>	
													<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
														<td><?=$race_runner->Place?></td>
														<td><?=$race_runner->Distance?></td>
														<td><?=$race_runner->Bib?></td>
														<td><?=$runner->FirstName." ".$runner->LastName?></td>
														<td><?=$team->Name?></td>
														<td><?=$elapsed?></td>
														<td>
															<?php
																if($timestamp != null)
																{
																	echo $race_runner->Timestamp;
																}
																else
																{
																	echo "-";
																}
															?>
														</td>
														<td><?=$race_runner->Status?></td>				
														<?php
															if(is_logged() == 1)
															{
														?>
															<td class="no-change">
																<a class="bg-primary text-white table-button" href="index.php?page=manage-runner-race">...</a>
																<a class="bg-danger text-white table-button" href="index.php?page=home&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>&remove=1">X</a>
															</td>
														<?php
															}
														?>
													</tr>						
												<?php
											}
										}
									?>
								</tbody>
							</table>
						<?php
					}
					if(exist_race_runners($instance, $search, "DNS"))
					{
						?> 
							<h3 class="text-left"> Do Not Start </h3>
							<table class="table table-bordered table-striped table-condensed">           
								<thead>
									<tr>
										<th>Place</th>
										<th>Bib</th>
										<th>Name</th>
										<th>Team</th>
										<th>Status</th>
										<?php
											if(is_logged() == 1)
											{
										?>
											<th>
												<a class="bg-success text-white table-button" href="index.php?page=manage-runner-race&race=<?=$id?>">+</a>
											</th>
										<?php
											}
										?>
									</tr>
								</thead>
								
								<tbody>
									<?php
										foreach(get_race_runners_by_status($race->ID, $search, "DNS") as $race_runner) {	
											$class = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
										
											if(($class->Gender == $_GET['gender']) && ($class->Distance == $_GET['distance'])) 
											{
												$runner = get_runner($race_runner->Runner);
												$timestamp = get_last_timestamp($race_runner->Runner, $race_runner->Race);
												$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
												?>	
													<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
														<td><?=$race_runner->Place?></td>
														<td><?=$race_runner->Bib?></td>
														<td><?=$runner->FirstName." ".$runner->LastName?></td>
														<td><?=$team->Name?></td>
														<td><?=$race_runner->Status?></td>				
														<?php
															if(is_logged() == 1)
															{
														?>
															<td class="no-change">
																<a class="bg-primary text-white table-button" href="index.php?page=manage-runner-race">...</a>
																<a class="bg-danger text-white table-button" href="index.php?page=home&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>&remove=1">X</a>
															</td>
														<?php
															}
														?>
													</tr>						
												<?php
											}
										}
									?>
								</tbody>
							</table>
						<?php
					}
					?>
				
				<?php 
					} 
					
					else if(isset($_GET['race']) && !empty($_GET['race'])) 
					{
				?>	
					<h2 class="page-title followed-title"><?= $race->Name ?></h2>
					<h3 class="page-subtitle"><?= $race->Date ?></h3>
					<p class="lead">Please, choose a category in the side menu to see the results.</p>
					<?php 
						if(is_logged() == 1)
						{
					?>	

						<a class="bg-primary text-white table-button" href="index.php?page=manage-race&race=<?= $id ?>">...</a>
						<a class="bg-danger text-white table-button" onclick="DeleteRaceAlert(<?= $id ?>);" href="#">X</a>
					<?php 
						} 
					} 
				?>
				</div>
			</main>
		<?php
	}
	
	else {
		?>
			<main role="main" class="container">
				<div class="starter-template">
					<h2 class="page-title">Black River Run</h2>
					<p class="lead">Welcome on the Black River Run website.</p>
				</div>
			</main>
		<?php
	}
?>