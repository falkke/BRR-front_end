<?php
	//require_once(); 
	function getPunches($unitId, $cc){
		
		date_default_timezone_set('Europe/Stockholm');
		$date = date("Y-m-d");
		$time = date("H:i:s");
		$database_connection = initiateDatabaseConnection();
		$timestamps_prepped_for_DB = array();
		
		//Will be set to the last punch ID in the $array_of_punches_from_API array and used to update the station
		//table in the database.
		$newest_last_punch_id = 0; 
										   
		if ($database_connection) {
						
			$array_of_punches_from_API = fetchPunchesFromAPI($unitId, $date, $time, $database_connection);
			$stations = fetchStationsFromDatabase($database_connection);
			$admin_station = identifyStation($stations, -1); //-1 is the length from start, and since this is the admin station it has no real length
			
			$punches_from_DB = fetchAllPunchesFromDatabaseAtSpecificStation(($date . " " . $time), $unitId, $database_connection);
			
			/*$original_punches_from_DB is needed so that it can be merged with $timestamps_prepped_for_DB
			  before adding them to the database. This is because the punches that are already stored in the 
			  database might need to have their place value updated based on the new punches. $punches_from_DB 
			  can not be used for this purpose as it is dynamically extended with the punches prepared for the
			  database so that they are taken into account when doing validations in the on-going foreach iterations.
			  
			  NOTE TO SEABASS: Jag var trött när jag skrev detta, men jag tror kanske att vi bara behöver $punches_from_DB
							   eftersom att den arrayen till slut innehåller alla punches från databasen samt alla punches som har blivit
							   förberedda för databasen, vilket en merge av $timestamps_prepped_for_DB och $original_punches_from_DB
							   uppnår.
			  */
			$original_punches_from_DB = $punches_from_DB; 
				
			foreach ($array_of_punches_from_API as $punch) {
		
				//Splits a single punch into an array consisting of its internal data (PunchId;ControlCode;SINumber;SITime)
				$punch = explode(";", $punch);
				
				$newest_last_punch_id = $punch[0];
				
				$station_correlated_with_CC = checkWhichStationTimestampShouldBeRegisteredAt($punch[1], $stations); //$punch[1] == Control Code			
										
				//Admin control code. Update status of runner
				if($punch[1] == $admin_station['CC']){
					adminCCupdateValuesInRaceRunnerTable(($date . " " . $time), $punch, $database_connection, $stations);					
				}				
						
				//Punch is at the correct station
				else if($station_correlated_with_CC == $unitId) {
				
					$timestamps_for_SIunit = filterPunchesFromDBOnSIUnitFromTheAPIpunch($punches_from_DB, $punch, $database_connection);						
					$validation_code = timestampIntervalValidations($punch, $timestamps_for_SIunit);		
					$res = validationCodeActions($validation_code, $timestamps_for_SIunit, $punch, $unitId, $database_connection, $stations);

					
					if($res != NULL){

						$timestamps_prepped_for_DB = array_merge($timestamps_prepped_for_DB, $res);
						//Also adds it to $punches_from_DB[] so that the next loop iteration takes it into account
						//when filtering
						$punches_from_DB[] = array('Timestamp' => $punch[3], 'SI_unit' => $punch[2], 'Runner'
						       => $timestamps_for_SIunit[0]['Runner'], 'Station' => $unitId,
						       'Race' => $timestamps_for_SIunit[0]['Race'], 'RaceInstance' => $timestamps_for_SIunit[0]['RaceInstance'],
							   'Place' => NULL, 'Lap' => $timestamps_prepped_for_DB[count($timestamps_prepped_for_DB)-1]['Lap']);
					}
				}
			
				//Punch is at the wrong station, create a temporary special case punches_from_DB to handle it
				else {					

					$special_case_punches_from_DB = fetchAllPunchesFromDatabaseAtSpecificStation(($date . " " . $time), $station_correlated_with_CC, $database_connection);
					$special_timestamps_for_SIunit = filterPunchesFromDBOnSIUnitFromTheAPIpunch($special_case_punches_from_DB, $punch, $database_connection);						
					$validation_code = timestampIntervalValidations($punch, $special_timestamps_for_SIunit);
					
					$res = validationCodeActions($validation_code, $special_timestamps_for_SIunit, $punch, $station_correlated_with_CC, $database_connection, $stations);
					if($res != NULL){
						addPunchesToDatabase(mergeAndSortTimestampsPreppedForDBandPunchesFromDB($res, $special_case_punches_from_DB), $database_connection);
					}					
				}					
			}
			
			
			addPunchesToDatabase(mergeAndSortTimestampsPreppedForDBandPunchesFromDB($timestamps_prepped_for_DB, $original_punches_from_DB), $database_connection);
			updateLastPunchIdInTheStationTable($newest_last_punch_id, $unitId, $database_connection);
		}
		else {
			echo ("Was unable to open up a connection to the database!" . "<br>" . "<br>");
		}
																							
		/*CLOSE DB CONNECTION*/
		$database_connection = NULL;
	}
	
	function fetchPunchesFromAPI($unitId, $date, $time, $database_connection){
		
		$sql = "SELECT LastID FROM station WHERE ID = ?";
		$stmt = $database_connection->prepare($sql);
		$stmt->execute(array($unitId));
		$lastId = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$lastId = $lastId[0]['LastID'];
		$stmt = NULL;
		
		//$date = "2019-01-13";
		//$time = "12:00:00";
		
		//$url = "http://roc.olresultat.se/getpunches.asp?unitId=" . $unitId . "&lastId=" . $lastId . "&date=" . $date . "&time=" . $time;
		//$url = "http://localhost:81/GetPunches/test/?unitId=" . $unitId . "&lastId=" . $lastId . "&date=" . $date . "&time=" . $time;
		$url = "http://localhost/brr/test/?unitId=" . $unitId . "&lastId=" . $lastId;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$res = curl_exec($ch);
		curl_close($ch);
		//Splits the string, $res, by every '\r\n' into an array of punches.
		$res = explode("\r\n", $res);
		$res = array_filter($res);
		
		//var_dump($res);
		//echo "<br>" . "<br>";
		
		return $res;
	}
	
	function initiateDatabaseConnection(){
		
		/*******************************************************/
		/******************** DATABASE INFO ********************/
		/**/											     /**/
		/**/	   $uName = "group5@s243341";				 /**/
		/**/	   $pWord = "BlackRiver2019";				 /**/
		/**/	   $host = "mysql679.loopia.se";			 /**/
		/**/	   $dbName = "sebastianoveland_com_db_1";	 /**/
		/**/											     /**/
		/*******************************************************/
		/*******************************************************/
		
		$connection = new PDO('mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8', $uName, $pWord);
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		$connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		return $connection;
	}
	
	function fetchStationsFromDatabase($database_connection){
		
		$sql1 = "SELECT * FROM `station`" ;
		$stmt = $database_connection->prepare($sql1);
		$stmt->execute();
		$stations = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = NULL;
		return $stations;
	}
	
	function identifyStation($stations, $lenght_to_check){
		foreach($stations as $s){

			if($s['LengthFromStart'] == $lenght_to_check){
				$data = array('CC' => $s['Code'], 'ID' => $s['ID']);
				return $data;
			}
		}
		
		echo ("No station ID was found!") . "<br>";
		echo ("Please notify an administrator.") . "<br>";
		die();
	}
	
	function fetchAllPunchesFromDatabaseAtSpecificStation($date, $unitId, $database_connection){
		$race_date = strtotime($date);
		$race_date_minus_24h = date("Y-m-d H:i:s",$race_date - 86400); //86400 seconds = 24h
		$race_date_plus_24h = date("Y-m-d H:i:s",$race_date + 86400);
				
		$sql1 = "SELECT * FROM `timestamp` WHERE Station = ? AND Timestamp Between ? AND ? ORDER BY Timestamp ASC";
		
		$stmt = $database_connection->prepare($sql1);
		$stmt->execute(array($unitId, $race_date_minus_24h, $race_date_plus_24h));
		$punches_from_DB = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = NULL;
		return $punches_from_DB;
	}
		
	function checkWhichStationTimestampShouldBeRegisteredAt($control_code, $stations){
		
		foreach($stations as $s){
			if($s['Code'] == $control_code){
				return $s['ID'];
			}
		}
		
		echo ("No matching station was found for the control code: ") . $control_code . "<br>";
		echo ("Please notify an administrator.") . "<br>";
		die();

	}
	
	function filterPunchesFromDBOnSIUnitFromTheAPIpunch($punches_from_DB, $timestamp, $database_connection){
		$timestamps_for_SIunit = NULL;
		
		if($punches_from_DB != NULL){
			
			//Fetch the runner related to the timestamp
			$sql = "SELECT DISTINCT Runner FROM `runner_units` WHERE SI_unit = ?"; //$timestamp[2] = SInumber
			$stmt = $database_connection->prepare($sql);
			$stmt->execute(array($timestamp[2]));
			$runner = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = NULL;
			
			if($runner != NULL){		
				foreach($punches_from_DB as $row){
					if($runner[0]['Runner'] == $row['Runner']){ 
						$data = array('Timestamp' => $row['Timestamp'], 'SI_unit' => $row['SI_unit'],
									  'Runner' => $row['Runner'], 'Station' => $row['Station'],
									  'Race' => $row['Race'], 'RaceInstance' => $row['RaceInstance'],
									  'Place' => $row['Place'], 'Lap' => $row['Lap']);
						$timestamps_for_SIunit[] = $data;
					}
				}	
			}
			
		}
		
		if($punches_from_DB == NULL || $timestamps_for_SIunit == NULL){
			$SINumber = (int)$timestamp[2];
			$sql1 = "SELECT Runner, Race, RaceInstance FROM
						      		(SELECT T1.Runner, RaceInstance FROM (SELECT Runner, RaceInstance FROM race_runner) AS T1
						      	   INNER JOIN
						      		(SELECT Runner FROM runner_units WHERE SI_unit = ?) AS T2
						      	   ON T1.Runner = T2.Runner) AS T3
						      	   INNER JOIN
						      		(SELECT Race, ID, Class FROM race_instance) AS T4
						      	   ON T4.ID = T3.RaceInstance";
			$stmt = $database_connection->prepare($sql1);
			$stmt->execute(array($SINumber));
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$timestamps_for_SIunit[] = array('Timestamp' => NULL, 'Runner' => $res[0]['Runner'], 'Race' => $res[0]['Race'], 'RaceInstance' => $res[0]['RaceInstance']);
			$stmt = NULL;
			echo "<br><br>";
		}
			
		return $timestamps_for_SIunit;
	}
	
	function timestampIntervalValidations($timestamp, $timestamps_for_SIunit){
		$five_second_check = timeIntervalSameStationCheck($timestamp[3], $timestamps_for_SIunit, 300);
		
						
		if($five_second_check == true){
			return 84341;
		}
		else{
			$fourtyMinuteCheck = timeIntervalSameStationCheck($timestamp[3], $timestamps_for_SIunit, 2400);
		
			if($fourtyMinuteCheck == true){
				return 98132;
			}
			else{
			    return 11132;
			}
		}
	}
	
	function timeIntervalSameStationCheck($time, $old_timestamps, $seconds_to_check){
		$newTime = strtotime($time);
		if($old_timestamps != null){
			foreach($old_timestamps as $ts){
				$oldTime = strtotime($ts["Timestamp"]);
				$diff = abs($oldTime - $newTime);

				if ($diff <= $seconds_to_check){
						return true;
				}
			}
		}
		return false;
	}
	
	function validationCodeActions($validation_code, $timestamps_for_SIunit, $timestamp, $unitId, $database_connection, $all_stations){
		
		if($validation_code == 11132){
			$timestamps_prepped_for_DB = prepareTimestampForDatabase($timestamps_for_SIunit, $timestamp, $unitId, $database_connection, $all_stations);				
			return $timestamps_prepped_for_DB;
		}						
		else if($validation_code == 84341){
			$runner = $timestamps_for_SIunit[0]['Runner'];
			$race = $timestamps_for_SIunit[0]['Race'];
			$race_instance = $timestamps_for_SIunit[0]['RaceInstance'];
			addToInvalidPunchesTable($timestamp, $unitId, $runner, $race, $race_instance, 1, $database_connection);
		}
		else if($validation_code == 98132){
			$runner = $timestamps_for_SIunit[0]['Runner'];
			$race = $timestamps_for_SIunit[0]['Race'];
			$race_instance = $timestamps_for_SIunit[0]['RaceInstance'];
			addToInvalidPunchesTable($timestamp, $unitId, $runner, $race, $race_instance, 2, $database_connection);
		}
	}
	
	function prepareTimestampForDatabase($timestamps_for_SIunit, $timestamp, $unitId, $database_connection, $all_stations){									
		
		$lap = updateWhichLap($unitId, $timestamps_for_SIunit, $all_stations, $database_connection);

		$sql = "SELECT Class FROM race_instance WHERE ID = ?";
		$stmt = $database_connection->prepare($sql);
		$stmt->execute(array($timestamps_for_SIunit[0]['RaceInstance']));
		$class = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$number_of_laps = numberOfLapsInRace($class[0]['Class']);
		if($lap > $number_of_laps){
			addToInvalidPunchesTable($timestamp, $unitId, $timestamps_for_SIunit[0]['Runner'], 
									 $timestamps_for_SIunit[0]['Race'], $timestamps_for_SIunit[0]['RaceInstance'],
									 3, $database_connection);
			return;
		}
		
		$timestamps_prepped_for_DB[] = array('Timestamp' => $timestamp[3], 'SI_unit' => $timestamp[2], 'Runner'
								     => $timestamps_for_SIunit[0]['Runner'], 'Station' => $unitId, 'Race' 
									 => $timestamps_for_SIunit[0]['Race'], 'RaceInstance' => $timestamps_for_SIunit[0]['RaceInstance'],
									 'Place' => NULL, 'Lap' => $lap);	
				
		return $timestamps_prepped_for_DB;
	}

	function updateWhichLap($station, $timestamps_for_SIunit, $all_stations, $database_connection){
		
		$first_station = identifyStation($all_stations, 0);

		//If there are no previous timestamps for the runner at the station
		if($timestamps_for_SIunit[0]['Timestamp'] == NULL){
			//If it's the first station it returns 2 because it wont register for the first lap
			if($station == $first_station['ID']){
				return 2;
			}

			return 1;
		}
		else if($station == $first_station['ID']){
			$sql = "SELECT MAX(Lap) FROM `timestamp` WHERE Runner = ? AND Race = ?";
			$stmt = $database_connection->prepare($sql);
			$stmt->execute(array($timestamps_for_SIunit[0]['Runner'], $timestamps_for_SIunit[0]['RaceInstance']));
			$max_lap_for_runner_over_all_stations = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = NULL;			
			$max_lap_in_timestamps_for_SIunit = max(array_column($timestamps_for_SIunit, 'Lap'));
			
			if($max_lap_for_runner_over_all_stations[0]['MAX(Lap)'] > $max_lap_in_timestamps_for_SIunit){
				return $max_lap_for_runner_over_all_stations + 1;
			}
			else{
				return $max_lap_in_timestamps_for_SIunit + 1;
			}			
		}
		else if($station != $first_station['ID']){
			$sql = "SELECT MAX(Lap) FROM `timestamp` WHERE Runner = ? AND Race = ?";
			$stmt = $database_connection->prepare($sql);
			$stmt->execute(array($timestamps_for_SIunit[0]['Runner'], $timestamps_for_SIunit[0]['RaceInstance']));
			$max_lap_for_runner_over_all_stations = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = NULL;			
			$max_lap_in_timestamps_for_SIunit = max(array_column($timestamps_for_SIunit, 'Lap'));
			$current_number_of_timestamps_at_station_for_runner = count(array_column($timestamps_for_SIunit, 'Timestamp'));
			
			//We might not need $max_lap_in_timestamps_for_SIunit...
			
			if($max_lap_for_runner_over_all_stations[0]['MAX(Lap)'] >= $max_lap_in_timestamps_for_SIunit){
				if($max_lap_for_runner_over_all_stations[0]['MAX(Lap)'] > $current_number_of_timestamps_at_station_for_runner){
					return $max_lap_for_runner_over_all_stations[0]['MAX(Lap)'] + 1;
				}
				else{
					return $current_number_of_timestamps_at_station_for_runner + 1;
				}
			}
			else if($max_lap_in_timestamps_for_SIunit > $max_lap_for_runner_over_all_stations[0]['MAX(Lap)']){
				if($max_lap_in_timestamps_for_SIunit > $current_number_of_timestamps_at_station_for_runner){
					return $max_lap_in_timestamps_for_SIunits + 1;
				}
				else{
					return $current_number_of_timestamps_at_station_for_runner + 1;
				}
			}
		}
		return 0;
	}

	function addToInvalidPunchesTable($timestamp, $unitId, $runner, $race, $race_instance, $error_code, $database_connection){
		$sql = "INSERT INTO invalid_punches(Timestamp, SI_unit, Runner, Station, Race, RaceInstance, DiscardReason)
				VALUES(?, ?, ?, ?, ?, ?, ?)";
		$stmt = $database_connection->prepare($sql);
		$stmt->execute(array($timestamp[3], $timestamp[2], $runner, $unitId, $race, $race_instance, $error_code));
		$stmt = NULL;
	}
	
	function mergeAndSortTimestampsPreppedForDBandPunchesFromDB($timestamps_prepped_for_DB, $punches_from_DB){
		if($punches_from_DB != NULL){
			//The punches that are already in the database might have to have their
			//'Place' value updated when new punches are to be inserted, therefor
			//they're also placed in the $timestamps_prepped_for_DB array
			$timestamps_prepped_for_DB = array_merge($timestamps_prepped_for_DB, $punches_from_DB);
		}
		
		//Sorts the $timestamps_prepped_for_DB array, first by Lap and then by Timestamp.
		array_multisort((array_column($timestamps_prepped_for_DB, 'RaceInstance')), (array_column($timestamps_prepped_for_DB, 'Lap')), SORT_ASC, (array_column($timestamps_prepped_for_DB, 'Timestamp')), SORT_ASC, $timestamps_prepped_for_DB);
		return $timestamps_prepped_for_DB;
	}
	
	function adminCCupdateValuesInRaceRunnerTable($date, $timestamp, $database_connection, $stations){
		
		$sql_runner_race_class = "SELECT Runner, Race, Class, ID, StartTime FROM
						      		(SELECT T1.Runner, RaceInstance FROM (SELECT Runner, RaceInstance FROM race_runner) AS T1
						      	   INNER JOIN
						      		(SELECT Runner FROM runner_units WHERE SI_unit = ?) AS T2
						      	   ON T1.Runner = T2.Runner) AS T3
						      	   INNER JOIN
						      		(SELECT Race, ID, Class, StartTime FROM race_instance) AS T4
						      	   ON T4.ID = T3.RaceInstance";
								   
		$stmt = $database_connection->prepare($sql_runner_race_class);
		$stmt->execute(array($timestamp[2]));
		$runner_race_class = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = NULL;
		
		$runner = $runner_race_class[0]['Runner'];
		$race = $runner_race_class[0]['Race'];
		$race_instance = $runner_race_class[0]['ID'];
		$race_start_time = $runner_race_class[0]['StartTime'];
		
		$fetch_all_punches_from_DB_for_specific_runner = fetchAllPunchesForSpecificRunner($date, $runner, $database_connection);

		
	
		if($fetch_all_punches_from_DB_for_specific_runner == NULL){
			//Sets status to DNS
			$sql = "UPDATE race_runner SET Status = 'DNS' WHERE Runner = ? AND RaceInstance = ?";
			$stmt = $database_connection->prepare($sql);
			$stmt->execute(array($runner, $race_instance));
			$stmt = NULL;
			//Also updates Place and TotalTime			
			updatePlaceAndTotalTimeInRaceRunnerTable($race_instance, $race, $race_start_time, true, $database_connection);
			return;
		}
	
		
		$lapsRan = max(array_column($fetch_all_punches_from_DB_for_specific_runner, 'Lap'));
		//$lapsRan = count(array_column($punches_from_DB_at_fourth_station_for_specific_runner, 'Lap'));
		$class = $runner_race_class[0]['Class'];
		$lapsInRace = numberOfLapsInRace($class);
		
		//echo "Runner: ". ($runner) . " " . ($lapsRan) . "<" . ($lapsInRace) . "<br>" . "<br>";
		
		if($lapsRan < $lapsInRace){
			//Sets status to DNF
			$sql = "UPDATE race_runner SET Status = 'DNF' WHERE Runner = ?' AND RaceInstance = ?";
			$stmt = $database_connection->prepare($sql);
			$stmt->execute(array($runner, $race_instance));
			$stmt = NULL;
			//Also updates Place and TotalTime
			updatePlaceAndTotalTimeInRaceRunnerTable($race_instance, $race, $race_start_time, false, $database_connection);
			return;
		}
		
		else{
			//Sets status to finished
			$sql = "UPDATE race_runner SET Status = 'Finished' WHERE Runner = ? AND RaceInstance = ?";
			$stmt = $database_connection->prepare($sql);
			$stmt->execute(array($runner, $race_instance));
			$stmt = NULL;
			//Also updates Place and TotalTime
			updatePlaceAndTotalTimeInRaceRunnerTable($race_instance, $race, $race_start_time, false, $database_connection);
			return;
		}
	}
	
	function updatePlaceAndTotalTimeInRaceRunnerTable($race_instance, $race, $race_start_time, $DNS_status, $database_connection){
		
		if($DNS_status == false){
			$sql = "SELECT Runner, TS, Station, LengthFromStart, Lap FROM
					(SELECT Runner, MAX(Timestamp) AS 'TS' 
					 FROM timestamp
					 WHERE Race = ? GROUP BY Runner) AS T1
				   INNER JOIN
					(SELECT Timestamp, Station, Lap, Place 
					 FROM timestamp) AS T2
				   ON T1.TS = T2.Timestamp
				   INNER JOIN
					(SELECT ID, LengthFromStart FROM station) AS T3
				   ON T2.station = T3.ID
				ORDER BY LAP DESC, LengthFromStart DESC, TS ASC";
				
			$stmt = $database_connection->prepare($sql);
			$stmt->execute(array($race));
			$res1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = NULL;
			
			$sql = "SELECT * FROM race_runner WHERE RaceInstance = ?";
			$stmt = $database_connection->prepare($sql);
			$stmt->execute(array($race_instance));
			$res2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = NULL;
			
			$sql = "SELECT Date FROM race WHERE ID = ?";
			$stmt = $database_connection->prepare($sql);
			$stmt->execute(array($race));
			$race_date = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = NULL;
			
			$place_counter = 1;
			foreach($res1 as $row1){
				foreach($res2 as $row2){
					if($row1['Runner'] == $row2['Runner']){
						$total_time = strtotime($row1['TS']) - strtotime(($race_date[0]['Date'] . $race_start_time));
						$total_time = gmdate("H:i:s", $total_time);
						$sql = "UPDATE race_runner SET Place = :PC, TotalTime = :TT WHERE RaceInstance = :RI AND Runner = :RUNNER";
						$stmt = $database_connection->prepare($sql);
						$stmt->bindValue(':PC', $place_counter);
						$stmt->bindValue(':TT', $total_time);
						$stmt->bindValue(':RI', $race_instance);
						$stmt->bindValue(':RUNNER', $row1['Runner']);
						$stmt->execute();
						$stmt = NULL;
						$place_counter++;
						break;
					}
				}
			}
		}
		
		//This code needs to be run whether the status is DNS, DNF or Finished
		
		$sql = "SELECT * FROM race_runner WHERE RaceInstance = ? AND Status = 'DNS' ORDER BY Bib";
		$stmt = $database_connection->prepare($sql);
		$stmt->execute(array($race_instance));
		$res3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = NULL;
		
		$sql = "SELECT MAX(Place) AS MP FROM race_runner WHERE RaceInstance = ? AND Status != 'DNS'";
		$stmt = $database_connection->prepare($sql);
		$stmt->execute(array($race_instance));
		$res4 = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = NULL;
		$place_counter = $res4[0]['MP'] + 1;
		foreach($res3 as $row){
			$sql = "UPDATE race_runner SET Place = ?, TotalTime = '00:00:00' WHERE Runner = ? AND RaceInstance = ?";
			$stmt = $database_connection->prepare($sql);
			$stmt->execute(array($place_counter, $row['Runner'], $race_instance));
			$stmt = NULL;
			$place_counter++;
		}		
	}
	
	function fetchAllPunchesForSpecificRunnerAtSpecificStation($date, $timestamp, $database_connection, $stations, $lenght_to_check){
		$race_date = strtotime($date);
		$race_date_minus_24h = date("Y-m-d H:i:s",$race_date - 86400); //86400 seconds = 24h
		$race_date_plus_24h = date("Y-m-d H:i:s",$race_date + 86400);
		$station = identifyStation($stations, $lenght_to_check);
		$sql1 = "SELECT * FROM `timestamp` WHERE Station = ? AND SI_unit = ? AND Timestamp Between ? AND ? ORDER BY Timestamp ASC";
		
		$stmt = $database_connection->prepare($sql1);
		$stmt->execute(array($station['ID'], $timestamp[2], $race_date_minus_24h, $race_date_plus_24h));
		$punches_from_DB_at_specific_station = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $punches_from_DB_at_specific_station;
	}
	
	function fetchAllPunchesForSpecificRunner($date, $runner, $database_connection){

		$race_date = strtotime($date);
		$race_date_minus_24h = date("Y-m-d H:i:s",$race_date - 86400); //86400 seconds = 24h
		$race_date_plus_24h = date("Y-m-d H:i:s",$race_date + 86400);
				
		$sql1 = "SELECT * FROM `timestamp` WHERE Runner = ? AND Timestamp Between ? AND ? ORDER BY Timestamp ASC";
		
		$stmt = $database_connection->prepare($sql1);
		$stmt->execute(array($runner, $race_date_minus_24h, $race_date_plus_24h));
		$all_punches_from_DB_for_specific_runner = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $all_punches_from_DB_for_specific_runner;
	}
	
	function numberOfLapsInRace($class){
		switch($class){
			case 1020:
				return 2;
			case 2020:
				return 2;
			case 1050:
				return 5;
			case 2050:
				return 5;
			case 1100:
				return 10;
			case 2100:
				return 10;
		}
	}
	
	function addPunchesToDatabase($timestamps_prepped_for_DB, $database_connection){
				
		if($timestamps_prepped_for_DB != NULL){
			
			$lap = 1;
			$place_counter = 1;
			$race_instance_check = $timestamps_prepped_for_DB[0]['RaceInstance'];
			
			foreach($timestamps_prepped_for_DB as $punch){		
				if($punch != NULL){
				//Since the $timestamps_prepped_for_DB array has been sorted first by RaceInstance,
				//then by Lap and finally by Timestamp, the place counter has to be reset to 1 every time 
				//the timestamps for the next lap is encountered so that the place value is correct,
				//aswell as when a new race_instance is processed.
					if($punch['Lap'] > $lap || ($race_instance_check != $punch['RaceInstance'])){
						$lap = $punch['Lap'];
						$place_counter = 1;
					}
				
					//Inserts the timestamp into the database, with all of the corresponding values
					$sql = "CALL insertTimestamp(:time_and_date, :si_unit, :runner, :station, :race, :race_instance, :place, :lap)";
					$stmt = $database_connection->prepare($sql);
					$stmt->bindValue(':time_and_date', $punch['Timestamp']);
					$stmt->bindValue(':si_unit', $punch['SI_unit']);
					$stmt->bindValue(':runner', $punch['Runner']);
					$stmt->bindValue(':station', $punch['Station']);
					$stmt->bindValue(':race', $punch['Race']);
					$stmt->bindValue(':race_instance', $punch['RaceInstance']);
					$stmt->bindValue(':place', $place_counter);
					$stmt->bindValue(':lap', $punch['Lap']);
					$stmt->execute();
								
					$place_counter++;
					$race_instance_check = $punch['RaceInstance'];
				}		
			}
		}
				
		$stmt = NULL;	
	}
	
	function updateLastPunchIdInTheStationTable($punchID, $station, $database_connection){
		$sql = "UPDATE station SET LastID = ? WHERE ID = ?"; 
		$stmt = $database_connection->prepare($sql);
		$stmt->execute(array($punchID, $station));
		$stmt = NULL;
	}
	

	$station = $_GET['unitId'];
	$controlcode = $_GET['controlcode'];

	getPunches($station, $controlcode);
	
