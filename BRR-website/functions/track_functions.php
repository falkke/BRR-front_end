<?php
	/* TRACK FUNCTIONS */
	
	function runner_track($keyword, $area) 
	{
		global $db;

        $e = array();
        $sql = "SELECT * FROM race_display";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$race_id = $req->fetch()['Race'];
		$status = "Running";
		
        $e = array(
            'race_id' => $race_id,
            'status' => $status
        );
		
		$sql = "SELECT rr.Race, rr.Runner, rr.Bib, rr.Club, rr.TotalTime, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, t.Timestamp, t.Station
				FROM race_runner AS rr, club AS c, runner AS r, timestamp AS t, station AS s
				WHERE rr.Race = :race_id AND c.ID = rr.Club AND r.ID = rr.Runner AND rr.Status = :status
				AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE '%{$keyword}%'
				AND t.Runner = rr.Runner AND t.Race = rr.Race AND  t.Timestamp = 
				(SELECT MAX(t.timestamp) FROM timestamp AS t WHERE t.Runner = rr.Runner AND t.Race = :race_id)
				AND  t.Station = s.ID";
							
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