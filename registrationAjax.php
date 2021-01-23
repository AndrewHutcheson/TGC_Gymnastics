<?php
session_start();

require_once("globals.php");
require_once("meetRegistrationClass.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(userIsLoggedIn()) //quick way of parsing input to prevent sql injections since we control who has login permission
{
	if(isset($_REQUEST['competitionID']))
		$Reg = new meetRegistration("byComp",$_REQUEST['competitionID']);
	else if(isset($_REQUEST['meetID']))
		$Reg = new meetRegistration("byMeet",$_REQUEST['meetID']);
	else if(isset($_REQUEST['getCompetitionsForMeet']))
		$Reg = new meetRegistration("byMeet",$_REQUEST['getCompetitionsForMeet']);

	if(isset($_REQUEST['getCompetitionsForMeet']))
	{
		$meet = $_REQUEST['getCompetitionsForMeet'];
		$list = $Reg->getCompetitionsInMeet($meet);
		
		echo json_encode($list);
	}

	if(isset($_REQUEST['changePersonsCompetition']))
	{
		$person = $_REQUEST['person'];
		$oldCompetition = $_REQUEST['oldCompetition'];
		$newCompetition = $_REQUEST['newCompetition'];
		$institution = $_REQUEST['institution'];
		$designation = $_REQUEST['designation'];
		
		//need to add team back in here later
		//updatePersonCompetition($person,$oldCompetition,$newCompetition,$institution);
		$Reg = new meetRegistration("byComp",$oldCompetition); //old and new will always be the same meetID here.
		$Reg->changePersonCompetitionInMeet($person,$oldCompetition,$newCompetition,$institution,$designation);
	}

	if(isset($_REQUEST['changePersonsDesignation']))
	{
		$person = $_REQUEST['person'];
		$designation = $_REQUEST['designation'];
		$oldDesignation = $_REQUEST['oldDesignation'];
		$competition = $_REQUEST['competition'];
		$institution = $_REQUEST['institution'];
		
		//need to add team back in here later
		//updatePersonCompetition($person,$oldCompetition,$newCompetition,$institution);
		$Reg = new meetRegistration("byComp",$competition); //old and new will always be the same meetID here.
		$Reg->changePersonDesignationInMeet($person,$competition,$designation,$institution,$oldDesignation);
	}

	if(isset($_REQUEST['changePersonsEvent']))
	{
		$person = $_REQUEST['person'];
		$competitionID = $_REQUEST['competitionID'];
		$eventID = $_REQUEST['eventID'];
		$registered = $_REQUEST['registered'];
		$countsForTeam = 1; //default
		if(isset($_REQUEST['countForTeam']))
			$countsForTeam == $_REQUEST['countForTeam'];
		//need to add team back in here later
		$reg->updatePersonEvent($personID, $competitionID, $eventID, $registered, $countsForTeam);
	}

	if(isset($_REQUEST['getMeetRegDateFees']))
	{
		$meetID = $_REQUEST['meetID'];
		//need to add team back in here later
		//echo json_encode(getRegFeeAndDates($meetID));
		echo json_encode($Reg->getRegFeeAndDates($meetID));
	}

	if(isset($_REQUEST['registerPersonForCompetition']))
	{
		$person = $_REQUEST['person'];
		$institution = $_REQUEST['institution'];
		$competition = $_REQUEST['competition'];
		$team = $_REQUEST['team'];
		$gender = $_REQUEST['gender'];
		$first = $_REQUEST['firstAdd'];
		$events = $_REQUEST['events']; //maybe this should be optional
		$eventCountFlags = $_REQUEST['eventCountFlags'];
		$minor = $_REQUEST['minor'];
		$designation = $_REQUEST['designation'];
		//echo "minor is " . $minor;
		//registerPersonForCompetition($competition, $institution, $person, $team, $gender, $events, $eventCountFlags, $first);
		$Reg = new meetRegistration("byComp",$_REQUEST['competition']);
		$Reg->registerPersonForCompetition($competition, $institution, $person, $team, $gender, $events, $eventCountFlags, $first, $minor, $designation);
	}

	if(isset($_REQUEST['unregisterPersonFromCompetition']))
	{
		$person = $_REQUEST['person'];
		$competitionID = $_REQUEST['competition'];
		$institutionID = $_REQUEST['institution'];
		$designation = $_REQUEST['designation'];
		$Reg = new meetRegistration("byComp",$competitionID);
		
		$meetID = $Reg->getMeetIDFromCompetitionID($competitionID);
		$dates = $Reg->getRegFeeAndDates($meetID);
		
		$lateDate = date("Y-m-d",strtotime($dates['lateDeadline']));
		$now = date("Y-m-d");
		
		if(($now <= $lateDate) || userIsExecutiveAdministrator())
		{
			$Reg->unregisterPerson($person,$competitionID,$institutionID,$designation);
			//echo "Now is " . $now . " late is " . $lateDate;
		}
	}

	if(isset($_REQUEST['unregisterPersonFromDiscipline']))
	{
		$personID = $_REQUEST['person'];
		$meetID = $_REQUEST['meetID'];
		$institutionID = $_REQUEST['institution'];
		$discipline = $_REQUEST['discipline'];
		$Reg = new meetRegistration("byMeet",$meetID);
		
		$dates = $Reg->getRegFeeAndDates($meetID);
		
		$lateDate = date("Y-m-d",strtotime($dates['lateDeadline']));
		$now = date("Y-m-d");
		
		if(($now <= $lateDate) || userIsExecutiveAdministrator())
		{
			$Reg->unregisterPersonFromDiscipline($personID,$meetID,$institutionID,$discipline);
			//echo "Now is " . $now . " late is " . $lateDate;
		}
	}


	if(isset($_REQUEST['getTeamHeaderData']))
	{
		$iInstitution = $_REQUEST['institutionID'];
		$iMeet = $_REQUEST['meetID'];
		
		$return_arr = $Reg->getTeamTableData($iInstitution, $iMeet);
		
		echo json_encode($return_arr);
	}
	 
	if(isset($_REQUEST['updateTeamOptions']))
	{
		$institution = $_REQUEST['institutionID'];
		$competition = $_REQUEST['competitionID'];
		$designation = $_REQUEST['teamDesignation'];
		$rotation = $_REQUEST['rotationID'];
		echo json_encode(updateRotation($institution,$competition,$designation,$rotation));
	}	
	
	if(isset($_REQUEST['updateEventCompetitionLevel']))
	{
		$person = $_REQUEST['person'];
		$oldCompetition = $_REQUEST['oldCompetition'];
		$newCompetition = $_REQUEST['newCompetition'];
		$institution = $_REQUEST['institution'];
		$apparatus = $_REQUEST['apparatus'];
		
		//need to add team back in here later
		//updatePersonCompetition($person,$oldCompetition,$newCompetition,$institution);
		$Reg = new meetRegistration("byComp",$oldCompetition); //old and new will always be the same meetID here.
		$Reg->updatePersonEventCompetition($person, $oldCompetition, $newCompetition, $institution, $apparatus);
	}

	if(isset($_REQUEST['addNewPersonToDatabase']))
	{
		$lastName = $_REQUEST['lastName'];
		$firstName = $_REQUEST['firstName'];
		$middleName = $_REQUEST['middleName'];
		$institutionID = $_REQUEST['institutionID'];
		
		$gender = ""; 
		$phone = ""; 
		$email = "";
		
		if(isset($_REQUEST['gender']))
			$gender = $_REQUEST['gender'];
		if(isset($_REQUEST['phone']))
			$phone = $_REQUEST['phone'];
		if(isset($_REQUEST['email']))
			$email = $_REQUEST['email'];
		
		//set a default if using the registration page
		$season = DATE("Y");
		if(DATE("n") >= 8) {$season++;}
		
		//if using team management or retrocscoring we don't necessarily want a default. 
		if(isset($_REQUEST['season']))
			$season = $_REQUEST['season'];
		
		echo addNewPerson($firstName,$lastName,$middleName,$season,$institutionID,$gender,$phone,$email);
	}

	if(isset($_REQUEST['getTeamRegistrationForCompetition']))
	{
		$iInstitution = $_REQUEST['institutionID'];
		$iMeet = $_REQUEST['meetID'];
		$iGender = $_REQUEST['genderID'];
		
		//$return_arr = getPeopleInTeam($iMeet, $iInstitution, $iGender);
		if($iGender < 4)
			$return_arr = $Reg->getPeopleInTeam($iMeet, $iInstitution, $iGender);
		else
		{
			$return_arr = $Reg->getEventLevelPeopleInTeam($iMeet, $iInstitution, $iGender);
		}
		echo json_encode($return_arr);
	}


	if(isset($_REQUEST['updateTeamScoreOption']))
	{
		$teamScore = $_REQUEST['hasTeamScore'];
		$competitionID = $_REQUEST['competitionID'];
		$institutionID = $_REQUEST['institutionID'];
		$designation = $_REQUEST['teamDesignation'];
		
		$return_arr = $Reg->updateTeamOptions($teamScore, $competitionID, $institutionID, $designation);
		
		echo $return_arr;
	}

	if(isset($_REQUEST['savePersonEventRegistration']))
	{
		$personID = $_REQUEST['person'];
		$competitionID = $_REQUEST['competition'];
		$institutionID = $_REQUEST['institution'];
		$event = $_REQUEST['theevent'];
		$registered = $_REQUEST['registered'];
		$Reg = new meetRegistration("byComp",$competitionID);
		
		$return_arr = $Reg->updateEventForPerson($personID,$institutionID,$competitionID,$event,$registered);
		
		echo $return_arr;
	}

	if(isset($_REQUEST['savePersonEventCountsRegistration']))
	{
		$personID = $_REQUEST['person'];
		$competitionID = $_REQUEST['competition'];
		$institutionID = $_REQUEST['institution'];
		$event = $_REQUEST['theevent'];
		$counts = $_REQUEST['counts'];
		
		$Reg = new meetRegistration("byComp",$competitionID);
		$return_arr = $Reg->updateEventCountsForPerson($personID,$institutionID,$competitionID,$event,$counts);
		
		echo $return_arr;
	}
}

function addNewPerson($firstName,$lastName,$middleName,$season,$institutionID,$gender,$phone,$email)
{
	global $conn;
	$error = false;
	$returnstuff = false;
	$alreadyThere = false;
	try
	{
		$conn->beginTransaction();
		
		//$alreadyThere = //I need to write a string search function by name.
		
		if(!$alreadyThere)
		{
			$sql = "
				INSERT INTO
					Identifiers_People(FirstName,LastName,MiddleName,UserName,Gender,Phone,Email)
				VALUES
					(?,?,?,?,?,?,?)
				;";
			$stmt = $conn->prepare($sql);
			
			if(($email == "") || ($email == null))
				$tempUserName = $firstName.$lastName;
			else
				$tempUserName = $email;
			
			$stmt->bindParam(1, $firstName, PDO::PARAM_STR, 150);
			$stmt->bindParam(2, $lastName, PDO::PARAM_STR, 150);
			$stmt->bindParam(3, $middleName, PDO::PARAM_STR, 150);
			$stmt->bindParam(4, $tempUserName, PDO::PARAM_STR, 150);
			$stmt->bindParam(5, $gender, PDO::PARAM_INT, 2);
			$stmt->bindParam(6, $phone, PDO::PARAM_STR, 150);
			$stmt->bindParam(7, $email, PDO::PARAM_STR, 150);
			$stmt->execute();
			
			$personID = $conn->lastInsertId(); //requires transaction
			
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
		$conn->rollBack();
		echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
	}
	
		if(!$error)
			$conn->commit();
		return $returnstuff; //false or inserted personID
}

function updateRotation($institution,$competition,$designation,$rotation)
{
	global $conn;
	$error = false;
	$returnstuff = false;
	
	try
	{
		$conn->beginTransaction();
		
		$sql = "
			Update
				Events_Teams
			Set
				Rotation = ?
			Where
				CompetitionID = ? AND
				InstitutionID = ? AND
				TeamDesignation = ?
			;";
			
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $rotation, PDO::PARAM_STR, 250);
		$stmt->bindParam(2, $competition, PDO::PARAM_INT, 5);
		$stmt->bindParam(3, $institution, PDO::PARAM_INT, 5);
		$stmt->bindParam(4, $designation, PDO::PARAM_STR, 5);
		$stmt->execute();
	}
	catch(PDOException $e)
	{
		$error = true;
		$conn->rollBack();
		echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
	}

	if(!$error)
	{
		$conn->commit();
		$returnstuff = array(
			'Error' => false,
			'Message'=>"No Error"
			);
	}
		return $returnstuff; //false or my real data
}
?>