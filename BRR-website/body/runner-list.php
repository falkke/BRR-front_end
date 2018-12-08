<?php
	if(	(isset($_GET['runner']) && !empty($_GET['runner']))
	&&	(isset($_GET['remove']) && !empty($_GET['remove']))){
		$runner_id = $_GET['runner'];
		$remove = $_GET['remove'];
		
		$delete_runner_error = "";
		
		if($remove == 1) {
			if(!does_runner_exist($runner_id)) {
				$delete_runner_error = "This runner does not exist.";
			}
			
			else if(!is_runner_empty($runner_id)) {
				$delete_runner_error = "This runner can not be deleted because he/she has some data.";
			}
			
			else {
				delete_runner($runner_id);
				header('Location:index.php?page=runner-list&runner-deleted=1');
			}
		}
	}

	$search = "";
	$sort = "ORDER BY ID ASC";
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
	}
	
	if(isset($_GET['sort_word']) && isset($_GET['sort_by'])) { 
		$sort = "ORDER BY ID DESC";
		$sort = "ORDER BY ".$_GET['sort_word']." ".$_GET['sort_by'];
	}

?>

<h2 class="page-title">Runner List</h2>

<?php
	if(isset($delete_runner_error)) {
		?>
			<p class="alert alert-danger" role="alert"><?=$delete_runner_error?></p>
		<?php
	}
	
	else if(isset($_GET['runner-deleted']) && !empty($_GET['runner-deleted']) && ($_GET['runner-deleted'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The runner has been succefully deleted.</p>
		<?php
	}
	
	else if(isset($_GET['runner-added']) && !empty($_GET['runner-added']) && ($_GET['runner-added'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The runner has been succefully added.</p>
		<?php
	}
	
	else if(isset($_GET['runner-modified']) && !empty($_GET['runner-modified']) && ($_GET['runner-modified'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The runner has been succefully modified.</p>
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
			<?php
				if(isset($_GET['sort_word']) && isset($_GET['sort_by'])
					&& $_GET['sort_word'] == "ID" && $_GET['sort_by'] == "ASC")
				{
			?>
					<th><a class="sort" href="index.php?page=runners&sort_word=ID&sort_by=DESC">ID</a></th>
			<?php
				}
				else
				{
			?>
					<th><a class="sort" href="index.php?page=runners&sort_word=ID&sort_by=ASC">ID</a></th>
			<?php
				}
			?>
			<?php
				}
			?>
			<?php
				if(isset($_GET['sort_word']) && isset($_GET['sort_by'])
					&& $_GET['sort_word'] == "LastName" && $_GET['sort_by'] == "ASC")
				{
			?>
					<th><a class="sort" href="index.php?page=runners&sort_word=LastName&sort_by=DESC">Name</a></th>
			<?php
				}
				else
				{
			?>
					<th><a class="sort" href="index.php?page=runners&sort_word=LastName&sort_by=ASC">Name</a></th>
			<?php
				}
			?>
			<?php
				if(isset($_GET['sort_word']) && isset($_GET['sort_by'])
					&& $_GET['sort_word'] == "DateOfBirth" && $_GET['sort_by'] == "ASC")
				{
			?>
					<th><a class="sort" href="index.php?page=runners&sort_word=DateOfBirth&sort_by=DESC">Date Of Birth</a></th>
			<?php
				}
				else
				{
			?>
					<th><a class="sort" href="index.php?page=runners&sort_word=DateOfBirth&sort_by=ASC">Date Of Birth</a></th>
			<?php
				}
			?>
			<?php
				if(isset($_GET['sort_word']) && isset($_GET['sort_by'])
					&& $_GET['sort_word'] == "Gender" && $_GET['sort_by'] == "ASC")
				{
			?>
					<th><a class="sort" href="index.php?page=runners&sort_word=Gender&sort_by=DESC">Gender</a></th>
			<?php
				}
				else
				{
			?>
					<th><a class="sort" href="index.php?page=runners&sort_word=Gender&sort_by=ASC">Gender</a></th>
			<?php
				}
			?>
			<?php
				if(is_logged() == 1)
				{
			?>
				<th>
					<a class="bg-success text-white table-button" href="index.php?page=manage-runner">+</a>
				</th>
			<?php
				}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach(search_runner($search, $sort) as $runner) {		
				?>	
					<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$runner->ID ?>">
						<?php
							if(is_logged() == 1)
							{
						?>
							<td><?=$runner->ID?></td>
						<?php
							}
						?>
						<td><?=$runner->FirstName ?> <?=$runner->LastName ?></td>
						<td><?=$runner->DateOfBirth ?></td>
						<td><?=$runner->Gender ?></td>
						<?php
							if(is_logged() == 1)
							{
						?>
							<td class='no-change'>
								<a class="bg-primary text-white table-button" href="index.php?page=manage-runner&runner=<?=$runner->ID ?>">...</a>
								<a class="bg-danger text-white table-button" onclick="DeleteAlert('runner', <?= $runner->ID ?>);" href="#">X</a>
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
