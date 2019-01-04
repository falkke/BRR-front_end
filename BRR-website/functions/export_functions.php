<?php
	/* EXPORT FUNCTIONS */
	/*
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
	
	function exist_participant($class_id, $race_id)
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
		
        $sql = "SELECT rr.Bib AS Bib, rr.Race AS Race, rr.Runner AS Runner, ri.StartTime AS StartTime
				FROM race_runner AS rr, race_instance AS ri
				WHERE ri.ID = :instance_id AND ri.ID = rr.RaceInstance";
        $req = $db->prepare($sql);
        $req->execute($e);
		
        $exist = $req->rowCount($sql);
		
        return($exist);	
	}*/
	function get_personnummer_from_dob($dob)
	{
		$personnummer = substr($dob, 2, 2).substr($dob, 5, 2).substr($dob, 8, 2);
		
		return $personnummer;
	}
	
	function get_class_by_id($class)
	{
		global $db;

        $e = array(
            'class' => $class
		);
        $sql = "SELECT * FROM class WHERE ID = :class";
        $req = $db->prepare($sql);
        $req->execute($e);
		
        $result = $req->fetchObject();
		
        return $result;
	}
	
	function export_race_runner($race_id)
	{
		global $db;

        $e = array(
            'race_id' => $race_id
        );
		
        $sql = "SELECT rr.Bib AS Bib, rr.Race AS Race, rr.Runner AS Runner, ri.StartTime AS StartTime, rr.Class AS Class
				FROM race_runner AS rr, race_instance AS ri
				WHERE ri.Race = :race_id AND ri.ID = rr.RaceInstance
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