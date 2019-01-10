<?php
	function get_latest_timestamps($instance_id) {
		global $db;
		
        $e = array(
            'instance_id' => $instance_id
        );
		
		$sql = "SELECT t.Timestamp, t.Place, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, ri.Race, rr.Runner, rr.Bib, rr.Status
				FROM timestamp AS t, race_runner AS rr, race_instance AS ri, station AS s
				WHERE ri.ID = :instance_id AND rr.RaceInstance = ri.ID AND t.RaceInstance = ri.ID AND t.Runner = rr.Runner AND t.Station = s.ID
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
	
	function get_resting_timestamps($instance_id) {
		global $db;
		
        $e = array(
            'instance_id' => $instance_id
        );
		
		$sql = "SELECT t.Timestamp, t.Place, (((t.Lap - 1) * 10) + s.LengthFromStart) AS Distance, ri.Race, rr.Runner, rr.Bib, rr.Status, t.Station, t.Lap
				FROM timestamp AS t, race_runner AS rr, race_instance AS ri, station AS s
				WHERE ri.ID = :instance_id AND rr.RaceInstance = ri.ID AND t.RaceInstance = ri.ID AND t.Runner = rr.Runner AND t.Station = s.ID
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