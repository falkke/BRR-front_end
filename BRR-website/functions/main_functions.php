<?php
	/* RACE RUNNER FUNCTIONS*/
	
	function get_race_instance($race_id, $gender, $distance) {
		global $db;
		
        $e = array(
            'race_id' => $race_id, 
			'gender' => $gender, 
			'distance' => $distance
        );

        $sql = "SELECT ri.ID AS ID, ri.Race AS Race, ri.Class AS Class, ri.StartTime AS StartTime 
				FROM race_instance AS ri, class AS c 
				WHERE c.Gender = :gender AND c.Distance = :distance AND ri.Class = c.ID AND ri.Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($e);

		$result = $req->fetchObject();
		
        return $result;
	}
	
	function get_race_runner($runner_id, $race_id) {
		global $db;
		
        $e = array(
            'runner_id' => $runner_id,
            'race_id' => $race_id
        );

		
        $sql = "SELECT ri.ID AS RaceInstance, ri.Race AS Race, ri.Class AS CLass, ri.StartTime AS StartTime, 
				rr.Runner AS Runner, rr.Bib AS Bib, rr.Club AS Club, rr.Status AS Status, rr.TotalTime AS TotalTime, rr.Place AS Place
				FROM race_runner AS rr, race_instance AS ri
				WHERE rr.Runner = :runner_id AND ri.Race = :race_id AND rr.RaceInstance = ri.ID";
        $req = $db->prepare($sql);
        $req->execute($e);

		$result = $req->fetchObject();
		
        return $result;
	}
	
	function exist_race_runners($instance, $keyword, $status) {
		global $db;

		if($status != NULL) {
			$e = array(
				'race_id' => $instance->Race,
				'status' => $status,
                'category' => $instance->Class,
                'keyword' => "%{$keyword}%"
			);
			$sql = "SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, rr.Club, rr.Place, rr.TotalTime
					FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.Class = :category AND rr.Status = :status
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE :keyword
					UNION
					SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, '' AS Club, rr.Place, rr.TotalTime
					FROM race_runner AS rr, runner AS r, race_instance AS ri
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND r.ID = rr.Runner AND ri.Class = :category AND rr.Status = :status AND rr.Club is null
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', rr.Bib) LIKE :keyword";
		}
		else
		{
			$e = array(
				'race_id' => $instance->Race,
				'category' => $instance->Class,
                'keyword' => "%{$keyword}%"
			);
			
			$sql = "SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, rr.Club, rr.Place, rr.TotalTime
					FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.Class = :category AND rr.Status is null
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE :keyword
					UNION
					SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, '' AS Club, rr.Place, rr.TotalTime
					FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.Class = :category AND rr.Status is null AND rr.Club is null
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', rr.Bib) LIKE :keyword";
		}
		
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
	}
	
	function get_race_runners($race_id, $keyword) {
		global $db;
		
        $e = array(
            'race_id' => $race_id,
			'keyword' => "%{$keyword}%"
        );
			
		$sql = "
			SELECT rr.* 
			FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri 
			WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND c.ID = rr.Club AND r.ID = rr.Runner AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE :keyword 
			ORDER BY rr.Place ASC
        ";
							
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_race_runners_by_status($race_id, $keyword, $status) {
	global $db;
		
        $e = array(
            'race_id' => $race_id,
            'status' => $status,
			'keyword' => "%{$keyword}%"
        );
		
		if($status == "DNF")
		{
			$sql = "SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, rr.Club, rr.Place, rr.TotalTime, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, t.Timestamp
					FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri, timestamp AS t, station AS s
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status = :status
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE :keyword
					AND t.Runner = rr.Runner AND t.RaceInstance = ri.ID AND  t.Timestamp = 
					(SELECT MAX(t.timestamp) FROM timestamp AS t, station AS s WHERE t.Runner = rr.Runner AND t.RaceInstance = ri.ID AND s.Code <> 99 AND t.Station = s.ID)
					AND  t.Station = s.ID
					UNION
					SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, '' AS Club, rr.Place, rr.TotalTime, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, t.Timestamp
					FROM race_runner AS rr, runner AS r, race_instance AS ri, timestamp AS t, station AS s
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status = :status AND rr.Club IS NULL
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', rr.Bib) LIKE :keyword
					AND t.Runner = rr.Runner AND t.RaceInstance = ri.ID AND  t.Timestamp = 
					(SELECT MAX(t.timestamp) FROM timestamp AS t, station AS s WHERE t.Runner = rr.Runner AND t.RaceInstance = ri.ID AND s.Code <> 99 AND t.Station = s.ID)
					AND  t.Station = s.ID
					ORDER BY Place ASC";
		}
		else if($status == "Running")
		{
			$sql = "SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, rr.Club, rr.Place, rr.TotalTime, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, t.Timestamp
					FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri, timestamp AS t, station AS s
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status = :status
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE :keyword
					AND t.Runner = rr.Runner AND t.RaceInstance = ri.ID AND  t.Timestamp = 
					(SELECT MAX(t.timestamp) FROM timestamp AS t WHERE t.Runner = rr.Runner AND t.RaceInstance = ri.ID)
					AND  t.Station = s.ID
					UNION
					SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, '-' AS Status, rr.Club, '-' AS Place, rr.TotalTime, '-' AS Distance, '-' AS Timestamp
					FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status is null
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE :keyword
					UNION
					SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, '' AS Club, rr.Place, rr.TotalTime, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, t.Timestamp
					FROM race_runner AS rr, runner AS r, race_instance AS ri, timestamp AS t, station AS s
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status = :status AND rr.Club IS NULL
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', rr.Bib) LIKE :keyword
					AND t.Runner = rr.Runner AND t.RaceInstance = ri.ID AND  t.Timestamp = 
					(SELECT MAX(t.timestamp) FROM timestamp AS t WHERE t.Runner = rr.Runner AND t.RaceInstance = ri.ID)
					AND  t.Station = s.ID
					UNION
					SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, '-' AS Status, '' AS Club, '-' AS Place, rr.TotalTime, '-' AS Distance, '-' AS Timestamp
					FROM race_runner AS rr, runner AS r, race_instance AS ri
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status is null AND rr.Club IS NULL
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', rr.Bib) LIKE :keyword
					ORDER BY Place ASC";
		}
		
		else
		{
			$sql = "SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, rr.Club, rr.Place, rr.TotalTime
					FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status = :status
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE :keyword
					UNION
					SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, '' AS Club, rr.Place, rr.TotalTime
					FROM race_runner AS rr, runner AS r, race_instance AS ri
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status = :status AND rr.Club IS NULL
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', rr.Bib) LIKE :keyword
					ORDER BY Place ASC";
		}
							
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_race_runners_that_stoped($race_id, $stop_distance) {
		global $db;
		
        $e = array(
            'race_id' => $race_id,
			'stop_distance' => $stop_distance
        );
		
		$sql = "SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, rr.Club, rr.Place, rr.TotalTime
				FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri, timestamp AS t, station AS s
				WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status = 'DNF'
				AND t.RaceInstance = ri.ID AND t.Runner = rr.Runner AND s.ID = t.Station AND (((t.Lap - 1) * 10) + s.LengthFromStart) = :stop_distance AND t.Timestamp = (
					SELECT MAX(t2.Timestamp) 
					FROM timestamp t2, race_runner rr2
					WHERE t2.Runner = r.ID AND rr2.RaceInstance = ri.ID AND t2.RaceInstance = rr2.RaceInstance AND rr2.Runner = r.ID AND t2.Runner = rr2.Runner
					AND t2.Station <> 'b827eb2d0304'
				)
				UNION
				SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, '' AS Club, rr.Place, rr.TotalTime
				FROM race_runner AS rr, runner AS r, race_instance AS ri, timestamp AS t, station AS s
				WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status = 'DNF' AND rr.Club IS NULL
				AND t.RaceInstance = ri.ID AND t.Runner = rr.Runner AND s.ID = t.Station AND (((t.Lap - 1) * 10) + s.LengthFromStart) = :stop_distance AND t.Timestamp = (
					SELECT MAX(t2.Timestamp) 
					FROM timestamp t2, race_runner rr2
					WHERE t2.Runner = r.ID AND rr2.RaceInstance = ri.ID AND t2.RaceInstance = rr2.RaceInstance AND rr2.Runner = r.ID AND t2.Runner = rr2.Runner
					AND t2.Station <> 'b827eb2d0304'
				)
				ORDER BY Place ASC";
							
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function add_race_runner($runner_id, $bib, $team_id, $race_instance_id) {
        global $db;
		
        $e = array(
                'runner_id' => $runner_id,
                'bib' => $bib,
                'team_id' => $team_id,
                'race_instance_id' => $race_instance_id
        );
		
        $sql = "INSERT INTO race_runner(RaceInstance, Runner, Bib, Club) VALUES(:race_instance_id, :runner_id, :bib, :team_id)";
        $req = $db->prepare($sql);
        $req->execute($e);
    }	
	
	function add_race_runner_no_team($runner_id, $bib, $race_instance_id) {
        global $db;
		
        $e = array(
                'runner_id' => $runner_id,
                'bib' => $bib,
                'race_instance_id' => $race_instance_id
        );
		
        $sql = "INSERT INTO race_runner(RaceInstance, Runner, Club, Bib) VALUES(:race_instance_id, :runner_id, NULL, :bib)";
        $req = $db->prepare($sql);
        $req->execute($e);
    }	

	function add_race_si_unit_runner($race_id, $runner_id, $si_unit_id) {
        global $db;
		
		$e = array(
            'runner_id' => $runner_id,
            'si_unit_id' => $si_unit_id,
            'race_id' => $race_id
        );
		
        $sql = "INSERT INTO runner_units(Runner, SI_unit, Race) VALUES(:runner_id, :si_unit_id, :race_id)";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$e = array(
            'si_unit_id' => $si_unit_id
        );
		
        $sql = "UPDATE si_unit SET Status = 'Active' WHERE ID = :si_unit_id";
        $req = $db->prepare($sql);
        $req->execute($e);
    }		
		
	function get_race_not_runners($race_id) {
		global $db;
		
        $e = array(
            'race_id' => $race_id
        );

		$sql = "SELECT * 
				FROM runner 
				WHERE ID NOT IN 
						(SELECT rr.Runner 
						FROM race_runner AS rr, race_instance AS ri
						WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID)";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
		
	function get_race_not_si_unit($race_id) {
		global $db;
		
        $e = array(
            'race_id' => $race_id
        );

		$sql = "SELECT * FROM si_unit WHERE ID NOT IN (SELECT SI_unit FROM runner_units WHERE Race = :race_id)";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_last_race_runner($runner_id) 
	{
		global $db;
		
        $e = array(
            'runner_id' => $runner_id
        );

        $sql = "SELECT ri.ID AS RaceInstance, ri.Race AS Race, ri.Class AS CLass, ri.StartTime AS StartTime, 
				rr.Runner AS Runner, rr.Bib AS Bib, rr.Club AS Club, rr.Status AS Status, rr.TotalTime AS TotalTime, rr.Place AS Place
				FROM race_runner AS rr, race_instance AS ri
				WHERE rr.Runner = :runner_id AND rr.RaceInstance = ri.ID";
        $req = $db->prepare($sql);
        $req->execute($e);

		$result = $req->fetchObject();
		
        return $result;
	}
	
	function get_races_runner($runner_id) 
	{
		global $db;
		
        $e = array(
            'runner_id' => $runner_id
        );

        $sql = "SELECT * FROM race_runner WHERE Runner = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($e);

		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_race_runner_class($runner_id, $race_id) 
	{
		global $db;
		
        $e = array(
            'runner_id' => $runner_id,
            'race_id' => $race_id
        );

        $sql = "SELECT ri.Class AS Class
				FROM race_runner AS rr, race_instance AS ri
				WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND rr.Runner = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$class_id = $req->fetch()['Class'];
		
		$e = array(
            'class_id' => $class_id
        );

        $sql = "SELECT * FROM class WHERE ID = :class_id";
        $req = $db->prepare($sql);
        $req->execute($e);

		$result = $req->fetchObject();
		
        return $result;
	}	
	
	function get_race_runner_team($runner_id, $race_id) 
	{
		global $db;
		
        $e = array(
            'runner_id' => $runner_id,
            'race_id' => $race_id
        );

        $sql = "SELECT rr.Club AS Club FROM 
				race_runner AS rr, race_instance AS ri
				WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND Runner = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$team_id = $req->fetch()['Club'];
		
		$e = array(
            'team_id' => $team_id
        );

        $sql = "SELECT * FROM club WHERE ID = :team_id";
        $req = $db->prepare($sql);
        $req->execute($e);

		if($req->rowCount($sql) != 0) {
			$result = $req->fetchObject();
		}
		else {
			$result = (object) array('ID' => '-1', 'Name' => '-');
		}
		
        return $result;
	}
	
	function get_race_runner_units($race_id) {
		global $db;
		
        $e = array(
            'race_id' => $race_id
        );

        $sql = "SELECT su.ID AS ID, r.ID AS Runner, su.Status AS Status, r.FirstName AS FirstName, r.LastName AS LastName FROM si_unit su, runner_units ru, runner r WHERE su.ID = ru.SI_unit AND ru.Runner = r.ID AND ru.Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_race_class_genders($race_id) 
	{
		global $db;
		
        $e = array(
            'race_id' => $race_id
        );

        $sql = "SELECT DISTINCT Gender FROM class WHERE ID IN (SELECT DISTINCT Class FROM race_instance WHERE Race = :race_id)";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetch()['Gender']) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}	
	
	function get_race_class_gender_distances($race_id, $class_gender) 
	{
		global $db;
		
        $e = array(
            'race_id' => $race_id,
            'class_gender' => $class_gender
        );

        $sql = "SELECT DISTINCT Distance FROM class WHERE Gender = :class_gender AND ID IN (SELECT DISTINCT Class FROM race_instance WHERE Race = :race_id)";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetch()['Distance']) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function delete_race_runner($race_id, $runner_id) {
        global $db;
		
        $r = array(
            'race_id' => $race_id,
            'runner_id' => $runner_id
        );
				
        $sql = "SELECT ri.ID AS ID
				FROM race_runner AS rr, race_instance AS ri 
				WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND rr.Runner = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$race_instance_id = $req->fetch()['ID'];
		
        $r = array(
            'race_instance_id' => $race_instance_id,
            'runner_id' => $runner_id
        );
		
        $sql = "DELETE FROM race_runner WHERE RaceInstance = :race_instance_id AND Runner = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
	
	function delete_runner_si_unit($runner_id, $si_unit_id, $race_id) {
        global $db;
		
        $r = array(
            'race_id' => $race_id,
            'runner_id' => $runner_id,
			'si_unit_id' => $si_unit_id
        );
		
        $sql = "DELETE FROM runner_units WHERE Race = :race_id AND Runner = :runner_id AND SI_unit = :si_unit_id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
	
	function delete_all_runner_si_unit($si_unit_id) {
        global $db;
		
        $r = array(
			'si_unit_id' => $si_unit_id
        );
		
        $sql = "DELETE FROM runner_units WHERE SI_unit = :si_unit_id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
	
	function has_si_unit($race_id, $runner_id) {
		global $db;
		
        $r = array(
            'race_id' => $race_id,
            'runner_id' => $runner_id
        );
		
		$sql = "SELECT * FROM runner_units WHERE Runner = :runner_id AND Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);

        $exist = $req->rowCount($sql);
		
        return($exist);
	}
	
	/* TIMESTAMP FUNCTIONS */
	
	function get_runner_timestamps($runner_id, $instance_id) 
	{
		global $db;
		
        $e = array(
            'runner_id' => $runner_id,
            'instance_id' => $instance_id
        );

        $sql = "SELECT * FROM timestamp WHERE Runner = :runner_id AND RaceInstance = :instance_id ORDER BY Timestamp DESC";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}	
	
	function get_timestamps($instance_id, $station_id, $lap) 
	{
		global $db;
		
        $e = array(
            'instance_id' => $instance_id,
            'station_id' => $station_id,
            'lap' => $lap
        );

        $sql = "SELECT * FROM timestamp WHERE RaceInstance = :instance_id AND Station = :station_id AND Lap = :lap";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_number_laps($runner_id, $instance_id, $timestamp, $station_id)
	{
		global $db;
	
		$var = array(
			'runner_id' => $runner_id,
			'instance_id' => $instance_id,
			'timestamp' => $timestamp
		);
			
		$sql = "
			SELECT * 
			FROM timestamp 
			WHERE Runner = :runner_id AND RaceInstance = :instance_id AND Timestamp IN (
				SELECT MAX(Timestamp) AS Max 
				FROM timestamp 
				WHERE Runner = :runner_id AND RaceInstance = :instance_id AND Timestamp < :timestamp
			)
		";
		
		$req = $db->prepare($sql);
		$req->execute($var);
		$result = $req->fetchObject();
			
		if(empty($result)) {
			return 1;
		}
		
		else if((get_station($result->Station)->Code) >= (get_station($station_id)->Code)) {
			return $result->Lap + 1;
		}

		else {
			return $result->Lap;
		}
	}
		
	function set_places($instance_id, $station_id, $lap) {
		global $db;
		
		foreach(get_timestamps($instance_id, $station_id, $lap) as $timestamp) {
			$e = array(
				'instance_id' => $instance_id,
				'station_id' => $station_id,
				'lap' => $lap,
				'timestamp' => $timestamp->Timestamp
			);
			
			$sql = "SELECT COUNT(Timestamp) AS Count FROM timestamp WHERE RaceInstance = :instance_id AND Station = :station_id AND Lap = :lap AND Timestamp < :timestamp";
			$req = $db->prepare($sql);
			$req->execute($e);
			
			$place = $req->fetch()['Count'] + 1;
			
			$e = array(
				'instance_id' => $instance_id,
				'station_id' => $station_id,
				'lap' => $lap,
				'timestamp' => $timestamp->Timestamp,
				'place' => $place
			);
			
			$sql = "UPDATE timestamp SET Place = :place WHERE RaceInstance = :instance_id AND Station = :station_id AND Lap = :lap AND  Timestamp = :timestamp";
			$req = $db->prepare($sql);
			$req->execute($e);
		}
	}
	
	function get_time_behind($race_runner)
	{
		global $db;
		
        $e = array(
            'race_id' => $race_runner->Race,
            'class_id' => $race_runner->Class
        );
		
        $sql = "SELECT * FROM race_instance WHERE Race = :race_id AND Class = :class_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$instance_id = $req->fetch()['ID'];
		
        $e = array(
            'runner_id' => $race_runner->Runner,
            'instance_id' => $instance_id
        );

        $sql = "SELECT TIMEDIFF(r1.TotalTime, r2.TotalTime) AS TimeBehind 
				FROM race_instance AS ri, race_runner AS r1, race_runner AS r2 
				WHERE r1.Runner = :runner_id AND ri.ID = :instance_id AND r1.RaceInstance = ri.ID AND r2.Place = 1 AND r2.RaceInstance = ri.ID";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetch()['TimeBehind'];;

		return $result;		
	}
	
	function get_time_behind_at_timestamp($runner_id, $instance_id, $lap, $station_id)
	{
		global $db;
		
        $e = array(
            'nb_lap' => $lap,
            'station_id' => $station_id,
            'runner_id' => $runner_id,
            'instance_id' => $instance_id
        );
		$sql = "SELECT TIMEDIFF(t1.timestamp, t2.timestamp) AS TimeBehind 
				FROM timestamp AS t1, timestamp AS t2 
				WHERE t1.Runner = :runner_id AND t1.RaceInstance = :instance_id AND t1.lap = :nb_lap AND t1.station = :station_id 
				AND t2.Place = 1 AND t2.RaceInstance = :instance_id AND t2.lap = :nb_lap AND t2.station = :station_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetch()['TimeBehind'];

		return $result;		
	}

	function get_total_elapsed_time($runner_id, $race_instance)
	{
		global $db;
		
        $e = array(
            'runner_id' => $runner_id,
            'race_instance' => $race_instance
        );
		$sql = "SELECT TIMEDIFF(t1.timestamp, t2.timestamp) AS TimeBehind 
				FROM timestamp AS t1, timestamp AS t2 
				WHERE t1.Runner = :runner_id AND t1.RaceInstance = :race_instance AND t2.Runner = :runner_id AND t2.RaceInstance = :race_instance AND t2.station = 0 
				AND t1.Timestamp =
					(SELECT MAX(t3.timestamp) 
					FROM timestamp AS t3, station AS s 
					WHERE t3.Runner = :runner_id AND t3.RaceInstance = :race_instance AND s.Code <> 99 AND t3.Station = s.ID)";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetch()['TimeBehind'];

		return $result;		
	}	

	
	function get_elapsed_time_at_timestamp($runner_id, $instance_id, $station_id, $timestamp)
	{
		global $db;
		
        $e = array(
            'station_id' => $station_id,
            'runner_id' => $runner_id,
            'instance_id' => $instance_id,
			'timestamp' => $timestamp
        );
		$sql = "SELECT TIMEDIFF(t1.timestamp, t2.timestamp) AS TimeBehind 
				FROM timestamp AS t1, timestamp AS t2 
				WHERE t1.Runner = :runner_id AND t1.RaceInstance = :instance_id AND t1.Timestamp = :timestamp AND t1.station = :station_id 
				AND t2.Runner = :runner_id AND t2.RaceInstance = :instance_id AND t2.station = 0";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetch()['TimeBehind'];

		return $result;		
	}	
	
	function does_timestamp_exist($timestamp, $runner_id) {
        global $db;

        $e = array(
            'timestamp' => $timestamp,
            'runner_id' => $runner_id
        );

        $sql = "SELECT * FROM timestamp WHERE Runner = :runner_id AND Timestamp = :timestamp";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
    }
	
	function get_timestamp($timestamp_time, $runner_id) {
        global $db;
		
		$e = array(
            'timestamp_time' => $timestamp_time,
            'runner_id' => $runner_id
        );
		$sql = "SELECT Runner, Race, Place, Station, DATE_FORMAT(timestamp, '%Y-%m-%d') AS Date, DATE_FORMAT(timestamp, '%H:%i:%s') AS Time FROM timestamp WHERE Runner = :runner_id AND Timestamp = :timestamp_time";
        $req = $db->prepare($sql);
        $req->execute($e);
		
        $result = $req->fetchObject();
		
        return $result;
    }
	
	
	/* TEAM FUNCTIONS */	
	
	function get_teams() 
	{
		global $db;

        $req = $db->query("SELECT * FROM club");

		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	function search_team($keyword, $sort) 
	{
		global $db;

        $e = array(
			'keyword' => "%{$keyword}%"
        );
		
        $sql = "SELECT * FROM club WHERE Name LIKE :keyword {$sort}";

        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function search_team_member($keyword, $race_id, $team_id) 
	{		
		global $db;
		
        $e = array(
            'race_id' => $race_id,
            'team_id' => $team_id,
            'keyword' => "%{$keyword}%"
        );

		$sql = "SELECT * 
				FROM runner 
				WHERE CONCAT(FirstName, ' ', LastName) LIKE :keyword AND ID IN 
						(SELECT rr.Runner 
						FROM race_runner AS rr, race_instance AS ri
						WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND rr.Club = :team_id)";
        $req = $db->prepare($sql);
        $req->execute($e);

		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	function get_team_races($team_id) {
        global $db;
		
		$e = array(
            'team_id' => $team_id
        );
		
		$sql = "SELECT * 
				FROM race 
				WHERE ID IN 
					(SELECT ri.Race 
					FROM race_runner AS rr, race_instance AS ri 
					WHERE rr.Club = :team_id AND rr.RaceInstance = ri.ID)";
        $req = $db->prepare($sql);
        $req->execute($e);

		$results = array();
		
        while($rows = $req->fetchObject()) {
            $results[] = $rows;
        }

        return $results;
    }
	
	
	function get_team($team_id) {
        global $db;
		
		$e = array(
            'team_id' => $team_id
        );
		
		$sql = "SELECT * FROM club WHERE ID = :team_id";
        $req = $db->prepare($sql);
        $req->execute($e);

		$result = $req->fetchObject();
		
        return $result;
    }
	
			
	function does_team_exist($team_id) {
        global $db;
		
        $u = array(
                'team_id' => $team_id
        );

        $sql = 'SELECT * FROM club WHERE ID = :team_id';
        $req = $db->prepare($sql);
        $req->execute($u);

        $exist = $req->rowCount($sql);
		
        return($exist);
    }
	
		
	function add_team($team_name) {
        global $db;
		
        $e = array(
                'team_name' => $team_name
        );
		
        $sql = "INSERT INTO club(Name) VALUES(:team_name)";
        $req = $db->prepare($sql);
        $req->execute($e);
    }
	
	function edit_team($team_id, $team_name) {
        global $db;
		
        $e = array(
                'team_id' => $team_id,
                'team_name' => $team_name
        );
		
		
        $sql = "UPDATE club SET Name = :team_name WHERE ID = :team_id";
        $req = $db->prepare($sql);
        $req->execute($e);
    }
	
	function delete_team($team_id) {
        global $db;
		
        $r = array(
                'team_id' => $team_id
        );
		
        $sql = "DELETE FROM club WHERE ID = :team_id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
		
	function is_team_empty($team_id) {
        global $db;
		
        $r = array(
                'team_id' => $team_id
        );
		
        $sql = "SELECT * FROM race_runner WHERE Club = :team_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_race_runner = $req->rowCount($sql);
		
		if($empty_race_runner == 0) {
			return 1;
		}
		
		else {
			return 0;
		}
    }
	
	
	/* STATION FUNCTIONS */
	
	function search_station($keyword, $sort) 
	{
		global $db;

        $e = array(
			'keyword' => "%{$keyword}%"
        );
		
        $sql = "SELECT * FROM station WHERE Name LIKE :keyword {$sort}";

        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_stations() {
		global $db;

        $req = $db->query("SELECT * FROM station");

		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
    }
	
	function get_station($station_id) {
        global $db;
		
        $r = array(
            'station_id' => $station_id
        );
				
        $sql = "SELECT * FROM station WHERE ID = :station_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
        $result = $req->fetchObject();
		
        return $result;
    }
		
	function add_station($id, $name, $code, $length_from_start) {
        global $db;
		
        $r = array(
				'id' => $id,
                'name' => $name,
                'code' => $code,
                'length_from_start' => $length_from_start
        );
		
        $sql = "INSERT INTO station(ID, Name, Code, LengthFromStart, LastID) VALUES(:id, :name, :code, :length_from_start, 0)";
        $req = $db->prepare($sql);
        $req->execute($r);
    }		
	
	function edit_station($id, $name, $code, $length_from_start) {
        global $db;
		
        $r = array(
                'id' => $id,
                'name' => $name,
                'code' => $code,
                'length_from_start' => $length_from_start
        );
		
        $sql = "UPDATE station SET Name = :name, Code = :code, LengthFromStart = :length_from_start WHERE ID = :id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
		
	function delete_station($station_id) {
        global $db;
		
        $r = array(
                'station_id' => $station_id
        );
		
        $sql = "DELETE FROM station WHERE ID = :station_id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
		
	function is_station_empty($station_id) {
        global $db;
		
        $r = array(
                'station_id' => $station_id
        );
		
        $sql = "SELECT * FROM timestamp WHERE Station = :station_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_timestamp = $req->rowCount($sql);
		
		if(($empty_timestamp) == 0) {
			return 1;
		}
		
		else {
			return 0;
		}
    }
	
	function does_station_exist($station_id) {
        global $db;

        $e = array(
            'station_id' => $station_id
        );

        $sql = "SELECT * FROM station WHERE ID = :station_id";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
    }
	
	
	/* CATEGORY FUNCTIONS */
	
	function search_category($keyword, $sort) 
	{
		global $db;

        $e = array(
			'keyword' => "%{$keyword}%"
        );
		
        $sql = "SELECT * FROM class WHERE Gender LIKE :keyword {$sort}";

        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_categories_distances() {
		global $db;

        $req = $db->query("SELECT DISTINCT Distance FROM class");

		$results = array();
		
        while($rows = $req->fetch()['Distance']) 
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	function get_category($category_id) {
        global $db;
		
        $r = array(
            'category_id' => $category_id
        );
				
        $sql = "SELECT * FROM class WHERE ID = :category_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
        $result = $req->fetchObject();
		
        return $result;
    }
	
	function get_categories() {
        global $db;

        $req = $db->query("SELECT * FROM class");
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
    }
		
	function add_category($gender, $distance) {
        global $db;
		
        $r = array(
			'gender' => $gender,
			'distance' => $distance
        );
		
        $sql = "INSERT INTO class(Gender, Distance) VALUES(:gender, :distance)";
        $req = $db->prepare($sql);
        $req->execute($r);
    }		
	
	function edit_category($category_id, $gender, $distance) {
        global $db;
		
        $r = array(
			'category_id' => $category_id,
			'gender' => $gender,
			'distance' => $distance
        );
		
        $sql = "UPDATE class SET Gender = :gender, Distance = :distance WHERE ID = :category_id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
		
	function delete_category($category_id) {
        global $db;
		
        $r = array(
            'category_id' => $category_id
        );
		
        $sql = "DELETE FROM class WHERE ID = :category_id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
		
	function is_category_empty($category_id) {
        global $db;
		
        $r = array(
            'category_id' => $category_id
        );
		
        $sql = "SELECT * FROM race_instance WHERE Class = :category_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_race_runner = $req->rowCount($sql);

		if($empty_race_runner == 0) {
			return 1;
		}
		
		else {
			return 0;
		}
    }
	
	function does_category_exist($category_id) {
        global $db;

        $e = array(
            'category_id' => $category_id
        );

        $sql = "SELECT * FROM class WHERE ID = :category_id";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
    }
	
	/* SI-UNIT FUNCTIONS */
	
	function search_si_unit($keyword, $sort) {
		global $db;

        $e = array(
			'keyword' => "%{$keyword}%"
        );
		
        $sql = "SELECT su.ID, su.Status, '-' AS Holder
				FROM si_unit AS su
				WHERE su.Status = 'Returned' AND CONCAT(su.Status, ' ', su.ID) LIKE :keyword
				UNION
				SELECT su.ID, su.Status, CONCAT(r.FirstName, ' ', r.LastName) AS Holder 
				FROM si_unit AS su, runner_units AS ru, runner AS r 
				WHERE ru.SI_unit = su.ID AND r.ID = ru.Runner AND CONCAT(r.FirstName, ' ', r.LastName, ' ', su.Status, ' ', su.ID) LIKE :keyword
				{$sort}";

        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_si_unit($si_unit_id) {
        global $db;

        $r = array(
                'si_unit_id' => $si_unit_id
        );
		
        $sql = "SELECT * FROM si_unit WHERE ID = :si_unit_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
        $result = $req->fetchObject();
		
        return $result;
	}	
	
	function get_status() {
        global $db;

        $req = $db->query("SELECT * FROM si_unit_status");
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	function add_si_unit($si_unit_id, $status) {
        global $db;
		
        $r = array(
                'si_unit_id' => $si_unit_id,
                'status' => $status
        );
		
        $sql = "INSERT INTO si_unit(ID, Status) VALUES(:si_unit_id, :status)";
        $req = $db->prepare($sql);
        $req->execute($r);
    }		
	
	function edit_si_unit($si_unit_id, $status) {
        global $db;
		
        $r = array(
                'si_unit_id' => $si_unit_id,
                'status' => $status
        );
		
        $sql = "UPDATE si_unit SET Status = :status WHERE ID = :si_unit_id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
		
	function delete_si_unit($si_unit_id) {
        global $db;
		
        $r = array(
                'si_unit_id' => $si_unit_id
        );
		
        $sql = "DELETE FROM si_unit WHERE ID = :si_unit_id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
		
	function is_si_unit_empty($si_unit_id) {
        global $db;
		
        $r = array(
                'si_unit_id' => $si_unit_id
        );
		
        $sql = "SELECT * FROM timestamp WHERE SI_unit = :si_unit_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_timestamp = $req->rowCount($sql);
		
        $sql = "SELECT * FROM runner_units WHERE SI_unit = :si_unit_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_runner_units = $req->rowCount($sql);
		
		if(($empty_timestamp + $empty_runner_units) == 0) {
			return 1;
		}
		
		else {
			return 0;
		}
    }
	
	function does_si_unit_exist($si_unit_id) {
        global $db;

        $e = array(
            'si_unit_id' => $si_unit_id
        );

        $sql = "SELECT * FROM si_unit WHERE ID = :si_unit_id";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
    }	
	
	function get_not_returned_si_units() {
		global $db;
		
        $req = $db->query("SELECT ru.Runner, ru.SI_unit, r.FirstName, r.LastName FROM runner AS r, runner_units AS ru, si_unit AS su WHERE su.Status = 'Active' AND su.ID = ru.SI_unit AND r.ID = ru.Runner");
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	function get_not_returned_si_unit($si_unit_id) {
		global $db;
		
        $e = array(
            'si_unit_id' => $si_unit_id
        );

        $sql = "SELECT * FROM runner WHERE ID IN (SELECT Runner FROM runner_units WHERE SI_unit = :si_unit_id)";
        $req = $db->prepare($sql);
        $req->execute($e);
		
        $result = $req->fetchObject();
		
        return $result;
	}
	
	
	
	/* RACE INSTANCE FUNCTIONS */
		
	function get_race_instances($race_id) {
        global $db;

        $var = array(
			'race_id' => $race_id
        );
		
        $sql = "SELECT * FROM race_instance WHERE Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($var);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	function get_race_instance_by_id_and_class($race_id, $class_id) {
        global $db;

        $var = array(
			'race_id' => $race_id,
			'class_id' => $class_id
        );
		
        $sql = "SELECT * FROM race_instance WHERE Race = :race_id AND Class = :class_id";
        $req = $db->prepare($sql);
        $req->execute($var);
		
        $result = $req->fetchObject();
		
        return $result;
	}
	
	function get_race_instance_by_id($race_instance_id) {
        global $db;

        $var = array(
			'race_instance_id' => $race_instance_id
        );
		
        $sql = "SELECT * FROM race_instance WHERE ID = :race_instance_id";
        $req = $db->prepare($sql);
        $req->execute($var);
		
        $result = $req->fetchObject();
		
        return $result;
	}
	
	function add_race_instance($race_id, $category_id, $start_time) {
        global $db;
		
        $var = array(
			'race_id' => $race_id,
			'category_id' => $category_id,
			'start_time' => $start_time
        );
		
        $sql = "INSERT INTO race_instance(Race, Class, StartTime) VALUES(:race_id, :category_id, :start_time)";
        $req = $db->prepare($sql);
        $req->execute($var);
    }		
	
	function edit_race_instance($race_instance_id, $race_id, $category_id, $start_time) {
        global $db;
		
        $var = array(
			'race_instance_id' => $race_instance_id,
			'race_id' => $race_id,
			'category_id' => $category_id,
			'start_time' => $start_time
        );
		
        $sql = "UPDATE race_instance SET Race = :race_id, Class = :category_id, StartTime = :start_time WHERE ID = :race_instance_id";
        $req = $db->prepare($sql);
        $req->execute($var);
    }
		
	function delete_race_instance($race_instance_id) {
        global $db;
		
        $var = array(
            'race_instance_id' => $race_instance_id
        );
		
        $sql = "DELETE FROM race_instance WHERE ID = :race_instance_id";
        $req = $db->prepare($sql);
        $req->execute($var);
    }
		
	function is_race_instance_empty($race_instance_id) {
        global $db;
		
        $var = array(
            'race_instance_id' => $race_instance_id
        );
		
        $sql = "SELECT * FROM race_runner WHERE RaceInstance = :race_instance_id";
        $req = $db->prepare($sql);
        $req->execute($var);
		
		$empty_race_runner = $req->rowCount($sql);
		
		if($empty_race_runner == 0) {
			return 1;
		}
		
		else {
			return 0;
		}
    }
	
	function does_race_instance_exist($race_id, $gender, $distance) {
        global $db;

        $e = array(
            'race_id' => $race_id,
            'gender' => $gender,
            'distance' => $distance
        );

        $sql = "SELECT ri.ID FROM race_instance ri, class c WHERE ri.Race = :race_id AND ri.Class = c.ID AND c.Gender = :gender AND c.Distance = :distance";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
    }
	
	function get_instances_distances($race_id) {
		global $db;

        $e = array(
            'race_id' => $race_id
        );

        $sql = "SELECT DISTINCT c.Distance FROM race_instance ri, class c WHERE ri.Race = :race_id AND ri.Class = c.ID";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetch()['Distance']) 
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	function get_instances($race_id) {
		global $db;

        $e = array(
            'race_id' => $race_id
        );

        $sql = "SELECT c.ID AS ID, c.Distance AS Distance, c.Gender AS Gender FROM race_instance ri, class c WHERE ri.Race = :race_id AND ri.Class = c.ID";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject())
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	function does_station_code_exist($runner_id, $instance_id, $code) {
		global $db;

        $e = array(
            'runner_id' => $runner_id,
			'instance_id' => $instance_id,
			'code' => $code
        );

        $sql = "
			SELECT * 
			FROM timestamp 
			WHERE Runner = :runner_id AND Race = (
				SELECT Race 
				FROM race_instance
				WHERE ID = :instance_id
			)
			AND Station = (
				SELECT ID
				FROM station 
				WHERE Code = :code
			)
		";
        $req = $db->prepare($sql);
        $req->execute($e);
		
        $exist = $req->rowCount($sql);
		
        return($exist);
		
	}
	
	function get_number_timestamps($runner_id, $instance_id) {
		global $db;

        $e = array(
            'runner_id' => $runner_id,
			'instance_id' => $instance_id
        );

        $sql = "
			SELECT * 
			FROM timestamp 
			WHERE Runner = :runner_id AND Race = (
				SELECT Race 
				FROM race_instance
				WHERE ID = :instance_id
			)
		";
        $req = $db->prepare($sql);
        $req->execute($e);
		
        $result = $req->rowCount($sql);
		
        return($result);
		
	}
	
	// OTHER
	
	function not_empty_race_gender($race_id, $class_gender) {
		global $db;

        $e = array(
            'race_id' => $race_id,
            'class_gender' => $class_gender
			
        );

        $sql = "SELECT rr.Runner
				FROM race_runner AS rr, race_instance AS ri, class AS c
				WHERE c.Gender = :class_gender AND ri.Class = c.ID AND rr.RaceInstance = ri.ID AND ri.Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
	}
	
	function not_empty_race_gender_distances($race_id, $class_gender, $class_gender_distance) {
		global $db;

        $e = array(
            'race_id' => $race_id,
            'class_gender' => $class_gender,
			'class_gender_distance' => $class_gender_distance
			
        );

        $sql = "SELECT rr.Runner
				FROM race_runner AS rr, race_instance AS ri, class AS c
				WHERE c.Gender = :class_gender AND c.Distance = :class_gender_distance AND ri.Class = c.ID AND rr.RaceInstance = ri.ID AND ri.Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
	}
	
	function get_race_from_instance($race_instance_id) {
		global $db;

        $e = array(
            'race_instance_id' => $race_instance_id
        );
        $sql = "SELECT * FROM race_instance WHERE ID = :race_instance_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
        $result = $req->fetchObject();
		
        return $result;
	}
	
	function is_category_empty_for_a_race($category_id, $race_id) {
        global $db;
		
        $r = array(
            'category_id' => $category_id,
			'race_id' => $race_id
        );
		
        $sql = "SELECT * FROM race_instance WHERE Class = :category_id AND Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_race_runner = $req->rowCount($sql);

		if($empty_race_runner == 0) {
			return 1;
		}
		
		else {
			return 0;
		}
    }
?>

