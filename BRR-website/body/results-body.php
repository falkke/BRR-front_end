<div>
	<a class="link-title" href="index.php?page=race&race=<?=$race->ID?>"><h3 class="page-title followed-title"><?=$race->Name?></h3></a>
	<h3 class="page-subtitle">Race Results</h3>
</div>	
	
<?php				
	foreach(get_classes() as $class) {
		if(exist_participant($class->ID, $race->ID)) {
?>
			<a class="link-title" href="index.php?page=race&race=<?=$race->ID?>&gender=<?=$class->Gender?>&distance=<?=$class->Distance?>"><h3 class="text-left"><?=$class->Gender?> <?=$class->Distance?> Miles</h3></a>
			<table class="table table-bordered table-striped table-condensed">           
				<thead>
					<tr>
						<th>Place</th>
						<th>Name</th>
						<th>Team</th>
						<th>Elaspsed Time</th>
						<th></th>
					</tr>
				</thead>
				
				<tbody>
					<?php
						foreach(get_race_runners_by_status($race->ID, $search, "Finished") as $race_runner) {	
							$class_runner = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
						
							if(($class->Gender == $class_runner->Gender) && ($class->Distance == $class_runner->Distance)) {
								$runner = get_runner($race_runner->Runner);
								$timestamp = get_last_timestamp($race_runner->Runner, $race_runner->Race);
								$behind = get_time_behind($race_runner);
								$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
								$elapsed = get_total_elapsed_time($race_runner->Runner, $race_runner->Race);
								?>	
									<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
										<td><?=$race_runner->Place?></td>
										<td><?=$runner->FirstName." ".$runner->LastName?></td>
										<td><?=$team->Name?></td>
										<td><?=$elapsed?></td>
										<td><?="+".$behind?></td>
									</tr>						
								<?php
							}
						}
						foreach(get_race_runners_by_status($race->ID, $search, "Running") as $race_runner) {	
							$class_runner = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
						
							if(($class->Gender == $class_runner->Gender) && ($class->Distance == $class_runner->Distance)) {
								$runner = get_runner($race_runner->Runner);
								$timestamp = get_last_timestamp($race_runner->Runner, $race_runner->Race);
								$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
								$elapsed = get_total_elapsed_time($race_runner->Runner, $race_runner->Race);
								?>	
									<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
										<td> - </td>
										<td><?=$runner->FirstName." ".$runner->LastName?></td>
										<td><?=$team->Name?></td>
										<td>
										<?php
											if($elapsed == null) {
												echo "00:00:00";
											}
											else {
												echo $elapsed;
											}
										?>
										</td>
										<td> Still Running </td>
									</tr>						
								<?php
							}
						}
						foreach(get_race_runners_by_status($race->ID, $search, "DNF") as $race_runner) {	
							$class_runner = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
						
							if(($class->Gender == $class_runner->Gender) && ($class->Distance == $class_runner->Distance)) {
								$runner = get_runner($race_runner->Runner);
								$timestamp = get_last_timestamp($race_runner->Runner, $race_runner->Race);
								$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
								?>	
									<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
										<td> - </td>
										<td><?=$runner->FirstName." ".$runner->LastName?></td>
										<td><?=$team->Name?></td>
										<td>Do Not Finish</td>
										<td><?=$race_runner->Distance." Miles"?></td>
									</tr>						
								<?php
							}
						}
						foreach(get_race_runners_by_status($race->ID, $search, "DNS") as $race_runner) {	
							$class_runner = get_race_runner_class($race_runner->Runner, $race_runner->Race) ;		
						
							if(($class->Gender == $class_runner->Gender) && ($class->Distance == $class_runner->Distance)) {
								$runner = get_runner($race_runner->Runner);
								$timestamp = get_last_timestamp($race_runner->Runner, $race_runner->Race);
								$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
								?>	
									<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
										<td> - </td>
										<td><?=$runner->FirstName." ".$runner->LastName?></td>
										<td><?=$team->Name?></td>
										<td>Do Not Start</td>
										<td> - </td>
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
?>
