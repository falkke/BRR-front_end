<?php
	$race = get_display_race();
	
	if($race != null) {
		$race_id = $race->Race;
		
		?>
			<main role="main" class="container no-gutters">
				<div class="row">
					<div class="nav-side-menu">
						<div class="brand">Category</div>
						<div class="menu-list">
							<ul id="menu-content" class="menu-content collapse out">
								<?php
									foreach(get_race_class_genders($race_id) as $class_gender) {
										?>							
											<li data-toggle="collapse" data-target="#<?=$class_gender ?>" class="collapsed">
												<a href="#"><?=$class_gender ?></a>
											</li>
											
											<ul class="sub-menu collapse" id="<?=$class_gender ?>">
												<?php
													foreach(get_race_class_gender_distances($race_id, $class_gender) as $class_gender_distance) {
														?>	
															<li><a href="index.php?page=race&race=<?= $race_id ?>&gender=<?=$class_gender ?>&distance=<?=$class_gender_distance ?>"><?=$class_gender_distance ?> miles</a></li>				
														<?php
													}
												?>	
											</ul>
										<?php
									}
								?>
							</ul>
						</div>
					</div>
				</div>
					
				<div class="section-template">
					<?php
						require "summary.php";
					
						if(is_logged()) {
							require "track.php";
							//require "map/map.php";	
						}
					?>
				</div>
			</main>
		<?php
	}
	
	else {
		?>
			<main role="main" class="container">
				<div class="starter-template">
					<h2 class="page-title">Black River Run</h2>
					<p class="lead">Welcome on the Black River Run website.</p>
				</div>
			</main>
		<?php
	}
?>