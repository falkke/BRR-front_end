<?php
	require "includes.php";
	
	$pages = scandir("pages/");

	$page = "home";
	
	if(isset($_GET["page"]) && !empty($_GET["page"])) {
		if(in_array($_GET["page"].".php", $pages)) {
			$page = $_GET["page"];
		}
		
		else {
			$page = "error";
		}
	}
?>

<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Black River Run</title>

		<!-- Include all the CSS files needed. -->
		
		<!-- Bootstrap core CSS : -->
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" rel="stylesheet">
		<!-- Custom styles : -->
		<link href="css/styles.css" rel="stylesheet">
	</head>

	<body>

		<!-- Main navigation menu : -->
		
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<a class="navbar-brand main-title" href="index.php?page=home">Black River Run</a>

			<div class="collapse navbar-collapse" id="navbarsExampleDefault">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item 
						<?php
							if($page == "home") {
								?>
									active
								<?php
							}
						?>
					">
						<a class="nav-link" href="index.php?page=home">Home</a>
					</li>
										
					<li class="nav-item 
						<?php
							if($page == "races") {
								?>
									active
								<?php
							}
						?>
					">
						<a class="nav-link" href="index.php?page=races">Races</a>
					</li>
										
					<li class="nav-item 
						<?php
							if($page == "runners") {
								?>
									active
								<?php
							}
						?>
					">
						<a class="nav-link" href="index.php?page=runners">Runners</a>
					</li>
										
					<li class="nav-item 
						<?php
							if($page == "teams") {
								?>
									active
								<?php
							}
						?>
					">
						<a class="nav-link" href="index.php?page=teams">Teams</a>
					</li>
				</ul>
				
				<?php 
					if(is_logged() == 1) {
						?>
							<ul class="navbar-nav my-2 my-lg-0">
								<li class="nav-item
									<?php
										if($page == "dashboard") {
											?>
												active
											<?php
										}
									?>
								">
									<a class="nav-link" href="index.php?page=dashboard">Dashboard</a>
								</li>
								
								<li class="nav-item
									<?php
										if($page == "settings") {
											?>
												active
											<?php
										}
									?>
								">
									<a class="nav-link" href="index.php?page=settings">Settings</a>
								</li>
								
								<li class="nav-item">
									<a class="nav-link" href="index.php?page=logout">Logout</a>
								</li>
							</ul>
						<?php 
					}
				?>
			</div>
		</nav>

		<!-- Require the content of the page. -->
		
		<?php
			require 'pages/'.$page.'.php';
		?>
		
		<!-- Include all the Javascript files needed. -->
		<!--<script src="jquery/external/jquery/jquery.js"></script>		
		<script src="jquery/jquery-ui.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="js/popper.min.js"></script>-->
		<script src="http://code.jquery.com/jquery-latest.min.js"></script>	
		<script src="jquery/jquery-ui.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.js"></script>
		
		
		<!-- Bootstrap core Javascript : -->
		<script src="bootstrap/js/bootstrap.min.js"></script>
		<script src="js/main.js"></script>
	</body>
</html>
