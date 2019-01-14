<?php
	date_default_timezone_set('Europe/Stockholm');

	require "../functions/initialisation_functions.php";
	
	$currentPos=$_POST['currentPos'];
	$runner_id = $_POST['runner'];
	$race_id = $_POST['race'];
	
	$max = 0;
	$min = 0;

	$velocity = 0.1020408163265306;
	
	$Station;
	$stationTime = 0;

	$currentTime = new DateTime();
	$currentTime = $currentTime->format('Y-m-d H:i:s');

	$var = array(
		'runner_id' => $runner_id,
		'race_id' => $race_id
	);
	
	$sql = "SELECT Station, Timestamp 
			FROM timestamp 
			WHERE Timestamp = (
				SELECT MAX(Timestamp) 
				FROM timestamp 
				WHERE Runner = :runner_id AND RaceInstance IN (
					SELECT ID 
					FROM race_instance 
					WHERE Race = :race_id
				)
			)
		";
	
	$req = $db->prepare($sql);
	$req->execute($var);
	
	if($req->rowCount($sql) > 0) {	
		$row = $req->fetchObject();
		$Station=$row->Station;
		$stationTime=$row->Timestamp;

		$index = stationIndex($Station);

		switch($Station){
			case 'b827eb53318a':
				$var1 = array(
					'runner_id' => $runner_id,
					'race_id' => $race_id,
					'station' => "b827eba42979"
				);
		
				$sql = "SELECT Timestamp, Station FROM timestamp
						WHERE Timestamp = (
							SELECT MAX(Timestamp) FROM timestamp WHERE Runner = :runner_id AND Station = :station AND RaceInstance = (
								SELECT ID FROM race_instance WHERE Race = :race_id
							)
						)";
						
				$req = $db->prepare($sql);
				$req->execute($var1);
						
				if($req->rowCount($sql) > 0) {
					$row = $req->fetchObject();
					$stationTime1=$row->Timestamp;
				
					$var2 = array(
						'runner_id' => $runner_id,
						'race_id' => $race_id,
						'station' => "b827ebdcbd98"
					);
					
					$req = $db->prepare($sql);
					$req->execute($var2);
					
					if($req->rowCount($sql) > 0) {
						$row = $req->fetchObject();
						$stationTime2=$row->Timestamp;
							
						$velocity = 161 / sub_time($stationTime2, $stationTime1);
						$distance = round($velocity * sub_time($currentTime, $stationTime));
					
						echo $distance;
					}
				}
				
				else {
					$distance = round($velocity * sub_time($currentTime, $stationTime));
					echo $distance;
				}
					
				break;
				
			case 'b827ebeb6d39':
					$var1 = array(
						'runner_id' => $runner_id,
						'race_id' => $race_id,
						'station' => "b827eb53318a"
					);
			
					$sql = "SELECT Timestamp, Station FROM timestamp
							WHERE Timestamp = (
								SELECT MAX(Timestamp) FROM timestamp WHERE Runner = :runner_id AND Station = :station AND RaceInstance = (
									SELECT ID FROM race_instance WHERE Race = :race_id
								)
							)";
							
					$req = $db->prepare($sql);
					$req->execute($var1);
							
					if($req->rowCount($sql) > 0) {
						$row = $req->fetchObject();
						$stationTime1=$row->Timestamp;
					
						$var2 = array(
							'runner_id' => $runner_id,
							'race_id' => $race_id,
							'station' => "b827ebeb6d39"
						);
						
						$req = $db->prepare($sql);
						$req->execute($var2);
						
						if($req->rowCount($sql) > 0) {
							$row = $req->fetchObject();
							$stationTime2=$row->Timestamp;
								
							$velocity = 161 / sub_time($stationTime2, $stationTime1);
							$distance = 161 + round($velocity * sub_time($currentTime, $stationTime2));
						
							echo $distance;
						}
					}
					
					else {
						$distance = 161 + round($velocity * sub_time($currentTime, $stationTime));
						echo $distance;
					}
					
					break;
					
			case 'b827eba42979':
				$var1 = array(
					'runner_id' => $runner_id,
					'race_id' => $race_id,
					'station' => 'b827ebeb6d39'
				);
		
				$sql = "SELECT Timestamp, Station FROM timestamp
						WHERE Timestamp = (
							SELECT MAX(Timestamp) FROM timestamp WHERE Runner = :runner_id AND Station = :station AND RaceInstance = (
								SELECT ID FROM race_instance WHERE Race = :race_id
							)
						)";
						
				$req = $db->prepare($sql);
				$req->execute($var1);
						
				if($req->rowCount($sql) > 0) {
					$row = $req->fetchObject();
					$stationTime1=$row->Timestamp;
				
					$var2 = array(
						'runner_id' => $runner_id,
						'race_id' => $race_id,
						'station' => 'b827eba42979'
					);
					
					$req = $db->prepare($sql);
					$req->execute($var2);
					
					if($req->rowCount($sql) > 0) {
						$row = $req->fetchObject();
						$stationTime2=$row->Timestamp;
						
						$velocity = 402 / sub_time($stationTime2, $stationTime1);
						$distance = 402 + round($velocity * sub_time($currentTime, $stationTime2));
					
						echo $distance;
					}
				}
				
				else {
					$distance = 402 + round($velocity * sub_time($currentTime, $stationTime));
					echo $distance;
				}
				
				break;
		
			case 'b827ebdcbd98':
				$distance = 475;
				echo $distance;
				break;
		}
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