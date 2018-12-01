<?php
	$id = "";
	$gender = "";
	$type = "";

	if(isset($_GET['race']) && !empty($_GET['race'])) 
	{
		$id = $_GET['race'];
		
		if(race_exists($id)) {
			$race = get_race($id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(isset($_GET['gender']) && !empty($_GET['gender'])) 
	{
		if($_GET['gender'] == "f") 
		{
			$gender = "Damer";
		}
		
		else 
		{
			$gender = "Herrar";
		}
	}
	
	if(isset($_GET['type']) && !empty($_GET['type'])) 
	{
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
		}
	}
?>

<main role="main" class="container no-gutters">
	<div class="row">
		<div class="nav-side-menu">
			<div class="brand">Category</div>
			<div class="menu-list">
				<ul id="menu-content" class="menu-content collapse out">
					<li data-toggle="collapse" data-target="#products" class="collapsed">
						<a href="#">Damer</a>
					</li>
					<ul class="sub-menu collapse" id="products">
						<!--
							<li class="active"><a href="index.php?page=race&id=<?= $id ?>&gender=f&type=100">100 miles</a></li>
						-->
						<li><a href="index.php?page=race&race=<?= $id ?>&gender=f&type=100">100 miles</a></li>
						<li><a href="index.php?page=race&race=<?= $id ?>&gender=f&type=50">50 miles</a></li>
						<li><a href="index.php?page=race&race=<?= $id ?>&gender=f&type=20">20 miles</a></li>
					</ul>

					<li data-toggle="collapse" data-target="#service" class="collapsed">
						<a href="#">Herrar</a>
					</li>  
					<ul class="sub-menu collapse" id="service">
						<li><a href="index.php?page=race&race=<?= $id ?>&gender=m&type=100">100 miles</a></li>
						<li><a href="index.php?page=race&race=<?= $id ?>&gender=m&type=50">50 miles</a></li>
						<li><a href="index.php?page=race&race=<?= $id ?>&gender=m&type=20">20 miles</a></li>
					</ul>
				</ul>
			</div>
		</div>
	</div>
		
	<div class="section-template">
	<?php 
		if	((isset($_GET['race']) && !empty($_GET['race'])) && 
			(isset($_GET['gender']) && !empty($_GET['gender'])) &&
			(isset($_GET['type']) && !empty($_GET['type'])))
		{
	?>	
		<h2 class="page-title followed-title"><?= $race->Name ?></h2>
		<h3 class="page-subtitle"><?= "Results " . $gender . " - " . $type ?></h3>
		
		<form class="form-inline my-2">
			<div class="input-group">
				<input class="form-control" type="text" style="text-align:right" placeholder="Search" aria-label="Search">    
				<span class="input-group-btn">
					<button class="btn  btn-default" type="submit">Search</button>
				</span>
			</div>
		</form>

		<table class="table table-bordered table-striped table-condensed">           
			<thead>
				<tr>
					<th>Place</th>
					<th>Bib</th>
					<th>Name</th>
					<th>Team</th>
					<th>Elaspsed Time</th>
					<th>Behind</th>
					<th>Date & Time</th>
					<th>Status</th>
					<?php
						if(is_logged() == 1)
						{
					?>
						<th>
							<a class="bg-success text-white table-button" href="index.php?page=manage-runner&race=<?= $id ?>">+</a>
						</th>
					<?php
						}
					?>
				</tr>
			</thead>
			<tbody>
				<tr class='clickable-row' data-href="index.php?page=runner&ssn=ssn&race=<?= $id ?>&gender=<?= $_GET['gender'] ?>&type=<?= $_GET['type'] ?>">
					<td>1</td>
					<td>2</td>
					<td>Bobby Bob</td>
					<td>Bobby Team</td>
					<td>23:52:48</td>
					<td></td>
					<td>Sat 16:52:48</td>
					<td>Finish</td>							
					<?php
						if(is_logged() == 1)
						{
					?>
						<td class='no-change'>
							<a class="bg-primary text-white table-button" href="index.php?page=manage-runner&race=<?= $id ?>&runner=bib">...</a>
							<a class="bg-danger text-white table-button" href="index.php?page=home&race=<?= $id ?>&bib=bib&remove=1">X</a>
						</td>
					<?php
						}
					?>
				</tr>
				<tr class='clickable-row' data-href="index.php?page=runner&ssn=ssn&race=<?= $id ?>&gender=<?= $_GET['gender'] ?>&type=<?= $_GET['type'] ?>">
					<td>2</td>
					<td>1</td>
					<td>Bob Bobby</td>
					<td>Bob Team</td>
					<td>23:58:59</td>
					<td>+0:06:11</td>
					<td>Sat 16:52:48</td>
					<td>Finish</td>
					<?php
						if(is_logged() == 1)
						{
					?>
						<td class='no-change'>
							<a class="bg-primary text-white table-button" href="index.php?page=manage-runner&race=<?= $id ?>&runner=bib">...</a>
							<a class="bg-danger text-white table-button" href="index.php?page=home&race=<?= $id ?>&bib=bib&remove=1">X</a>
						</td>
					<?php
						}
					?>
				</tr>
			</tbody>
		</table>
	
	<?php 
		} 
		
		else if(isset($_GET['race']) && !empty($_GET['race'])) 
		{
	?>	
		<h2 class="page-title followed-title"><?= $race->Name ?></h2>
		<h3 class="page-subtitle"><?= $race->Date ?></h3>
		<?php 
			if(is_logged() == 1)
			{
		?>	
			<p class="lead">Please, choose a category in the side menu to see the results.</p>
			<a class="bg-primary text-white table-button" href="index.php?page=manage-race&race=<?= $id ?>">...</a>
			<a class="bg-danger text-white table-button" onclick="DeleteRaceAlert(<?= $id ?>);" href="#">X</a>
		<?php 
			} 
		} 
	?>
	</div>
</main>