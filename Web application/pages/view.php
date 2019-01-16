<?php
	$id = "";
	$gender = "";
	$distance = "";

	if(isset($_GET['race']) && !empty($_GET['race'])) {
		$id = $_GET['race'];
		
		if(does_race_exist($id)) {
			$race = get_race($id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(isset($_GET['gender']) && !empty($_GET['gender'])) {
		$gender = $_GET['gender'];
	}
	
	if(isset($_GET['distance']) && !empty($_GET['distance'])) {
		if($_GET['distance'] == "100") 
		{
			$distance = 100;
		}
		
		else if($_GET['distance'] == "50") 
		{
			$distance = 50;
		}
		
		else
		{
			$distance = 20;
		}
	}
	
	if (!empty($race) && !empty($gender) && !empty($distance)) {	
		$instance = get_race_instance($race->ID, $gender, $_GET['distance']);
	}
	
	$view = "";
	if(isset($_GET['view']) && !empty($_GET['view'])) {
		$view = $_GET['view'];
	}
?>

<?php
	$page = "index.php?page=view&race=".$race->ID."&gender=".$gender."&distance=".$distance."&view=".$view;
	$sec = "30";
?>
<meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">

<?php 
	if	((isset($_GET['race']) && !empty($_GET['race'])) && 
		(isset($_GET['gender']) && !empty($_GET['gender'])) &&
		(isset($_GET['distance']) && !empty($_GET['distance'])))
	{
?>
		<a class="link-title" href="index.php?page=race&race=<?=$race->ID?>&gender=<?=$gender?>&distance=<?=$distance?>"><h3 class="text-left"><?=$gender?> - <?=$distance?> Miles</h3></a>
			
		<?php
		if($view != null && $view != "") {
		?>
			<table class="table table-bordered table-striped table-condensed">           
				<thead>
					<tr>
						<th>Timestamp</th>
						<th>Place</th>
						<th>Distance</th>
						<th>Bib</th>
						<th>Name</th>
						<th>Team</th>
						<th>Status</th>
					</tr>
				</thead>
				
				<tbody>
					<?php
					if($view == "latest") {
						foreach(get_latest_timestamps($instance->ID) as $race_runner) {	
							$class = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
						
							if(($class->Gender == $_GET['gender']) && ($class->Distance == $_GET['distance']) && $race_runner->Status != "DNS") 
							{
								$runner = get_runner($race_runner->Runner);
								$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
								
								if($race_runner->Distance == -1) {
								?>	
									<tr style="background-color:#cc0000;color:#e1ffff" class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
								<?php
								}
								else if($race_runner->Distance == $distance) {
								?>	
									<tr style="background-color:#28A745;color:#e1ffff" class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
								<?php
								}
								else {
								?>
									<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
								<?php
								}
								?>
									<td><?=$race_runner->Timestamp?></td>
									<td>
										<?php
										if($race_runner->Distance <= 0) {
											echo "-";
										}
										else {
											echo $race_runner->Place;
										}
										?>
									</td>
									<td>
										<?php
										if($race_runner->Distance == -1) {
											echo "ABANDON";
										}
										else if($race_runner->Distance == 0) {
											echo "START";
										}
										else if($race_runner->Distance == $distance) {
											echo "FINISH";
										}
										else {
											echo $race_runner->Distance;
										}
										?>
									</td>
									<td><?=$race_runner->Bib?></td>
									<td><?=$runner->FirstName." ".$runner->LastName?></td>
									<td><?=$team->Name?></td>
									<td><?=$race_runner->Status?></td>
								</tr>
								<?php
							}
						}
					}
					else if($view == "resting") {
						foreach(get_resting_timestamps($instance->ID) as $race_runner) {	
						$class = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
						
							if(($class->Gender == $_GET['gender']) && ($class->Distance == $_GET['distance']) && $race_runner->Status != "DNS"
							&& ($race_runner->Station == "b827eba42979" || $race_runner->Station == "b827ebdcbd98")) 
							{
								$runner = get_runner($race_runner->Runner);
								$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
								
								if($race_runner->Distance == $distance) {
								?>	
									<tr style="background-color:#28A745;color:#e1ffff" class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
								<?php
								}
								else {
								?>
									<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
								<?php
								}
								?>
									<td><?=$race_runner->Timestamp?></td>
									<td>
										<?php
										if($race_runner->Distance <= 0) {
											echo "-";
										}
										else {
											echo $race_runner->Place;
										}
										?>
									</td>
									<td>
										<?php
										if($race_runner->Distance == $distance) {
											echo "FINISH";
										}
										else if($race_runner->Station == "b827eba42979") {
											echo "Finish Lap ".$race_runner->Lap;
										}
										else if($race_runner->Station == "b827ebdcbd98") {
											$tmp = $race_runner->Lap + 1;
											echo "Starting Lap ".$tmp;
										}
										else {
											echo $race_runner->Distance;
										}
										?>
									</td>
									<td><?=$race_runner->Bib?></td>
									<td><?=$runner->FirstName." ".$runner->LastName?></td>
									<td><?=$team->Name?></td>
									<td><?=$race_runner->Status?></td>
								</tr>	
								<?php
							}
						}
					}
				?>
				</tbody>
			</table>
		<?php
		}
		else {
			?>
				<p class="alert alert-danger" role="alert">The selected view type does not exist. (latest or resting are valid)</p>
			<?php
		}
	}
?>