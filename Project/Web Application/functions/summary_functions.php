<?php
	/* SUMMARY FUNCTIONS */
	
	function get_runner_unit($runner_id, $race_id)
	{
		global $db;

        $e = array(
            'race_id' => $race_id,
            'runner_id' => $runner_id
        );
        $sql = "SELECT * FROM runner_units WHERE Runner = :runner_id AND Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
        $result = $req->fetchObject();
		
        return $result;
	}
	
	function get_classes()
	{
		global $db;

        $e = array();
        $sql = "SELECT * FROM class";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_active_race()
	{
		global $db;

        $e = array();
        $sql = "SELECT r.ID, r.Name, r.Date, r.EndTime 
				FROM race_display AS rd, race AS r
				WHERE rd.Race = r.ID";
        $req = $db->prepare($sql);
        $req->execute($e);
		
        $result = $req->fetchObject();
		
        return $result;
	}
	
	function exist_participant($class_id, $race_id, $mode)
	{
		global $db;

        $e = array(
            'race_id' => $race_id,
            'class_id' => $class_id
        );
		
        $sql = "SELECT * FROM race_instance WHERE Race = :race_id AND Class = :class_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$instance_id = $req->fetch()['ID'];
		
        $e = array(
            'instance_id' => $instance_id
        );
		
        $sql = "SELECT rr.Bib AS Bib, ri.Race AS Race, rr.Runner AS Runner, ri.StartTime AS StartTime
				FROM race_runner AS rr, race_instance AS ri
				WHERE ri.ID = :instance_id AND ri.ID = rr.RaceInstance";
        $req = $db->prepare($sql);
        $req->execute($e);
		
        $exist = $req->rowCount($sql);
		
		if($mode == "results") {
			$e = array(
				'class_id' => $class_id
			);
			
			$sql = "SELECT * FROM class WHERE ID = :class_id";
			$req = $db->prepare($sql);
			$req->execute($e);
			
			$stop_distance = $req->fetch()['Distance'];
			
			$e = array(
				'race_id' => $race_id,
				'stop_distance' => $stop_distance,
				'class_id' => $class_id
			);
			
			$sql = "SELECT rr.Runner
					FROM race_runner AS rr,race_instance AS ri, timestamp AS t, station AS s, runner AS r, class AS c
					WHERE ri.Race = :race_id AND rr.RaceInstance = ri.ID AND ri.ID = rr.RaceInstance AND rr.Status = 'DNF' AND r.ID = rr.Runner AND c.ID = :class_id AND r.Gender = c.Gender
					AND t.RaceInstance = ri.ID AND t.Runner = rr.Runner AND s.ID = t.Station AND (((t.Lap - 1) * 10) + s.LengthFromStart) = :stop_distance AND t.Timestamp = (
						SELECT MAX(t2.Timestamp) 
						FROM timestamp t2
						WHERE t2.Runner = rr.Runner AND t2.RaceInstance = ri.ID AND t2.Station <> 'b827eb2d0304'
					)";
					
			$req = $db->prepare($sql);
			$req->execute($e);
			
			$exist = $exist + $req->rowCount($sql);
		}
        return($exist);	
	}
	
	function participant_list($class_id, $race_id)
	{
		global $db;

        $e = array(
            'race_id' => $race_id,
            'class_id' => $class_id
        );
		
        $sql = "SELECT * FROM race_instance WHERE Race = :race_id AND Class = :class_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$instance_id = $req->fetch()['ID'];
		
        $e = array(
            'instance_id' => $instance_id
        );
		
        $sql = "SELECT rr.Bib AS Bib, ri.Race AS Race, rr.Runner AS Runner, ri.StartTime AS StartTime
				FROM race_runner AS rr, race_instance AS ri
				WHERE ri.ID = :instance_id AND ri.ID = rr.RaceInstance
				ORDER BY Bib";
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