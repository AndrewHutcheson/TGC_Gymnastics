<?php
session_start();
require_once("globals.php");
date_default_timezone_set("America/Chicago");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	
	try {
		$conn= new PDO("mysql:host=$sql_login_host; port=3306; dbname=$sql_login_db", $sql_login_user, $sql_login_pass);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch (PDOException $e){
		echo "PDO object error: " . $e->getMessage();
	}
	
function createInvoice($meetID,$institutionID)
{
	
}
	
function getLatestInvoice($meetID,$institutionID)
{
	
}
	
?>