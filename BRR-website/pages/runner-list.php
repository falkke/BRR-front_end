<?php
	/*if(isset($_GET['race']) && !empty($_GET['race'])) {
		$id = $_GET['race'];
		
		if(race_exists($id)) {
			$race = get_race($id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	*/
	$search = "";
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<h2 class="page-title">Runner List</h2>
		<form method="post" class="form-inline my-2">
			<div class="input-group">
				<input class="form-control" type="text" style="text-align:right" placeholder="Search" name="search" id="search" aria-label="Search" <?php
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
					<th>Name</th>
					<th>Date Of Birth</th>
					<th>Gender</th>
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
				foreach(search_runner($search) as $runner) {		
					?>		
					<tr class='clickable-row' data-href="index.php?page=runner&runner=<?=$runner->ID ?> ?>">
						<td><?=$runner->FirstName ?> <?=$runner->LastName ?></td>
						<td><?=$runner->DateOfBirth ?></td>
						<td><?=$runner->Gender ?></td>
						<?php
							if(is_logged() == 1)
							{
						?>
							<td>
								<a class="bg-primary text-white table-button" href="index.php?page=manage-runner&runner=<?=$runner->ID ?>">...</a>
								<a class="bg-danger text-white table-button" href="index.php?page=home&runner=<?=$runner->ID ?>&remove=1">X</a>
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
	</div>
</main>