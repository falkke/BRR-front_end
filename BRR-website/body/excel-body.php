<?php
	$search = "";
	$sort = "";
	
	if(isset($_POST['submit'])) {
		$search = htmlspecialchars(trim($_POST['search']));
		$_SESSION['bbr']['search-runner'] = $search;
	}
		
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
?>

<a class="link-title" href="index.php?page=race&race=<?=$race->ID?>?>"><h2 class="page-title followed-title"><?= $race->Name ?></h2></a>

<h3 class="text-left"> Export as .csv file</h3>
<a class="bg-primary text-white table-button" href="pages/export.php?race=<?=$race->ID?>">↓</a>
<?php
	//export();
?>
<!--
	<button class="col-lg-3	pull-right btn btn-default" type="submit" name="submit">Submit</button>
-->
	
<h3 class="text-left"> Import a .csv file</h3>
---TO-DO---