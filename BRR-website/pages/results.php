<?php
	$_SESSION['dashboard'] = 0;
	
	if(isset($_GET['race']) && !empty($_GET['race'])) 
	{
		$id = $_GET['race'];
		
		if(does_race_exist($id)) {
			$race = get_race($id);
		}
		
		else {
			header("Location:index.php?page=home");
		}
	}
	
	if(!isset($_GET['race'])) {
		$race = get_active_race();
	}
	
	$search = "";
?>

<main role="main" class="container no-gutters">
	<div class="row">
		<div class="nav-side-menu">
			<div class="brand">Category</div>
			<span class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></span>
			
			<div class="menu-list">
				<ul id="menu-content" class="menu-content collapse out">
				<?php
					foreach(get_race_class_genders($id) as $class_gender) {
						if(not_empty_race_gender($id, $class_gender)) {
						?>							
							<li data-toggle="collapse" data-target="#<?=$class_gender ?>" class="collapsed">
								<a href="#"><?=$class_gender ?></a>
							</li>
							<ul class="sub-menu collapse" id="<?=$class_gender ?>">
								<?php
									foreach(get_race_class_gender_distances($id, $class_gender) as $class_gender_distance) {
										if(not_empty_race_gender_distances($id, $class_gender, $class_gender_distance)) {
										?>	
											<li><a href="index.php?page=race&race=<?= $id ?>&gender=<?=$class_gender ?>&distance=<?=$class_gender_distance ?>"><?=$class_gender_distance ?> miles</a></li>				
										<?php
										}
									}
								?>	
							</ul>
					
						<?php
						}
					}
				?>
				</ul>
			</div>
		</div>
	</div>
		

	<div class="section-template">
		<?php
			require 'body/results-body.php';
		?>
	</div>
</main>	