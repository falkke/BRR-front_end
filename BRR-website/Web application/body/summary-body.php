<?php
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
	
	if(!isset($_GET['race'])) {
		$race = get_active_race();
	}
?>

<h2 class="page-title followed-title">Race Participants</h2>
				
<?php				
	foreach(get_classes() as $class) {
		if(exist_participant($class->ID, $race->ID, "summary")) {
?>
			<a class="link-title" href="index.php?page=race&race=<?=$race->ID?>&gender=<?=$class->Gender?>&distance=<?=$class->Distance?>"><h3 class="text-left"><?=$class->Gender?> <?=$class->Distance?> Miles</h3></a>
			<table class="table table-bordered table-striped table-condensed">           
				<thead>
					<tr>
						<th>Bib</th>
						<?php
							if(is_logged() == 1)
							{
						?>
								<th>SI-Unit</th>
						<?php
							}
						?>
						<th>Name</th>
						<th>Team</th>
						<th>Starting Time</th>
					</tr>
				</thead>
				
				<tbody>
					<?php
						foreach(participant_list($class->ID, $race->ID) as $race_runner) {
							$runner = get_runner($race_runner->Runner);
							$runner_unit = get_runner_unit($race_runner->Runner, $race_runner->Race);
							$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
					?>	
							<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$race_runner->Runner?>&race=<?=$race_runner->Race?>">
								<td><?=$race_runner->Bib?></td>		
								<?php
									if(is_logged() == 1)
									{
										if($runner_unit != null) {		
								?>
											<td><?=$runner_unit->SI_unit?></td>
									<?php
										}
										else {
									?>
											<td> - </td>
								<?php
										}
									}
								?>
								<td><?=$runner->FirstName." ".$runner->LastName?></td>
								<td><?=$team->Name?></td>
								<td><?=$race_runner->StartTime?></td>
							</tr>
					<?php
						}
					?>
				</tbody>
			</table>
<?php
		}
	}
?>
