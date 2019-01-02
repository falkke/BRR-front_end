<?php
	require 'functions/session.php';
	
	$search = $_SESSION['bbr']['search-runner'];
	$sort = "";
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
		$_SESSION['bbr']['search-runner'] = $search;
	}
	?>

<h2 class="page-title followed-title">Tracking Runner</h2>
		
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
		
<h3 class="text-left"> Running Area </h3>
<table class="table table-bordered table-striped table-condensed">           
	<thead>
		<tr>
			<th>Distance</th>
			<th>Bib</th>
			<th>Name</th>
			<th>Team</th>
			<th>Elaspsed Time</th>
			<th>Date & Time</th>
		</tr>
	</thead>
	
	<tbody>
		<?php
			foreach(runner_track($search, "run") as $race_runner) {	
			if($race_runner->Station != "b827eba42979") {
					$runner = get_runner($race_runner->Runner);
					$timestamp = get_last_timestamp($race_runner->Runner, $race_runner->Race);
					$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
					$elapsed = get_total_elapsed_time($race_runner->Runner, $race_runner->Race);
					?>	
						<tr>
							<td><?=$race_runner->Distance?></td>
							<td><?=$race_runner->Bib?></td>
							<td><?=$runner->FirstName." ".$runner->LastName?></td>
							<td><?=$team->Name?></td>
							<td>
								<?php
									if($elapsed != null)
									{
										echo $elapsed;
									}
									else
									{
										echo "-";
									}
								?>
							</td>
							<td>
								<?php
									if($timestamp != null)
									{
										echo $timestamp->Timestamp;
									}
									else
									{
										echo "-";
									}
								?>
							</td>
						</tr>
				<?php
				}
			}
		?>
	</tbody>
</table>
		
<h3 class="text-left"> Resting Area </h3>
<table class="table table-bordered table-striped table-condensed">           
	<thead>
		<tr>
			<th>Distance</th>
			<th>Bib</th>
			<th>Name</th>
			<th>Team</th>
			<th>Elaspsed Time</th>
			<th>Date & Time</th>
		</tr>
	</thead>
	
	<tbody>
		<?php
			foreach(runner_track($search, "rest") as $race_runner) {	
				if($race_runner->Station == "b827eba42979") {
					$runner = get_runner($race_runner->Runner);
					$timestamp = get_last_timestamp($race_runner->Runner, $race_runner->Race);
					$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
					$elapsed = get_total_elapsed_time($race_runner->Runner, $race_runner->Race);
					?>	
						<tr>
							<td><?=$race_runner->Distance?></td>
							<td><?=$race_runner->Bib?></td>
							<td><?=$runner->FirstName." ".$runner->LastName?></td>
							<td><?=$team->Name?></td>
							<td>
								<?php
									if($elapsed != null)
									{
										echo $elapsed;
									}
									else
									{
										echo "-";
									}
								?>
							</td>
							<td>
								<?php
									if($timestamp != null)
									{
										echo $timestamp->Timestamp;
									}
									else
									{
										echo "-";
									}
								?>
							</td>
						</tr>
				<?php
				}
			}
		?>
	</tbody>
</table>
