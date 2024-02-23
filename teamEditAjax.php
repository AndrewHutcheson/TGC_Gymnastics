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
	
if(userIsLoggedIn()) //quick way of parsing input since we already control who has access
{	
	if(isset($_REQUEST['getTeamData']))
	{
		$institutionID = $_REQUEST['institutionID'];
		$year = $_REQUEST['year'];

		echo json_encode(getTeamData($institutionID,$year));
	}

	if(isset($_REQUEST['removePersonFromSeason']))
	{
		$personID = $_REQUEST['personID'];
		$institutionID = $_REQUEST['institutionID'];
		$season = $_REQUEST['season'];

		echo json_encode(unaffiliatePerson($personID,$institutionID,$season));
	}

	if(isset($_REQUEST['affiliatePersontoSeason']))
	{
		$personID = $_REQUEST['personID'];
		$institutionID = $_REQUEST['institutionID'];
		$season = $_REQUEST['season'];

		echo json_encode(affiliatePerson($personID,$institutionID,$season));
	}

	if(isset($_REQUEST['getOrgData']))
	{
		$institutionID = $_REQUEST['institutionID'];
		$year = $_REQUEST['year'];

		echo json_encode(getOrgData($institutionID,$year));
	}

	if(isset($_REQUEST['updateEmail']))
	{
		$ID = $_REQUEST['ID'];
		$Email = $_REQUEST['Email'];

		echo json_encode(updateEmail($ID,$Email));
	}

	if(isset($_REQUEST['updateTGCPermission']))
	{
		//oof we really need some kind of security check here...
		$permission = $_REQUEST['updateTGCPermission'];
		$permissionValue = $_REQUEST['permissionValue'];
		$personID = $_REQUEST['personID'];
		$season = $_REQUEST['Season'];

		echo json_encode(updateTGCPermission($permission,$permissionValue,$personID,$season));
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

function unaffiliatePerson($personID,$institutionID,$season)
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
					Identifiers_Affiliations
				Where
					PersonID = ? AND
					ClubID = ? AND
					Season = ?
				";
				
		$stmt = $conn->prepare($sql0);
		$stmt->bindParam(1, $personID, PDO::PARAM_INT);
		$stmt->bindParam(2, $institutionID, PDO::PARAM_INT);
		$stmt->bindParam(3, $season, PDO::PARAM_INT);
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			if($row["num"] > 0 )
				$alreadyThere = true;
		}
		
		if($alreadyThere)
		{
			$sql2 = "
				Delete
				From
					Identifiers_Affiliations
				Where
					Season = ? AND
					PersonID = ? AND
					ClubID = ?
				Limit 1
				;";
			$stmt2 = $conn->prepare($sql2);
			$stmt2->bindParam(1, $season, PDO::PARAM_INT);
			$stmt2->bindParam(2, $personID, PDO::PARAM_INT);
			$stmt2->bindParam(3, $institutionID, PDO::PARAM_INT);
			$stmt2->execute();

			$sql3 = "
				Delete
				From
					Identifiers_Permissions
				Where
					Season = ? AND
					PersonID = ? AND
					PermissionName IN ('Registration','InstitutionAdmin','MeetScoring') AND
					PermissionValue = ?
				Limit 1
				;";
			$stmt3 = $conn->prepare($sql3);
			$stmt3->bindParam(1, $season, PDO::PARAM_INT);
			$stmt3->bindParam(2, $personID, PDO::PARAM_INT);
			$stmt3->bindParam(3, $institutionID, PDO::PARAM_INT);
			$stmt3->execute();
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
					Identifiers_Affiliations
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
					Identifiers_Affiliations(Season,PersonID,ClubID,GymnastPermission)
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

function updateTGCPermission($permission,$permissionValue,$personID,$season)
{
	global $conn;
	$error = false;
	$errorMsg = "";
	
	//this isn't exactly superfluous... it was a cheap way of doing input sanitization and renaming on $permission.
	if($permission == "TGCSuperAdmin")
	{
		$permission = "TGCSuperAdmin";
	}
	elseif($permission == "TGCEmulation")
	{
		$permission = "TGCEmulation";
	}
	elseif($permission == "TGCAdmin")
	{
		$permission = "TGCAdmin";
	}
	elseif($permission == "TGCEmulation")
	{
		$permission = "TGCEmulation";
	}
	elseif($permission == "IssueRefund")
	{
		$permission = "IssueRefund";
	}
	elseif($permission == "CreateWaiver")
	{
		$permission = "CreateWaiver";
	}
	else
	{
		exit;
	}
	
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
		#if permission exists, then delete it
		#else insert it.
		if($permissionValue == 1)
		{		
			$sql = "INSERT INTO
					Identifiers_Permissions(Season,PersonID,PermissionName,PermissionValue)
					Values(?,?,?,?)
				";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(1, $season, PDO::PARAM_INT, 4);
			$stmt->bindParam(2, $personID, PDO::PARAM_INT, 10);
			$stmt->bindParam(3, $permission, PDO::PARAM_STR, 100);
			$stmt->bindParam(4, $permissionValue, PDO::PARAM_INT, 10);
			$stmt->execute();
		}
		else
		{
			$sql = "Delete 
					From
						Identifiers_Permissions
					Where
						Season = ? AND
						PersonID = ? AND
						PermissionName Like ?
					Limit 1
				";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(1, $season, PDO::PARAM_INT, 4);
			$stmt->bindParam(2, $personID, PDO::PARAM_INT, 10);
			$stmt->bindParam(3, $permission, PDO::PARAM_STR, 100);
			$stmt->execute();
		}
	}
	catch (PDOException $e)
	{
		$error = true;
		$errorMsg .= 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
	}
	if(!$error)
	{
		
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
	
	if($permission == "TGCSuperAdmin")
	{
		$permission = "TGCCSuperAdmin";
	}
	elseif($permission == "TGCEmulation")
	{
		$permission = "TGCEmulation";
	}
	elseif($permission == "Registration")
	{
		$permission = "Registration";
	}
	elseif($permission == "InstitutionAdmin")
	{
		$permission = "InstitutionAdmin";
	}
	elseif($permission == "MeetScoring")
	{
		$permission = "MeetScoring";
	}
	else
	{
		exit;
	}
	
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
		#if permission exists, then delete it
		#else insert it.
		if($permissionValue == 1)
		{		
			$sql = "INSERT INTO
					Identifiers_Permissions(Season,PersonID,PermissionName,PermissionValue)
					Values(?,?,?,?)
				";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(1, $season, PDO::PARAM_INT, 4);
			$stmt->bindParam(2, $personID, PDO::PARAM_INT, 10);
			$stmt->bindParam(3, $permission, PDO::PARAM_STR, 100);
			$stmt->bindParam(4, $institutionID, PDO::PARAM_INT, 10);
			$stmt->execute();
		}
		else
		{
			$sql = "Delete 
					From
						Identifiers_Permissions
					Where
						Season = ? AND
						PersonID = ? AND
						PermissionName Like ? AND
						PermissionValue = ?	
					Limit 1
				";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(1, $season, PDO::PARAM_INT, 4);
			$stmt->bindParam(2, $personID, PDO::PARAM_INT, 10);
			$stmt->bindParam(3, $permission, PDO::PARAM_STR, 100);
			$stmt->bindParam(4, $institutionID, PDO::PARAM_INT, 10);
			$stmt->execute();
		}
	}
	catch (PDOException $e)
	{
		$error = true;
		$errorMsg .= 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
	}
	if(!$error)
	{
		
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
					Identifiers_Affiliations
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
					Identifiers_Affiliations.ID AS AffiliationID,
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
					Type
				From
					Identifiers_People,
					Identifiers_Affiliations
				Where
					Identifiers_People.ID = Identifiers_Affiliations.PersonID AND
					Identifiers_Affiliations.ClubID = ? AND
					Identifiers_Affiliations.Season = ? AND
					Identifiers_People.ID > 100
				;";
				
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $institutionID, PDO::PARAM_INT, 6);
		$stmt->bindParam(2, $year, PDO::PARAM_INT, 4);
		
		$stmt->execute();
		$count = 0;
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$person = $row['ID'];
			$registration = canUserRegisterForClub($person,$institutionID,$year);
			$administrate = isUserClubAdmin($person,$institutionID,$year);
			$scoring = canUserMeetScore($person,$institutionID,$year);
			
			//fucking boolean vs t/f string vs 1/0
			//if($registration == 1) { $registration = true; }
			//if($administrate == 1) { $administrate = true; }
			
			$return_array[$count] = array(
										'PermissionID'=>$row['AffiliationID'],
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
										'Administrate'=>$administrate,
										'Scoring'=>$scoring
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

function getOrgData($institutionID,$year)
{
	global $conn;
	$error = false;
	$return_array = array();
	try
	{
		//$conn->beginTransaction();
		
		$sql = "
				Select
					Identifiers_Affiliations.ID AS AffiliationID,
					Identifiers_People.ID AS ID,
					FirstName,
					LastName,
					MiddleName,
					Phonetic,
					Birthday,
					Gender,
					Email,
					Phone,
					Type
				From
					Identifiers_People,
					Identifiers_Affiliations
				Where
					Identifiers_People.ID = Identifiers_Affiliations.PersonID AND
					Identifiers_Affiliations.ClubID = ? AND
					Identifiers_Affiliations.Season = ?
				;";
				
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $institutionID, PDO::PARAM_INT, 6);
		$stmt->bindParam(2, $year, PDO::PARAM_INT, 4);
		
		$stmt->execute();
		$count = 0;
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$person = $row['ID'];
			$TGCAdmin = isUserTGCAdmin($person,1,$year);
			$emulate = canUserEmulate($person,1,$year);
			$super = isUserTGCSuperAdmin($person,1,$year);
			//$refund = canUserRefund($person,1,$year);
			//$waiver = calogoutnUserCreateWaiver($person,1,$year);
			
			//fucking boolean vs t/f string vs 1/0
			//if($registration == 1) { $registration = true; }
			//if($administrate == 1) { $administrate = true; }
			
			$return_array[$count] = array(
										'PermissionID'=>$row['AffiliationID'],
										'ID'=>$row['ID'],
										'FirstName'=>$row['FirstName'],
										'LastName'=>$row['LastName'],
										'MiddleName'=>$row['MiddleName'],
										'Phonetic'=>$row['Phonetic'],
										'Gender'=>$row['Gender'],
										'Birthday'=>$row['Birthday'],
										'Email'=>$row['Email'],
										'Phone'=>$row['Phone'],
										'TGCSuperAdmin'=>$super,
										'TGCAdmin'=>$TGCAdmin,
										'Emulate'=>$emulate
										//'Refund'=>$refund,
										//'Waiver'=>$waiver,
										//'MembershipOptionsArray'=>getPersonMembershipOptions($person,$year)
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

function isUserTGCAdmin($user,$club,$season)
{
	global $conn;
		
	$permissionFound = false;
	
	$query = "	SELECT 
					* 
				FROM
					Identifiers_Permissions
				Where 
					PersonID = ? AND
					PermissionName = 'TGCAdmin' AND
					PermissionValue = ? AND
					Season = ?
			";
	$stmt = $conn->prepare($query);
	
	$stmt->bindParam(1, $user, PDO::PARAM_STR, 100);
	$stmt->bindParam(2, $club, PDO::PARAM_INT, 20);
	$stmt->bindParam(3, $season, PDO::PARAM_INT, 4);
	
	$stmt->execute();
	
	//while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	if($stmt->rowCount() > 0)
	{
		$permissionFound = true;
	}
	
	return $permissionFound;
}

function canUserEmulate($user,$club,$season)
{
	global $conn;
		
	$permissionFound = false;
	
	$query = "	SELECT 
					* 
				FROM
					Identifiers_Permissions
				Where 
					PersonID = ? AND
					PermissionName = 'TGCEmulation' AND
					PermissionValue = ? AND
					Season = ?
			";
	$stmt = $conn->prepare($query);
	
	$stmt->bindParam(1, $user, PDO::PARAM_STR, 100);
	$stmt->bindParam(2, $club, PDO::PARAM_INT, 20);
	$stmt->bindParam(3, $season, PDO::PARAM_INT, 4);
	
	$stmt->execute();
	
	//while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	if($stmt->rowCount() > 0)
	{
		$permissionFound = true;
	}
	
	return $permissionFound;
}

function isUserTGCSuperAdmin($user,$club,$season)
{
	global $conn;
		
	$permissionFound = false;
	
	$query = "	SELECT 
					* 
				FROM
					Identifiers_Permissions
				Where 
					PersonID = ? AND
					PermissionName = 'TGCSuperAdmin' AND
					PermissionValue = ? AND
					Season = ?
			";
	$stmt = $conn->prepare($query);
	
	$stmt->bindParam(1, $user, PDO::PARAM_STR, 100);
	$stmt->bindParam(2, $club, PDO::PARAM_INT, 20);
	$stmt->bindParam(3, $season, PDO::PARAM_INT, 4);
	
	$stmt->execute();
	
	//while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	if($stmt->rowCount() > 0)
	{
		$permissionFound = true;
	}
	
	return $permissionFound;
}

function isUserClubAdmin($user,$club,$season)
{
	global $conn;
		
	$permissionFound = false;
	
	$query = "	SELECT 
					* 
				FROM
					Identifiers_Permissions
				Where 
					PersonID = ? AND
					PermissionName = 'InstitutionAdmin' AND
					PermissionValue = ? AND
					Season = ?
			";
	$stmt = $conn->prepare($query);
	
	$stmt->bindParam(1, $user, PDO::PARAM_STR, 100);
	$stmt->bindParam(2, $club, PDO::PARAM_INT, 20);
	$stmt->bindParam(3, $season, PDO::PARAM_INT, 4);
	
	$stmt->execute();
	
	//while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	if($stmt->rowCount() > 0)
	{
		$permissionFound = true;
	}
	
	return $permissionFound;
}

function canUserRegisterForClub($user,$club,$season)
{
	global $conn;
		
	$permissionFound = false;
	
	$query = "	SELECT 
					* 
				FROM
					Identifiers_Permissions
				Where 
					PersonID = ? AND
					PermissionName = 'Registration' AND
					PermissionValue = ? AND
					Season = ?
			";
	$stmt = $conn->prepare($query);
	
	$stmt->bindParam(1, $user, PDO::PARAM_STR, 100);
	$stmt->bindParam(2, $club, PDO::PARAM_INT, 20);
	$stmt->bindParam(3, $season, PDO::PARAM_INT, 4);
	
	$stmt->execute();
	
	//while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	if($stmt->rowCount() > 0)
	{
		$permissionFound = true;
	}
	
	return $permissionFound;
}

function canUserMeetScore($user,$club,$season)
{
	global $conn;
		
	$permissionFound = false;
	
	$query = "	SELECT 
					* 
				FROM
					Identifiers_Permissions
				Where 
					PersonID = ? AND
					PermissionName = 'MeetScoring' AND
					PermissionValue = ? AND
					Season = ?
			";
	$stmt = $conn->prepare($query);
	
	$stmt->bindParam(1, $user, PDO::PARAM_STR, 100);
	$stmt->bindParam(2, $club, PDO::PARAM_INT, 20);
	$stmt->bindParam(3, $season, PDO::PARAM_INT, 4);
	
	$stmt->execute();
	
	//while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	if($stmt->rowCount() > 0)
	{
		$permissionFound = true;
	}
	
	return $permissionFound;
}

?>