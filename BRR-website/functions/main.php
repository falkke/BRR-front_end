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
	
	
	/* ADMINISTRATION */
	
    function is_logged() {
        if(isset($_SESSION['brr'])) {
            $logged = 1;
        }
        else {
            $logged = 0;
        }

        return $logged;
    }
	
	function user_exist($username, $password) {
        global $db;
        $u = array(
                'username' => $username
        );

        $sql = 'SELECT * FROM administrator WHERE Username = :username';
        $req = $db->prepare($sql);
        $req->execute($u);
		
		$hash = $req->fetch()['Password'];

		if(password_verify($password, $hash)) {
			return 1;
		}
		
		else {
			return 0;
		}
    }
	
	
	/* RACE */
	
	function get_races() {
        global $db;
		
        $req = $db->query("SELECT * FROM race ORDER BY id ASC");
        $results = array();

        while($rows = $req->fetchObject()) {
            $results[] = $rows;
        }

        return $results;
    }
	
	function get_race($id) {
        global $db;
		
        $req = $db->query("SELECT * FROM race WHERE ID = '{$id}'");
		
        $results = $req->fetchObject();
		
        return $results;
    }
	
	function add_race($name, $date) {
        global $db;
        $r = array(
                'name' => $name,
                'date' => $date,
        );
		
        $sql = 'INSERT INTO race(Name, Date) VALUES(:name, :date)';
        $req = $db->prepare($sql);
        $req->execute($r);
    }		
	
	function edit_race($id, $name, $date) {
        global $db;
        $r = array(
                'id' => $id,
                'name' => $name,
                'date' => $date
        );
		
        $sql = 'UPDATE race SET Name = :name, Date = :date WHERE ID = :id';
        $req = $db->prepare($sql);
        $req->execute($r);
    }
	
	function race_exists($id) {
        global $db;

        $e = array(
            'id' => $id
        );

        $sql = "SELECT * FROM race WHERE ID = :id ";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
    }
	
	function search_race($keyword) 
	{
		global $db;

        $req = $db->query("SELECT * FROM race WHERE Name LIKE '%{$keyword}%'");

		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	
	/* RUNNER */
	
	function get_runners() {
        global $db;
		
        $req = $db->query("SELECT * FROM runner ORDER BY id ASC");
        $results = array();

        while($rows = $req->fetchObject()) {
            $results[] = $rows;
        }

        return $results;
    }
	
	function get_runner($id) {
        global $db;
		
        $req = $db->query("SELECT * FROM runner WHERE ID = '{$id}'");
		
        $results = $req->fetchObject();
		
        return $results;
    }
	
	function add_runner($first_name, $last_name, $birth_date, $gender) {
        global $db;
        $r = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'birth_date' => $birth_date,
				'gender' => $gender
        );
		
        $sql = 'INSERT INTO runner(FirstName, LastName, DateOfBirth, Gender) VALUES(:first_name, :last_name, :birth_date, :gender)';
        $req = $db->prepare($sql);
        $req->execute($r);
    }
	
	function edit_runner($id, $first_name, $last_name, $birth_date) {
        global $db;
        $r = array(
                'id' => $id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'birth_date' => $birth_date,
        );
		
		
        $sql = 'UPDATE runner SET FirstName = :first_name, LastName = :last_name, DateOfBirth = :birth_date WHERE ID = :id';
        $req = $db->prepare($sql);
        $req->execute($r);
    }
	
	function runner_exists($id) 
	{
        global $db;

        $e = array(
            'id' => $id
        );

        $sql = "SELECT * FROM runner WHERE ID = :id";
        $req = $db->prepare($sql);
        $req->execute($e);

        $exist = $req->rowCount($sql);
		
        return($exist);
    }
	
	function search_runner($keyword) 
	{
		global $db;

        $req = $db->query("SELECT * FROM runner WHERE CONCAT(FirstName, ' ', LastName) LIKE '%{$keyword}%'");

		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }

        return $results;
	}
	
	
	/* RACE RUNNER*/
	
	function get_race_runner($id_runner, $id_race) 
	{
		global $db;
		
        $e = array(
            'id_runner' => $id_runner,
            'id_race' => $id_race
        );

        $sql = "SELECT * FROM race_runner WHERE Runner = :id_runner AND Race = :id_race";
        $req = $db->prepare($sql);
        $req->execute($e);

		$result = $req->fetchObject();
		
        return $result;
	}
	
	function get_race_runners($id_race) 
	{
		global $db;
		
        $e = array(
            'id_race' => $id_race
        );

        $sql = "SELECT * FROM race_runner WHERE Race = :id_race";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	
	function get_last_race_runner($id) 
	{
		global $db;
		
        $e = array(
            'id' => $id
        );

        $sql = "SELECT * FROM race_runner WHERE Runner = :id";
        $req = $db->prepare($sql);
        $req->execute($e);

		$result = $req->fetchObject();
		
        return $result;
	}
	
	function get_races_runner($id) 
	{
		global $db;
		
        $e = array(
            'id' => $id
        );

        $sql = "SELECT * FROM race_runner WHERE Runner = :id";
        $req = $db->prepare($sql);
        $req->execute($e);

		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_race_runner_class($id_runner, $id_race) 
	{
		global $db;
		
        $e = array(
            'id_runner' => $id_runner,
            'id_race' => $id_race
        );

        $sql = "SELECT * FROM race_runner WHERE Runner = :id_runner AND Race = :id_race";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$class = $req->fetch()['Class'];
		
		$e = array(
            'class' => $class
        );

        $sql = "SELECT * FROM class WHERE ID = :class";
        $req = $db->prepare($sql);
        $req->execute($e);

		$result = $req->fetchObject();
		
        return $result;
	}	
	
	function get_race_runner_team($id_runner, $id_race) 
	{
		global $db;
		
        $e = array(
            'id_runner' => $id_runner,
            'id_race' => $id_race
        );

        $sql = "SELECT * FROM race_runner WHERE Runner = :id_runner AND Race = :id_race";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$team = $req->fetch()['Club'];
		
		$e = array(
            'team' => $team
        );

        $sql = "SELECT * FROM club WHERE ID = :team";
        $req = $db->prepare($sql);
        $req->execute($e);

		$result = $req->fetchObject();
		
        return $result;
	}
	
	function get_race_class_genders($id_race) 
	{
		global $db;
		
        $e = array(
            'id_race' => $id_race
        );

        $sql = "SELECT DISTINCT Gender FROM class WHERE ID IN (SELECT DISTINCT Class FROM race_runner WHERE Race = :id_race)";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetch()['Gender']) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}	
	
	function get_race_class_gender_distances($id_race, $class_gender) 
	{
		global $db;
		
        $e = array(
            'id_race' => $id_race,
            'class_gender' => $class_gender
        );

        $sql = "SELECT DISTINCT Distance FROM class WHERE Gender = :class_gender AND ID IN (SELECT DISTINCT Class FROM race_runner WHERE Race = :id_race)";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetch()['Distance']) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_timestamps($id_runner, $id_race) 
	{
		global $db;
		
        $e = array(
            'id_runner' => $id_runner,
            'id_race' => $id_race
        );

        $sql = "SELECT * FROM timestamp WHERE Runner = :id_runner AND Race = :id_race ORDER BY Timestamp DESC";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$results = array();
		
        while($rows = $req->fetchObject()) 
		{
            $results[] = $rows;
        }
		
		return $results;
	}
	
	function get_station($id_station) 
	{
		global $db;
		
        $e = array(
            'id_station' => $id_station
        );

        $sql = "SELECT * FROM station WHERE ID = :id_station";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetchObject();

		return $result;
	}
		
	function get_number_laps($id_runner, $id_race, $timestamp, $station)
	{
		global $db;
		
        $e = array(
            'id_runner' => $id_runner,
            'id_race' => $id_race,
            'timestamp' => $timestamp,
            'station' => $station
        );

        $sql = "SELECT COUNT(Timestamp) AS Count FROM timestamp WHERE Runner = :id_runner AND Race = :id_race AND Station = :station AND Timestamp < :timestamp";
        $req = $db->prepare($sql);
        $req->execute($e);
		
		$result = $req->fetch()['Count'];

		return $result;
	}
						
?>

