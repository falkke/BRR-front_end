<?php
	if	((!isset($_SESSION['bbr']['search-race']))
	|| 	(!isset($_SESSION['bbr']['search-runner']))
	|| 	(!isset($_SESSION['bbr']['search-team']))
	|| 	(!isset($_SESSION['bbr']['search-si-unit']))
	|| 	(!isset($_SESSION['bbr']['search-station']))
	|| 	(!isset($_SESSION['bbr']['search-category']))) {
		$_SESSION['bbr']['search-race'] = '';
		$_SESSION['bbr']['search-runner'] = '';
		$_SESSION['bbr']['search-team'] = '';
		$_SESSION['bbr']['search-si-unit'] = '';
		$_SESSION['bbr']['search-station'] = '';
		$_SESSION['bbr']['search-category'] = '';
	}

	if($page == "dashboard") {
		if($_GET['list'] == "races") {
			$_SESSION['bbr']['search-runner'] = '';
			$_SESSION['bbr']['search-team'] = '';
			$_SESSION['bbr']['search-si-unit'] = '';
			$_SESSION['bbr']['search-station'] = '';
			$_SESSION['bbr']['search-category'] = '';
		}
		
		else if($_GET['list'] == "runners") {
			$_SESSION['bbr']['search-race'] = '';
			$_SESSION['bbr']['search-team'] = '';
			$_SESSION['bbr']['search-si-unit'] = '';
			$_SESSION['bbr']['search-station'] = '';
			$_SESSION['bbr']['search-category'] = '';
		}
		
		else if($_GET['list'] == "teams") {
			$_SESSION['bbr']['search-race'] = '';
			$_SESSION['bbr']['search-runner'] = '';
			$_SESSION['bbr']['search-si-unit'] = '';
			$_SESSION['bbr']['search-station'] = '';
			$_SESSION['bbr']['search-category'] = '';
		}
		
		else if($_GET['list'] == "si-units") {
			$_SESSION['bbr']['search-race'] = '';
			$_SESSION['bbr']['search-runner'] = '';
			$_SESSION['bbr']['search-team'] = '';
			$_SESSION['bbr']['search-station'] = '';
			$_SESSION['bbr']['search-category'] = '';
		}
		
		else if($_GET['list'] == "stations") {
			$_SESSION['bbr']['search-race'] = '';
			$_SESSION['bbr']['search-runner'] = '';
			$_SESSION['bbr']['search-team'] = '';
			$_SESSION['bbr']['search-si-unit'] = '';
			$_SESSION['bbr']['search-category'] = '';
		}
		
		else if($_GET['list'] == "categories") {
			$_SESSION['bbr']['search-race'] = '';
			$_SESSION['bbr']['search-runner'] = '';
			$_SESSION['bbr']['search-team'] = '';
			$_SESSION['bbr']['search-si-unit'] = '';
			$_SESSION['bbr']['search-station'] = '';
		}
		
		else {		
			$_SESSION['bbr']['search-race'] = '';
			$_SESSION['bbr']['search-runner'] = '';
			$_SESSION['bbr']['search-team'] = '';
			$_SESSION['bbr']['search-si-unit'] = '';
			$_SESSION['bbr']['search-station'] = '';
			$_SESSION['bbr']['search-category'] = '';
		}
	}	

	else if($page == "races") {	
		$_SESSION['bbr']['search-runner'] = '';
		$_SESSION['bbr']['search-team'] = '';
		$_SESSION['bbr']['search-si-unit'] = '';
		$_SESSION['bbr']['search-station'] = '';
		$_SESSION['bbr']['search-category'] = '';
	}

	else if($page == "runners") {	
		$_SESSION['bbr']['search-race'] = '';
		$_SESSION['bbr']['search-team'] = '';
		$_SESSION['bbr']['search-si-unit'] = '';
		$_SESSION['bbr']['search-station'] = '';
		$_SESSION['bbr']['search-category'] = '';
	}

	else if($page == "teams") {	
		$_SESSION['bbr']['search-races'] = '';
		$_SESSION['bbr']['search-runner'] = '';
		$_SESSION['bbr']['search-si-unit'] = '';
		$_SESSION['bbr']['search-station'] = '';
		$_SESSION['bbr']['search-category'] = '';
	}
	
	else {
		$_SESSION['bbr']['search-race'] = '';
		$_SESSION['bbr']['search-runner'] = '';
		$_SESSION['bbr']['search-team'] = '';
		$_SESSION['bbr']['search-si-unit'] = '';
		$_SESSION['bbr']['search-station'] = '';
		$_SESSION['bbr']['search-category'] = '';
	}
?>