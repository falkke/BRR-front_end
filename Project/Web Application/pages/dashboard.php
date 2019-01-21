<?php
	$list = "";
	$_SESSION['dashboard'] = 1;
	
	if(is_logged() == 0) {
		header('Location:index.php?page=home');
	}

	if(isset($_GET['remove']) && $_GET['remove'] == 1) {
		if(isset($_GET['si-unit']) && (!empty($_GET['si-unit']) || $_GET['si-unit'] == 0)) {
			$si_unit_id = $_GET['si-unit'];
			
			$delete_si_unit_error = "";
			
			if(!does_si_unit_exist($si_unit_id)) {
				$delete_si_unit_error = "This SI-unit does not exist.";
			}
			
			else if(!is_si_unit_empty($si_unit_id)) {
				$delete_si_unit_error = "This SI-unit can not be deleted because it has some data.";
			}
			
			else {
				delete_si_unit($si_unit_id);
				header('Location:index.php?page=dashboard&list=si-units&si-unit-deleted=1');
			}
		}
		
		else if(isset($_GET['station']) && !empty($_GET['station'])) {
			$station_id = $_GET['station'];
			
			$delete_station_error = "";
			
			if(!does_station_exist($station_id)) {
				$delete_station_error = "This station does not exist.";
			}
			
			else if(!is_station_empty($station_id)) {
				$delete_station_error = "This station can not be deleted because it has some data.";
			}
			
			else {
				delete_station($station_id);
				header('Location:index.php?page=dashboard&list=stations&station-deleted=1');
			}
		}
		
		else if(isset($_GET['race']) && !empty($_GET['race'])) {
			$race_id = $_GET['race'];
			
			$delete_race_error = "";
			
			if(!does_race_exist($race_id)) {
				$delete_race_error = "This race does not exist.";
			}
			
			else if(!is_race_empty($race_id)) {
				$delete_race_error = "This race can not be deleted because it has some data.";
			}
			
			else {
				delete_race($race_id);
				
				if($_SESSION['dashboard'] == 1) {
					header('Location:index.php?page=dashboard&list=races&race-deleted=1');
				}
				
				else {
					header('Location:index.php?page=races&race-deleted=1');
				}
			}
		}
		
		else if(isset($_GET['category']) && !empty($_GET['category'])) {
			$category_id = $_GET['category'];
			
			$delete_category_error = "";

			if(!does_category_exist($category_id)) {
				$delete_category_error = "This category does not exist.";
			}
			
			else if(!is_category_empty($category_id)) {
				$delete_category_error = "This category can not be deleted because it has some data.";
			}
			
			else {
				delete_category($category_id);
				header('Location:index.php?page=dashboard&list=categories&category-deleted=1');
			}
		}
		
		else if(isset($_GET['runner']) && !empty($_GET['runner'])){
			$runner_id = $_GET['runner'];
			
			$delete_runner_error = "";

			if(!does_runner_exist($runner_id)) {
				$delete_runner_error = "This runner does not exist.";
			}
			
			else if(!is_runner_empty($runner_id)) {
				$delete_runner_error = "This runner can not be deleted because he/she has some data.";
			}
			
			else {
				delete_runner($runner_id);
				
				if($_SESSION['dashboard'] == 1) {
					header('Location:index.php?page=dashboard&list=runners&runner-deleted=1');
				}
				
				else {
					header('Location:index.php?page=runners&runner-deleted=1');
				}
			}
		}
		
			
		if(isset($_GET['team']) && !empty($_GET['team'])){
			$team_id = $_GET['team'];
			
			$delete_team_error = "";
			
			if(!does_team_exist($team_id)) {
				$delete_team_error = "This team does not exist.";
			}
			
			else if(!is_team_empty($team_id)) {
				$delete_team_error = "This team can not be deleted because it has some data.";
			}
			
			else {
				delete_team($team_id);
				
				if($_SESSION['dashboard'] == 1) {
					header('Location:index.php?page=dashboard&list=teams&team-deleted=1');
				}
				
				else {
					header('Location:index.php?page=teams&team-deleted=1');
				}
			}
		}
	}
?>

<main role="main" class="container no-gutters">
	<div class="row">
		<div class="nav-side-menu">
			<div class="brand">Dashboard</div>

			<div class="menu-list">
				<ul id="menu-content" class="menu-content collapse out">
					<li data-toggle="collapse" data-target="#system" class="collapsed">
						<a href="#">System</a>
					</li>
					<ul class="sub-menu collapse
					<?php 
						if(isset($_GET['list'])) {
							?>
								show
							<?php
						}
					?>" id="system">
						<li><a href="index.php?page=dashboard&list=races">Races</a></li>
						<li><a href="index.php?page=dashboard&list=runners">Runners</a></li>
						<li><a href="index.php?page=dashboard&list=teams">Teams</a></li>
						<li><a href="index.php?page=dashboard&list=si-units">SI-Units</a></li>
						<li><a href="index.php?page=dashboard&list=categories">Categories</a></li>
						<li><a href="index.php?page=dashboard&list=stations">Stations</a></li>
					</ul>
					
					<li data-toggle="collapse" data-target="#current_races" class="collapsed">
						<a href="#">Current Races</a>
					</li>
					
					<ul class="sub-menu collapse 
						<?php 
							if(isset($_GET['current-race'])) {
								?>
									show
								<?php 
							}
						?>
					" id="current_races">
						<?php
							foreach(get_current_races() as $current_race) {
								?>
									<li><a href="index.php?page=manage&race=<?=$current_race->ID?>"><?=$current_race->Name ?></a></li>
								<?php
							}
						?>
					</ul>

					<li data-toggle="collapse" data-target="#planned_races" class="collapsed">
						<a href="#">Planned Races</a>
					</li>
					<ul class="sub-menu collapse 
						<?php 
							if(isset($_GET['planned-race'])) {
								?>
									show
								<?php 
							}
						?>
					" id="planned_races">
						<?php
							foreach(get_planned_races() as $planned_race) {
								?>		
									<li><a href="index.php?page=manage&race=<?=$planned_race->ID?>"><?=$planned_race->Name ?></a></li>
								<?php
							}
						?>
					</ul>

					<li data-toggle="collapse" data-target="#past_races" class="collapsed">
						<a href="#">Past Races</a>
					</li>
					
					<ul class="sub-menu collapse 
						<?php 
							if(isset($_GET['past-race'])) {
								?>
									show
								<?php 
							}
						?>
					" id="past_races">
						<?php
							foreach(get_past_races() as $past_race) {
								?>		
									<li><a href="index.php?page=manage&race=<?=$past_race->ID?>"><?=$past_race->Name ?></a></li>
								<?php
							}
						?>
					</ul>
				</ul>
			</div>
		</div>
	</div>

	<div class="section-template">
		<?php
			if(isset($_GET['list'])) {
				if($_GET['list'] == "races")
					require 'body/race-list.php';
				else if($_GET['list'] == "runners")
					require 'body/runner-list.php';
				else if($_GET['list'] == "teams")
					require 'body/team-list.php';
				else if($_GET['list'] == "stations")
					require 'body/station-list.php';
				else if($_GET['list'] == "categories")
					require 'body/category-list.php';
				else if($_GET['list'] == "si-units")
					require 'body/si-unit-list.php';
			}
			
			else {
				?>
					<h2 class="page-title">Dashboard</h2>
					
					<p class="lead">Select a option in the side menu.</p>
				<?php
			}
		?>
	</div>
</main>
