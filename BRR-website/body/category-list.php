<?php
	require 'functions/session.php';

	$search = $_SESSION['bbr']['search-category'];
	$sort = "";
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
		$_SESSION['bbr']['search-category'] = $search;
	}
	
	if(isset($_GET['sort_word']) && ($_GET['sort_word'] == "ID" || $_GET['sort_word'] == "Gender" || $_GET['sort_word'] == "Distance")
	&& isset($_GET['sort_by']) && ($_GET['sort_by'] == "ASC" || $_GET['sort_by'] == "DESC")) {
		$sort = "ORDER BY ".$_GET['sort_word']." ".$_GET['sort_by'];
	}
	else if(isset($_GET['sort_word']) || isset($_GET['sort_by']))
	{
		header('Location:index.php?page=dashboard&list=categories');
	}
?>

<h2 class="page-title">Category List</h2>
		
<?php
	if(isset($delete_category_error)) {
		?>
			<p class="alert alert-danger" role="alert"><?=$delete_category_error?></p>
		<?php
	}
	
	else if(isset($_GET['category-deleted']) && !empty($_GET['category-deleted']) && ($_GET['category-deleted'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The category has been succefully deleted.</p>
		<?php
	}
	
	else if(isset($_GET['category-added']) && !empty($_GET['category-added']) && ($_GET['category-added'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The category has been succefully added.</p>
		<?php
	}
	
	else if(isset($_GET['category-modified']) && !empty($_GET['category-modified']) && ($_GET['category-modified'] == 1)) {				
		?>
			<p class="alert alert-success" role="alert">The category has been succefully modified.</p>
		<?php
	}
?>

<form method="post" class="form-inline my-2">
	<div class="input-group">
		<input class="form-control" type="text" style="text-align:right" placeholder="Search" name="search" id="search" aria-label="Search" <?php
			if(isset($_SESSION['bbr']['search-category'])) {
				?>
					value="<?=$_SESSION['bbr']['search-category']?>"
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
			
			<th>
				<a class="sort" href="index.php?page=<?=$_GET['page']?>
					<?php
						if(isset($_GET['list'])) {
							?>
								&list=<?=$_GET['list']?>
							<?php
						}
					?>
						&sort_word=Distance
					<?php
						if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Distance" &&
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
					Distance
					<font class="sort-arrow">
						<?php
							if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Distance" &&
								isset($_GET['sort_by']) && $_GET['sort_by'] == "ASC") {
						?>
								 v
						<?php
							}
							else if(isset($_GET['sort_word']) && $_GET['sort_word'] == "Distance" &&
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
							<a class="bg-success text-white table-button" href="index.php?page=manage&category">+</a>
						</th>
					<?php
				}
			?>
		</tr>
	</thead>
	
	<tbody>
		<?php
			foreach(search_category($search, $sort) as $category) {		
				?>		
					<tr>
						<td><?=$category->ID?></td>
						<td><?=$category->Gender?></td>
						<td><?=$category->Distance?></td>
						
						<?php
							if(is_logged() == 1)
							{
								?>
									<td class="no-change">
										<a class="bg-primary text-white table-button" href="index.php?page=manage&category=<?=$category->ID?>">...</a>
										<a class="bg-danger text-white table-button" onclick="DeleteAlert(1, 'category', <?=$category->ID ?>);" href="#">X</a>
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