<?php
	require 'includes.php';
	
	$pages = scandir('pages/');

	$page = "home";
	
	if(isset($_GET['page']) && !empty($_GET['page'])) 
	{
		if(in_array($_GET['page'].'.php', $pages)) 
		{
			$page = $_GET['page'];
		}
		
		else 
		{
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

		<!-- Bootstrap core CSS -->
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="css/styles.css" rel="stylesheet">
	</head>

	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<a class="navbar-brand main-title" href="index.php?page=home">Black River Run</a>

			<div class="collapse navbar-collapse" id="navbarsExampleDefault">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item 
					<?php
						if($page == "home")
						{
					?>
						active
					<?php
						}
					?>">
						<a class="nav-link" href="index.php?page=home">Home</a>
					</li>
										
					<li class="nav-item 
					<?php
						if($page == "race-list")
						{
					?>
						active
					<?php
						}
					?>">
						<a class="nav-link" href="index.php?page=race-list">Races</a>
					</li>
										
					<li class="nav-item 
					<?php
						if($page == "runner-list")
						{
					?>
						active
					<?php
						}
					?>">
						<a class="nav-link" href="index.php?page=runner-list">Runners</a>
					</li>
				</ul>
				
				
				<ul class="navbar-nav my-2 my-lg-0">
					<li class="nav-item">
						<?php 
							if(is_logged() == 1) 
							{
						?>
								<a class="nav-link" href="index.php?page=logout">Logout</a>
								
						<?php 
							}
						?>
					</li>
				</ul>
			</div>
		</nav>

		<?php
			require 'pages/'.$page.'.php';
		?>
		
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="js/jquery.min.js"></script>
		<script src="js/popper.min.js"></script>
		<script src="bootstrap/js/bootstrap.min.js"></script>
		<script src="js/main.js"></script>
	</body>
</html>
