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
		
        $req = $db->query("SELECT * FROM race WHERE EndTime < CURTIME() ORDER BY EndTime ASC");
		
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
				WHERE r.ID = ri.Race AND r.Date = CURDATE() AND ri.StartTime > CURTIME() AND ri.StartTime IN (
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
			SELECT r.ID AS ID, r.Name AS Name, r.Date AS Date, r.EndTime AS EndTime
			FROM race r, race_instance ri 
			WHERE r.ID = ri.Race AND r.EndTime > CURDATE() AND r.Date <= CURDATE() AND ri.StartTime < CURTIME() AND ri.StartTime IN (
				SELECT Min(StartTime) 
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
                'race_end_datetime' => $race_end_datetime,
        );
		
        $sql = "UPDATE race SET Name = :race_name, Date = :race_start_date, EndTime = :race_end_datetime WHERE ID = :race_id";
        $req = $db->prepare($sql);
        $req->execute($var);
    }
	
	// Function that deletes a race from the database.	
	function delete_race($race_id) {
        global $db;
		
        $r = array(
                'race_id' => $race_id
        );
		
        $sql = "DELETE FROM race WHERE ID = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
		
		
		
	function is_race_empty($race_id) {
        global $db;
		
        $r = array(
                'race_id' => $race_id
        );
		
        $sql = "SELECT * FROM race_runner WHERE Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_race_runner = $req->rowCount($sql);
		
        $sql = "SELECT * FROM timestamp WHERE Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_timestamp = $req->rowCount($sql);
		
        $sql = "SELECT * FROM runner_units WHERE Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_runner_unit = $req->rowCount($sql);
		
        $sql = "SELECT * FROM race_station WHERE Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_race_station = $req->rowCount($sql);
		
		if(($empty_race_runner + $empty_timestamp + $empty_runner_unit + $empty_race_station) == 0) {
			return 1;
		}
		
		else {
			return 0;
		}
    }
	
	function does_race_exist($race_id) {
        global $db;

        $e = array(
            'race_id' => $race_id
        );

        $sql = "SELECT * FROM race WHERE ID = :race_id";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
    }
	
	function search_race($keyword, $sort) 
	{
		global $db;

        $req = $db->query("SELECT * FROM race WHERE Name LIKE '%{$keyword}%' {$sort}");

		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	function is_planned_race($race_id) {
		 global $db;
		
        $r = array(
                'race_id' => $race_id
        );
		
        $sql = "SELECT * FROM timestamp WHERE Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_timestamp = $req->rowCount($sql);
		
		if($empty_timestamp == 0) {
			return 1;
		}
		
		else {
			return 0;
		}
	}
?>