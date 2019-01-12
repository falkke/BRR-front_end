<?php
	require 'functions/session.php';

	$search = $_SESSION['bbr']['search-team'];
	$sort = "";
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
		$_SESSION['bbr']['search-team'] = $search;
	}
	
	if(isset($_GET['sort_word']) && ($_GET['sort_word'] == "ID" || $_GET['sort_word'] == "Name")
	&& isset($_GET['sort_by']) && ($_GET['sort_by'] == "ASC" || $_GET['sort_by'] == "DESC")) {
		$sort = "ORDER BY ".$_GET['sort_word']." ".$_GET['sort_by'];
	}
	else if(isset($_GET['sort_word']) || isset($_GET['sort_by']))
	{
		if($dashboard == 1) {
			header('Location:index.php?page=dashboard&list=teams');
		}
		else {
			header('Location:index.php?page=teams');
		}
	}
?>

<h2 class="page-title">Team List</h2>
		
<?php
	if(isset($delete_team_error)) {
		?>
			<p class="alert alert-danger" role="alert"><?=$delete_team_error?></p>
		<?php
	}
	
	else if(isset($_GET['team-deleted']) && !empty($_GET['team-deleted']) && ($_GET['team-deleted'] == 1)) {
		?>
			<p class="alert alert-success" role="alert">The team has been succefully deleted.</p>
		<?php
	}
	
	else if(isset($_GET['team-added']) && !empty($_GET['team-added']) && ($_GET['team-added'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The team has been succefully added.</p>
		<?php
	}
	
	else if(isset($_GET['team-modified']) && !empty($_GET['team-modified']) && ($_GET['team-modified'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The team has been succefully modified.</p>
		<?php
	}
?>

<form method="post" class="form-inline my-2">
	<div class="input-group">
		<input class="form-control" type="text" style="text-align:right" placeholder="Search" name="search" id="search" aria-label="Search" 
			<?php
				if(isset($_SESSION['bbr']['search-team'])) {
					?>
						value="<?=$_SESSION['bbr']['search-team']?>"
					<?php
				}
			?>
		>    
		<span class="input-group-btn">
			<button class="btn  btn-default" type="submit" id="submit" name="submit">Search</button>
		</span>
	</div>
</form>

<table class="table table-bordered table-striped table-condensed">
	<thead>
		<tr>			
			<?php
				if(is_logged() == 1)
				{
					?>
						<th class="th-sm">
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
					<?php
				}
			?>
			
			<th class="th-sm">
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
			
			<?php
				if(is_logged() == 1) {
					?>
						<th>
							<a class="bg-success text-white table-button" href="index.php?page=manage&team">+</a>
						</th>
					<?php
				}
			?>
		</tr>
	</thead>
	
	<tbody>
		<?php
			foreach(search_team($search, $sort) as $team) {		
				?>		
					<tr class='clickable-row' data-href="index.php?page=team&team=<?=$team->ID?>">
						<?php
							if(is_logged() == 1)
							{
								?>
									<td><?=$team->ID?></td>
								<?php
							}
						?>
						
						<td><?=$team->Name?></td>
						
						<?php
							if(is_logged() == 1)
							{
								?>
									<td class="no-change">
										<a class="bg-primary text-white table-button" href="index.php?page=manage&team=<?=$team->ID?>">...</a>
										<a class="bg-danger text-white table-button" onclick="DeleteAlert(<?=$_SESSION['dashboard']?>, 'team', <?=$team->ID ?>);" href="#">X</a>
									</td>
								<?php
							}
						?>
					</tr>
				<?php
			}
		?>					
	</tbody>
</table>