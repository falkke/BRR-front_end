<?php
	/*if(is_logged() == 0) {
        header('Location:index.php?page=home');
    }
	
	if(isset($_GET['race']) && !empty($_GET['race'])) {
		$id = $_GET['race'];
		
		if(race_exists($id)) {
			$race = get_race($id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(isset($_POST['submit'])) {
		$name = htmlspecialchars(trim($_POST['name']));
		$date = $_POST['date'];
		
		if(isset($_GET['race'])) {
			edit_race($id, $name, $date);
		}
		
		else {
			add_race($name, $date);
		}
		
        header('Location:index.php?page=home');
	}*/
	$search = "";
	
	if(isset($_GET['team']) && !empty($_GET['team'])) {
		$team_id = $_GET['team'];
		
		if(team_exists($team_id)) {
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
		<h2 class="page-title"></h2>
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