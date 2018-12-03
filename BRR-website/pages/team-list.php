<?php
	$search = "";
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<h2 class="page-title">Team List</h2>
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
					<?php
						if(is_logged() == 1)
						{
					?>
						<th>
							<a class="bg-success text-white table-button" href="index.php?page=manage-team">+</a>
						</th>
					<?php
						}
					?>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach(search_team($search) as $team) {		
					?>		
						<tr class='clickable-row' data-href="index.php?page=team&team=<?=$team->ID ?>">
							<td><?=$team->Name ?></td>
							<?php
								if(is_logged() == 1)
								{
									?>
										<td class="no-change">
											<a class="bg-primary text-white table-button" href="index.php?page=manage-team&team=<?=$team->ID ?>">...</a>
											<a class="bg-danger text-white table-button" onclick="DeleteRaceAlert(<?= $team->ID ?>);" href="#">X</a>
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