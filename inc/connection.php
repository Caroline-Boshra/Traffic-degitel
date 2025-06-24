<?php


header('Content-Type: application/json');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


$conn=mysqli_connect("localhost","root","","trafficdigital");

if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
?>