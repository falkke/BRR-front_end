<?php
	$id = "";
	$gender = "";
	$distance = "";

	if(isset($_GET['race']) && !empty($_GET['race'])) 
	{
		$id = $_GET['race'];
		
		if(does_race_exist($id)) {
			$race = get_race($id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(isset($_GET['gender']) && !empty($_GET['gender'])) 
	{
		$gender = $_GET['gender'];
	}
	
	if(isset($_GET['distance']) && !empty($_GET['distance'])) 
	{
		if($_GET['distance'] == "100") 
		{
			$distance = "100 miles";
		}
		
		else if($_GET['distance'] == "50") 
		{
			$distance = "50 miles";
		}
		
		else
		{
			$distance = "20 miles";
		}
	}
	
	if (!empty($race) && !empty($gender) && !empty($distance)) {	
		$instance = get_race_instance($race->ID, $gender, $_GET['distance']);
	}
	
	$search = "";
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
	}
	
	
	if(	(isset($_GET['runner']) && !empty($_GET['runner']))
	&&	(isset($_GET['race']) && !empty($_GET['race']))
	&&	(isset($_GET['remove']) && !empty($_GET['remove']) && $_GET['remove'] == 1)){
		$race_id = $_GET['race'];
		$runner_id = $_GET['runner'];

		if(!has_si_unit($race_id, $runner_id)) {
			delete_race_runner($race_id, $runner_id);
		}
		
		header('Location:index.php?page=race&race='.$race_id.'&runner-deleted=1');
	}
?>

<main role="main" class="container no-gutters">
	<div class="row">
		<div class="nav-side-menu">
			<div class="brand">Category</div>
			<span class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></span>
			
			<div class="menu-list">
				<ul id="menu-content" class="menu-content collapse out">
				<?php
					foreach(get_race_class_genders($id) as $class_gender) {
						if(not_empty_race_gender($id, $class_gender)) {
						?>							
							<li data-toggle="collapse" data-target="#<?=$class_gender ?>" class="collapsed">
								<a href="#"><?=$class_gender ?></a>
							</li>
							<ul class="sub-menu collapse" id="<?=$class_gender ?>">
								<?php
									foreach(get_race_class_gender_distances($id, $class_gender) as $class_gender_distance) {
										if(not_empty_race_gender_distances($id, $class_gender, $class_gender_distance)) {
										?>	
											<li><a href="index.php?page=race&race=<?= $id ?>&gender=<?=$class_gender ?>&distance=<?=$class_gender_distance ?>"><?=$class_gender_distance ?> miles</a></li>				
										<?php
										}
									}
								?>	
							</ul>
					
						<?php
						}
					}
				?>
				</ul>
			</div>
		</div>
	</div>
		
	<div class="section-template">
		<?php 
			if	((isset($_GET['race']) && !empty($_GET['race'])) && 
				(isset($_GET['gender']) && !empty($_GET['gender'])) &&
				(isset($_GET['distance']) && !empty($_GET['distance']))) {
				?>	
				<a class="link-title" href="index.php?page=race&race=<?=$race->ID?>"><h2 class="page-title followed-title"><?= $race->Name ?></h2></a>
				<h3 class="page-subtitle"><?= "Results " . $gender . " - " . $distance ?></h3>
				
				<a title="Early view in the race" class="bg-primary text-white table-button" href="index.php?page=view&race=<?=$id?>&gender=<?=$gender?>&distance=<?=$_GET['distance']?>&view=resting">&#9658</a>
				<a title="Late view in the race" class="bg-primary text-white table-button" href="index.php?page=view&race=<?=$id?>&gender=<?=$gender?>&distance=<?=$_GET['distance']?>&view=latest">&#9658|</a>
				
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
										<th>Elapsed Time</th>
										<th>Date & Time</th>
										<th>Status</th>
									</tr>
								</thead>
								
								<tbody>
									<?php
										foreach(get_race_runners_by_status($race->ID, $search, "Running") as $race_runner) {	
											$class = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
										
											if(($class->Gender == $_GET['gender']) && ($class->Distance == $_GET['distance'])) {
												$runner = get_runner($race_runner->Runner);
												$timestamp = get_last_timestamp($race_runner->Runner, $instance->ID);
												$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
												$elapsed = get_total_elapsed_time($race_runner->Runner, $instance->ID);
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
										<th>Elapsed Time</th>
										<th>Behind</th>
										<th>Date & Time</th>
										<th>Status</th>
									</tr>
								</thead>
								
								<tbody>
									<?php
										foreach(get_race_runners_by_status($race->ID, $search, "Finished") as $race_runner) {	
											$class = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
										
											if(($class->Gender == $_GET['gender']) && ($class->Distance == $_GET['distance'])) {
												$runner = get_runner($race_runner->Runner);
												$timestamp = get_last_timestamp($race_runner->Runner, $instance->ID);
												$behind = get_time_behind($race_runner);
												$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
												$elapsed = get_total_elapsed_time($race_runner->Runner, $instance->ID);
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
							<h3 class="text-left"> Did Not Finish </h3>
							<table class="table table-bordered table-striped table-condensed">           
								<thead>
									<tr>
										<th>Place</th>
										<th>Distance</th>
										<th>Bib</th>
										<th>Name</th>
										<th>Team</th>
										<th>Elapsed Time</th>
										<th>Date & Time</th>
										<th>Status</th>
									</tr>
								</thead>
								
								<tbody>
									<?php
										foreach(get_race_runners_by_status($race->ID, $search, "DNF") as $race_runner) {	
											$class = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
										
											if(($class->Gender == $_GET['gender']) && ($class->Distance == $_GET['distance'])) {
												$runner = get_runner($race_runner->Runner);
												$timestamp = get_last_timestamp($race_runner->Runner, $instance->ID);
												$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
												$elapsed = get_total_elapsed_time($race_runner->Runner, $instance->ID);
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
																if($timestamp != null) {
																	echo $race_runner->Timestamp;
																}
																
																else {
																	echo "-";
																}
															?>
														</td>
														<td><?=$race_runner->Status?></td>
													</tr>						
												<?php
											}
										}
									?>
								</tbody>
							</table>
						<?php
					}
					
					if(exist_race_runners($instance, $search, "DNS")) {
						?> 
							<h3 class="text-left"> Did Not Start </h3>
							<table class="table table-bordered table-striped table-condensed">           
								<thead>
									<tr>
										<th>Place</th>
										<th>Bib</th>
										<th>Name</th>
										<th>Team</th>
										<th>Status</th>
									</tr>
								</thead>
								
								<tbody>
									<?php
										foreach(get_race_runners_by_status($race->ID, $search, "DNS") as $race_runner) {	
											$class = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
										
											if(($class->Gender == $_GET['gender']) && ($class->Distance == $_GET['distance'])) {
												$runner = get_runner($race_runner->Runner);
												$timestamp = get_last_timestamp($race_runner->Runner, $instance->ID);
												$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
												?>	
													<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
														<td><?=$race_runner->Place?></td>
														<td><?=$race_runner->Bib?></td>
														<td><?=$runner->FirstName." ".$runner->LastName?></td>
														<td><?=$team->Name?></td>
														<td><?=$race_runner->Status?></td>
													</tr>						
												<?php
											}
										}
									?>
								</tbody>
							</table>
						<?php
					}
			} 
			
			else if(isset($_GET['race']) && !empty($_GET['race'])) {
				?>	
					<h2 class="page-title followed-title"><?= $race->Name ?></h2>
					<h3 class="page-subtitle"><?= $race->Date ?></h3>
					
					<p class="lead">Please, choose a category in the side menu to see the results.</p>
					<a title="Race Results" class="bg-primary text-white table-button" href="index.php?page=results&race=<?=$id?>">&#9872</a>
					<?php	
						require "summary.php";
					
						if(is_logged()) {
							require "track.php";	
						}
			}
		?>
	</div>
</main>