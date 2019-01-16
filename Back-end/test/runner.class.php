<?php

    include('database.inc.php'); // connectToDatabase-function is needed.

    class Runner {
        private $name;
        private $laps;
        private $class;
        private $si_unit;
        private $si_unit_fail_percent;
        private $si_unit_borked_percent;
        private $time;
        private $last_station;
        private $average_minutes_per_station;
        private $completed_laps;
        private $si_unit_borked;
        private $timestamps;
        private $running;
        private $runner_id;
        private $race_instance;
        private static $names_first = array(
            array("Carl", "Elias", "Buster", "Silas", "Adolfo", "Nigel", "Brady", "Phil", "Maurice", "Salvador", "Bert", "Fabian", "Timothy", "Osvaldo", "Brooks", "Will", "Nathanael", "Jody", "Russel", "Mikel", "Derek", "Forest", "Dee", "Lawerence", "Valentine", "Ivan", "Martin", "Dalton", "Jonathon", "Colton", "Antione", "Wiley", "Eddy", "Alvaro", "Dante", "Wilton", "Emmanuel", "Hung", "Lamar", "Orval", "Wilford", "Stephen", "Ruben", "Bennie", "Rickie", "Elijah", "Hobert", "Nestor", "Jacques", "Geoffrey"),
            array("Annelle", "Anja", "Sharonda", "Leanne", "Margarita", "Chantell", "Iliana", "Lorna", "Ute", "Na", "Asuncion", "Adelia", "Alvera", "Ellie", "Cuc", "Ethelyn", "Kenyatta", "Sherril", "Cristie", "Tanna", "Grace", "Ciara", "Jamee", "Sharleen", "Oma", "Shanta", "Bethel", "Eliza", "Librada", "Soledad", "Mallory", "Sherron", "Barb", "Joana", "Josefina", "Melisa", "Selena", "Renda", "Kasi", "Kristel", "Tessa", "Gertrud", "Cris", "Merrilee", "Petrina", "Bao", "Chandra", "Tuyet", "Keira", "Lina"));
        private static $names_last = array("Arrieta", "Rains", "Thibodeaux", "Knecht", "Maul", "Gehl", "Macedo", "Lou", "Goguen", "Stumpff", "Venzon", "Bonet", "Hayner", "Nading", "Poche", "Forsman", "Ranck", "Dulle", "Frisina", "Roselli", "Bal", "Aultman", "Carriere", "Singleton", "Wedeking", "Fulgham", "Labar", "Hutchings", "Tom", "Bugg", "Waddell", "Iskra", "Gould", "Kirkendoll", "Johnson", "Kuster", "Hernadez", "Dollins", "Freitas", "Sampsel", "Schimmel", "Brunner", "Amick", "Guerrant", "Gaiser", "Mccrory", "Paolini", "Rickenbacker", "Curren", "Danks"); 

        public function __construct($build_only, $si_unit_fail_percent, $si_unit_borked_percent, $start_time, $average_minutes_per_station, $laps, $distance) {
            //$this->name = $name; //TODO: Register to database
            $this->si_unit_fail_percent = $si_unit_fail_percent;
            $this->si_unit_borked_percent = $si_unit_borked_percent;
            $this->time = new DateTime($start_time);
            $this->last_station = 0;
            $this->average_minutes_per_station = $average_minutes_per_station;
            $this->laps = $laps;
            $this->completed_laps = 0;
            $this->si_unit_borked = false;
            $this->timestamps = null;
            $this->running = true;
            $this->distance = $distance;

            $gender = rand(0, 1);
            $first_name = self::$names_first[$gender][rand(0, count(self::$names_first[$gender])-1)];
            $last_name = self::$names_last[rand(0, count(self::$names_last)-1)];
            $this->race_instance = '1000'.($gender+1).$distance;

            if ($build_only){
                $this->runner_id = $this->registerRunner($first_name, $last_name, $gender, $this->race_instance);
                $this->si_unit = $this->getSiUnit($this->runner_id);
            } else {
                $temp = mt_rand(0, 978307200); // Used for generating birth date. Needed to get same rand position
                $runner = $this->getRunner($first_name, $last_name);
                $this->runner_id = $runner['Runner'];
                $this->si_unit = $runner['SI_unit'];
                $temp = rand(10000000, 1000000000); // Used for generating si-unit. Needed to get same rand position
            }
        }

        public function generateTimestamps() {
            if ($this->timestamps == null){
                while ($this->running){
                    if (rand(1, 100) >= $this->si_unit_fail_percent){
                        $timestamps = $this->getNextTimestamp(1);
                    } else {
                        $timestamps = $this->getNextTimestamp(rand(1, 5));
                        if ($timestamps != null){
                            shuffle($timestamps);
                        }
                    }

                    if ($timestamps != null){
                        foreach($timestamps as $timestamp){
                            $this->timestamps[$timestamp['Station']][] = $timestamp;
                        }
                    }
                }
            }
        }

        public function getTimestamps($station){
            if (array_key_exists($station, $this->timestamps))
                return $this->timestamps[$station];
            return null;
        }
       
        private function getNextTimestamp($n) {
            // If passed last station at last running lap, send finish code. Is sent regardless of if the last station correctly sent the punch
            if ($this->completed_laps >= $this->laps || $this->completed_laps >= $this->distance){
                $this->running = false;
                $split_time = rand(10, 120);
                $this->time->add(new DateInterval('PT'.$split_time.'S'));
                return array(array("Send_time" => $this->time->format('Y-m-d H:i:s'), "Station" => NUM_STATIONS, "Si_unit" => $this->si_unit, "Timestamp" => $this->time->format('Y-m-d H:i:s')));
            }

            if (rand(1, 100) >= $this->si_unit_borked_percent || $this->si_unit_borked){
                for ($i = 0; $i < $n; ++$i){
                    if ($this->completed_laps < $this->laps){
                        // Control if si_unit is broken and replace at start of next lap
                        // If broken, no timestamps are sent
                        if ($this->si_unit_borked){
                            if ($this->last_station == NUM_STATIONS - 1){
                                $this->si_unit_borked = false;
                            } else {
                                $this->last_station++;
                                return null;
                            }
                        }
                        
                        // Generate timestamps for all stations that missed sending and current station
                        $split_time = $this->average_minutes_per_station * 60 + rand(-60, 600) + $this->completed_laps * 60;
                        $this->time->add(new DateInterval('PT'.$split_time.'S'));
                        $this->last_station = ($this->last_station += 1) % NUM_STATIONS;

                        if ($this->last_station == NUM_STATIONS - 1)
                            $this->completed_laps++;

                        $timestamp[] = array("Station" => $this->last_station, "Si_unit" => $this->si_unit, "Timestamp" => $this->time->format('Y-m-d H:i:s'));

                        // Send an extra timestamp 5% of the times
                        if (rand(1, 100) <= 5){
                            $this->time->add(new DateInterval('PT'.rand(0, 2).'S'));
                            $timestamp[] = array("Station" => $this->last_station, "Si_unit" => $this->si_unit, "Timestamp" => $this->time->format('Y-m-d H:i:s'));
                        }
                    }
                }                
                foreach($timestamp as &$t){
                    $t["Send_time"] = $this->time->format('Y-m-d H:i:s');
                }
                return $timestamp;
                
            } else { // si_unit broke, replace at start
                $this->si_unit_borked = true;
                $this->si_unit = $this->getSiUnit($this->runner_id);
            }
            
            return null;
        }

        /*private static function connectToDatabase(){
            // Database information :
            $dbhost = "s679.loopia.se";
            $dbname = "sebastianoveland_com_db_1";
            $dbuser = "group5@s243341";
            $dbpassword = "**************";
    
            // Try to connect to the database.
            try {
                $db = new PDO(
                    "mysql:host=".$dbhost.";dbname=".$dbname, 
                    $dbuser, 
                    $dbpassword, 
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
                );
            } catch(PDOexception $e) {
                die("Error while trying to connect to the database...");
            }
            
            return $db;
        }*/

        private static function SiUnitInUse($si_unit){
            static $si_unit_set = array();
            if (in_array($si_unit, $si_unit_set)){
                return true;
            } else {
                $si_unit_set[] = $si_unit;
                return false;
            }
        }

        private static function getSiUnit($runner_id){

            do {
                $si_unit = rand(10000000, 1000000000);
            } while (self::SiUnitInUse($si_unit));

            $db = connectToDatabase();
            $stmt_create = $db->prepare("INSERT INTO `si_unit` (`ID`, `Status`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `Status` = ?;");
            $stmt_create->execute(array($si_unit, 'Active', 'Active'));
            $stmt_create = null;

            $stmt_register = $db->prepare("INSERT INTO `runner_units` (`Runner`, `Si_unit`, `Race`) VALUES (?, ?, ?);");
            //$stmt_register->execute(array($runner_id, $si_unit, $race_instance));
            if ($stmt_register->execute(array($runner_id, $si_unit, 1000))){
                //echo 'success';
            } else {
                echo 'ERROR while inserting si_unit: '.$stmt_register->errorCode().'<br />';
                return self::getSiUnit($runner_id);
            }
            $stmt_register = null;
            $db = null;

            return $si_unit;
        }

        private function getRunner($first_name, $last_name){
            $db = connectToDatabase();
            $sql = "SELECT `ID` FROM `runner` WHERE `FirstName` = ? AND `LastName` = ? AND `Country` = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute(array($first_name, $last_name, 'Automatic_Backend_Test'));
            $id = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;
            if ($id != null){
                $sql = "SELECT `SI_unit` FROM `runner_units` WHERE `Runner` = ? AND `Race` = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute(array($id['ID'], 1000));
                $si = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt = null;
                $db = null;
                if (self::SiUnitInUse($si['SI_unit']))
                    $si['SI_unit'] = self::getSiUnit($id['ID']);
                return array("Runner" => $id['ID'], "SI_unit" => $si['SI_unit']);
            } else {
                echo 'Test runner not found. Try to rebuild test.';
                die();
            }
            return null;
        }

        private static function registerRunner($first_name, $last_name, $gender, $race_instance){
            $db = connectToDatabase();

            $data = array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'birth_date' => date("Y-m-d", mt_rand(0, 978307200)),
                    'gender' => ($gender ? 'Woman' : 'Man'),
                    'country' => 'Automatic_Backend_Test',
                    'city' => 'Västerås'
            );

            $sql = "INSERT INTO `runner` (`FirstName`, `LastName`, `DateOfBirth`, `Gender`, `Country`, `City`)
                    VALUES (:first_name, :last_name, :birth_date, :gender, :country, :city);";
            $stmt = $db->prepare($sql);
            if ($stmt->execute($data)){
                //echo 'success';
            } else {
                echo 'ERROR while inserting runner: '.$stmt->errorCode().'<br />';
            }
            $stmt = null;
            $id = $db->lastInsertId();

            $sql = "INSERT INTO `race_runner` (`RaceInstance`, `Runner`, `Bib`)
                    VALUES (?, ?, ?);";
            $stmt = $db->prepare($sql);
            if ($stmt->execute(array($race_instance, $id, '1000'.$id.$id))){
                //echo 'success';
            } else {
                echo 'ERROR while inserting race_runner: '.$stmt->errorCode().'<br />';
            }
            $stmt = null;
            $db = null;

            return $id;
        }
    } 