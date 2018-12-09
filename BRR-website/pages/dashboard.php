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
					<li data-toggle="collapse" data-target="#races" class="collapsed">
						<a href="#">Planned Races</a>
					</li>
					<ul class="sub-menu collapse 
						<?php 
							if(isset($_GET['race'])) {
								?>
									show
								<?php 
							}
						?>
					" id="races">
						<li><a href="index.php?page=dashboard">List</a></li>
						<li><a href="index.php?page=dashboard">Add</a></li>
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
		?>
	</div>
</main>
