<?php
	if(	(isset($_GET['timestamp']) && !empty($_GET['timestamp']))
	&&	(isset($_GET['runner']) && !empty($_GET['runner']) && does_runner_exist($_GET['runner']))
	&&	(isset($_GET['race']) && !empty($_GET['race']) && does_race_exist($_GET['race']))
	&&	(isset($_GET['remove']) && !empty($_GET['remove']))){
		$timestamp = $_GET['timestamp'];
		$remove = $_GET['remove'];
		$runner_id = $_GET['runner'];
		$race_id = $_GET['race'];
		
		$delete_runner_error = "";
		
		if($remove == 1) {
			if(!does_timestamp_exist($timestamp, $runner_id)) {
				$delete_timestamp_error = "This timestamp does not exist.";
			}
			else {
				if((get_station(get_timestamp($timestamp, $runner_id)->Station)->Code) != 0 || (get_number_timestamps($runner_id, get_race_runner($runner_id, $race->ID)->RaceInstance) == 1)) {
					delete_timestamp($runner_id, get_instance_from_runner_race($runner_id, $race_id)->ID, $timestamp);
					header('Location:index.php?page=runner&runner='.$runner_id.'&race='.$race_id.'&timestamp-deleted=1');
				}
				else {
					$delete_timestamp_error = "This timestamp can not be removed.";
				}
			}
		}
	}

	if(isset($_GET['runner']) && !empty($_GET['runner'])) {
		$runner_id = $_GET['runner'];
		
		if(does_runner_exist($runner_id)) {
			$runner = get_runner($runner_id);
		}
		else {
			header("Location:index.php?page=home");
		}
	}
	else {
		header("Location:index.php?page=home");
	}
	
	$race = "";
	
	if(isset($_GET['race']) && !empty($_GET['race'])) {
		if(does_race_exist($_GET['race'])) {
			$race = get_race($_GET['race']);
			$race_runner = get_race_runner($runner_id, $race->ID);
			$class = get_race_runner_class($runner_id, $race->ID);
			$club = get_race_runner_team($runner_id, $race->ID);
		}
		else {
			header("Location:index.php?page=runner&runner=".$runner_id);
		}
	}
	else {
		if(get_last_race_runner($runner_id)) {
			$race_runner = get_last_race_runner($runner_id);
			$race = get_race($race_runner->Race);
			$class = get_race_runner_class($runner_id, $race_runner->Race);
			$club = get_race_runner_team($runner_id, $race_runner->Race);
		}
	}
?>

<main role="main" class="container no-gutters">
	<div class="row">
		<div class="nav-side-menu">
		
			<div class="brand"><?= $runner->FirstName ?> <?= $runner->LastName ?></div>
			<div class="runner-info">
				<ul>
					<li><?= $runner->Gender ?></li>
					<li><?= $runner->DateOfBirth ?></li>
				</ul>
			</div>
			
			<?php 
				if($race != "") {
					?>
						<div class="brand"><?=$race->Name ?></div>
						<div class="runner-race-info">
							<ul>
								<li>Place : <?= $race_runner->Place ?></li>
								<li>Bib : <?= $race_runner->Bib ?></li>
								<li>Team : <?= $club->Name ?></li>
							</ul>
						</div>
				
					<?php
					if(sizeof(get_races_runner($runner_id)) > 1) {
						?>
							<div class="brand">History</div>
							
							<span class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></span>
							<div class="menu-list">
								<ul id="menu-content" class="menu-content collapse out">
									<li data-toggle="collapse" data-target="#truc" class="collapsed">
										<a href="#">Other Races</a>
									</li>
									
									<ul class="sub-menu collapse" id="truc">
										<?php
											foreach(get_races_runner($runner_id) as $other_race_runner) {
												$other_race = get_race(get_race_from_instance($other_race_runner->RaceInstance)->Race);
												if($other_race->ID != $race->ID){
													?>		
														<li><a href="index.php?page=runner&runner=<?=$runner_id ?>&race=<?=$other_race->ID ?>"><?=$other_race->Name ?></a></li>
													<?php
												}
											}
										?>
									</ul>
								</ul>
							</div>
						<?php 
					}
				}
			?>
		</div>
	</div>
	
	<div class="section-template">
		<?php 
			if($race != "") {
				?>
					<a class="link-title" href="index.php?page=race&race=<?=$race->ID?>&gender=<?=$class->Gender?>&distance=<?=$class->Distance?>"><h2 class="page-title followed-title"><?= $race->Name ?></h2></a>
					<h3 class="page-subtitle"><?= "Results " . $class->Gender . " - " . $class->Distance ?> miles</h3>
					
					<?php 
						if(is_current_race($race->ID)) {
							?>
								<a title="Map" class="bg-primary text-white table-button" href="index.php?page=map&race=<?=$race->ID?>&runner=<?=$runner_id?>">&#127966</a>
							<?php
						}
					?>
					<h2 class="page-title followed-title">Timestamps</h2>
					<?php
						if(isset($delete_timestamp_error)) {
							?>
								<p class="alert alert-danger" role="alert"><?=$delete_timestamp_error?></p>
							<?php
						}
						
						else if(isset($_GET['timestamp-deleted']) && !empty($_GET['timestamp-deleted']) && ($_GET['timestamp-deleted'] == 1)) {				
							?>
								<p class="alert alert-success" role="alert">The timestamp has been succefully deleted.</p>
							<?php
						}
						
						else if(isset($_GET['timestamp-added']) && !empty($_GET['timestamp-added']) && ($_GET['timestamp-added'] == 1)) {				
							?>
								<p class="alert alert-success" role="alert">The timestamp has been succefully added.</p>
							<?php
						}
						
						else if(isset($_GET['timestamp-modified']) && !empty($_GET['timestamp-modified']) && ($_GET['timestamp-modified'] == 1)) {				
							?>
								<p class="alert alert-success" role="alert">The timestamp has been succefully modified.</p>
							<?php
						}
					?>
					<table class="table table-bordered table-striped table-condensed">           
						<thead>
							<tr>
								<th>Lap</th>
								<th>Station</th>
								<th>Distance</th>
								<th>Place</th>
								<th>Elapsed Time</th>
								<th>Behind</th>
								<th>Date & Time</th>
								<?php
									if(is_logged() == 1) {
										?>
											<th>
												<a class="bg-success text-white table-button" href="index.php?page=manage&timestamp&runner=<?=$runner_id?>&race=<?=$race->ID?>">+</a>
											</th>
										<?php
									}
								?>
							</tr>
						</thead>
					
						<tbody>
							<?php
								foreach(get_runner_timestamps($runner_id, get_instance_from_runner_race($runner_id, $race->ID)->ID) as $timestamp) {		
									$station = get_station($timestamp->Station);
									$behind = get_time_behind_at_timestamp($runner_id, get_instance_from_runner_race($runner_id, $race->ID)->ID, $timestamp->Lap, $station->ID);
									$elapsed = get_elapsed_time_at_timestamp($runner_id, get_race_runner($runner_id, $race->ID)->RaceInstance, $station->ID, $timestamp->Timestamp);
							?>	
									<tr>
										<td><?=$timestamp->Lap?></td>
										<td><?php
												if($station->Code == 99 && is_logged() != 1) {
													echo "ABANDON";
												}
												else {
													echo $station->Name;
												}
											?>
										</td>
										<td>
											<?php
												if($station->Code == 99) {
													echo "-";
												}
												
												else {
													echo (($timestamp->Lap - 1) * 10) + $station->LengthFromStart;
												}
											?>
										</td>
										
										<td>
											<?php
												if($station->Code == 0 || $station->Code == 99) {
													echo "-";
												}
												
												else {
													echo $timestamp->Place;
												}
											?>
										</td>
										
										<td>
											<?php 
												echo $elapsed; 
											?>
										</td>
										
										<td>
											<?php
												if($behind == "00:00:00" || $behind == null) {
													echo "-";
												}
												
												else {
													echo "+" . $behind;
												}
											?>
										</td>
										
										<td><?=$timestamp->Timestamp?></td>	
										
										<?php
											if(is_logged() == 1) {
												?>
													<td class="no-change">
														<a class="bg-primary text-white table-button" href="index.php?page=manage&timestamp=<?=$timestamp->Timestamp?>&runner=<?=$timestamp->Runner?>&race=<?=$race->ID?>">...</a>
														<a class="bg-danger text-white table-button" onclick="DeleteAlert_timestamp('<?=$timestamp->Timestamp?>', <?=$runner_id?>, <?=$race->ID?>);" href="#">X</a>
													</td>
												<?php
											}
										?>
									</tr>
							<?php
								}
							?>
						</tbody>
					</table>
				
				<?php 
			}
					
			else {
				?>
					<h2 class="page-title"><?= $runner->FirstName." ".$runner->LastName ?></h2>
					<p class="lead">This runner has not run in a race yet.</p>
				<?php 
			}
		?>
	</div>
</main>