<?php
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
						if(isset($_GET['runners']) || isset($_GET['races']) || isset($_GET['teams']) || isset($_GET['si-units']) || isset($_GET['categories']) || isset($_GET['stations'])) {
					?>
					show
					<?php 
						}
					?>" id="system">
						<li><a href="index.php?page=dashboard&races">Races</a></li>
						<li><a href="index.php?page=dashboard&runners">Runners</a></li>
						<li><a href="index.php?page=dashboard&teams">Teams</a></li>
						<li><a href="index.php?page=dashboard&si-units">SI-Units</a></li>
						<li><a href="index.php?page=dashboard&categories">Categories</a></li>
						<li><a href="index.php?page=dashboard&stations">Stations</a></li>
					</ul>					
					<li data-toggle="collapse" data-target="#races" class="collapsed">
						<a href="#">Planned Races</a>
					</li>
					<ul class="sub-menu collapse <?php 
						//if(isset($_GET['runners']) || isset($_GET['races']) || isset($_GET['teams']) || isset($_GET['si-units']) || isset($_GET['categories']) || isset($_GET['stations'])) {
					?>
					show
					<?php 
						//}
					?>" id="races">
					</ul>
				</ul>
			</div>
		</div>
	</div>

	<div class="section-template">
		<?php
			if(isset($_GET['races'])) {
				require 'body/race-list.php';
			}
			
			else if(isset($_GET['runners'])) {
				require 'body/runner-list.php';
			}
			
			else if(isset($_GET['teams'])) {
				require 'body/team-list.php';
			}
			
			else if(isset($_GET['stations'])) {
				require 'body/station-list.php';
			}
			
			else if(isset($_GET['categories'])) {
				require 'body/category-list.php';
			}
			
			else if(isset($_GET['si-units'])) {
				require 'body/si-unit-list.php';
			}
		?>
	</div>
</main>
