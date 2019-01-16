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
		<!--<link href="bootstrap/css/bootstrap.css" rel="stylesheet">-->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

		<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" rel="stylesheet">
		<!-- Custom styles : -->
		<link href="css/styles.css" rel="stylesheet">
	</head>

	<body>

		<!-- Main navigation menu : -->
		<?php
			if($page != "view") {
				?>
					<nav class="navbar navbar-expand-md navbar-dark fixed-top">
						<a class="navbar-brand main-title" href="index.php?page=home">Black River Run</a>

						<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-id" aria-controls="navbar-id" aria-expanded="false" aria-label="Toggle navigation">
							<span class="navbar-toggler-icon"></span>
						</button>
						
						<div class="navbar-collapse collapse" id="navbar-id">
							<ul class="navbar-nav nav guest-nav mr-auto">
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
										<ul class="navbar-nav admin-nav my-2 my-lg-0">
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
				<?php
			}
		?>

		<!-- Require the content of the page. -->
		
		<?php
			require 'pages/'.$page.'.php';
		?>
		
		<!-- Include all the Javascript files needed. -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
		<script src="js/main.js"></script>
	</body>
</html>
