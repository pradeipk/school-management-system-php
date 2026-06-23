<?php  

$sName = "localhost";
$uName = "u123456789_sms_user";
$pass  = "Schoolofindia@03";
$db_name = "u123456789_sms_db";

try {
	$conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOExeption $e){
	echo "Connection failed: ". $e->getMessage();
	exit;
}