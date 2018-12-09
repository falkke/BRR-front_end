<?php

	/* INITIALISATION */
	session_start();

	$dbhost = 's679.loopia.se';
	$dbname = 'sebastianoveland_com_db_1';
	$dbuser = 'group5@s243341';
	$dbpassword = 'BlackRiver2019';

	try {
		$db = new PDO('mysql:host='.$dbhost.';dbname='.$dbname, $dbuser, $dbpassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	}
	
	catch(PDOexception $e) {
		die("Error while trying to connect to the database...");
	}

	
	/* LOGIN FUNCTIONS */
	
    function is_logged() {
        if(isset($_SESSION['admin'])) {
            return 1;
        }
        else {
            return 0;
        }
    }
	
	function user_exist($username, $password) {
        global $db;
		
        $r = array(
                'username' => $username
        );

        $sql = "SELECT * FROM administrator WHERE Username = :username";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$hash = $req->fetch()['Password'];

		if(password_verify($password, $hash)) {
			return 1;
		}
		
		else {
			return 0;
		}
    }
	
	
	/* RACE FUNCTIONS */
	
	function get_races() {
        global $db;
		
        $req = $db->query("SELECT * FROM race ORDER BY id ASC");
		
        $results = array();

        while($rows = $req->fetchObject()) {
            $results[] = $rows;
        }

        return $results;
    }
	
	function get_race($race_id) {
        global $db;
		
        $r = array(
                'race_id' => $race_id
        );
				
        $sql = "SELECT * FROM race WHERE ID = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
        $result = $req->fetchObject();
		
        return $result;
    }
	
	function add_race($race_name, $race_date) {
        global $db;
        $r = array(
                'race_name' => $race_name,
                'race_date' => $race_date,
        );
		
        $sql = "INSERT INTO race(Name, Date) VALUES(:race_name, :race_date)";
        $req = $db->prepare($sql);
        $req->execute($r);
    }		
	
	function edit_race($race_id, $race_name, $race_date) {
        global $db;
		
        $r = array(
                'race_id' => $race_id,
                'race_name' => $race_name,
                'race_date' => $race_date
        );
		
        $sql = "UPDATE race SET Name = :race_name, Date = :race_date WHERE ID = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
		
	function delete_race($race_id) {
        global $db;
		
        $r = array(
                'race_id' => $race_id
        );
		
        $sql = "DELETE FROM race WHERE ID = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
    }
		
	function is_race_empty($race_id) {
        global $db;
		
        $r = array(
                'race_id' => $race_id
        );
		
        $sql = "SELECT * FROM race_runner WHERE Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_race_runner = $req->rowCount($sql);
		
        $sql = "SELECT * FROM timestamp WHERE Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_timestamp = $req->rowCount($sql);
		
        $sql = "SELECT * FROM runner_units WHERE Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_runner_unit = $req->rowCount($sql);
		
        $sql = "SELECT * FROM race_station WHERE Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_race_station = $req->rowCount($sql);
		
		if(($empty_race_runner + $empty_timestamp + $empty_runner_unit + $empty_race_station) == 0) {
			return 1;
		}
		
		else {
			return 0;
		}
    }
	
	function does_race_exist($race_id) {
        global $db;

        $e = array(
            'race_id' => $race_id
        );

        $sql = "SELECT * FROM race WHERE ID = :race_id";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
    }
	
	function search_race($keyword, $sort) 
	{
		global $db;

        $req = $db->query("SELECT * FROM race WHERE Name LIKE '%{$keyword}%' {$sort}");

		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	function is_planned_race($race_id) {
		 global $db;
		
        $r = array(
                'race_id' => $race_id
        );
		
        $sql = "SELECT * FROM timestamp WHERE Race = :race_id";
        $req = $db->prepare($sql);
        $req->execute($r);
		
		$empty_timestamp = $req->rowCount($sql);
		
		if($empty_timestamp == 0) {
			return 1;
		}
		
		else {
			return 0;
		}
	}
	
	/* RUNNER FUNCTIONS */
	
	function get_runners() {
        global $db;
		
        $req = $db->query("SELECT * FROM runner ORDER BY id ASC");
        $results = array();

        while($rows = $req->fetchObject()) {
            $results[] = $rows;
        }

        return $results;
    }
	
	function get_runner($runner_id) {
        global $db;
		
		$e = array(
            'runner_id' => $runner_id
        );
		
		$sql = "SELECT * FROM runner WHERE ID = :runner_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
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

        $req = $db->query("SELECT * FROM runner WHERE CONCAT(FirstName, ' ', LastName) LIKE '%{$keyword}%' {$sort}");

		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	
	/* RACE RUNNER FUNCTIONS*/
	
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
	
	function get_race_runners($race_id, $keyword) {
		global $db;
		
        $e = array(
            'race_id' => $race_id
        );

		$sql = "SELECT rr.* FROM race_runner AS rr, club AS c, runner AS r WHERE rr.Race = :race_id AND c.ID = rr.Club AND r.ID = rr.Runner AND CONCAT(r.FirstName, ' ', r.LastName, ' ', c.Name, ' ', rr.Bib) LIKE '%{$keyword}%' ORDER BY rr.Place ASC";
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
		
        $sql = "INSERT INTO race_runner(Race, Category, Runner, Bib, Club) VALUES(:race_id, :category_id, :runner_id, :bib, :team_id)";
        $req = $db->prepare($sql);
        $req->execute($e);
    }	

	function add_race_si_unit_runner($race_id, $runner_id, $si_unit_id) {
        global $db;
		
		$e = array(
            'race_id' => $race_id,
            'runner_id' => $runner_id,
            'si_unit_id' => $si_unit_id
        );
		
        $sql = "INSERT INTO runner_units(Race, SI_unit, Runner) VALUES(:race_id, :si_unit_id, :runner_id)";
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
	
	
	/* TIMESTAMP FUNCTIONS */
	
	function get_timestamps($runner_id, $race_id) 
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
	
	
	/* STATION FUNCTIONS */
	
	function get_station($station_id) 
	{
		global $db;
		
        $e = array(
            'station_id' => $station_id
        );

        $sql = "SELECT * FROM station WHERE ID = :station_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetchObject();

		return $result;
	}
		
	function get_number_laps($runner_id, $race_id, $timestamp, $station_id)
	{
		global $db;
		
        $e = array(
            'runner_id' => $runner_id,
            'race_id' => $race_id,
            'timestamp' => $timestamp,
            'station_id' => $station_id
        );

        $sql = "SELECT COUNT(Timestamp) AS Count FROM timestamp WHERE Runner = :runner_id AND Race = :race_id AND Station = :station_id AND Timestamp < :timestamp";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetch()['Count'];

		return $result;
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
            'nb_lap' => $lap + 1,
            'station_id' => $station_id,
            'runner_id' => $runner_id,
            'race_id' => $race_id
        );
		$sql = "SELECT TIMEDIFF(t1.timestamp, t2.timestamp) AS TimeBehind FROM timestamp AS t1, timestamp AS t2 WHERE t1.Runner = :runner_id AND t1.Race = :race_id AND t1.lap = :nb_lap AND t1.station = :station_id AND t2.Place = 1 AND t2.Race = :race_id AND t2.lap = :nb_lap AND t2.station = :station_id";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetch()['TimeBehind'];;

		return $result;		
	}


	
	function get_elapsed_time_at_timestamp($runner_id, $race_id, $lap, $station_id)
	{
		global $db;
		
        $e = array(
            'nb_lap' => $lap + 1,
            'station_id' => $station_id,
            'runner_id' => $runner_id,
            'race_id' => $race_id
        );
		$sql = "SELECT TIMEDIFF(t1.timestamp, t2.timestamp) AS TimeBehind FROM timestamp AS t1, timestamp AS t2 WHERE t1.Runner = :runner_id AND t1.Race = :race_id AND t1.lap = :nb_lap AND t1.station = :station_id AND t2.Runner = :runner_id AND t2.Race = :race_id AND t2.station = 0";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetch()['TimeBehind'];

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
	
	
	/* SI-UNIT FUNCTIONS */
	
	function search_si_unit($keyword, $sort) 
	{
		global $db;

        $req = $db->query("SELECT * FROM si_unit WHERE Status LIKE '%{$keyword}%' {$sort}");

		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
	}
?>

