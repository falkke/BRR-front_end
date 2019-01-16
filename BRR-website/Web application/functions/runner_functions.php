<?php
	/* RUNNER FUNCTIONS */
	
	// Function that gets all runners.
	function get_runners() {
        global $db;
		
        $req = $db->query("SELECT * FROM runner");
        $results = array();

        while($rows = $req->fetchObject()) {
            $results[] = $rows;
        }

        return $results;
    }
	
	function get_runner($runner_id) {
        global $db;
		
		$var = array(
            'runner_id' => $runner_id
        );
		
		$sql = "SELECT * FROM runner WHERE ID = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($var);
		
        $result = $req->fetchObject();
		
        return $result;
    }
	
	function add_runner($runner_first_name, $runner_last_name, $runner_birth_date, $runner_gender) {
        global $db;
		
        $e = array(
                'runner_first_name' => $runner_first_name,
                'runner_last_name' => $runner_last_name,
                'runner_birth_date' => $runner_birth_date,
				'runner_gender' => $runner_gender
        );
		
        $sql = "INSERT INTO runner(FirstName, LastName, DateOfBirth, Gender) VALUES(:runner_first_name, :runner_last_name, :runner_birth_date, :runner_gender)";
        $req = $db->prepare($sql);
        $req->execute($e);
    }
	
	function edit_runner($runner_id, $runner_first_name, $runner_last_name, $runner_birth_date) {
        global $db;
		
        $e = array(
                'runner_id' => $runner_id,
                'runner_first_name' => $runner_first_name,
                'runner_last_name' => $runner_last_name,
                'runner_birth_date' => $runner_birth_date,
        );
		
		
        $sql = "UPDATE runner SET FirstName = :runner_first_name, LastName = :runner_last_name, DateOfBirth = :runner_birth_date WHERE ID = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($e);
    }
	
	function delete_runner($runner_id) {
        global $db;
		
        $r = array(
                'runner_id' => $runner_id
        );
		
        $sql = "DELETE FROM runner WHERE ID = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
		
	function is_runner_empty($runner_id) {
        global $db;
		
        $r = array(
                'runner_id' => $runner_id
        );
		
        $sql = "SELECT * FROM race_runner WHERE Runner = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_race_runner = $req->rowCount($sql);
		
        $sql = "SELECT * FROM timestamp WHERE Runner = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_timestamp = $req->rowCount($sql);
		
        $sql = "SELECT * FROM runner_units WHERE Runner = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_runner_unit = $req->rowCount($sql);
		
		if(($empty_race_runner + $empty_timestamp + $empty_runner_unit) == 0) {
			return 1;
		}
		
		else {
			return 0;
		}
    }
	
	function does_runner_exist($runner_id) 
	{
        global $db;

        $e = array(
            'runner_id' => $runner_id
        );

        $sql = "SELECT * FROM runner WHERE ID = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
    }
	
	function search_runner($keyword, $sort) 
	{
		global $db;

        $e = array(
			'keyword' => "%{$keyword}%"
        );
		
        $sql = "SELECT * 
				FROM runner 
				WHERE CONCAT(FirstName, ' ', LastName) LIKE :keyword 
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
?>