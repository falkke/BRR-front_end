<?php
	/* RACE FUNCTIONS */
	
	// Function that gets all races.
	function get_races() {
        global $db;
		
        $req = $db->query("SELECT * FROM race");
		
        $results = array();

        while($rows = $req->fetchObject()) {
            $results[] = $rows;
        }

        return $results;
    }
	
	// Function that gets a race with its ID.
	function get_race($race_id) {
        global $db;
		
        $r = array(
			'race_id' => $race_id
        );
				
        $sql = "SELECT * FROM race WHERE ID = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
        $result = $req->fetchObject();
		
        return $result;
    }
	
	// Function that gets all past races.
	function get_past_races() {
        global $db;
		
        $req = $db->query("SELECT * FROM race WHERE EndTime < NOW() ORDER BY EndTime ASC");
		
        $results = array();

        while($rows = $req->fetchObject()) {
            $results[] = $rows;
        }

        return $results;
    }
	
	// Function that gets all planned races.
	function get_planned_races() {
        global $db;
		
        $req = $db->query("
			SELECT *
			FROM race
			WHERE Date > CURDATE()
			UNION (
				SELECT r.ID AS ID, r.Name AS Name, r.Date AS Date, r.EndTime AS EndTime
				FROM race r, race_instance ri 
				WHERE r.ID = ri.Race AND r.Date = CURDATE() AND ri.StartTime > CURTIME() AND ri.StartTime = (
					SELECT Min(StartTime) 
					FROM race_instance 
					WHERE Race = r.ID
				)
			)
		");
		
        $results = array();

        while($rows = $req->fetchObject()) {
            $results[] = $rows;
        }

        return $results;
    }

	// Function that gets all current races.
	function get_current_races() {
		global $db;
		
		$req = $db->query("
			SELECT DISTINCT r.ID AS ID, r.Name AS Name, r.Date AS Date, r.EndTime AS EndTime
			FROM race r, race_instance ri 
			WHERE r.ID = ri.Race AND r.EndTime >= NOW() AND CONCAT(r.Date, ' ', ri.StartTime) <= NOW() AND ri.StartTime = (
				SELECT Min(StartTime) 
				FROM race_instance 
				WHERE Race = r.ID
			)
			UNION
			SELECT DISTINCT r.ID AS ID, r.Name AS Name, r.Date AS Date, r.EndTime AS EndTime
			FROM race r
			WHERE r.EndTime >= NOW() AND r.Date <= CURDATE() AND r.ID NOT IN (
				SELECT Race
				FROM race_instance 
				WHERE Race = r.ID
			)
		");
		
        $results = array();

        while($rows = $req->fetchObject()) {
            $results[] = $rows;
        }

        return $results;
    }
	
	function is_current_race($race_id) {
        global $db;
		
        $var = array(
			'race_id' => $race_id
        );
				
        $sql = "
			SELECT r.*
			FROM race r, race_instance ri 
			WHERE r.ID = :race_id AND r.EndTime >= NOW() AND CONCAT(r.Date, ' ', ri.StartTime) <= NOW() AND ri.StartTime IN (
				SELECT Min(StartTime) 
				FROM race_instance 
				WHERE Race = r.ID
			)
			UNION		
			SELECT *
			FROM race r
			WHERE r.EndTime >= CURDATE() AND r.Date <= CURDATE() AND r.ID NOT IN (
				SELECT Race
				FROM race_instance 
				WHERE Race = r.ID
			)
		";
        $req = $db->prepare($sql);
        $req->execute($var);
		
        $exist = $req->rowCount($sql);
		
        return($exist);
    }
	
	
	function is_displayed($race_id) {
		global $db;
		
        $var = array(
			'race_id' => $race_id
        );
				
        $sql = "
			SELECT *
			FROM race_display
			WHERE Race = :race_id
		";
        $req = $db->prepare($sql);
        $req->execute($var);
		
        $exist = $req->rowCount($sql);
		
        return($exist);
	}

	function get_display_race() {
		global $db;
		
        $req = $db->query("SELECT * FROM race_display");
	
        $result = $req->fetchObject();
		
        return $result;
	}		
	
	function display_race($race_id) {
        global $db;
		
        $var = array(
			'race_id' => $race_id
        );
		
        $req = $db->query("DELETE FROM race_display");
		
        $sql = "INSERT INTO race_display (Race) VALUES (:race_id)";
        $req = $db->prepare($sql);
        $req->execute($var);
	}
	
	function do_not_display_race() {
        global $db;
		
        $req = $db->query("DELETE FROM race_display");
	}
	
	// Function that adds a race in the database.
	function add_race($race_name, $race_start_date, $race_end_datetime) {
        global $db;
		
        $var = array(
			'race_name' => $race_name,
			'race_start_date' => $race_start_date,
			'race_end_datetime' => $race_end_datetime
        );
		
        $sql = "INSERT INTO race(Name, Date, EndTime) VALUES(:race_name, :race_start_date, :race_end_datetime)";
        $req = $db->prepare($sql);
        $req->execute($var);
    }		
	
	// Function that edits a race in the database.
	function edit_race($race_id, $race_name, $race_start_date, $race_end_datetime) {
        global $db;
		
        $var = array(
			'race_id' => $race_id,
			'race_name' => $race_name,
			'race_start_date' => $race_start_date,
			'race_end_datetime' => $race_end_datetime
        );
		
        $sql = "UPDATE race SET Name = :race_name, Date = :race_start_date, EndTime = :race_end_datetime WHERE ID = :race_id";
        $req = $db->prepare($sql);
        $req->execute($var);
    }
	
	// Function that deletes a race from the database.	
	function delete_race($race_id) {
        global $db;
		
        $var = array(
                'race_id' => $race_id
        );
		
        $sql = "DELETE FROM race WHERE ID = :race_id";
        $req = $db->prepare($sql);
        $req->execute($var);
    }
		
	// Function that returns 1 if the race has no link with other table and 0 if else.
	function is_race_empty($race_id) {
        global $db;
		
        $var = array(
            'race_id' => $race_id
        );
		
        $sql = "SELECT * 
				FROM race_runner AS rr, race_instance AS ri
				WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID";
        $req = $db->prepare($sql);
        $req->execute($var);
		
		$empty_race_runner = $req->rowCount($sql);
		
        $sql = "SELECT * 
				FROM timestamp AS t, race_instance AS ri
				WHERE ri.Race = :race_id AND t.RaceInstance = ri.ID";
        $req = $db->prepare($sql);
        $req->execute($var);
		
		$empty_timestamp = $req->rowCount($sql);
		
        $sql = "SELECT * FROM runner_units WHERE Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($var);
		
		$empty_runner_unit = $req->rowCount($sql);
		
        $sql = "SELECT * FROM race_instance WHERE Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($var);
		
		$empty_race_instance = $req->rowCount($sql);
		
		if(($empty_race_runner + $empty_timestamp + $empty_runner_unit + $empty_race_instance) == 0) {
			return 1;
		}
		
		else {
			return 0;
		}
    }
	
	// Function that returns 1 if the race exists and 0 if not.
	function does_race_exist($race_id) {
        global $db;

        $var = array(
            'race_id' => $race_id
        );

        $sql = "SELECT * FROM race WHERE ID = :race_id";
        $req = $db->prepare($sql);
        $req->execute($var);

        $exist = $req->rowCount($sql);
		
        return($exist);
    }

	// Function that gets races matching with a giving keyword.
	function search_race($keyword, $sort) {
		global $db;

        $e = array(
			'keyword' => "%{$keyword}%"
        );
		
        $sql = "SELECT * FROM race WHERE Name LIKE :keyword {$sort}";

        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	// Function that returns 1 if a race is planned and 0 if not.
	function is_planned_race($race_id) {
		 global $db;
		
        $var = array(
            'race_id' => $race_id
        );
		
        $sql = "
			SELECT *
			FROM race
			WHERE ID = :race_id AND Date > CURDATE()
			UNION (
				SELECT r.ID AS ID, r.Name AS Name, r.Date AS Date, r.EndTime AS EndTime
				FROM race r, race_instance ri 
				WHERE r.ID = :race_id AND r.ID = ri.Race AND r.Date = CURDATE() AND ri.StartTime > CURTIME() AND ri.StartTime IN (
					SELECT Min(StartTime) 
					FROM race_instance 
					WHERE Race = r.ID
				)
			)
		";
        $req = $db->prepare($sql);
        $req->execute($var);
		
		$result = $req->rowCount($sql);
		
		if($result != 0) {
			return 1;
		}
		
		else {
			return 0;
		}
	}
?>