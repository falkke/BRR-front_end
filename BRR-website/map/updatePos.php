<?php
$currentPos=$_POST['currentPos'];

$conn=conn();
$maxID=0;
$min=0;
$stationTime=0;
$currentTime= new DateTime();
$currentTime=$currentTime->format('Y-m-d H:i:s');
//kilometers per second
$velocity=0.1020408163265306;
$sql = "SELECT  MIN(ID) AS ID,MIN(time) AS time FROM arrive_check WHERE arrive =0";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    $row = $result->fetch_assoc();
    $min=$row['ID'];
    $timeMin=$row['time'];
    }
if($min<=$currentPos){
    echo $min;
}
else{
    $sql = "SELECT  MAX(ID) AS ID,MAX(time) AS time FROM arrive_check WHERE arrive =1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
     // output data of each row
      $row = $result->fetch_assoc();
        $maxID=$row['ID'];
        $stationTime=$row['time'];
        switch($maxID){
        case 0:

            $distance=round($velocity*sub_time($currentTime, $stationTime));
            echo $distance;
            break;
        case 115:
                $sql = "SELECT  time FROM arrive_check WHERE ID =0";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    // output data of each row
                    $row = $result->fetch_assoc();
                    $stationTime1=$row['time'];}
                $sql = "SELECT  time FROM arrive_check WHERE ID =115";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    // output data of each row
                    $row = $result->fetch_assoc();
                    $stationTime2=$row['time'];}
                $velocity=39.1304347826087/sub_time($stationTime2, $stationTime1);

                $distance=115+round($velocity*sub_time($currentTime, $stationTime2));
                echo $distance;
                break;
            case 164:
                $sql = "SELECT  time FROM arrive_check WHERE ID =115";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    // output data of each row
                    $row = $result->fetch_assoc();
                    $stationTime1=$row['time'];}
                $sql = "SELECT  time FROM arrive_check WHERE ID =164";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    // output data of each row
                    $row = $result->fetch_assoc();
                    $stationTime2=$row['time'];}
                $velocity=91.83673469387755/sub_time($stationTime2, $stationTime1);

                $distance=164+round($velocity*sub_time($currentTime, $stationTime2));
                echo $distance;
                break;
            case 341:
                $sql = "SELECT  time FROM arrive_check WHERE ID =164";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    // output data of each row
                    $row = $result->fetch_assoc();
                    $stationTime1=$row['time'];}
                $sql = "SELECT  time FROM arrive_check WHERE ID =115";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    // output data of each row
                    $row = $result->fetch_assoc();
                    $stationTime2=$row['time'];}
                $velocity=50.84743762711864/sub_time($stationTime2, $stationTime1);

                $distance=341+round($velocity*sub_time($currentTime, $stationTime2));
                echo $distance;
                break;
            case 475:
                $distance=475;
                echo $distance;
                break;
        }
    }

}

//
//
//
//
//
//
//if ($result->num_rows > 0) {
//    // output data of each row
//    $row = $result->fetch_assoc();
//    $max=$row['ID'];
//    $maxNew=$row['ID'];
//    $stationTime=$row['time'];
//    $index=$row['ID'];
//}
//
//
////current position
//if($index==0){$index=1;}
//$distance=round($velocity*sub_time($currentTime, $stationTime)/($index*1000));
//
//$max= (int)$distance+$max ;
//if($min<=$max){
//    $max=$min;
//echo $min;
//}
//else{echo $max;}
//
function sub_time($currentTime, $stationTime) {
    $to_time = strtotime($currentTime);
    $from_time = strtotime($stationTime);
    return round(abs(($to_time - $from_time) / 60)*60,2);

}
function conn(){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "brr";

// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
$conn->close();
?>