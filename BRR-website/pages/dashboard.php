<?php
	$list = "";
	$_SESSION['dashboard'] = 1;
	
	if(is_logged() == 0) {
        header('Location:index.php?page=home');
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
					<ul class="sub-menu collapse <?php 
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
							if(isset($_GET['curent-race'])) {
								?>
									show
								<?php 
							}
						?>
					" id="current_races">
						<?php
							foreach(get_current_races() as $current_race) {
								?>		
									<li><a href="index.php?page=dashboard&current-race&race=<?=$current_race->ID?>"><?=$current_race->Name ?></a></li>
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
									<li><a href="index.php?page=dashboard&planned-race&race=<?=$planned_race->ID?>"><?=$planned_race->Name ?></a></li>
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
									<li><a href="index.php?page=dashboard&past-race&race=<?=$past_race->ID?>"><?=$past_race->Name ?></a></li>
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
			
			
			else if(isset($_GET['planned-race']) || isset($_GET['past-race'])) {
				require 'body/race-form.php';
			}
		?>
	</div>
</main>
