<?php

    require_once('station.class.php');
    require_once('runner.class.php');
    require_once('database.inc.php');

    if (isset($_GET['clear']) || isset($_GET['build'])){
        $db = connectToDatabase();
        clearTestData($db);
        $db = null;
        echo 'Testdata cleared.<br />';
        if (isset($_GET['clear']))
            die();
    }

    //TODO: Double punches, DNF, DNS, Runnning backwards
    define("NUM_STATIONS", 4); // Number of stations to run through each lap
    define("MAX_LAPS", 10);
    srand(0xFEE15BAD);

    $build = (isset($_GET['build']) ? true : false);
    $req_pid = (isset($_GET['lastId']) ? $_GET['lastId'] : 0);
    $req_station = (isset($_GET['unitId']) ? $_GET['unitId'] : 0);
    switch($req_station){
        case 'b827eb53318a':
            $req_station = 0;
            break;
        case 'b827ebeb6d39':
            $req_station = 1;
            break;
        case 'b827eba42979':
            $req_station = 2;
            break;
        case 'b827ebdcbd98':
            $req_station = 3;
            break;
        case 'b827eb2d0304':
            $req_station = 4;
            break;
        default:
            break;
    }
    if ($req_station > NUM_STATIONS){
        echo 'Invalid station';
        die();
    }
    $req_date_in = (isset($_GET['date']) ? $_GET['date'] : 0);
    $req_time_in = (isset($_GET['time']) ? $_GET['time'] : 0);
    if ($req_date_in != 0 && $req_time_in != 0){
        $first_time = new DateTime($req_date_in.' '.$req_time_in);
        $last_time = new DateTime($req_date_in.' '.$req_time_in);
        $last_time->add(new DateInterval('PT1H'));
    } else {
        $first_time = null;
    }


    $stations = array(new Station(35), new Station(40), new Station(50), new Station(60), new Station(99));

    function sendToStation($station, ...$data){
        global $stations;
        $stations[$station]->addTimestamp(...$data);
    }
    
    $start_date = '2019-01-10 10:00:00';
    $num_runners = 20;
    $distances = array(2, 5, 10);
    $average = 20; //Average runnning time between stations
    $runners = null;
    $timestamp_collection = null;

    for ($i = 0; $i < $num_runners; ++$i){
        switch($i){ 
            case 0: // Happy route
                $runners[] = new Runner($build, 0, 0, $start_date, $average, MAX_LAPS, 10);
                break;
            case 1: // Delayed sending
                $runners[] = new Runner($build, 20, 0, $start_date, $average, MAX_LAPS, 10);
                break;
            case 2: // Broken and replaced si_unit (including missed stations until start)
                $runners[] = new Runner($build, 0, 5, $start_date, $average, MAX_LAPS, 10);
                break;
            case 3: // Biker
                $runners[] = new Runner($build, 0, 0, $start_date, 1, MAX_LAPS, 10);
                break;
            case 4: // Broke race after 50 miles
                $runners[] = new Runner($build, 0, 0, $start_date, $average, 5, 10);
                break;
            case 5: // Broke race after 70 miles
                $runners[] = new Runner($build, 0, 0, $start_date, $average, 7, 10);
                break;
            case 6: // Broke race after 10 miles
                $runners[] = new Runner($build, 0, 0, $start_date, $average, 1, 10);
                break;
            default: // Random runners some faults may occur
                $runners[] = new Runner($build, 10, 2, $start_date, $average, rand(0, 10), $distances[rand(0, 2)]);
                break;
        }
    }

    if (!$build){
        foreach ($runners as $runner){
            $runner->generateTimestamps();
        }

        // Generate all timestamps for the race
        foreach ($runners as $runner){
            $runner_timestamps = $runner->getTimestamps($req_station);
            if ($runner_timestamps != null)
                foreach($runner->getTimestamps($req_station) as $timestamp)
                    $timestamp_collection[] = $timestamp;
        }

        // Sort the collection with timestamps ascending
        $timestamps = array_column($timestamp_collection, 'Send_time');
        array_multisort($timestamps, SORT_ASC, $timestamp_collection);

        foreach($timestamp_collection as $timestamp){
            sendToStation($timestamp['Station'], $timestamp['Si_unit'], $timestamp['Timestamp'], $timestamp['Send_time']);
        }

        $timestamp_collection = $stations[$req_station]->getTimestamps();


       /* if ($req_time != 0)
        echo $req_time->format('Y-m-d H:i:s');
            $req_time->add(new DateInterval('PT'.$split_time.'S'));*/

        // Loop through all timestamps and display the ones requested
        foreach ($timestamp_collection as $timestamp){
            if ($timestamp['pid'] >= $req_pid){
                if ($first_time == null || ($timestamp['send_time'] >= $first_time->format('Y-m-d H:i:s') && $timestamp['send_time'] <= $last_time->format('Y-m-d H:i:s')))
                    echo $timestamp['pid'].';'.$timestamp['code'].';'.$timestamp['si_unit'].';'.$timestamp['timestamp']."\r\n";
            }
        }
    } else {
        echo 'Build finished';
    }