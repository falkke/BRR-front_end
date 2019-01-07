<?php
	function get_latest_timestamps($race_id) {
		global $db;
		
        $e = array(
            'race_id' => $race_id
        );
		
		$sql = "SELECT t.Timestamp, t.Place, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, rr.Race, rr.Runner, rr.Bib, rr.Status
				FROM timestamp AS t, race_runner AS rr, station AS s
				WHERE rr.Race = :race_id AND t.Race = rr.Race AND t.Runner = rr.Runner AND t.Station = s.ID
				ORDER BY t.Timestamp DESC";
							
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_resting_timestamps($race_id) {
		global $db;
		
        $e = array(
            'race_id' => $race_id
        );
		
		$sql = "SELECT t.Timestamp, t.Place, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, rr.Race, rr.Runner, rr.Bib, rr.Status, t.Station, t.Lap
				FROM timestamp AS t, race_runner AS rr, station AS s
				WHERE rr.Race = :race_id AND t.Race = rr.Race AND t.Runner = rr.Runner AND t.Station = s.ID
				ORDER BY t.Timestamp DESC";
							
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