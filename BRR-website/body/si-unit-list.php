<?php
	require 'functions/session.php';

	/*if(	(isset($_GET['team']) && !empty($_GET['team']))
	&&	(isset($_GET['remove']) && !empty($_GET['remove']))){
		$team_id = $_GET['team'];
		$remove = $_GET['remove'];
		
		$delete_team_error = "";
		
		if($remove == 1) {
			if(!does_team_exist($team_id)) {
				$delete_team_error = "This team does not exist.";
			}
			
			else if(!is_team_empty($team_id)) {
				$delete_team_error = "This team can not be deleted because it has some data.";
			}
			
			else {
				delete_team($team_id);
				header('Location:index.php?page=team-list&team-deleted=1');
			}
		}
	}*/

	$search = $_SESSION['bbr']['search-si-unit'];
	$sort = "";
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
		$_SESSION['bbr']['search-si-unit'] = $search;
	}
	
	if(isset($_GET['sort_word']) && isset($_GET['sort_by'])) {
		$sort = "ORDER BY ".$_GET['sort_word']." ".$_GET['sort_by'];
	}
?>

<h2 class="page-title">SI-Unit List</h2>
		
<?php
	/*if(isset($delete_team_error)) {
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
	}*/
?>

<form method="post" class="form-inline my-2">
	<div class="input-group">
		<input class="form-control" type="text" style="text-align:right" placeholder="Search" name="search" id="search" aria-label="Search" <?php
			if(isset($_SESSION['bbr']['search-si-unit'])) {
				?>
					value="<?=$_SESSION['bbr']['search-si-unit']?>"
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
				if(isset($_GET['list']))
				{
				?>
					&list=<?=$_GET['list']?>
				<?php
				}
				?>
				&sort_word=ID
				<?php
				if(isset($_GET['sort_word']) && $_GET['sort_word'] == "ID" &&
					isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC")
				{
				?>
					&sort_by=DESC
				<?php
				}
				else
				{
				?>
					&sort_by=ASC
				<?php
				}
				?>">ID</a>
			</th>
			<th>
				<a class="sort" href="index.php?page=<?=$_GET['page']?>
				<?php
				if(isset($_GET['list']))
				{
				?>
					&list=<?=$_GET['list']?>
				<?php
				}
				?>
				&sort_word=Status
				<?php
				if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Status" &&
					isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC")
				{
				?>
					&sort_by=DESC
				<?php
				}
				else
				{
				?>
					&sort_by=ASC
				<?php
				}
				?>">Status</a>
			</th>
			<th>
				<a class="bg-success text-white table-button" href="index.php?page=manage-si-unit">+</a>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php
		foreach(search_si_unit($search, $sort) as $si_unit) {		
			?>		
				<tr>
					<td><?=$si_unit->ID?></td>
					<td><?=$si_unit->Status?></td>
					<td class="no-change">
						<a class="bg-primary text-white table-button" href="index.php?page=manage-si-unit&si-unit=<?=$si_unit->ID?>">...</a>
						<a class="bg-danger text-white table-button" onclick="DeleteAlert('si-unit', <?= $si_unit->ID ?>);" href="#">X</a>
					</td>
				</tr>
			<?php
		}
	?>					
	</tbody>
</table>