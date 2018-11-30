<?php
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
	
	function add_race($name, $date) {
        global $db;
        $r = array(
                'name' => $name,
                'length' => 521,
                'date' => $date,
				'class' => 'man-20'
        );
		
        $sql = 'INSERT INTO race(Name, Length, Date, Class) VALUES(:name, :length, :date, :class)';
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

        $sql = "SELECT * FROM runner WHERE ID = :id ";
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
?>

