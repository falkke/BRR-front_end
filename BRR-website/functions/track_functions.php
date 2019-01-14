<?php
	/* TRACK FUNCTIONS */
	
	function exist_track($keyword, $area, $race_id)
	{
		global $db;
		
		$e = array(
			'race_id' => $race_id,
			'status' => "Running",
			'station_id' => "b827ebdcbd98",
			'keyword' => "%{$keyword}%"
		);
		
		if($area == "run") {
			$sql = "SELECT ri.Race, rr.Runner, rr.Bib, rr.Club, rr.TotalTime, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, t.Timestamp, t.Station
					FROM race_runner AS rr, race_instance AS ri, club AS c, runner AS r, timestamp AS t, station AS s
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND c.ID = rr.Club AND r.ID = rr.Runner AND rr.Status = :status
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE :keyword
					AND t.Runner = rr.Runner AND t.RaceInstance = ri.ID AND  t.Timestamp = 
					(SELECT MAX(t.timestamp) FROM timestamp AS t, race_instance AS ri WHERE ri.Race = :race_id AND t.Runner = rr.Runner AND t.RaceInstance = ri.ID)
					AND t.Station = s.ID AND s.ID <> :station_id";
		}
		
		else {
			$sql = "SELECT ri.Race, rr.Runner, rr.Bib, rr.Club, rr.TotalTime, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, t.Timestamp, t.Station
					FROM race_runner AS rr, race_instance AS ri, club AS c, runner AS r, timestamp AS t, station AS s
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND c.ID = rr.Club AND r.ID = rr.Runner AND rr.Status = :status
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE :keyword
					AND t.Runner = rr.Runner AND t.RaceInstance = ri.ID AND  t.Timestamp = 
					(SELECT MAX(t.timestamp) FROM timestamp AS t, race_instance AS ri WHERE ri.Race = :race_id AND t.Runner = rr.Runner AND t.RaceInstance = ri.ID)
					AND t.Station = s.ID AND s.ID = :station_id";
		}	
		
        $req = $db->prepare($sql);
        $req->execute($e);
		
        $exist = $req->rowCount($sql);
		
        return($exist);	
	}
	
	function runner_track($keyword, $area, $race_id) 
	{
		global $db;
		
        $e = array(
            'race_id' => $race_id,
            'status' => "Running",
			'station_id' => "b827ebdcbd98",
			'keyword' => "%{$keyword}%"
        );
		
		if($area == "run") {
			$sql = "SELECT ri.Race, rr.Runner, rr.Bib, rr.Club, rr.TotalTime, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, t.Timestamp, t.Station, ri.ID AS RaceInstance
					FROM race_runner AS rr, race_instance AS ri, club AS c, runner AS r, timestamp AS t, station AS s
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND c.ID = rr.Club AND r.ID = rr.Runner AND rr.Status = :status
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE :keyword
					AND t.Runner = rr.Runner AND t.RaceInstance = ri.ID AND  t.Timestamp = 
					(SELECT MAX(t.timestamp) FROM timestamp AS t, race_instance AS ri WHERE ri.Race = :race_id AND t.Runner = rr.Runner AND t.RaceInstance = ri.ID)
					AND t.Station = s.ID AND s.ID <> :station_id";
		}
		
		else {
			$sql = "SELECT ri.Race, rr.Runner, rr.Bib, rr.Club, rr.TotalTime, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, t.Timestamp, t.Station, ri.ID AS RaceInstance
					FROM race_runner AS rr, race_instance AS ri, club AS c, runner AS r, timestamp AS t, station AS s
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND c.ID = rr.Club AND r.ID = rr.Runner AND rr.Status = :status
					AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE :keyword
					AND t.Runner = rr.Runner AND t.RaceInstance = ri.ID AND  t.Timestamp = 
					(SELECT MAX(t.timestamp) FROM timestamp AS t, race_instance AS ri WHERE ri.Race = :race_id AND t.Runner = rr.Runner AND t.RaceInstance = ri.ID)
					AND t.Station = s.ID AND s.ID = :station_id";
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
?>