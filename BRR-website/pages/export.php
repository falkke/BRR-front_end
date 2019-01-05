<?php
	require "../includes.php";
	
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

	header('Content-Encoding: UTF-8');
	header('Content-Type: text/csv; charset=UTF-8');
	header('Content-Disposition: attachment; filename="' . str_replace(" ", "_", $race->Name) . '.csv"');
	ob_end_clean();
	
	$user_CSV[0] = array('NR-lapp', 'SI-NR', 'Klass', 'Starttid', 'Namn', 'Klubb', 'Personnummer');
	$i = 1;
	foreach (export_race_runner($race->ID) as $race_runner) {
		$runner = get_runner($race_runner->Runner);
		$team = get_race_runner_team($race_runner->Runner, $race_runner->Race);
		$runner_unit = get_runner_unit($race_runner->Runner, $race_runner->Race);
		$class = get_class_by_id($race_runner->Class);
		$personnummer = get_personnummer_from_dob($runner->DateOfBirth);
		if($runner_unit == null) {
			$runner_unit = "";
		}
		else {
			$runner_unit = $runner_unit->SI_unit;
		}
		$user_CSV[$i] = array($race_runner->Bib, $runner_unit, $class->Gender." ".$class->Distance." Miles", $race_runner->StartTime, $runner->FirstName." ".$runner->LastName, $team->Name, $personnummer);
		$i = $i + 1;
	}
	$fp = fopen('php://output', 'wb');
	fputs($fp, "\xEF\xBB\xBF");
	foreach ($user_CSV as $line) {
		// in many countries (including France) separator is ";"
		fputcsv($fp, $line, ';');
	}
	fclose($fp);
?>