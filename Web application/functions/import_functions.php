<?php
	/* IMPORT FUNCTIONS */
	
	function clean($string) {
		return preg_replace('/[^A-Za-z0-9\-\;\,\?\*\%\@\$\!\(\)\#\=\&]/', '', $string);
	}
	
	function validateDate($Personnummer)
	{
		$current_year = substr(date("Y"), 2, 2);
		if(substr($Personnummer, 0, 2) < $current_year) {
			$date = "20".substr($Personnummer, 0, 2)."-".substr($Personnummer, 2, 2)."-".substr($Personnummer, 4, 2);
		}
		else {
			$date = "19".substr($Personnummer, 0, 2)."-".substr($Personnummer, 2, 2)."-".substr($Personnummer, 4, 2);
		}
		$tempDate = explode('-', $date);
		return checkdate($tempDate[1], $tempDate[2], $tempDate[0]);
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
		$date_verification = validateDate($data[6]);
		if(!($date_verification)) {
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
			$runner_id = import_runner($row_data);
			$si_unit_id = import_si_unit($row_data);
			$club_id = import_club($row_data);
			import_runner_units($race_id, $runner_id, $si_unit_id);
			$race_instance_id = import_race_instance($row_data, $race_id);
			import_race_runner($race_instance_id, $runner_id, $row_data[0], $club_id);
			$row = $row + 1;
		}
	}
	
	function import_runner($data) {
        global $db;
		
		$Namn = [];
		$Namn = explode(' ', $data[4]);
		$firstname = $Namn[0];
		$lastname = $Namn[1];
		
		$Klass = [];
		$Klass = explode(' ', $data[2]);
		if($Klass[0] == "Man" || $Klass[0] == "Herrar") {
			$gender = "Man";
		}
		else {
			$gender = "Woman";
		}
		
		$Personnummer = $data[6];
		$current_year = substr(date("Y"), 2, 2);
		if(substr($Personnummer, 0, 2) < $current_year) {
			$dateofbirth = "20".substr($Personnummer, 0, 2)."-".substr($Personnummer, 2, 2)."-".substr($Personnummer, 4, 2);
		}
		else {
			$dateofbirth = "19".substr($Personnummer, 0, 2)."-".substr($Personnummer, 2, 2)."-".substr($Personnummer, 4, 2);
		}

        $e = array(
            'firstname' => $firstname,
			'lastname' => $lastname,
			'dateofbirth' => $dateofbirth,
			'gender' => $gender
        );

        $sql = "SELECT * FROM runner WHERE FirstName = :firstname AND LastName = :lastname AND DateOfBirth = :dateofbirth AND Gender = :gender";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        if(!($exist)) {
			add_runner($firstname, $lastname, $dateofbirth, $gender);
			$e = array(
				'firstname' => $firstname,
				'lastname' => $lastname,
				'dateofbirth' => $dateofbirth,
				'gender' => $gender
			);
	
			$sql = "SELECT * FROM runner WHERE FirstName = :firstname AND LastName = :lastname AND DateOfBirth = :dateofbirth AND Gender = :gender";
			$req = $db->prepare($sql);
			$req->execute($e);
			
		}
		$runner_id = $req->fetch()['ID'];
		
		return $runner_id;
	}
	
	function import_si_unit($data) {
		global $db;
		
		$siunit = $data[1];
		
        $e = array(
            'siunit' => $siunit
        );

        $sql = "SELECT * FROM si_unit WHERE ID = :siunit";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        if(!($exist)) {
			add_si_unit($siunit, "Active");
			$e = array(
				'siunit' => $siunit
			);

			$sql = "SELECT * FROM si_unit WHERE ID = :siunit";
			$req = $db->prepare($sql);
			$req->execute($e);
		}		
		$si_unit_id = $req->fetch()['ID'];
		
		return $si_unit_id;
	}
	
	function import_club($data) {
		global $db;
		
		$club = $data[5];
		
        $e = array(
            'club' => $club
        );

        $sql = "SELECT * FROM club WHERE Name = :club";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        if(!($exist)) {
			add_team($club);
			$e = array(
				'club' => $club
			);

			$sql = "SELECT * FROM club WHERE Name = :club";
			$req = $db->prepare($sql);
			$req->execute($e);
		}
		$club_id = $req->fetch()['ID'];
		
		return $club_id;
	}
	
	function import_race_instance($data, $race_id) {
		global $db;
		
		$Klass = [];
		$Klass = explode(' ', $data[2]);
		if($Klass[0] == "Man" || $Klass[0] == "Herrar") {
			$gender = "Man";
		}
		else {
			$gender = "Woman";
		}
		$distance = $Klass[1];	
		
        $e = array(
            'gender' => $gender,
			'distance' => $distance
        );

        $sql = "SELECT * FROM class WHERE Gender = :gender AND Distance = :distance";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$class_id = $req->fetch()['ID'];
		
        $e = array(
			'race_id' => $race_id,
            'class_id' => $class_id
        );

        $sql = "SELECT * FROM race_instance WHERE Race = :race_id AND Class = :class_id";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        if(!($exist)) {
			$start_time = $data[3];
			add_race_instance($race_id, $class_id, $start_time);
			$e = array(
				'race_id' => $race_id,
				'class_id' => $class_id
			);

			$sql = "SELECT * FROM race_instance WHERE Race = :race_id AND Class = :class_id";
			$req = $db->prepare($sql);
			$req->execute($e);
		}
		$race_instance_id = $req->fetch()['ID'];
		
		return $race_instance_id;
	}
	
	function import_runner_units($race_id, $runner_id, $si_unit_id) {
		global $db;
		
        $e = array(
			'runner_id' => $runner_id,
			'si_unit_id' => $si_unit_id,
			'race_id' => $race_id
        );

        $sql = "SELECT * FROM runner_units WHERE Runner = :runner_id AND Race = :race_id AND SI_unit = :si_unit_id";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        if(!($exist)) {
			add_race_si_unit_runner($race_id, $runner_id, $si_unit_id);
		}
	}
	
	function import_race_runner($race_instance_id, $runner_id, $bib, $club_id) {
		global $db;
		
		$race_instance = get_race_instance_by_id($race_instance_id);
		$class = get_category($race_instance->Class);
		
        $e = array(
			'runner_id' => $runner_id,
			'race_id' => $race_instance->Race
        );

        $sql = "SELECT * 
				FROM race_runner AS rr, race_instance AS ri 
				WHERE rr.Runner = :runner_id AND ri.Race = :race_id AND rr.RaceInstance = ri.ID";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        if(!($exist)) {
			add_race_runner($runner_id, $bib, $club_id, $race_instance_id);
		}
	}
?>