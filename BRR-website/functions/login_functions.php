<?php
	/* LOGIN FUNCTIONS */
	
	// Function that returns 1 if an administrator is connected and 0 if not.
    function is_logged() {
        if(isset($_SESSION['admin'])) {
            return 1;
        }
        else {
            return 0;
        }
    }
	
	// Function that returns 1 if a combination of username and password match an actual administrator's account and 0 if not.
	function does_admin_exist($username, $password) {
        global $db;
		
        $var = array(
                'username' => $username
        );

        $sql = "SELECT * FROM administrator WHERE Username = :username";
        $req = $db->prepare($sql);
        $req->execute($var);
		
		$hash = $req->fetch()['Password'];

		if(password_verify($password, $hash)) {
			return 1;
		}
		
		else {
			return 0;
		}
    }
	
	// Function that changes the password of an administrator's account.
	function edit_admin($username, $password) {
        global $db;
		
        $var = array(
                'username' => $username,
                'password' => $password
        );

        $sql = "UPDATE administrator SET Password = :password WHERE Username = :username";
        $req = $db->prepare($sql);
        $req->execute($var);
    }
?>