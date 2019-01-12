<?php
	require 'functions/session.php';
	
	$search = $_SESSION['bbr']['search-si-unit'];
	$sort = "";
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
		$_SESSION['bbr']['search-si-unit'] = $search;
	}
	
	if(isset($_GET['sort_word']) && ($_GET['sort_word'] == "ID" || $_GET['sort_word'] == "Status" || $_GET['sort_word'] == "Holder")
	&& isset($_GET['sort_by']) && ($_GET['sort_by'] == "ASC" || $_GET['sort_by'] == "DESC")) {
		$sort = "ORDER BY ".$_GET['sort_word']." ".$_GET['sort_by'];
	}
	
	else if(isset($_GET['sort_word']) || isset($_GET['sort_by'])) {
		header('Location:index.php?page=dashboard&list=si-units');
	}
?>

<h2 class="page-title">SI-Unit List</h2>
		
<?php
	if(isset($delete_si_unit_error)) {
		?>
			<p class="alert alert-danger" role="alert"><?=$delete_si_unit_error?></p>
		<?php
	}
	
	else if(isset($_GET['si-unit-deleted']) && !empty($_GET['si-unit-deleted']) && ($_GET['si-unit-deleted'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The SI-unit has been succefully deleted.</p>
		<?php
	}
	
	else if(isset($_GET['si-unit-added']) && !empty($_GET['si-unit-added']) && ($_GET['si-unit-added'] == 1)) {	
		?>
			<p class="alert alert-success" role="alert">The SI-unit has been succefully added.</p>
		<?php
	}
	
	else if(isset($_GET['si-unit-modified']) && !empty($_GET['si-unit-modified']) && ($_GET['si-unit-modified'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The SI-unit has been succefully returned.</p>
		<?php
	}
	
	else if(isset($_GET['si-unit-modified']) && ($_GET['si-unit-modified'] == 0)
			&& isset($_GET['si-unit'])) {	
		edit_si_unit($_GET['si-unit'], "Returned");
		delete_all_runner_si_unit($_GET['si-unit']);
		//header('Location:index.php?page=dashboard&list=si-units&si-unit-modified=1');
	}
?>

<form method="post" class="form-inline my-2">
	<div class="input-group">
		<input class="form-control" type="text" style="text-align:right" placeholder="Search" name="search" id="search" aria-label="Search" 
			<?php
				if(isset($_SESSION['bbr']['search-si-unit'])) {
					?>
						value="<?=$_SESSION['bbr']['search-si-unit']?>"
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
						&sort_word=Status
					<?php
						if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Status" &&
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
					Status
					<font class="sort-arrow">
						<?php
							if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Status" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
						?>
								 v
						<?php
							}
							else if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Status" &&
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
						&sort_word=Holder
					<?php
						if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Holder" &&
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
					Holder
					<font class="sort-arrow">
						<?php
							if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Holder" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
						?>
								 v
						<?php
							}
							else if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Holder" &&
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
				<a class="bg-success text-white table-button" href="index.php?page=manage&si-unit">+</a>
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
						<td><?=$si_unit->Holder?></td>
						
						<td class="no-change">
							<a title="Return SI-unit" class="bg-primary text-white table-button" href="index.php?page=dashboard&list=si-units&si-unit=<?=$si_unit->ID?>&si-unit-modified=0">&#9745</a>
							<a class="bg-danger text-white table-button" onclick="DeleteAlert(1, 'si-unit', <?=$si_unit->ID?>);" href="#">X</a>
						</td>
					</tr>
				<?php
			}
		?>					
	</tbody>
</table>