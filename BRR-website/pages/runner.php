<?php
	if(isset($_GET['runner']) && !empty($_GET['runner'])) 
	{
		$runner_id = $_GET['runner'];
		
		if(runner_exists($runner_id)) {
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
	
	if(isset($_GET['race']) && !empty($_GET['race'])) 
	{
		if(race_exists($_GET['race'])) 
		{
			$race = get_race($_GET['race']);
			$race_runner = get_race_runner($runner_id, $race->ID);
			$class = get_race_runner_class($runner_id, $race->ID);
			$club = get_race_runner_team($runner_id, $race->ID);
		}
		
		else
		{
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
	<?php 

	?>	
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
						<!--<li><?= $type ?></li>-->
					</ul>
				</div>
				
				<?php
					if(sizeof(get_races_runner($runner_id)) > 1) 
					{
				?>
				<div class="brand">History</div>
				
				<div class="menu-list">
					<ul id="menu-content" class="menu-content collapse out">
						<li data-toggle="collapse" data-target="#truc" class="collapsed">
							<a href="#">Other Races</a>
						</li>
						<ul class="sub-menu collapse" id="truc">
						<?php
							foreach(get_races_runner($runner_id) as $other_race_runner) {		
								$other_race = get_race($other_race_runner->Race);
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
			if($race != "") 
			{
		?>
			<a class="link-title" href="index.php?page=race&race=<?=$race->ID?>&gender=<?=$class->Gender?>&distance=<?=$class->Distance?>"><h2 class="page-title followed-title"><?= $race->Name ?></h2></a>
			<h3 class="page-subtitle"><?= "Results " . $class->Gender . " - " . $class->Distance ?> miles</h3>
			
			<table class="table table-bordered table-striped table-condensed">           
			<thead>
				<tr>
					<th>Distance</th>
					<th>Place</th>
					<th>Elapsed Time</th>
					<th>Behind</th>
					<th>Date & Time</th>
					<?php
						if(is_logged() == 1)
						{
					?>
						<th>
							<a class="bg-success text-white table-button" href="index.php?page=add-runner">+</a>
						</th>
					<?php
						}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
					$lap = -1;
					foreach(get_timestamps($runner_id, $race->ID) as $timestamp) {		
						$station = get_station($timestamp->Station);						
						$lap = get_number_laps($runner_id, $race->ID, $timestamp->Timestamp, $timestamp->Station);
						$behind = get_time_behind_at_timestamp($race_runner);
						?>	
							<tr>
								<td><?=($lap * 10) + $station->LengthFromStart?></td>
								<td>
										<?php
											if($timestamp->Station == 0)
											{
												echo "-";
											}
											else
											{
												echo $timestamp->Place;
											}
										?>
								</td>
								<td></td>
								<td>
										<?php
											if($behind == 0)
											{
												echo "-";
											}
											else
											{
												echo "+" . $behind;
											}
										?>
								</td>
								<td><?=$timestamp->Timestamp?></td>						
								<?php
									if(is_logged() == 1)
									{
								?>
									<td class="no-change">
										<a class="bg-primary text-white table-button" href="index.php?page=manage-timestamp">...</a>
										<a class="bg-danger text-white table-button" href="index.php?page=home&timestamp=<?=$timestamp->Timestamp?>&remove=1">X</a>
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
			
			else
			{
		?>
			<h2 class="page-title"><?= $runner->FirstName." ".$runner->LastName ?></h2>
			<p class="lead">This runner has not run in a race yet.</p>
		<?php 
			}
		?>
	</div>
</main>