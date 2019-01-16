<?php
	require 'functions/session.php';

	$search = $_SESSION['bbr']['search-runner'];
	$sort = "";
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
		$_SESSION['bbr']['search-runner'] = $search;
	}
	
	if(isset($_GET['sort_word']) && ($_GET['sort_word'] == "ID" || $_GET['sort_word'] == "LastName" || $_GET['sort_word'] == "DateOfBirth" || $_GET['sort_word'] == "Gender")
	&& isset($_GET['sort_by']) && ($_GET['sort_by'] == "ASC" || $_GET['sort_by'] == "DESC")) {
		$sort = "ORDER BY ".$_GET['sort_word']." ".$_GET['sort_by'];
	}
	else if(isset($_GET['sort_word']) || isset($_GET['sort_by']))
	{
		if($dashboard == 1) {
			header('Location:index.php?page=dashboard&list=runners');
		}
		else {
			header('Location:index.php?page=runners');
		}
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
				if(isset($_SESSION['bbr']['search-runner'])) {
					?>
						value="<?=$_SESSION['bbr']['search-runner']?>"
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
			
			<th>
				<a class="sort" href="index.php?page=<?=$_GET['page']?>
					<?php
						if(isset($_GET['list'])) {
							?>
								&list=<?=$_GET['list']?>
							<?php
						}
					?>
						&sort_word=LastName
					<?php
						if(isset($_GET['sort_word']) && $_GET['sort_word'] == "LastName" &&
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
							if(isset($_GET['sort_word']) && $_GET['sort_word'] == "LastName" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
						?>
								 v
						<?php
							}
							else if(isset($_GET['sort_word']) && $_GET['sort_word'] == "LastName" &&
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
						&sort_word=DateOfBirth
					<?php
						if(isset($_GET['sort_word']) && $_GET['sort_word'] == "DateOfBirth" &&
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
					Date Of Birth
					<font class="sort-arrow">
						<?php
							if(isset($_GET['sort_word']) && $_GET['sort_word'] == "DateOfBirth" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
						?>
								 v
						<?php
							}
							else if(isset($_GET['sort_word']) && $_GET['sort_word'] == "DateOfBirth" &&
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
						&sort_word=Gender
					<?php
						if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Gender" &&
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
					Gender
					<font class="sort-arrow">
						<?php
							if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Gender" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
						?>
								 v
						<?php
							}
							else if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Gender" &&
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
				if(is_logged() == 1)
				{
					?>
						<th>
							<a class="bg-success text-white table-button" href="index.php?page=manage&runner">+</a>
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
							if(is_logged() == 1) {
								?>
									<td><?=$runner->ID?></td>
								<?php
							}
						?>
						
						<td><?=$runner->FirstName ?> <?=$runner->LastName ?></td>
						<td><?=$runner->DateOfBirth ?></td>
						<td><?=$runner->Gender ?></td>
						
						<?php
							if(is_logged() == 1) {
								?>
									<td class='no-change'>
										<a class="bg-primary text-white table-button" href="index.php?page=manage&runner=<?=$runner->ID?>">...</a>
										<a class="bg-danger text-white table-button" onclick="DeleteAlert(<?=$_SESSION['dashboard']?>, 'runner', <?= $runner->ID ?>);" href="#">X</a>
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