<?php
	if(	(isset($_GET['race']) && !empty($_GET['race']))
	&&	(isset($_GET['remove']) && !empty($_GET['remove']))){
		$race_id = $_GET['race'];
		$remove = $_GET['remove'];
		
		$delete_race_error = "";
		
		if($remove == 1) {
			if(!does_race_exist($race_id)) {
				$delete_race_error = "This race does not exist.";
			}
			
			else if(!is_race_empty($race_id)) {
				$delete_race_error = "This race can not be deleted because it has some data.";
			}
			
			else {
				delete_race($race_id);
				header('Location:index.php?page=race-list&race-deleted=1');
			}
		}
	}
	
	$search = "";
	$sort = "";
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
	}
	
	if(isset($_GET['sort_word']) && isset($_GET['sort_by'])) {
		$sort = "ORDER BY ".$_GET['sort_word']." ".$_GET['sort_by'];
	}
?>

<h2 class="page-title">Race List</h2>

<?php
	if(isset($delete_race_error)) {
		?>
			<p class="alert alert-danger" role="alert"><?=$delete_race_error?></p>
		<?php
	}
	
	else if(isset($_GET['race-deleted']) && !empty($_GET['race-deleted']) && ($_GET['race-deleted'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The race has been succefully deleted.</p>
		<?php
	}
	
	else if(isset($_GET['race-added']) && !empty($_GET['race-added']) && ($_GET['race-added'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The race has been succefully added.</p>
		<?php
	}
	
	else if(isset($_GET['race-modified']) && !empty($_GET['race-modified']) && ($_GET['race-modified'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The race has been succefully modified.</p>
		<?php
	}
?>

<form method="post" class="form-inline my-2">
	<div class="input-group">
		<input class="form-control" type="text" style="text-align:right" placeholder="Search" name="search" id="search" aria-label="Search" 
		<?php
			if(isset($_POST['submit'])) {
				?>
					value="<?= $_POST['search']?>"
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
			<?php
				if(is_logged() == 1)
				{
			?>
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
			<?php
				}
			?>
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
				&sort_word=Name
				<?php
				if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Name" &&
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
				?>">Name</a>
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
				&sort_word=Date
				<?php
				if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Date" &&
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
				?>">Date</a>
			</th>
			<?php
				if(is_logged() == 1)
				{
			?>
				<th>
					<a class="bg-success text-white table-button" href="index.php?page=manage-race">+</a>
				</th>
			<?php
				}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach(search_race($search, $sort) as $race) {		
				?>		
				<tr class='clickable-row' data-href="index.php?page=race&race=<?=$race->ID ?>">
					<?php
						if(is_logged() == 1)
						{
					?>
						<td><?=$race->ID?></td>
					<?php
						}
					?>
					<td><?=$race->Name ?></td>
					<td><?=$race->Date ?></td>
					<?php
						if(is_logged() == 1)
						{
					?>
						<td class="no-change">
							<a class="bg-primary text-white table-button" href="index.php?page=manage-race&race=<?=$race->ID ?>">...</a>
							<a class="bg-danger text-white table-button" onclick="DeleteAlert('race', <?= $race->ID ?>);" href="#">X</a>
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