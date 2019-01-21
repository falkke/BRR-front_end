<?php
	/* EXPORT FUNCTIONS */

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
		
        $sql = "SELECT rr.Bib AS Bib, ri.Race AS Race, rr.Runner AS Runner, ri.StartTime AS StartTime, ri.Class AS Class
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