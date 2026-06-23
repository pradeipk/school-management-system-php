<?php  

$sName = "localhost";
$uName = "u895854558_sms_db";
$pass  = "Schoolofindia@03";
$db_name = "u895854558_sms_db";

try {
	$conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOExeption $e){
	echo "Connection failed: ". $e->getMessage();
	exit;
}