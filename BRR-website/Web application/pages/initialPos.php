<?php
	date_default_timezone_set('Europe/Stockholm');

	$runner_id = $_GET['runner'];
	$race_id = $_GET['race'];

	$max=0;
	$min=0;
	
	$Station;
	$stationTime = 0;
	
	$currentTime = new DateTime();
	$currentTime = $currentTime->format('Y-m-d H:i:s');
	
	//kilometers per second
	$velocity = 0.1020408163265306;
	
	$var = array(
		'runner_id' => $runner_id,
		'race_id' => $race_id
	);

	$sql = "SELECT Station, Timestamp FROM timestamp WHERE Timestamp = (
			SELECT MAX(Timestamp) FROM timestamp WHERE Runner = :runner_id AND RaceInstance = (
				SELECT ID FROM race_instance WHERE Race = :race_id
			)
		)
	";
	$req = $db->prepare($sql);
	$req->execute($var);
	

	if($req->rowCount($sql) > 0) {
		// output data of each row	
		$row = $req->fetchObject();
		$Station=$row->Station;
		$stationTime=$row->Timestamp;

		$index = stationIndex($Station);
		
		//current position
		if($index == 0){
			$index = 1;
		}
		
		//$stationTime='2019-01-10 14:20:00';
		
		$distance = round($velocity * sub_time($currentTime, $stationTime) / ($index * 1000));
		
		$max= (int) $distance + $index;
	}

	function sub_time($currentTime, $stationTime) {
		$to_time = strtotime($currentTime);
		$from_time = strtotime($stationTime);
		return round(abs(($to_time - $from_time) / 60) * 60, 2);
	}
	
	function stationIndex($station){
		switch($station){
			case 'b827eb53318a': return 0;
			break;
			
			case 'b827ebeb6d39': return 161;
			break;
			
			case 'b827eba42979': return 402;
			break;
			
			case 'b827ebdcbd98': return 375;
			break;
			
			default: return 0;
			break;
		}
	}
?>