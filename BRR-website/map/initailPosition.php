<?php
global $db;

$max=0;
$min=0;
$stationTime=0;
$currentTime= new DateTime();
$currentTime=$currentTime->format('Y-m-d H:i:s');
//kilometers per second
$velocity=55;
$sql = "SELECT  MIN(ID) AS ID, time FROM arrive_check WHERE arrive =0";
$result = $db->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    $row = $result->fetch_assoc();
    $min=$row['ID'];
    }




$sql = "SELECT  MAX(ID) AS ID, MAX(time) AS time FROM arrive_check WHERE arrive = 1";
$result = $db->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    $row = $result->fetch_assoc();
    $max=$row['ID'];
    $maxNew=$row['ID'];
    $stationTime=$row['time'];
    $index=$row['ID'];
}
echo $index."<br><br>";
echo $stationTime."<br>".$currentTime."<br>";
echo sub_time($stationTime,$currentTime).'<br>';

//current position
if($index==0){$index=1;}
$distance=round($velocity*sub_time($currentTime, $stationTime)/($index*1000));

$max= (int)$distance+$max ;
if($min<=$max){
    $max=$min;
echo $min;
}

function sub_time($currentTime, $stationTime) {
    $to_time = strtotime($currentTime);
    $from_time = strtotime($stationTime);
    return round(abs(($to_time - $from_time) / 60)*60,2);

}/*
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
?>*/