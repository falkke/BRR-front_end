<?php
	if(isset($_GET['runner']) && !empty($_GET['runner'])) 
	{
		$id = $_GET['runner'];
		
		if(runner_exists($id)) {
			$runner = get_runner($id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	else {
		header("Location:index.php?page=home");
	}
	
	$race = "";
	
	if(get_last_race_runner($id)) {
		$race_runner = get_last_race_runner($id);
		$race = get_race($race_runner->Race);
		$class = get_race_runner_class($id, $race_runner->Race);
		$club = get_race_runner_team($id, $race_runner->Race);
	}
	
	
	/*$race = get_race($_GET['race']);
	
	if($_GET['gender'] == "f") 
	{
		$gender = "Damer";
	}
	
	else 
	{
		$gender = "Herrar";
	}
	
	if($_GET['type'] == "100") 
	{
		$type = "100 miles";
	}

	else if($_GET['type'] == "50") 
	{
		$type = "50 miles";
	}

	else
	{
		$type = "20 miles";
	}*/
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
					<li><?= $race_runner->Place ?></li>
					<li><?= $race_runner->Bib ?></li>
					<li><?= $club->Name ?></li>
					<!--<li><?= $type ?></li>-->
				</ul>
			</div>
			
			<div class="brand">History</div>
			
			<div class="menu-list">
				<ul id="menu-content" class="menu-content collapse out">
					<li data-toggle="collapse" data-target="#products" class="collapsed">
						<a href="#">Other Races</a>
					</li>
					<ul class="sub-menu collapse" id="products">
					<!--
						<li class="active"><a href="index.php?page=runner&ssn=ssn&race=<?= $race->ID ?>&gender=f&type=100">2018 - Gender - Type</a></li>
						<li><a href="index.php?page=runner&ssn=ssn&race=<?= $race->ID ?>&gender=f&type=50">2017 - Gender - Type</a></li>
					-->
					</ul>
				</ul>
			</div>
			<?php 
				}
			?>
		</div>
	</div>
	
	<div class="section-template">
		<?php 
			if($race != "") 
			{
		?>
			<h2 class="page-title followed-title"><?= $race->Name ?></h2>
			<h3 class="page-subtitle"><?= "Results " . $class->Gender . " - " . $class->Distance ?></h3>
			
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
				<!--<tbody>
					<tr>
						<td>Finish</td>
						<td>1</td>
						<td>20:25:52</td>
						<td></td>
						<td>Sun 06:25:52</td>						
						<?php
							if(is_logged() == 1)
							{
						?>
							<td>
								<a class="bg-primary text-white table-button" href="index.php?page=edit-runner">...</a>
								<a class="bg-danger text-white table-button" href="index.php?page=home&race=<?= $race->ID ?>&bib=bib&remove=1">X</a>
							</td>
						<?php
							}
						?>
					</tr>
				</tbody>-->
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