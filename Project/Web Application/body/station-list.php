<?php
	require 'functions/session.php';

	$search = $_SESSION['bbr']['search-station'];
	$sort = "";
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
		$_SESSION['bbr']['search-station'] = $search;
	}
	
	if(isset($_GET['sort_word']) && ($_GET['sort_word'] == "ID" || $_GET['sort_word'] == "Name" || $_GET['sort_word'] == "Code" || $_GET['sort_word'] == "LengthFromStart")
	&& isset($_GET['sort_by']) && ($_GET['sort_by'] == "ASC" || $_GET['sort_by'] == "DESC")) {
		$sort = "ORDER BY ".$_GET['sort_word']." ".$_GET['sort_by'];
	}
	else if(isset($_GET['sort_word']) || isset($_GET['sort_by']))
	{
		header('Location:index.php?page=dashboard&list=stations');
	}
?>

<h2 class="page-title">Station List</h2>
		
<?php
	if(isset($delete_station_error)) {
		?>
			<p class="alert alert-danger" role="alert"><?=$delete_station_error?></p>
		<?php
	}
	
	else if(isset($_GET['station-deleted']) && !empty($_GET['station-deleted']) && ($_GET['station-deleted'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The station has been succefully deleted.</p>
		<?php
	}
	
	else if(isset($_GET['station-added']) && !empty($_GET['station-added']) && ($_GET['station-added'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The station has been succefully added.</p>
		<?php
	}
	
	else if(isset($_GET['station-modified']) && !empty($_GET['station-modified']) && ($_GET['station-modified'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The station has been succefully modified.</p>
		<?php
	}
?>

<form method="post" class="form-inline my-2">
	<div class="input-group">
		<input class="form-control" type="text" style="text-align:right" placeholder="Search" name="search" id="search" aria-label="Search" <?php
			if(isset($_SESSION['bbr']['search-station'])) {
				?>
					value="<?=$_SESSION['bbr']['search-station']?>"
				<?php
			}
		?>>    
		<span class="input-group-btn">
			<button class="btn  btn-default" type="submit" id="submit" name="submit">Search</button>
		</span>
	</div>
</form>

<table class="table table-bordered table-striped table-condensed">           
	<thead>
		<tr>
			<th>
				<a class="sort" href="index.php?page=<?=$_GET['page']?>
					<?php
						if(isset($_GET['list'])) {
							?>
								&list=<?=$_GET['list']?>
							<?php
						}
					?>
						&sort_word=ID
					<?php
						if(isset($_GET['sort_word']) && $_GET['sort_word'] == "ID" &&
							isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
							?>
								&sort_by=DESC
							<?php
						}
						
						else {
							?>
								&sort_by=ASC
							<?php
						}
					?>
				">
					ID
					<font class="sort-arrow">
						<?php
							if(isset($_GET['sort_word']) && $_GET['sort_word'] == "ID" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
						?>
								 v
						<?php
							}
							
							else if(isset($_GET['sort_word']) && $_GET['sort_word'] == "ID" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "DESC") {
						?>
								 ^
						<?php
							}
						?>
					</font>
				</a>
			</th>
			
			<th>
				<a class="sort" href="index.php?page=<?=$_GET['page']?>
					<?php
						if(isset($_GET['list'])) {
							?>
								&list=<?=$_GET['list']?>
							<?php
						}
					?>
						&sort_word=Name
					<?php
						if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Name" &&
							isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
							?>
								&sort_by=DESC
							<?php
						}
						
						else {
							?>
								&sort_by=ASC
							<?php
						}
					?>
				">
					Name
					<font class="sort-arrow">
						<?php
							if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Name" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
						?>
								 v
						<?php
							}
							else if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Name" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "DESC") {
						?>
								 ^
						<?php
							}
						?>
					</font>
				</a>
			</th>
			
			<th>
				<a class="sort" href="index.php?page=<?=$_GET['page']?>
					<?php
						if(isset($_GET['list'])) {
							?>
								&list=<?=$_GET['list']?>
							<?php
						}
					?>
						&sort_word=Code
					<?php
						if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Code" &&
							isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
							?>
								&sort_by=DESC
							<?php
						}
						
						else {
							?>
								&sort_by=ASC
							<?php
						}
					?>
				">
					Code
					<font class="sort-arrow">
						<?php
							if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Code" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
						?>
								 v
						<?php
							}
							else if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Code" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "DESC") {
						?>
								 ^
						<?php
							}
						?>
					</font>
				</a>
			</th>
			<th>
				<a class="sort" href="index.php?page=<?=$_GET['page']?>
					<?php
						if(isset($_GET['list'])) {
							?>
								&list=<?=$_GET['list']?>
							<?php
						}
					?>
						&sort_word=LengthFromStart
					<?php
					if(isset($_GET['sort_word']) && $_GET['sort_word'] == "LengthFromStart" &&
						isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
						?>
							&sort_by=DESC
						<?php
					}
					
					else {
						?>
							&sort_by=ASC
						<?php
					}
					?>
				">
					Length From Start
					<font class="sort-arrow">
						<?php
							if(isset($_GET['sort_word']) && $_GET['sort_word'] == "LengthFromStart" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
						?>
								 v
						<?php
							}
							else if(isset($_GET['sort_word']) && $_GET['sort_word'] == "LengthFromStart" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "DESC") {
						?>
								 ^
						<?php
							}
						?>
					</font>
				</a>
			</th>
			
			<th>
				<a class="bg-success text-white table-button" href="index.php?page=manage&station">+</a>
			</th>
		</tr>
	</thead>
	
	<tbody>
		<?php
			foreach(search_station($search, $sort) as $station) {		
				?>		
					<tr>
						<td><?=$station->ID?></td>
						<td><?=$station->Name?></td>
						<td><?=$station->Code?></td>
						<td><?=$station->LengthFromStart?></td>
						
						<td class="no-change">
							<a class="bg-primary text-white table-button" href="index.php?page=manage&station=<?=$station->ID?>">...</a>
							<a class="bg-danger text-white table-button" onclick="DeleteAlert(1, 'station', '<?=$station->ID?>');" href="#">X</a>
						</td>
					</tr>
				<?php
			}
		?>					
	</tbody>
</table>