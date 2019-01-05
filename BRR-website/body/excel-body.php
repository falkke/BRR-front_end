<?php
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
	
	if(isset($_POST['submit'])) {
		if($_FILES['import_file']['tmp_name'] != "") {
			if(($handle = fopen($_FILES['import_file']['tmp_name'], "r")) !== FALSE) {
				$data = [];
				$row = 0;
				while (($data[$row] = fgetcsv($handle, 1000, ";")) !== FALSE) {
					$row = $row + 1;
				}
				$import_error = [];
				$import_error = verify_import($data, $row);
				if($import_error[0] == 0 && $import_error[1] == 0) {
					echo "<p class='alert alert-success' role='alert'>The import has been succefully done.</p>";
					import_data($data, $row, $race->ID);
				}
				else if($import_error[0] != 0 && $import_error[1] == 0) {
					echo "<p class='alert alert-danger' role='alert'>The import has not been done due to incorect file data. 
						(line ".$import_error[0].")</p>";	
				}
				else {
					echo "<p class='alert alert-danger' role='alert'>The import has not been done due to incorect file data. 
						(line ".$import_error[0].", column ".$import_error[1].")<br>";	
					if($import_error[1] == 1) {
						echo "NR-lapp must be a number.";	
					}
					else if($import_error[1] == 2) {
						echo "SI-NR identifier must be a number.";	
					}
					else if($import_error[1] == 3) {
						echo "Klass must follow this format 'GENDER DISTANCE Miles'<br>";
						echo "With GENDER = Man, Woman, Herrar, Damer ";
						echo "and DISTANCE = 20, 50, 100.";
					}
					else if($import_error[1] == 4) {
						echo "Starttid must follow this format 'HH:MM:SS'";	
					}
					else if($import_error[1] == 5) {
						echo "Namn must follow this format 'FIRSTNAME LASTNAME'";	
					}
					else if($import_error[1] == 7) {
						echo "Personnummer must follow this format 'YYMMDD'";	
					}
					echo "</p>";
				}
				fclose($handle);
			}
		}
	}
?>

<a class="link-title" href="index.php?page=race&race=<?=$race->ID?>?>"><h2 class="page-title followed-title"><?= $race->Name ?></h2></a>

<h3 class="text-left"> Export as .csv file</h3>
<a class="bg-primary text-white table-button" href="pages/export.php?race=<?=$race->ID?>">↓</a>
	
<h3 class="text-left"> Import a .csv file</h3>
<form method="post" class="form-horizontal form-add-edit" enctype="multipart/form-data">
	<input id="import_file" name="import_file" type="file" class="col-lg-6 d-inline-block form-control h-100">
	<button class="bg-primary text-white table-button" type="submit" name="submit">↑</button>
</form>