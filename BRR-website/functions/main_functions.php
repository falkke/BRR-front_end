<?php
	/* RACE RUNNER FUNCTIONS*/
	
	function get_race_instance($race_id, $gender, $distance) {
		global $db;
		
        $e = array(
            'race_id' => $race_id, 
			'gender' => $gender, 
			'distance' => $distance
        );

        $sql = "SELECT ri.ID AS ID, ri.Race AS Race, ri.Class AS Class, ri.StartTime AS StartTime FROM race_instance AS ri, class AS c 
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

        $sql = "SELECT * FROM race_runner WHERE Runner = :runner_id AND Race = :race_id";
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
				'category' => $instance->Class
			);
			$sql = "SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, rr.Club, rr.Place, rr.TotalTime
					FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri
					WHERE rr.Race = :race_id AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.Class = :category AND ri.ID = rr.RaceInstance AND rr.Status = :status
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE '%{$keyword}%'";
		}
		else
		{
			$e = array(
				'race_id' => $instance->Race,
				'category' => $instance->Class
			);
			
			$sql = "SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, rr.Club, rr.Place, rr.TotalTime
					FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri
					WHERE rr.Race = :race_id AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.Class = :category AND ri.ID = rr.RaceInstance AND rr.Status is null
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE '%{$keyword}%'";
		}
		
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
	}
	
	function get_race_runners($race_id, $keyword) {
		global $db;
		
        $e = array(
            'race_id' => $race_id
        );
			
		$sql = "
			SELECT rr.* 
			FROM race_runner AS rr, club AS c, runner AS r 
			WHERE rr.Race = :race_id AND c.ID = rr.Club AND r.ID = rr.Runner AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE '%{$keyword}%' 
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
            'status' => $status
        );
		
		if($status == "DNF")
		{
			$sql = "SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, rr.Club, rr.Place, rr.TotalTime, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, t.Timestamp
					FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri, timestamp AS t, station AS s
					WHERE rr.Race = :race_id AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status = :status
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE '%{$keyword}%'
					AND t.Runner = rr.Runner AND t.Race = ri.Race AND  t.Timestamp = 
					(SELECT MAX(t.timestamp) FROM timestamp AS t, station AS s WHERE t.Runner = rr.Runner AND t.Race = :race_id AND s.Code <> 99 AND t.Station = s.ID)
					AND  t.Station = s.ID
					ORDER BY Place ASC";
		}
		else if($status == "Running")
		{
			$sql = "SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, rr.Club, rr.Place, rr.TotalTime, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, t.Timestamp
					FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri, timestamp AS t, station AS s
					WHERE rr.Race = :race_id AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status = :status
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE '%{$keyword}%'
					AND t.Runner = rr.Runner AND t.Race = ri.Race AND  t.Timestamp = 
					(SELECT MAX(t.timestamp) FROM timestamp AS t WHERE t.Runner = rr.Runner AND t.Race = :race_id)
					AND  t.Station = s.ID
					UNION
					SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, rr.Club, rr.Place, rr.TotalTime, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, t.Timestamp
					FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri, timestamp AS t, station AS s
					WHERE rr.Race = :race_id AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status is null
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE '%{$keyword}%'
					AND t.Runner = rr.Runner AND t.Race = ri.Race AND  t.Timestamp = 
					(SELECT MAX(t.timestamp) FROM timestamp AS t WHERE t.Runner = rr.Runner AND t.Race = :race_id)
					AND  t.Station = s.ID
					ORDER BY Place ASC";
		}
		
		else
		{
			$sql = "SELECT ri.Race, ri.Class, ri.StartTime, rr.Runner, rr.Bib, rr.Status, rr.Club, rr.Place, rr.TotalTime
					FROM race_runner AS rr, club AS c, runner AS r, race_instance AS ri
					WHERE rr.Race = :race_id AND c.ID = rr.Club AND r.ID = rr.Runner AND ri.ID = rr.RaceInstance AND rr.Status = :status
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE '%{$keyword}%'
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
	
	function add_race_runner($race_id, $category_distance, $runner_id, $bib, $team_id) {
        global $db;
		
		$e = array(
            'runner_id' => $runner_id
        );

        $sql = "SELECT Gender FROM runner WHERE ID = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($e);

		$category_gender = $req->fetch()['Gender'];
		
		$e = array(
            'category_distance' => $category_distance,
			'category_gender' => $category_gender
        );

        $sql = "SELECT ID FROM class WHERE Distance = :category_distance AND Gender = :category_gender";
        $req = $db->prepare($sql);
        $req->execute($e);

		$category_id = $req->fetch()['ID'];
		
        $e = array(
                'race_id' => $race_id,
                'category_id' => $category_id,
                'runner_id' => $runner_id,
                'bib' => $bib,
                'team_id' => $team_id,
        );
		
        $sql = "INSERT INTO race_runner(Race, Class, Runner, Bib, Club) VALUES(:race_id, :category_id, :runner_id, :bib, :team_id)";
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

		$sql = "SELECT * FROM runner WHERE ID NOT IN (SELECT Runner FROM race_runner WHERE Race = :race_id)";
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

        $sql = "SELECT * FROM race_runner WHERE Runner = :runner_id";
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

        $sql = "SELECT * FROM race_runner WHERE Runner = :runner_id AND Race = :race_id";
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

        $sql = "SELECT * FROM race_runner WHERE Runner = :runner_id AND Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$team_id = $req->fetch()['Club'];
		
		$e = array(
            'team_id' => $team_id
        );

        $sql = "SELECT * FROM club WHERE ID = :team_id";
        $req = $db->prepare($sql);
        $req->execute($e);

		$result = $req->fetchObject();
		
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

        $sql = "SELECT DISTINCT Gender FROM class WHERE ID IN (SELECT DISTINCT Class FROM race_runner WHERE Race = :race_id)";
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

        $sql = "SELECT DISTINCT Distance FROM class WHERE Gender = :class_gender AND ID IN (SELECT DISTINCT Class FROM race_runner WHERE Race = :race_id)";
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
		
        $sql = "DELETE FROM race_runner WHERE Race = :race_id AND Runner = :runner_id";
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
	
	
	function get_runner_timestamps($runner_id, $race_id) 
	{
		global $db;
		
        $e = array(
            'runner_id' => $runner_id,
            'race_id' => $race_id
        );

        $sql = "SELECT * FROM timestamp WHERE Runner = :runner_id AND Race = :race_id ORDER BY Timestamp DESC";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}	
	
	function get_timestamps($race_id, $station_id, $lap) 
	{
		global $db;
		
        $e = array(
            'race_id' => $race_id,
            'station_id' => $station_id,
            'lap' => $lap
        );

        $sql = "SELECT * FROM timestamp WHERE Race = :race_id AND Station = :station_id AND Lap = :lap";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_last_timestamp($runner_id, $race_id)
	{
		global $db;
		
        $e = array(
            'runner_id' => $runner_id,
            'race_id' => $race_id
        );

        $sql = "SELECT * FROM timestamp WHERE Timestamp = (SELECT MAX(Timestamp) FROM timestamp WHERE Runner = :runner_id AND Race = :race_id)";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
		$result = $req->fetchObject();

		return $result;
	}
	
	function get_number_laps($runner_id, $race_id, $timestamp, $station_id)
	{
		global $db;
		
        $e = array(
            'runner_id' => $runner_id,
            'race_id' => $race_id,
            'station_id' => $station_id
        );
        $sql = "SELECT COUNT(Timestamp) AS Count FROM timestamp WHERE Runner = :runner_id AND Race = :race_id AND Station = :station_id AND Timestamp < {$timestamp}";
        $req = $db->prepare($sql);
        $req->execute($e);
		$result = $req->fetch()['Count'] + 1;
		/*echo $result;
        $e = array(
            'runner_id' => $runner_id,
            'race_id' => $race_id
        );
        $sql = "SELECT MAX(Timestamp) AS Max
				FROM timestamp
				WHERE Runner = :runner_id AND Race = :race_id";
		$req = $db->prepare($sql);
		$req->execute($e);
		$max_timestamp = $req->fetch()['Max'];
		echo " max :" . $max_timestamp;
		echo " normal :" . $timestamp;
		echo ($timestamp > $max_timestamp);
		if($timestamp > $max_timestamp) {
			echo "AU DESSUS DU MAX";
			$e = array(
				'runner_id' => $runner_id,
				'race_id' => $race_id
			);
			$sql = "SELECT Max(Lap) AS Lap FROM timestamp WHERE Runner = :runner_id AND Race = :race_id";
			$req = $db->prepare($sql);
			$req->execute($e);
			$lap = $req->fetch()['Lap'] + 1;
			echo $lap;
			$e = array(
				'runner_id' => $runner_id,
				'race_id' => $race_id,
				'station_id' => $station_id,
				'lap' => $lap
			);
			$sql = "SELECT Timestamp
					FROM timestamp AS t
					WHERE t.Runner = :runner_id AND t.Race = :race_id AND t.Station = :station_id AND t.Lap = :lap";
			$req = $db->prepare($sql);
			$req->execute($e);
			$exist = $req->rowCount($sql);
			if($exist)
			{
				$lap = $lap + 1;
				echo $lap;
			}
			if($lap > $result) {
				$result = $lap;
			}
		}
		echo $result;*/
		return $result;
	}
		
	function set_places($race_id, $station_id, $lap) {
		global $db;
		
		foreach(get_timestamps($race_id, $station_id, $lap) as $timestamp) {
			$e = array(
				'race_id' => $race_id,
				'station_id' => $station_id,
				'lap' => $lap,
				'timestamp' => $timestamp->Timestamp
			);
			
			$sql = "SELECT COUNT(Timestamp) AS Count FROM timestamp WHERE Race = :race_id AND Station = :station_id AND Lap = :lap AND Timestamp < :timestamp";
			$req = $db->prepare($sql);
			$req->execute($e);
			$place = $req->fetch()['Count'] + 1;
			
			$e = array(
				'race_id' => $race_id,
				'station_id' => $station_id,
				'lap' => $lap,
				'timestamp' => $timestamp->Timestamp,
				'place' => $place
			);
			
			$sql = "UPDATE timestamp SET Place = :place WHERE Race = :race_id AND Station = :station_id AND Lap = :lap AND  Timestamp = :timestamp";
			$req = $db->prepare($sql);
			$req->execute($e);
		}
	}
	
	function get_time_behind($race_runner)
	{
		global $db;
		
        $e = array(
            'runner_id' => $race_runner->Runner,
            'class_id' => $race_runner->Class,
            'race_id' => $race_runner->Race
        );

        $sql = "SELECT TIMEDIFF(r1.TotalTime, r2.TotalTime) AS TimeBehind FROM race_runner AS r1, race_runner AS r2 WHERE r1.Runner = :runner_id AND r1.Race = :race_id AND r1.Class = :class_id AND r2.Place = 1 AND r2.Race = :race_id AND r2.Class = :class_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetch()['TimeBehind'];;

		return $result;		
	}
	
	function get_time_behind_at_timestamp($runner_id, $race_id, $lap, $station_id)
	{
		global $db;
		
        $e = array(
            'nb_lap' => $lap,
            'station_id' => $station_id,
            'runner_id' => $runner_id,
            'race_id' => $race_id
        );
		$sql = "SELECT TIMEDIFF(t1.timestamp, t2.timestamp) AS TimeBehind 
				FROM timestamp AS t1, timestamp AS t2 
				WHERE t1.Runner = :runner_id AND t1.Race = :race_id AND t1.lap = :nb_lap AND t1.station = :station_id 
				AND t2.Place = 1 AND t2.Race = :race_id AND t2.lap = :nb_lap AND t2.station = :station_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetch()['TimeBehind'];

		return $result;		
	}

	function get_total_elapsed_time($runner_id, $race_id)
	{
		global $db;
		
        $e = array(
            'runner_id' => $runner_id,
            'race_id' => $race_id
        );
		$sql = "SELECT TIMEDIFF(t1.timestamp, t2.timestamp) AS TimeBehind FROM timestamp AS t1, timestamp AS t2 WHERE t1.Runner = :runner_id AND t1.Race = :race_id AND t2.Runner = :runner_id AND t2.Race = :race_id AND t2.station = 0 AND t1.Timestamp =
				(SELECT MAX(t3.timestamp) FROM timestamp AS t3, station AS s WHERE t3.Runner = :runner_id AND t3.Race = :race_id AND s.Code <> 99 AND t3.Station = s.ID)";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetch()['TimeBehind'];

		return $result;		
	}	

	
	function get_elapsed_time_at_timestamp($runner_id, $race_id, $station_id, $timestamp)
	{
		global $db;
		
        $e = array(
            'station_id' => $station_id,
            'runner_id' => $runner_id,
            'race_id' => $race_id,
			'timestamp' => $timestamp
        );
		$sql = "SELECT TIMEDIFF(t1.timestamp, t2.timestamp) AS TimeBehind FROM timestamp AS t1, timestamp AS t2 WHERE t1.Runner = :runner_id AND t1.Race = :race_id AND t1.Timestamp = :timestamp AND t1.station = :station_id AND t2.Runner = :runner_id AND t2.Race = :race_id AND t2.station = 0";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetch()['TimeBehind'];

		return $result;		
	}	
	
	function does_timestamp_exist($timestamp, $runner_id, $race_id) 
	{
        global $db;

        $e = array(
            'timestamp' => $timestamp,
            'runner_id' => $runner_id,
            'race_id' => $race_id
        );

        $sql = "SELECT * FROM timestamp WHERE Runner = :runner_id AND Timestamp = :timestamp AND Race = :race_id";
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

        $req = $db->query("SELECT * FROM club WHERE Name LIKE '%{$keyword}%' {$sort}");

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
            'team_id' => $team_id
        );

		$sql = "SELECT * FROM runner WHERE CONCAT(FirstName, ' ', LastName) LIKE '%{$keyword}%' AND ID IN (SELECT Runner FROM race_runner WHERE Race = :race_id AND Club = :team_id)";
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
		
		$sql = "SELECT * FROM race WHERE ID IN (SELECT Race FROM race_runner WHERE Club = :team_id)";
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

        $req = $db->query("SELECT * FROM station WHERE Name LIKE '%{$keyword}%' {$sort}");

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
		
	function add_station($name, $code, $length_from_start) {
        global $db;
		
        $r = array(
                'name' => $name,
                'code' => $code,
                'length_from_start' => $length_from_start
        );
		
        $sql = "INSERT INTO station(Name, Code, LengthFromStart, LastID) VALUES(:name, :code, :length_from_start, 0)";
        $req = $db->prepare($sql);
        $req->execute($r);
    }		
	
	function edit_station($station_id, $name, $code, $length_from_start) {
        global $db;
		
        $r = array(
                'station_id' => $station_id,
                'name' => $name,
                'code' => $code,
                'length_from_start' => $length_from_start
        );
		
        $sql = "UPDATE station SET Name = :name, Code = :code, LengthFromStart = :length_from_start WHERE ID = :station_id";
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
		
        $sql = "SELECT * FROM race_station WHERE Station = :station_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_race_station = $req->rowCount($sql);
		
        $sql = "SELECT * FROM timestamp WHERE Station = :station_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_timestamp = $req->rowCount($sql);
		
		if(($empty_race_station + $empty_timestamp) == 0) {
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

        $req = $db->query("SELECT * FROM class WHERE Gender LIKE '%{$keyword}%' {$sort}");

		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	function get_categories_distances() 
	{
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
		
        $sql = "SELECT * FROM race_runner WHERE Class = :category_id";
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

        $req = $db->query("	SELECT su.ID, su.Status, '-' AS Holder
							FROM si_unit AS su
							WHERE su.Status = 'Returned' AND CONCAT(su.Status, ' ', su.ID) LIKE '%{$keyword}%'
							UNION
							SELECT su.ID, su.Status, CONCAT(r.FirstName, ' ', r.LastName) AS Holder 
							FROM si_unit AS su, runner_units AS ru, runner AS r 
							WHERE ru.SI_unit = su.ID AND r.ID = ru.Runner AND CONCAT(r.FirstName, ' ', r.LastName, ' ', su.Status, ' ', su.ID) LIKE '%{$keyword}%'
							{$sort}");

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
?>

