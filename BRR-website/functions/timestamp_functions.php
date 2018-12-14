<?php
	function set_totaltime($runner_id, $race_id) {
		global $db;
		
		$r = array(
			'runner_id' => $runner_id,
			'race_id' => $race_id
        );
		$sql = "SELECT t.Timestamp AS Timestamp, t.Station AS Station
				FROM timestamp AS t
				WHERE t.Runner = :runner_id AND t.Race = :race_id AND t.Timestamp = 
				(SELECT MAX(t.timestamp) FROM timestamp AS t, station AS s WHERE t.Runner = rr.Runner AND t.Race = :race_id AND s.Code <> 99 AND t.Station = s.ID)";
		$req = $db->prepare($sql);
        $req->execute($r);
		$timestamp = $req->fetchObject();
		if(!empty($timestamp)) {
			$totaltime = get_elapsed_time_at_timestamp($runner_id, $race_id, $timestamp->Station, $timestamp->Timestamp);
		}
		else {
			$totaltime = 0;
		}
		return($totaltime);
	}
	
	function set_status($runner_id, $race_id, $race_instance_id) {
		global $db;
		
		// get timestamp at station 99
		$r = array(
			'runner_id' => $runner_id,
			'race_id' => $race_id
        );
		$sql = 	"SELECT t.Timestamp
				FROM timestamp AS t, station AS s
				WHERE t.Runner = :runner_id AND t.Race = :race_id AND t.Timestamp = 
				(SELECT MAX(t.timestamp) FROM timestamp AS t, station AS s WHERE t.Runner = :runner_id AND t.Race = :race_id AND s.Code = 99 AND t.Station = s.ID)
				AND  t.Station = s.ID";
		$req = $db->prepare($sql);
        $req->execute($r);
		$exist_stop = $req->rowCount($sql);
		
		// get number of timestamp
		$r = array(
			'runner_id' => $runner_id,
			'race_id' => $race_id
        );
		$sql = 	"SELECT t.Timestamp
				FROM timestamp AS t
				WHERE t.Runner = :runner_id AND t.Race = :race_id";
		$req = $db->prepare($sql);
        $req->execute($r);
		$timestamp_number = $req->rowCount($sql);
		
		// has the runner DNF / DNS
		if($exist_stop == 1 && $timestamp_number == 1) {
			return("DNS");
		}
		else if($exist_stop == 1) {
			return("DNF");
		}
		
		// get distance
		$r = array(
			'runner_id' => $runner_id,
			'race_id' => $race_id
        );
		$sql = 	"SELECT (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance
				FROM timestamp AS t, station AS s
				WHERE t.Runner = :runner_id AND t.Race = :race_id AND t.Timestamp in 
					(SELECT MAX(t.Timestamp) FROM timestamp AS t, station AS s 
					WHERE t.Runner = :runner_id AND t.Race = :race_id 
					AND s.Code <> 99 AND t.Station = s.ID)
				AND  s.ID = t.Station";
		$req = $db->prepare($sql);
        $req->execute($r);
        $distance = $req->fetch()['Distance'];
		
		// get race_length
		$r = array(
			'race_instance_id' => $race_instance_id
        );
		$sql = "SELECT c.Distance AS Distance
				FROM race_instance AS ri, class AS c
				WHERE ri.ID = :race_instance_id AND c.ID = ri.Class";
		$req = $db->prepare($sql);
        $req->execute($r);
        $race_length = $req->fetch()['Distance'];
		
		//compare race_length & distance
		if($race_length == $distance) {
			return("Finished");
		}
		else {
			return("Running");
		}
	}

	function update_race_runner($runner_id, $race_id) {
		global $db;
		
		$r = array(
			'runner_id' => $runner_id,
			'race_id' => $race_id
        );
		$sql = "SELECT ri.ID, ri.Race, ri.Class
				FROM race_instance AS ri, race_runner AS rr
				WHERE rr.Runner = :runner_id AND rr.Race = :race_id
				AND rr.RaceInstance = ri.ID";
		$req = $db->prepare($sql);
        $req->execute($r);
        $race_instance = $req->fetchObject();
		
		$status = set_status($runner_id, $race_id, $race_instance->ID);
		if($status == "DNS") {
			$totaltime = "00:00:00";
		}
		else {
			$totaltime = set_totaltime($runner_id, $race_id);
		}
		
		$r = array(
			'runner_id' => $runner_id,
			'race_id' => $race_id,
			'race_instance_id' => $race_instance->ID,
			'totaltime' => $totaltime,
			'status' => $status
		);
		
		$sql = "UPDATE race_runner SET Status = :status ,TotalTime = :totaltime
				WHERE RaceInstance = :race_instance_id AND Runner = :runner_id AND Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
	}
	
	function add_timestamp($runner_id, $race_id, $new_datetime, $station) {
        global $db;
		
		$lap = get_number_laps($runner_id, $race_id, $new_datetime, $station);

        $r = array(
                'runner_id' => $runner_id,
                'race_id' => $race_id,
                'station' => $station,
				'lap' => $lap	
        );
		
        $sql = "INSERT INTO timestamp(Timestamp, SI_Unit, Runner, Station, Race, Lap) VALUES({$new_datetime}, 0, :runner_id, :station, :race_id, :lap)";
        $req = $db->prepare($sql);
        $req->execute($r);
		
				
		$r = array(
			'runner_id' => $runner_id,
			'race_id' => $race_id,
			'lap' => $lap
        );
		
		$sql = "UPDATE timestamp SET Lap = Lap + 1 WHERE Race = :race_id AND Runner = :runner_id AND Timestamp > {$new_datetime} AND Lap >= :lap";
        $req = $db->prepare($sql);
        $req->execute($r);
		$r = array(
                'runner_id' => $runner_id,
                'race_id' => $race_id
        );
		
        $sql = "SELECT MAX(Lap) AS Max FROM timestamp WHERE Race = :race_id AND Runner = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		$max_lap = $req->fetch()['Max'];
				
		update_race_runner($runner_id, $race_id);
		$i = $lap;
		while($i <= $max_lap)
		{
			set_places($race_id, $station, $i);
// update placeS in race_runner
			$i = $i + 1;
		}
    }		
	
	function edit_timestamp($old_timestamp, $runner_id, $race_id, $new_datetime,  $station) {
		delete_timestamp($runner_id, $race_id, $old_timestamp);
		add_timestamp($runner_id, $race_id, $new_datetime, $station);
    }
		
	function delete_timestamp($runner_id, $race_id, $timestamp) {
        global $db;
		
		// get the timestamp to delete
		$r = array(
				'timestamp' => $timestamp,
                'runner_id' => $runner_id,
                'race_id' => $race_id
        );
		
        $sql = "SELECT * FROM timestamp WHERE Race = :race_id AND Runner = :runner_id AND Timestamp = :timestamp";
        $req = $db->prepare($sql);
        $req->execute($r);
		$timestamp_to_delete = $req->fetchObject();
		
		// count the number of timestamp in a given lap
		$r = array(
				'lap' => $timestamp_to_delete->Lap,
                'runner_id' => $runner_id,
                'race_id' => $race_id
        );
		
        $sql = "SELECT COUNT(Timestamp) AS Count FROM timestamp WHERE Race = :race_id AND Runner = :runner_id AND Lap = :lap";
        $req = $db->prepare($sql);
        $req->execute($r);
		$timestamp_count = $req->fetch()['Count'];
		
		$lap = $timestamp_to_delete->Lap;
		$station = $timestamp_to_delete->Station;
		
		// delete the timestamp
		$r = array(
            'timestamp' => $timestamp,
            'runner_id' => $runner_id,
            'race_id' => $race_id
        );
		
        $sql = "DELETE FROM timestamp WHERE Runner = :runner_id AND Timestamp = :timestamp AND Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		update_race_runner($runner_id, $race_id);
		//update Lap if needed
		if((($timestamp_count == 2) && ($lap == 1)) || ($timestamp_count == 1))
		{
			$r = array(
				'timestamp' => $timestamp,
				'runner_id' => $runner_id,
				'race_id' => $race_id
			);
			
			$sql = "UPDATE timestamp SET Lap = Lap - 1 WHERE Race = :race_id AND Runner = :runner_id AND Timestamp > :timestamp";
			$req = $db->prepare($sql);
			$req->execute($r);
			
			// update place
			$r = array(
				'runner_id' => $runner_id,
				'race_id' => $race_id
			);
				
			$sql = "SELECT MAX(Lap) AS Max FROM timestamp WHERE Race = :race_id AND Runner = :runner_id";
			$req = $db->prepare($sql);
			$req->execute($r);
			$max_lap = $req->fetch()['Max'];	
			
			while($lap <= $max_lap)
			{
				set_places($race_id, $station, $lap);
// update placeS in race_runner
				$lap = $lap + 1;
			} 
		}
		else
		{
			set_places($race_id, $station, $lap);
// update place in race_runner
		}
    }

?>