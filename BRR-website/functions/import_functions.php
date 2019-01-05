<?php
	/* IMPORT FUNCTIONS */
	
	function clean($string) {
		return preg_replace('/[^A-Za-z0-9\-\;\,\?\*\%\@\$\!\(\)\#\=\&]/', '', $string);
	}
	
	function verify_import_header($header)
	{
		if(!(clean($header[0]) == "NR-lapp")) {
			return 1;
		}		
		
		if(!(clean($header[1]) == "SI-NR")) {
			return 2;
		}		
		
		if(!(clean($header[2]) == "Klass")) {
			return 3;
		}		
		
		if(!(clean($header[3]) == "Starttid")) {
			return 4;
		}		
		
		if(!(clean($header[4]) == "Namn")) {
			return 5;
		}		
		
		if(!(clean($header[5]) == "Klubb")) {
			return 6;
		}		
		
		if(!(clean($header[6]) == "Personnummer")) {
			return 7;
		}
		
		return 0;
	}
	
	function verify_import_row($data)
	{	
		if(!(is_numeric($data[0]))) {
			return 1;
		}
		
		if(!(is_numeric($data[1]) || $data[1] == "")) {
			return 2;
		}
		
		$Klass = [];
		$Klass = explode(' ', $data[2]);
		if(!(($Klass[0] == "Man" || $Klass[0] == "Woman" || $Klass[0] == "Herrar" || $Klass[0] == "Damer") 
			&& ($Klass[1] == "20" || $Klass[1] == "50" || $Klass[1] == "100"))) {
			return 3;
		}
		
		$Starttid = [];
		$Starttid = explode(':', $data[3]);
		$Starttid_count = count($Starttid);
		if(!($Starttid_count == 3
			&& is_numeric($Starttid[0]) && $Starttid[0] >= 0 && $Starttid[0] <= 23 
			&& is_numeric($Starttid[1]) && $Starttid[1] >= 0 && $Starttid[1] <= 59 
			&& is_numeric($Starttid[2]) && $Starttid[2] >= 0 && $Starttid[2] <= 59)) {
			return 4;
		}
		
		$Namn = [];
		$Namn = explode(' ', $data[4]);
		$Namn_count = count($Namn);
		if(!($Namn_count >= 2)) {
			return 5;
		}
		if(!(is_numeric($data[6]) && strlen($data[6]) == 6)) {
			return 7;
		}
		
		return 0;
	}
	
	function verify_import($data, $max_row)
	{
		$error_position = [];
		$error_position[0] = 1;
		$error_position[1] = 1;
		
		$row = 0;
		$header = $data[$row];
		if(count($header) != 7) {
			$error_position[1] = 0;
			return $error_position;
		}
		$error_position[1] = verify_import_header($header);
		if($error_position[1] == 0) {
			$row = $row + 1;
			while($row < $max_row) {
				$error_position[0] = $row + 1;
				$row_data = $data[$row];
				if(count($row_data) != 7) {
					$error_position[1] = 0;
					return $error_position;
				}
				$error_position[1] = verify_import_row($row_data);
				if($error_position[1] != 0) {
					return $error_position;
				}
				$row = $row + 1;
			}
			$error_position[0] = 0;
			$error_position[1] = 0;
			return $error_position;
		}
		return $error_position;
	}
	
	function import_data($data, $max_row, $race_id) {
		$row_data = [];
		$row = 1;
		
		while($row < $max_row) {
			$row_data = $data[$row];
			/* import data for that row */
			import_runner($row_data, $race_id);
			import_si_unit($row_data, $race_id);
			import_club($row_data, $race_id);
			import_race_instance($row_data, $race_id);
			import_runner_units($row_data, $race_id);
			import_race_runner($row_data, $race_id);
			$row = $row + 1;
		}
	}
	
	function import_runner($row_data, $race_id) {
		
	}
	
	function import_si_unit($row_data, $race_id) {
		
	}
	
	function import_club($row_data, $race_id) {
		
	}
	
	function import_race_instance($row_data, $race_id) {
		
	}
	
	function import_runner_units($row_data, $race_id) {
		
	}
	
	function import_race_runner($row_data, $race_id) {
		
	}
?>