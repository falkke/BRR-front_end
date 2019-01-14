<?php
	$search = "";
	
	if(isset($_GET['team']) && !empty($_GET['team'])) {
		$team_id = $_GET['team'];
		
		if(does_team_exist($team_id)) {
			$team = get_team($team_id);
		}
		
		else {
			header("Location:index.php?page=team-list");
		}
	}
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
	}
?>

<main role="main" class="container">
	<div class="starter-template">
		<h2 class="page-title"><?=$team->Name?></h2>
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
					<th>Race</th>
					<th>Member</th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach(get_team_races($_GET['team']) as $race) {	
					?>
					<tr>
						<td><?=$race->Name ?></td>
						<td>
							<?php
								foreach(search_team_member($search, $race->ID, $_GET['team']) as $team_member) {		
									echo $team_member->FirstName." ".$team_member->LastName.", ";
								}
							?>
						</td>
						</tr>
					<?php
				}
			?>					
			</tbody>
		</table>
	</div>
</main>	