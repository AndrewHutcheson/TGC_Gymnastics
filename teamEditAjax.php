<?php
session_start();
require_once("globals.php");
require_once("auth.php");
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
	
if(userIsLoggedIn()) //quick way of parsing input to prevent sql injections since I post code to github
{	
	if(isset($_REQUEST['getTeamData']))
	{
		$institutionID = $_REQUEST['institutionID'];
		$year = $_REQUEST['year'];

		echo json_encode(getTeamData($institutionID,$year));
	}

	if(isset($_REQUEST['affiliatePersontoSeason']))
	{
		$personID = $_REQUEST['personID'];
		$institutionID = $_REQUEST['institutionID'];
		$season = $_REQUEST['season'];

		echo json_encode(affiliatePerson($personID,$institutionID,$season));
	}

	if(isset($_REQUEST['updateEmail']))
	{
		$ID = $_REQUEST['ID'];
		$Email = $_REQUEST['Email'];

		echo json_encode(updateEmail($ID,$Email));
	}

	if(isset($_REQUEST['updatePhone']))
	{
		$ID = $_REQUEST['ID'];
		$Phone = $_REQUEST['Phone'];

		echo json_encode(updatePhone($ID,$Phone));
	}

	if(isset($_REQUEST['updatePermission']))
	{
		//oof we really need some kind of security check here...
		$permission = $_REQUEST['updatePermission'];
		$permissionValue = $_REQUEST['permissionValue'];
		$personID = $_REQUEST['personID'];
		$season = $_REQUEST['Season'];
		$institutionID = $_REQUEST['institutionID'];

		echo json_encode(updatePermission($permission,$permissionValue,$personID,$season,$institutionID));
	}

	if(isset($_REQUEST['updateName']))
	{
		$ID = $_REQUEST['ID'];
		$value = $_REQUEST['Name'];
		$nameType = $_REQUEST['updateName'];

		echo json_encode(updateName($ID,$value,$nameType));
	}

	if(isset($_REQUEST['updatePersonType']))
	{
		$Type = $_REQUEST['updatePersonType'];
		$ID = $_REQUEST['ID'];
		$Season = $_REQUEST['Season'];
		$institutionID = $_REQUEST['institutionID'];

		echo json_encode(updatePersonType($ID,$Season,$Type,$institutionID));
	}

	if(isset($_REQUEST['updateGender']))
	{
		$ID = $_REQUEST['ID'];
		$Gender = $_REQUEST['Gender'];

		echo json_encode(updateGender($ID,$Gender));
	}
}

function affiliatePerson($personID,$institutionID,$season)
{
	global $conn;
	$error = false;
	$returnstuff = false;
	$alreadyThere = false;
	try
	{
		$conn->beginTransaction();
		
		$alreadyThere = false;
		
		$sql0 = "Select 
					count(*) as num
				From
					Identifiers_Permissions
				Where
					PersonID = ? AND
					ClubID = ? AND
					Season = ?
				";
				
		$stmt = $conn->prepare($sql0);
		$stmt->bindParam(1, $personID, PDO::PARAM_INT, 7);
		$stmt->bindParam(2, $institutionID, PDO::PARAM_INT, 6);
		$stmt->bindParam(3, $season, PDO::PARAM_INT, 4);
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			if($row["num"] > 0 )
				$alreadyThere = true;
		}
		
		if(!$alreadyThere)
		{
			$sql2 = "
				INSERT INTO
					Identifiers_Permissions(Season,PersonID,ClubID,GymnastPermission)
				VALUES
					(?,?,?,1)
				;";
			$stmt2 = $conn->prepare($sql2);
			$stmt2->bindParam(1, $season, PDO::PARAM_INT, 4);
			$stmt2->bindParam(2, $personID, PDO::PARAM_INT, 6);
			$stmt2->bindParam(3, $institutionID, PDO::PARAM_INT, 6);
			$stmt2->execute();
		}
	}
	catch(PDOException $e)
	{
		$error = true;
		//echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
	}
	
	if(!$error)
	{
		$conn->commit();
		$return_arr = array(
							'Error' => false,
							'Message'=>"success"
							);	
	}
	else
	{
		$errorMsg = 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		$conn->rollBack();
		$return_arr = array(
							'Error' => true,
							'Message'=>$errorMsg
							);	
	}
	
	return $return_arr;
}

function updateEmail($ID,$Email)
{
	global $conn;
	$error = false;
	try
	{
		//$conn->beginTransaction();
		
		$sql = "
				Update
					Identifiers_People
				Set
					Email = ?,
					Username = ?
				Where 
					ID = ?
				LIMIT 1
				;";
				
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $Email, PDO::PARAM_STR, 100);
		$stmt->bindParam(2, $Email, PDO::PARAM_STR, 100);
		$stmt->bindParam(3, $ID, PDO::PARAM_INT, 10);
		
		$stmt->execute();
	}
	catch (PDOException $e)
	{
		$error = true;
		//$conn->rollBack();
	}
	if(!$error)
	{
		//$conn->commit();
		$return_arr = array(
							'Error' => false,
							'Message'=>"success"
							);	
	}
	else
	{
		$errorMsg = 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		$return_arr = array(
							'Error' => true,
							'Message'=>$errorMsg
							);	
	}
	return $return_arr;
}

function updatePhone($ID,$Phone)
{
	global $conn;
	$error = false;
	try
	{
		//$conn->beginTransaction();
		
		$sql = "
				Update
					Identifiers_People
				Set
					Phone = ?
				Where 
					ID = ?
				LIMIT 1
				;";
				
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $Phone, PDO::PARAM_STR, 50);
		$stmt->bindParam(2, $ID, PDO::PARAM_INT, 10);
		
		$stmt->execute();
	}
	catch (PDOException $e)
	{
		$error = true;
		//$conn->rollBack();
	}
	if(!$error)
	{
		//$conn->commit();
		$return_arr = array(
							'Error' => false,
							'Message'=>"success"
							);	
	}
	else
	{
		$errorMsg = 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		$return_arr = array(
							'Error' => true,
							'Message'=>$errorMsg
							);	
	}
	return $return_arr;
}

function updatePermission($permission,$permissionValue,$personID,$season,$institutionID)
{
	global $conn;
	$error = false;
	$errorMsg = "";
	
	if($permissionValue=="false") //because of this bullshit https://bugs.php.net/bug.php?id=38546 https://stackoverflow.com/questions/10242312/pdo-bindvalue-with-pdoparam-bool-causes-statement-execute-to-fail-silently
	{
		$permissionValue = 0;
	}
	else
	{
		$permissionValue = 1;
	}
	
	try
	{
		//$conn->beginTransaction();
		
		$sqlAdmin = "
				Update
					Identifiers_Permissions
				Set
					InstitutionAdminPermission = ?
				Where 
					PersonID = ? AND
					ClubID = ? AND
					Season = ?
				LIMIT 1
				;";
		$sqlRegister = "
				Update
					Identifiers_Permissions
				Set
					CaptainPermission = ?
				Where 
					PersonID = ? AND
					ClubID = ? AND
					Season = ?
				LIMIT 1
				;";
		
		if($permission == "Administrate") {$sql = $sqlAdmin;}
		if($permission == "Registration") {$sql = $sqlRegister;}
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $permissionValue, PDO::PARAM_INT,1);
		$stmt->bindParam(2, $personID, PDO::PARAM_INT, 7);
		$stmt->bindParam(3, $institutionID, PDO::PARAM_INT,6);
		$stmt->bindParam(4, $season, PDO::PARAM_INT, 4);
		
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows < 1)
		{
			$error = true;
			$errorMsg .= "rows updated: " . $rows . " personID is " . $personID;
		}
	}
	catch (PDOException $e)
	{
		$error = true;
		$errorMsg .= 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
	}
	if(!$error)
	{
		//$conn->commit();
		$return_arr = array(
							'Error' => false,
							'Message'=>"success"
							);	
	}
	else
	{
		$return_arr = array(
							'Error' => true,
							'Message'=>$errorMsg
							);	
	}
	return $return_arr;
}

function updateName($ID,$value,$name)
{
	global $conn;
	$error = false;
	$errorMsg = "";
	try
	{
		//$conn->beginTransaction();
		
		$sqlFirst = "
				Update
					Identifiers_People
				Set
					FirstName = ?
				Where 
					ID = ?
				LIMIT 1
				;";
		$sqlMiddle = "
				Update
					Identifiers_People
				Set
					MiddleName = ?
				Where 
					ID = ?
				LIMIT 1
				;";
		$sqlLast = "
				Update
					Identifiers_People
				Set
					LastName = ?
				Where 
					ID = ?
				LIMIT 1
				;";
		$sqlNick = "
				Update
					Identifiers_People
				Set
					Phonetic = ?
				Where 
					ID = ?
				LIMIT 1
				;";
		
		if($name == "FirstName") {$sql = $sqlFirst;}
		if($name == "MiddleName") {$sql = $sqlMiddle;}
		if($name == "LastName") {$sql = $sqlLast;}
		if($name == "Phonetic") {$sql = $sqlNick;}
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $value, PDO::PARAM_STR, 50);
		$stmt->bindParam(2, $ID, PDO::PARAM_INT, 10);
		
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows < 1)
		{
			$error = true;
			$errorMsg .= "rows updated: " . $rows . " name is " . $name . " value is " . $value . " ID is " . $ID ;
		}
	}
	catch (PDOException $e)
	{
		$error = true;
		$errorMsg .= 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
	}
	if(!$error)
	{
		//$conn->commit();
		$return_arr = array(
							'Error' => false,
							'Message'=>"success"
							);	
	}
	else
	{
		$return_arr = array(
							'Error' => true,
							'Message'=>$errorMsg
							);	
	}
	return $return_arr;
}

function updatePersonType($ID,$Season,$Type,$institutionID)
{
	global $conn;
	$error = false;
	try
	{
		//$conn->beginTransaction();
		
		$sql = "
				Update
					Identifiers_Permissions
				Set
					Type = ?
				Where 
					PersonID = ? AND
					Season = ? AND
					ClubID = ?
				LIMIT 1
				;";
				
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $Type, PDO::PARAM_STR, 50);
		$stmt->bindParam(2, $ID, PDO::PARAM_INT, 10);
		$stmt->bindParam(3, $Season, PDO::PARAM_INT, 4);
		$stmt->bindParam(4, $institutionID, PDO::PARAM_INT, 6);
		
		$stmt->execute();
	}
	catch (PDOException $e)
	{
		$error = true;
		//$conn->rollBack();
	}
	if(!$error)
	{
		//$conn->commit();
		$return_arr = array(
							'Error' => false,
							'Message'=>"success"
							);	
	}
	else
	{
		$errorMsg = 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		$return_arr = array(
							'Error' => true,
							'Message'=>$errorMsg
							);	
	}
	return $return_arr;
}

function updateGender($ID,$Gender)
{
	global $conn;
	$error = false;
	try
	{
		//$conn->beginTransaction();
		
		$sql = "
				Update
					Identifiers_People
				Set
					Gender = ?
				Where 
					ID = ?
				LIMIT 1
				;";
				
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $Gender, PDO::PARAM_INT, 2);
		$stmt->bindParam(2, $ID, PDO::PARAM_INT, 10);
		
		$stmt->execute();
	}
	catch (PDOException $e)
	{
		$error = true;
		//$conn->rollBack();
	}
	if(!$error)
	{
		//$conn->commit();
		$return_arr = array(
							'Error' => false,
							'Message'=>"success"
							);	
	}
	else
	{
		$errorMsg = 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		$return_arr = array(
							'Error' => true,
							'Message'=>$errorMsg
							);	
	}
	return $return_arr;
}

function getTeamData($institutionID,$year)
{
	global $conn;
	$error = false;
	$return_array = array();
	try
	{
		//$conn->beginTransaction();
		
		$sql = "
				Select
					Identifiers_Permissions.ID AS PermissionID,
					Identifiers_People.ID AS ID,
					FirstName,
					LastName,
					MiddleName,
					Phonetic,
					Gender,
					Birthday,
					Email,
					Phone,
					Privacy,
					Type,
					CoachPermission,
					CaptainPermission,
					InstitutionAdminPermission
				From
					Identifiers_People,
					Identifiers_Permissions
				Where
					Identifiers_People.ID = Identifiers_Permissions.PersonID AND
					Identifiers_Permissions.ClubID = ? AND
					Identifiers_Permissions.Season = ? AND
					Identifiers_People.ID > 100
				;";
				
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $institutionID, PDO::PARAM_INT, 6);
		$stmt->bindParam(2, $year, PDO::PARAM_INT, 4);
		
		$stmt->execute();
		$count = 0;
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$registration = $row['CaptainPermission'] ;
			$administrate = $row['InstitutionAdminPermission'];
			
			//fucking boolean vs t/f string vs 1/0
			if($registration == 1) { $registration = true; }
			if($administrate == 1) { $administrate = true; }
			
			$return_array[$count] = array(
										'PermissionID'=>$row['PermissionID'],
										'ID'=>$row['ID'],
										'FirstName'=>$row['FirstName'],
										'LastName'=>$row['LastName'],
										'MiddleName'=>$row['MiddleName'],
										'Phonetic'=>$row['Phonetic'],
										'Gender'=>$row['Gender'],
										'Birthday'=>$row['Birthday'],
										'Email'=>$row['Email'],
										'Phone'=>$row['Phone'],
										'Privacy'=>$row['Privacy'],
										'Type'=>$row['Type'],
										'Registration'=>$registration,
										'Administrate'=>$administrate
									);
			$count++;
		}
	}
	catch (PDOException $e)
	{
		$error = true;
		//$conn->rollBack();
		echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
	}
	if(!$error)
	{
		//$conn->commit();
		return $return_array;
	}
	else
	{
		return 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
	}
}
?>