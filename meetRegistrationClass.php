<?php

/*

function constructor takes two variables.
	-The first tells us what type of item the second variable is, a meetID or a competitionID. 
	-The second is the actual ID

========PRIVATE FUNCTIONS=======
private function doesUserHaveRegistrationAccessForInstitution($institutionID)
	- returns a boolean if the logged in user has a permission that allows then to access the institution's registration
private function addToRegistrationLog($competitionID,$personID,$teamID,$action,$key,$value)
	- this adds an entry to the registration log every time something is changed
private function
private function


========PUBLIC FUNCTIONS=======
public function getMeetIDFromCompetitionID($competitionID)
	- returns the meetID from a competitionID
public function getInstitutionsInMeet($meet)
	- returns a list of institutions who have someone registered for the meet
public function getCompetitionsInMeet($meet)
	- returns a list of the competitions in a meet
public function getRegFeeAndDates($meetID)
	- returns the registration fees and deadlines
public function maxNumberOfCompetitorsPerEvent($competitionID)
	- returns the maximum allowed competitors per event
public function howManyRegisteredOnEvent($competitionID,$institutionID,$eventID)
	- returns how many people this institutions has registered already on an event
public function registerPersonForCompetition($competitionID, $institutionID, $personID, $teamID, $Discipline, $events, $eventCountFlags, $firstAdd, $minor, $designation)
	- used for INITIAL registration to add someone to a competition
	- this function adds somebody to every apparatus within that competition, with the registered flag set to false. Then, it calls two other functions to set the teamFlag and registeredFlag
checkIfPersonAlreadyRegisteredForMeetDiscipline($personID,$competitionID,$Discipline)
	- returns true if they have been registered for this discipline for this meet
isPersonRegisteredForEvent($personID,$eventID,$competitionID)
	- returns true if a person is registered for an event for a *competition*

	

*/
require_once("auth.php");
class meetRegistration
{
	//set db here see https://stackoverflow.com/questions/2047264/use-of-pdo-in-classes
	
	private $core;
	private $conn;
	public $meetDate;
	public $meetName;
	public $address;
	
	private $stmtIsPersonRegisteredForEvent;
	private $stmtDoesPersonCountForEvent;
	
	function __construct($type,$ID)
	{
		$core = Core::getInstance();
		$conn = $core->dbh;
		if($type == "byComp") //construct by competitionID
			$MeetID = $this->getMeetIDFromCompetitionID($ID);
		elseif($type =="byMeet") //construct by meetID
			$MeetID = $ID;
		else
			;//throw an error.
		
		$stmtMeets = $conn->prepare("
			SELECT
				Date,
				MeetName,
				Hostclub
			FROM
				Events_Meets
			WHERE
				ID = ?
			");
			
		$stmtMeets->bindParam(1, $MeetID, PDO::PARAM_INT, 5);	
		$stmtMeets->execute();
		
		//set address, date & name
		while($row = $stmtMeets->fetch(PDO::FETCH_ASSOC))
		{
			$this->meetDate = $row['Date'];
			$this->meetName = $row['MeetName'];
		}
		$address = [
					'Display'=>'', //the full address with street city state zip 
					'Address1'=>'',
					'Address2'=>'',
					'City'=>'',
					'State'=>'',
					'Zip'=>'',
					'lat'=>'',
					'lng'=>'',
					];
					
					
		$sqlIsPersonRegisteredForEvent = "SELECT
					Registered
				FROM
					Events_Routines
				WHERE
					PersonID = ? AND
					Apparatus = ? AND
					CompetitionID = ?
				;";

		$this->stmtIsPersonRegisteredForEvent = $conn->prepare($sqlIsPersonRegisteredForEvent);
		
		$sqlDoesPersonCountForEvent = "
				SELECT
					ApparatusTeamScore
				FROM
					Events_Routines
				WHERE
					PersonID = ? AND
					Apparatus = ? AND
					CompetitionID = ?
				;";
		
		$this->stmtDoesPersonCountForEvent = $conn->prepare($sqlDoesPersonCountForEvent);
	}
	
	///////////////////////////
	//informational functions//
	///////////////////////////
	public function getMeetIDFromCompetitionID($competitionID)
	{
		global $conn;
		$sql = "
				SELECT
					MeetID
				FROM
					Events_Competitions
				WHERE
					ID = ?
				;";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $competitionID, PDO::PARAM_INT, 6);	
		$stmt->execute();
		
		$meetID = array();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$meetID = $row['MeetID'];
		}
		
		return $meetID;
	}
	
	public function getInstitutionsInMeet($meet)
	{
		global $conn;
		$sql = "
				SELECT
					DISTINCT ClubID As ID
				FROM
					Events_Routines
				WHERE
					CompetitionID IN (SELECT ID FROM Events_Competitions WHERE MeetID = ?)
				;";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $meet, PDO::PARAM_INT, 5);	
		$stmt->execute();
		
		$insts = array();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$insts[] = $row['ID'];
		}
		//return "'".implode("', '", $insts)."'";
		return implode(",", $insts);
	}	
	
	public function getCompetitionsInMeet($meet)
	{
		global $conn;
		$stmtCompetitions = $conn->prepare("
			SELECT
				Events_Competitions.ID,
				Constraints_Leauges.ShortName AS Leauge,
				Constraints_MeetDivisions.Name AS Division,
				Constraints_MeetLevels.DisplayName AS Level,
				Constraints_Disciplines.DisciplineShortName AS Discipline,
				Events_Competitions.Discipline AS DisciplineID
			FROM
				Events_Competitions, 
				Constraints_Leauges,
				Constraints_Disciplines,
				Constraints_MeetDivisions,
				Constraints_MeetLevels
			WHERE
				Events_Competitions.Leauge = Constraints_Leauges.ID AND
				Events_Competitions.Division = Constraints_MeetDivisions.ID AND
				Events_Competitions.Level = Constraints_MeetLevels.ID AND
				Events_Competitions.Discipline = Constraints_Disciplines.ID AND
				Events_Competitions.MeetID = ?
			ORDER BY
				Events_Competitions.Division,
				Events_Competitions.Discipline,
				Events_Competitions.Level
			;");
		
		$stmtCompetitions->bindParam(1, $meet, PDO::PARAM_INT, 5);
		$stmtCompetitions->execute();
		
		//return $stmtCompetitions;
		$count = 0;
		while($row = $stmtCompetitions->fetch(PDO::FETCH_ASSOC))
		{
			$return_arr[$count] = array(
										"ID" => $row['ID'],
										"Division" => $row['Division'],
										"Level" => $row['Level'],
										"Discipline" => $row['Discipline']
										);
			$count++;
		}
		
		return $return_arr;
	}
	
	public function getCompetitionDetails()
	{
		//return level, Discipline, division, etc as array of key value pairs for displayname and ID
		
	}
	
	public function getRegFeeAndDates($meetID,$type = "byMeet") //eventually rewrite so that comps each have their own dates.
	{
		global $conn;
		if($type == "byComp")
		{
			$temp = $this->getMeetIDFromCompetitionID($meetID);
			$meetID = $temp;
		}
		
		//set a default
		$returnStuff = array(
						'stdFee'=>1000,
						'lateFee'=>1000,
						'lateDeadline'=>'',
						'finalDeadline'=>'',
						'perTeamEventLimit'=>'0'
						);
		
		$sql = "
				SELECT
					stdFee,
					lateFee,
					lateDeadline,
					finalDeadline,
					perTeamEventLimit
				FROM
					Events_Meets
				WHERE
					ID = ?
				;";
				
		$stmtRegister = $conn->prepare($sql);
		$stmtRegister->bindParam(1, $meetID, PDO::PARAM_INT, 5);
		
		$stmtRegister->execute();
		
		while($row = $stmtRegister->fetch(PDO::FETCH_ASSOC))
		{
			$returnStuff['stdFee'] = $row['stdFee'];
			$returnStuff['lateFee'] = $row['lateFee'];
			$returnStuff['lateDeadline'] = $row['lateDeadline'];
			$returnStuff['finalDeadline'] = $row['finalDeadline'];
			$returnStuff['perTeamEventLimit'] = $row['perTeamEventLimit'];
		}
		return $returnStuff;
	}
	
	/////////////////////////////
	//lets define some security//
	/////////////////////////////
	private function doesInstitutionAllowSelfRegister($institutionID)
	{
		
	}
	
	private function doesUserHaveRegistrationAccessForInstitution($institutionID)
	{
		$returnValue = false;
		if(userIsExecutiveAdministrator)
			$returnValue = true;
		if(userIsCoachFor($institutionID) || userIsCaptainFor($institutionID))
			$returnValue = true;
			
		return $returnValue;
	}
	
	//////////////////////////////
	//actual registration stuffs//
	//////////////////////////////
	private function addToRegistrationLog($competitionID,$personID,$teamID,$action,$key,$value)
	{
		$userID = getUserID();
		$timestamp = ""; //done on mysql end
		
		global $conn;
		//make a table:
		/*
			0 ID
			1 CompetitionID
			2 PersonID
			3 TeamID
			4 Action
			5 ItemKey
			6 Value
			7 ActionPerformedBy always userid of logged in person
			8 Timestamp always auto in mysql engine
		*/
		/*
		examples:
			create person
				//need to get new person ID?
				1 CompetitionID 	null
				2 PersonID			new id
				3 TeamID			null
				4 Action			createNewPerson
				5 ItemKey			null
				6 Value				new id
		
			add person to competition
				1 CompetitionID	$competitionID
				2 PersonID		$subjectID
				3 TeamID		$teamID
				4 Action		"Add person to competition"
				5 ItemKey		$competitionID
				6 Value			null
			
			remove person to competition
				1 CompetitionID	$competitionID
				2 PersonID		$subjectID
				3 TeamID		$teamID
				4 Action		"remove person from competition"
				5 ItemKey		$competitionID
				6 Value			null
				
			edit person event
				1 CompetitionID	competition id
				2 PersonID		subject id
				3 TeamID		team id
				4 Action		add person to event | remove person from event
				5 ItemKey		eventID
				6 Value			1 | 0
			
			edit person eventCountFlag
				1 CompetitionID	competition id
				2 PersonID		subject id
				3 TeamID		team id
				4 Action		person counts for team score | person doesn't count for team score
				5 ItemKey		eventID
				6 Value			1 | 0
			
			edit team score
				1 CompetitionID	competition id
				2 PersonID		null
				3 TeamID		team id
				4 Action		Team score added | team score removed
				5 ItemKey		null
				6 Value			1 | 0
			
			change person's competition
				1 CompetitionID		OLD competition ID
				2 PersonID			subject
				3 TeamID			null
				4 Action			"change person's competition"
				5 ItemKey			NEW competition ID
				6 Value				null
				
			change person's team (if I add functionality)
			
			change entire team (if I add functionality)
			
		*/
		$sql = "
				Insert Into Log_Registration (CompetitionID, PersonID, TeamID, Action, ItemKey, Value, UserID, EmulatorID) Values(?,?,?,?,?,?,?,?)
				";
				
		$stmt = $conn->prepare($sql);

		$emulatorID = getemulatorUserID();

		$stmt->bindParam(1, $competitionID, PDO::PARAM_INT);
		$stmt->bindParam(2, $personID, PDO::PARAM_INT);	
		$stmt->bindParam(3, $teamID, PDO::PARAM_INT);	
		$stmt->bindParam(4, $action, PDO::PARAM_STR);	
		$stmt->bindParam(5, $key, PDO::PARAM_STR);	
		$stmt->bindParam(6, $value, PDO::PARAM_STR);	
		$stmt->bindParam(7, $_SESSION['userID'], PDO::PARAM_INT);
		$stmt->bindParam(8, $emulatorID, PDO::PARAM_INT);
		
		$stmt->execute();
	}
	
	public function maxNumberOfCompetitorsPerEvent($competitionID)
	{
		global $conn;
		$sql = "
				Select
					TeamMaxOnEvent
				From
					Events_Competitions
				Where
					ID = ?
				";
				
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $competitionID, PDO::PARAM_INT, 5);
		
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$returnStuff = $row['TeamMaxOnEvent'];
		}
		return $returnStuff; //should be int type
	}
	
	public function howManyRegisteredOnEvent($competitionID,$institutionID,$eventID,$designation)
	{
		//NEED CHECK BOTH ON INITIAL ADD AND ON EVENT UPDATE
		global $conn;
		$count = 0;
		$sql = "
				SELECT
					count(*) as num
				FROM
					Events_Routines
				WHERE
					ClubID = ? AND
					Apparatus = ? AND
					CompetitionID = ? AND
					TeamDesignation = ? AND
					Registered = 1
				;";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $institutionID, PDO::PARAM_INT);
		$stmt->bindParam(2, $eventID, PDO::PARAM_INT);	
		$stmt->bindParam(3, $competitionID, PDO::PARAM_INT);	
		$stmt->bindParam(4, $designation, PDO::PARAM_STR);
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$count = $row['num'];
		}
		
		return $count;
	}
	
	public function registerPersonForCompetition($competitionID, $institutionID, $personID, $teamID, $Discipline, $events, $eventCountFlags, $firstAdd, $minor, $designation)
	{
		global $conn;
		$error = false;
		$alreadyRegistered = false;
		$pdoError = "Not Set";
		$executeWasCalled = "";
		$return_arr = array(
					'Error' => false,
					'Message'=>"No error"
					);
		
		//because of this bullshit https://bugs.php.net/bug.php?id=38546 https://stackoverflow.com/questions/10242312/pdo-bindvalue-with-pdoparam-bool-causes-statement-execute-to-fail-silently
		if(($firstAdd == false)||($firstAdd == "")||($firstAdd == "false"))
			$firstAdd = 0;
		if(($firstAdd == true)||($firstAdd=="true"))
			$firstAdd = 1;
		
		if($minor == "false")
			$minor = 0;
		else
			$minor = 1;
		
		try
		{
			$conn->beginTransaction();
			
			//if($firstAdd == true) //FIRSTADD IS A TERRIBLE NAME. it should be eventUpdateOnly or something like that. //also fucking booleans being converted to strings...
			if(true)
			{
				//$alreadyRegistered = checkIfPersonAlreadyRegisteredForCompetition($personID,$competitionID);
				$alreadyRegistered = $this->checkIfPersonAlreadyRegisteredForMeetDiscipline($personID,$competitionID,$Discipline);
				//if($alreadyRegistered == 1)
					//$alreadyRegistered = true;
				//echo "forcompetitioncalled";
			}
			else //we are only editing an event.
			{
				//I want it false, it'll just update the events and not add them.
				//although I need to do this for event-specific last updated timestamps.
				//echo "formeetDisciplinecalled";
			}
			
			//echo "alreadyregistered".$firstAdd;
			//
			
			//ok so the lastregistered date is on insert/update curr_timestamp
			//meaning that I need to set the FEE as well depending on the date. I can either do internally w/SQL or w/passing it in (not in js which can be edited... but php cant)
			//and I need to say if firstadd == true cause modifying doesn't affect the fee, just adding a person.
			$isLate = false;
			$regFeeDates = $this->getRegFeeAndDates($this->getMeetIDFromCompetitionID($competitionID));
			$isLate = (time() > strtotime($regFeeDates['lateDeadline']));
			
			//something about the below line doesn't work.
			if($isLate && ($firstAdd == true)) //if they are late adding it for the first time then its late
				$fee = $regFeeDates['lateFee'];
			else								//if they are just editing an event, or adding someone when its not late
				$fee = $regFeeDates['stdFee'];
			
			$sql = "
					INSERT INTO
						Events_Routines(PersonID,ClubID,TeamDesignation,CompetitionID,ApparatusTeamScore,Exhibitionist,Apparatus,RegisteredBy,Fee,Minor)
						SELECT
							?,
							?,
							?,
							?,
							1,
							0,
							Constraints_Apparatus.ID,
							?,
							?,
							?
						FROM
							Constraints_Apparatus
						WHERE
							Discipline = ?
					;";
					
			$stmtRegister = $conn->prepare($sql);
			
			$stmtRegister->bindParam(1, $personID, PDO::PARAM_INT, 5);			//person
			$stmtRegister->bindParam(2, $institutionID, PDO::PARAM_INT, 5);		//institution
			$stmtRegister->bindParam(3, $designation, PDO::PARAM_STR, 5);			//team A,B,C etc
			$stmtRegister->bindParam(4, $competitionID, PDO::PARAM_INT, 5);		//competition
			$stmtRegister->bindParam(5, $_SESSION['userID'], PDO::PARAM_INT, 6);
			$stmtRegister->bindParam(6, $fee, PDO::PARAM_STR, 6);				//fee
			$stmtRegister->bindParam(7, $minor, PDO::PARAM_BOOL);				//under18
			///whereclause
			$stmtRegister->bindParam(8, $Discipline, PDO::PARAM_INT, 5);			//Discipline
			
			
			if(!$alreadyRegistered)
			{
				//if(numCurrentlyRegistered < maxNumberOfCompetitorsPerEvent)
				if(true)
				{
					$stmtRegister->execute();
					$executeWasCalled = "Execute was called with rownum " . $stmtRegister->rowCount();
					//and then I should also add them to the permission for that MeetSeason.
						//call some other function.
				}
			}
			
			$this->updatePersonEvents($personID,$institutionID,$competitionID,$events,$designation);
			$this->updatePersonEventsCounts($personID,$competitionID,$eventCountFlags,$designation);
			$this->addToRegistrationLog($competitionID,$personID,$designation,"Person Initially Registered for Competition","","");
		}
		catch (PDOException $e)
		{
			$error = true;
			$conn->rollBack();
			$pdoError = 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		}
		
		$newMessage = $this->autoProcessTeamStuff($institutionID,$competitionID,$designation);
		$newMessage = $newMessage['Message'];
		
		if(!$error)
		{
			$conn->commit();
			$return_arr = array(
					'Error' => false,
					//'Message'=>"Commit was called. !alreadyRegistered = " . !$alreadyRegistered . " " . $executeWasCalled . " PDO Error is " . $pdoError
					'Message'=>"firstAdd is " . $firstAdd . " Fee is " . $fee . " islate is " . $isLate . " strtotime(latedeadline) is " . strtotime($regFeeDates['lateDeadline']) . " now is " . time() . " latedeadlinestring is " . $regFeeDates['lateDeadline']
					//'Message'=>print_r($regFeeDates)
					);
			//$this->addToRegistrationLog($competitionID,$personID,"Registered for Competition","","");
		}
		
		
		if(($alreadyRegistered)&&($firstAdd)) //alreadyregistered is set for event updates anyway
		{
			$return_arr = array(
					'Error' => true,
					'Message'=>"That person is already registered for this discipline in this meet",
					'DevMsg'=>"firstAdd is " . $firstAdd . " alreadyRegistered is " . $alreadyRegistered
					);
		}

		if($error)
		{
			$return_arr = array(
					'Error' => true,
					'Message'=>"PDO: " . $pdoError,
					'alreadyRegistered'=> $alreadyRegistered,
					'firstadd'=>$firstAdd
					);
		}
		
		echo json_encode($return_arr);
	}
	
	public function checkIfPersonAlreadyRegisteredForMeetDiscipline($personID,$competitionID,$Discipline)
	{
		global $conn;

		$sql = "
			SELECT 
				count(*) as Num
			FROM
				Events_Routines
			WHERE
				PersonID = ? AND
				CompetitionID IN (Select 
										ID 
									FROM 
										Events_Competitions 
									WHERE 
										Events_Competitions.Discipline = ? AND 
										Events_Competitions.MeetID IN (Select MeetID From Events_Competitions Where ID = ?))
			;";
		$stmtRegister = $conn->prepare($sql);
		
		$stmtRegister->bindParam(1, $personID, PDO::PARAM_INT, 5);		//person
		$stmtRegister->bindParam(2, $Discipline, PDO::PARAM_INT, 1);		//Discipline
		$stmtRegister->bindParam(3, $competitionID, PDO::PARAM_INT, 5);	
		
		$stmtRegister->execute();
		$count = 0;
		
		while($row = $stmtRegister->fetch(PDO::FETCH_ASSOC))
		{
			$count = $row['Num'];
		}
		
		if($count == 0)
			return false;
		else
			return true;
	}
	
	public function isPersonRegisteredForEvent($personID,$eventID,$competitionID)
	{
		global $conn;
		$isRegistered = false;
		
		$stmt = $this->stmtIsPersonRegisteredForEvent;
		
		$stmt->bindParam(1, $personID, PDO::PARAM_INT, 5);
		$stmt->bindParam(2, $eventID, PDO::PARAM_INT, 3);	
		$stmt->bindParam(3, $competitionID, PDO::PARAM_INT, 5);	
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			if($row['Registered'] == 1)
				$isRegistered = true;
			if($row['Registered'] == 0)
				$isRegistered = false;
		}
		
		return $isRegistered;
		
	}
	
	public function checkLogIfPersonRegisteredForMeetBefore()
	{
		//and maybe throw back the timestamp so if its like <5min we can waive fee increases...
	}

	public function doesPersonCountForEvent($personID,$eventID,$competitionID)
	{
		global $conn;
		$isRegistered = false;
		
		$stmt = $this->stmtDoesPersonCountForEvent;
		
		$stmt->bindParam(1, $personID, PDO::PARAM_INT, 5);
		$stmt->bindParam(2, $eventID, PDO::PARAM_INT, 3);	
		$stmt->bindParam(3, $competitionID, PDO::PARAM_INT, 5);	
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			if($row['ApparatusTeamScore'] == 1)
				$isRegistered = true;
		}
		
		return $isRegistered;
		
	}
	
	public function checkIfPersonIsRegisteredForCompetition($competitionID,$personID)
	{
		global $conn;

		$sql = "
			SELECT 
				count(*) as theCount
			FROM
				Events_Routines
			WHERE
				PersonID = ? AND
				CompetitionID = ?
			;";
		$stmtRegister = $conn->prepare($sql);
		
		$stmtRegister->bindParam(1, $personID, PDO::PARAM_INT, 5);		//person
		$stmtRegister->bindParam(2, $competitionID, PDO::PARAM_INT, 5);	//institution
		
		$stmtRegister->execute();
		$count = 0;
		
		while($row = $stmtRegister->fetch(PDO::FETCH_ASSOC))
		{
			$count = $row['theCount'];
		}
		
		if($count == 0)
			return false;
		else
			return true;
		
		//return a few things:
		//are they registered for the specific competition
		//are they registered for that discipline (aka meet-Discipline pair)
	}
	
	public function registerPersonForEvent($personID,$competitionID,$event)
	{
		//$this->addToRegistrationLog($competitionID,$personID,"Registered For Event",$event,"");
	}
	
	public function unregisterPersonForEvent($personID,$competitionID,$event)
	{
		//$this->addToRegistrationLog($competitionID,$personID,"Un-Registered From Event",$event,"");
	}
	
	public function setPersonCountsForEvent($personID,$competitionID,$event)
	{
		//$this->addToRegistrationLog($competitionID,$personID,"Set person counts for team score for event",$event,"");
	}
	
	public function unsetPersonCountsForEvent($personID,$competitionID,$event)
	{
		//$this->addToRegistrationLog($competitionID,$personID,"Un set person counts for team score for event",$event,"");
	}
	
	public function checkHowManyPeopleAreRegisteredForCompetitionPerTeam($competitionID,$institutionID)
	{
		
	}
	
	public function getMaxNumerOfPeoplePerTeamPerCompetition($competitionID)
	{
		
	}
	
	public function getMaxNumberOfPeopleThatCountForEvent($institutionID, $competitionID)
	{
		
	}
	
	public function changePersonDesignationInMeet($person,$competition,$designation,$institution,$oldDesignation)
	{
		global $conn;
		$error = false;
		//$alreadyRegistered = false;
		try
		{
			$conn->beginTransaction();
			
			//$alreadyRegistered = checkIfPersonAlreadyRegisteredForCompetition($personID,$newCompetitionID);
			
			$sql = "
					UPDATE
						Events_Routines
					SET
						TeamDesignation = ?,
						RegisteredBy = ?
					WHERE
						PersonID = ? AND
						CompetitionID = ?
					;";
					
			$stmtRegister = $conn->prepare($sql);
			
			$stmtRegister->bindParam(1, $designation, PDO::PARAM_STR, 5);
			$stmtRegister->bindParam(2, $_SESSION['userID'], PDO::PARAM_INT, 6);
			$stmtRegister->bindParam(3, $person, PDO::PARAM_INT, 5);	
			$stmtRegister->bindParam(4, $competition, PDO::PARAM_INT, 5);		
			
			//if(!$alreadyRegistered)
			if(true)
			{
				$stmtRegister->execute();
			}
		}
		catch (PDOException $e)
		{
			$error = true;
			$conn->rollBack();
			echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		}
		//I tried a finally block but php blew up.
		if(!$error)
		{
			$conn->commit();
			$this->autoProcessTeamStuff($institution,$competition,$oldDesignation); //this isn't removing the old team if empty
			$this->autoProcessTeamStuff($institution,$competition,$designation);
		}
		
		$this->addToRegistrationLog($competition,$person,"","Person's Team Designation Changed",$oldDesignation,$designation);
	}
	
	public function changePersonCompetitionInMeet($personID, $oldCompetitionID, $newCompetitionID, $institution, $designation)
	{
		global $conn;
		$error = false;
		//$alreadyRegistered = false;
		try
		{
			$conn->beginTransaction();
			
			//$alreadyRegistered = checkIfPersonAlreadyRegisteredForCompetition($personID,$newCompetitionID);
			
			$sql = "
					UPDATE
						Events_Routines
					SET
						CompetitionID = ?,
						RegisteredBy = ?
					WHERE
						PersonID = ? AND
						CompetitionID = ?
					;";
					
			$stmtRegister = $conn->prepare($sql);
			
			$stmtRegister->bindParam(1, $newCompetitionID, PDO::PARAM_INT, 5);
			$stmtRegister->bindParam(2, $_SESSION['userID'], PDO::PARAM_INT, 6);
			$stmtRegister->bindParam(3, $personID, PDO::PARAM_INT, 5);	
			$stmtRegister->bindParam(4, $oldCompetitionID, PDO::PARAM_INT, 5);		
			
			//if(!$alreadyRegistered)
			if(true)
			{
				$stmtRegister->execute();
			}
		}
		catch (PDOException $e)
		{
			$error = true;
			$conn->rollBack();
			echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		}
		//I tried a finally block but php blew up.
		if(!$error)
		{
			$conn->commit();
			$this->autoProcessTeamStuff($institution,$oldCompetitionID,$designation); //double check these.
			$this->autoProcessTeamStuff($institution,$newCompetitionID,$designation);
		}
		
		$this->addToRegistrationLog($oldCompetitionID,$personID,"","Person's Competition (level/division) Changed",$oldCompetitionID,$newCompetitionID);
	}
	
	public function updatePersonEventCompetition($personID, $oldCompetitionID, $newCompetitionID, $institution, $apparatus)
	{
		global $conn;
		$error = false;
		//$alreadyRegistered = false;
		try
		{
			$conn->beginTransaction();
			
			//$alreadyRegistered = checkIfPersonAlreadyRegisteredForCompetition($personID,$newCompetitionID);
			
			$sql = "
					UPDATE
						Events_Routines
					SET
						CompetitionID = ?,
						RegisteredBy = ?
					WHERE
						PersonID = ? AND
						CompetitionID = ? AND
						Apparatus =?
					;";
					
			$stmtRegister = $conn->prepare($sql);
			
			$stmtRegister->bindParam(1, $newCompetitionID, PDO::PARAM_INT, 5);
			$stmtRegister->bindParam(2, $_SESSION['userID'], PDO::PARAM_INT, 6);
			$stmtRegister->bindParam(3, $personID, PDO::PARAM_INT, 5);	
			$stmtRegister->bindParam(4, $oldCompetitionID, PDO::PARAM_INT, 5);		
			$stmtRegister->bindParam(5, $apparatus, PDO::PARAM_INT, 5);		
			
			//if(!$alreadyRegistered)
			if(true)
			{
				$stmtRegister->execute();
			}
		}
		catch (PDOException $e)
		{
			$error = true;
			$conn->rollBack();
			echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		}
		//I tried a finally block but php blew up.
		if(!$error)
		{
			$conn->commit();
			
			$this->autoProcessTeamStuff($institution,$oldCompetitionID,"A");
			$this->autoProcessTeamStuff($institution,$newCompetitionID,"A");
			$this->autoProcessTeamStuff($institution,$oldCompetitionID,"B");
			$this->autoProcessTeamStuff($institution,$newCompetitionID,"B");
			$this->autoProcessTeamStuff($institution,$oldCompetitionID,"C");
			$this->autoProcessTeamStuff($institution,$newCompetitionID,"C");
			$this->autoProcessTeamStuff($institution,$oldCompetitionID,"D");
			$this->autoProcessTeamStuff($institution,$newCompetitionID,"D");
			$this->autoProcessTeamStuff($institution,$oldCompetitionID,"E");
			$this->autoProcessTeamStuff($institution,$newCompetitionID,"E");
			$this->autoProcessTeamStuff($institution,$oldCompetitionID,"F");
			$this->autoProcessTeamStuff($institution,$newCompetitionID,"F");
		}
		
		$this->addToRegistrationLog($oldCompetitionID,$personID,"","Person's Competition (level/division) changed for apparatus",$newCompetitionID,$apparatus);
	}
	
	public function updatePersonEvent($personID, $competitionID, $eventID, $registered, $countsForTeamEvent)
	{
		global $conn;
		$error = false;
		$alreadyRegistered = false;
		
		try
		{
			$conn->beginTransaction();
			
			$sql = "
					UPDATE
						Events_Routines
					SET
						Registered = ?,
						RegisteredBy = ?,
						ApparatusTeamScore = ?
					WHERE
						PersonID = ? AND
						CompetitionID = ? AND
						Apparatus = ?
					;";
					
			$stmtRegister = $conn->prepare($sql);
			
			$stmtRegister->bindParam(1, $registered, PDO::PARAM_INT, 1);
			$stmtRegister->bindParam(2, $_SESSION['userID'], PDO::PARAM_INT, 6);
			$stmtRegister->bindParam(2, $countsForTeamEvent, PDO::PARAM_INT, 1);
			$stmtRegister->bindParam(3, $personID, PDO::PARAM_INT, 5);
			$stmtRegister->bindParam(4, $competitionID, PDO::PARAM_INT, 5);
			$stmtRegister->bindParam(5, $eventID, PDO::PARAM_INT, 5);
			
			if($registered=="false") //because of this bullshit https://bugs.php.net/bug.php?id=38546 https://stackoverflow.com/questions/10242312/pdo-bindvalue-with-pdoparam-bool-causes-statement-execute-to-fail-silently
				$registered = 0;
			else
				$registered = 1;
			
			$stmt->execute();
		}
		catch (PDOException $e)
		{
			$error = true;
			$conn->rollBack();
			echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		}
		
		if(!$error)
		{
			$conn->commit();
		}
	}
	
	public function unregisterPersonFromDiscipline($personID,$meetID,$institutionID,$discipline)
	{
		global $conn;
		$sql = "	
			DELETE
			FROM
				Events_Routines
			WHERE
				PersonID = ? AND
				CompetitionID IN (Select ID From Events_Competitions Where MeetID = ? AND Discipline = ?)
			;";
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $personID, PDO::PARAM_INT,10);
		$stmt->bindParam(2, $meetID, PDO::PARAM_INT, 5);	
		$stmt->bindParam(3, $discipline, PDO::PARAM_INT, 5);	
		
		$stmt->execute();
		$this->addToRegistrationLog($meetID,$personID,"","Person Unregistered from Meet for Discipline",$discipline,""); //todo: add teamid
		
		$sql = "SELECT
					ID
				FROM
					Events_Competitions
				WHERE
					MeetID = ?
				";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $meetID, PDO::PARAM_INT,10);
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$competitionID = $row['ID'];
			$this->autoProcessTeamStuff($institutionID,$competitionID,"A");
			$this->autoProcessTeamStuff($institutionID,$competitionID,"B");
			$this->autoProcessTeamStuff($institutionID,$competitionID,"C");
			$this->autoProcessTeamStuff($institutionID,$competitionID,"D");
			$this->autoProcessTeamStuff($institutionID,$competitionID,"E");
			$this->autoProcessTeamStuff($institutionID,$competitionID,"F");
			$this->autoProcessTeamStuff($institutionID,$competitionID,"G");
		}
	}
	
	public function unregisterPerson($personID,$competitionID,$institutionID,$designation)
	{
		global $conn;
		$sql = "	
			DELETE
			FROM
				Events_Routines
			WHERE
				PersonID = ? AND
				CompetitionID = ?
			;";
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $personID, PDO::PARAM_INT,10);
		$stmt->bindParam(2, $competitionID, PDO::PARAM_INT, 5);	
		
		$stmt->execute();
		$this->addToRegistrationLog($competitionID,$personID,"","Person Unregistered from Competition","",""); //todo: add teamid
		
		$this->autoProcessTeamStuff($institutionID,$competitionID,$designation);
	}
	
	///////////////////////////
	///auxilliary team stuff///
	///////////////////////////
	
	public function updateTeamOptions($teamScore, $competitionID, $institutionID, $designation)
	{
		global $conn;
		
		$error = false;
		try
		{
			$conn->beginTransaction();
			$sql = 
				"
					UPDATE
						Events_Teams
					SET
						TeamScore = ?,
						ModifiedBy = ?
					WHERE
						CompetitionID = ? AND
						InstitutionID = ? AND
						TeamDesignation = ?
				";
				
			$stmt = $conn->prepare($sql);
			
			$stmt->bindParam(1, $hasScore, PDO::PARAM_INT, 1);
			$stmt->bindParam(2, $_SESSION['userID'], PDO::PARAM_INT, 7);
			$stmt->bindParam(3, $competitionID, PDO::PARAM_INT, 5);	
			$stmt->bindParam(4, $institutionID, PDO::PARAM_INT, 5);	
			$stmt->bindParam(5, $designation, PDO::PARAM_STR, 5);	
			
			if($teamScore=="false") //because of this bullshit https://bugs.php.net/bug.php?id=38546 https://stackoverflow.com/questions/10242312/pdo-bindvalue-with-pdoparam-bool-causes-statement-execute-to-fail-silently
				$hasScore = 0;
			else
				$hasScore = 1;
			
			$stmt->execute();
			$this->addToRegistrationLog($competitionID,$institutionID,"","Team Score Changed",$teamScore,$designation); //todo: add teamid
		}
		catch (PDOException $e)
		{
			$error = true;
			$conn->rollBack();
			echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		}
		//I tried a finally block but php blew up.
		if(!$error)
		{
			$conn->commit();
		}
		$return_arr = array(
					'Error' => false,
					'Message'=>"No Error"
					);
		if($error)
		{
			$return_arr = array(
					'Error' => true,
					'Message'=>"Error setting team score flag"
					);
		}
		return json_encode($return_arr);
		
	}
	
	public function isTeamEmpty($institution,$competition,$designation)
	{
		global $conn;
		
		$sql = "
				SELECT
					count(*) AS Num
				FROM
					Events_Routines
				WHERE
					CompetitionID = ? AND
					ClubID = ? AND
					TeamDesignation = ?
				";
				
		$stmt = $conn->prepare($sql);
			
		$stmt->bindParam(1, $competition, PDO::PARAM_INT, 5);		//person
		$stmt->bindParam(2, $institution, PDO::PARAM_INT, 5);		//institution	
		$stmt->bindParam(3, $designation, PDO::PARAM_STR, 5);		//team	
		
		$stmt->execute();
		
		$count = 0;
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$count = $row['Num'];
		}
		
		if($count == 0)
			return true;
		else
			return false;
		
	}

	public function doesTeamExist($institution,$competition,$designation)
	{
		global $conn;
		
		$sql = "
				SELECT
					count(*) AS Num
				FROM
					Events_Teams
				WHERE
					CompetitionID = ? AND
					InstitutionID = ? AND
					TeamDesignation = ?
				";
		
		$stmt = $conn->prepare($sql);
			
		$stmt->bindParam(1, $competition, PDO::PARAM_INT, 5);		//person
		$stmt->bindParam(2, $institution, PDO::PARAM_INT, 5);		//institution		
		$stmt->bindParam(3, $designation, PDO::PARAM_STR, 5);		//team			
		
		$stmt->execute();
		
		$count = 0;
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$count = $row['Num'];
		}
		
		if($count == 0)
			return false;
		else
			return true;
	}
	
	public function autoProcessTeamStuff($institutionID,$competitionID,$teamDesignation)
	{
		global $conn;
		//this function will be called on adding a person, changing a persons team/competition, and on removing a person.
		
		$teamExists = false;
		$peopleOnTeam = false;
		$error = false;
		
		//try
		{
			//$conn->beginTransaction();
			//Check if a team exists in teams.
			$teamExists = $this->doesTeamExist($institutionID,$competitionID,$teamDesignation);
			//echo "Team Exists ".$teamExists;
			//Check if there are people on the team.
			$peopleOnTeam = !$this->isTeamEmpty($institutionID,$competitionID,$teamDesignation);
			//echo "PeopleExists ".$peopleOnTeam;
			
			//this triggers when I first add a person for a competition. 
			if(!$teamExists && $peopleOnTeam)
			{
				$sql1 = "
					INSERT INTO
						Events_Teams(CompetitionID, InstitutionID, TeamDesignation, Name)
					Values
						(?,?,?, (SELECT CONCAT(
												Constraints_MeetDivisions.Name, ' ',
												Constraints_MeetLevels.DisplayName, ' ',
												Constraints_Disciplines.DisciplineShortName
											 ) 
								FROM 
									Events_Competitions,
									Constraints_Disciplines,
									Constraints_MeetDivisions,
									Constraints_MeetLevels
								WHERE
									Events_Competitions.Division = Constraints_MeetDivisions.ID AND
									Events_Competitions.Level = Constraints_MeetLevels.ID AND
									Events_Competitions.Discipline = Constraints_Disciplines.ID AND
									Events_Competitions.ID = ?
								)
						)
					";
				$stmt1 = $conn->prepare($sql1);
				$stmt1->bindParam(1, $competitionID, PDO::PARAM_INT, 5);	
				$stmt1->bindParam(2, $institutionID, PDO::PARAM_INT, 5);
				$stmt1->bindParam(3, $teamDesignation, PDO::PARAM_STR, 5);
				$stmt1->bindParam(4, $competitionID, PDO::PARAM_INT, 5);
				
				$stmt1->execute();
			}
			
			//this triggers when all people are removed from a team.
			if(!$peopleOnTeam && $teamExists)
			{
				$sql3 = "
					DELETE FROM
						Events_Teams
					WHERE
						CompetitionID = ? AND
						InstitutionID = ? AND
						TeamDesignation = ?
					LIMIT 1
					";
				$stmt3 = $conn->prepare($sql3);
				$stmt3->bindParam(1, $competitionID, PDO::PARAM_INT, 5);	
				$stmt3->bindParam(2, $institutionID, PDO::PARAM_INT, 5);
				$stmt3->bindParam(3, $teamDesignation, PDO::PARAM_STR, 5);
				
				$stmt3->execute();
			}
		}
		/*catch (PDOException $e)
		{
			$error = true;
			$conn->rollBack();
			echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		}*/
		//I tried a finally block but php blew up.
		if(!$error)
		{
			//$conn->commit();
		}
		$return_arr = array(
					'Error' => false,
					'Message'=>"No Error"
					);
		if($error)
		{
			$return_arr = array(
					'Error' => true,
					'Message'=>"Team consolidation failure."
					);
		}
		//return json_encode($return_arr); //ah crap now Im returning duplicate arrays
	}
	
	private function getEventLevelCompID($iMeet,$iPerson,$iApparatus)
	{
		global $conn;
		$sql = "SELECT
					CompetitionID
				FROM
					Events_Routines
				WHERE
					Apparatus = ? AND
					PersonID = ? AND
					CompetitionID IN (Select ID From Events_Competitions Where MeetID = ?)
				";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $iApparatus, PDO::PARAM_INT, 5);	
		$stmt->bindParam(2, $iPerson, PDO::PARAM_INT, 8);	
		$stmt->bindParam(3, $iMeet, PDO::PARAM_INT, 8);	
		
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			return $row['CompetitionID'];
		}
	}
	
	private function getEventLevelCompName($iMeet,$iPerson,$iApparatus)
	{
		global $conn;
		$sql = "SELECT
					concat(
							Constraints_MeetDivisions.Name, ' ',
							Constraints_MeetLevels.DisplayName, ' ',
							Constraints_Disciplines.DisciplineShortName
						 ) AS CompetitionName
				FROM
					Events_Routines,
					Constraints_MeetDivisions,
					Constraints_MeetLevels,
					Constraints_Disciplines,
					Events_Competitions
				WHERE
					Apparatus = ? AND
					PersonID = ? AND
					CompetitionID IN (Select ID From Events_Competitions Where MeetID = ?) AND
					Events_Routines.CompetitionID = Events_Competitions.ID AND
					Events_Competitions.Division = Constraints_MeetDivisions.ID AND
					Events_Competitions.Level = Constraints_MeetLevels.ID AND
					Events_Competitions.Discipline = Constraints_Disciplines.ID
				";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $iApparatus, PDO::PARAM_INT, 5);	
		$stmt->bindParam(2, $iPerson, PDO::PARAM_INT, 8);	
		$stmt->bindParam(3, $iMeet, PDO::PARAM_INT, 8);	
		
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			return $row['CompetitionName'];
		}
	}
	
	public function getEventLevelPeopleInTeam($iMeet, $iInstitution, $iDiscipline)
	{
		global $conn;
		$theArray = array();
		
		$sql = "SELECT
					DISTINCT Events_Routines.PersonID,
					Minor,
					Concat(LastName, ', ', FirstName) AS PersonName,
					Identifiers_Institutions.Name AS Institution,
					Identifiers_Institutions.ID AS InstitutionID
				FROM
					Events_Routines,
					Identifiers_People,
					Identifiers_Institutions
				WHERE
					Identifiers_Institutions.ID = Events_Routines.ClubID AND
					Events_Routines.PersonID = Identifiers_People.ID AND
					Events_Routines.CompetitionID IN (Select ID From Events_Competitions Where MeetID = ?) AND
					Events_Routines.ClubID = ?
			";
			
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $iMeet, PDO::PARAM_INT, 8);	
		$stmt->bindParam(2, $iInstitution, PDO::PARAM_INT, 8);	
		
		$stmt->execute();
		
		$count = 0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			//echo "in the loop";
			$person = $row['PersonID'];
			$TRCompID = $this->getEventLevelCompID($iMeet,$person,17);
			$DMCompID = $this->getEventLevelCompID($iMeet,$person,18);
			$STCompID = $this->getEventLevelCompID($iMeet,$person,19);
			$RFCompID = $this->getEventLevelCompID($iMeet,$person,20);
			
			$TR = $this->isPersonRegisteredForEvent($person,17,$TRCompID);
			$DM = $this->isPersonRegisteredForEvent($person,18,$DMCompID);
			$ST = $this->isPersonRegisteredForEvent($person,19,$STCompID);
			$RF = $this->isPersonRegisteredForEvent($person,20,$RFCompID);
			
			if($TR == false) {$TR = "Not Registered";} else {$TR = $this->getEventLevelCompName($iMeet,$person,17);}
			if($DM == false) {$DM = "Not Registered";} else {$DM = $this->getEventLevelCompName($iMeet,$person,18);}
			if($ST == false) {$ST = "Not Registered";} else {$ST = $this->getEventLevelCompName($iMeet,$person,19);}
			if($RF == false) {$RF = "Not Registered";} else {$RF = $this->getEventLevelCompName($iMeet,$person,20);}
			
			$theArray[$count] = array(
									'ID'=>$person,
									'MeetID'=>$iMeet,
									'Minor'=>$row['Minor'],
									'Name'=>$row['PersonName'],
									'Institution'=>$row['Institution'],
									'InstitutionID'=>$row['InstitutionID'],
									'TRCompID'=>$TRCompID,
									'TR'=>$TR,
									'DMCompID'=>$DMCompID,
									'DM'=>$DM,
									'STCompID'=>$STCompID,
									'ST'=>$ST,
									'RFCompID'=>$RFCompID,
									'RF'=>$RF
									);
			
			$count++;
		}
		return $theArray;
	}
	
	public function getPeopleInTeam($meetID, $institutionID, $Discipline)
	{
		
		$theArray = array();
		if($this->getInstitutionsInMeet($meetID) != "")
		{
			if($institutionID == "false")
			{
				$newInstitutionID = $this->getInstitutionsInMeet($meetID);
				//echo $institutionID;
			}
			
			global $conn;
			//ughh I need a left join in case something is ever missing.
			$sql = "SELECT
						DISTINCT Events_Routines.PersonID,
						Minor,
						Concat(LastName, ', ', FirstName) AS PersonName,
						Events_Routines.CompetitionID,
						concat(
							Constraints_MeetDivisions.Name, ' ',
							Constraints_MeetLevels.DisplayName, ' ',
							Constraints_Disciplines.DisciplineShortName
						 ) AS CompetitionName,
						LatestDateRegistered,
						RegPersonName,
						MAX(Fee) As theFee,
						Identifiers_Institutions.Name AS Institution,
						TeamDesignation
					FROM
						Identifiers_People,
						Identifiers_Institutions,
						Events_Competitions,
						Constraints_MeetDivisions,
						Constraints_MeetLevels,
						Constraints_Disciplines,
						(Select ID, Concat(LastName, ', ', FirstName) AS RegPersonName FROM Identifiers_People) alias2,
						Events_Routines
					LEFT JOIN
						(Select MAX(RegDate) AS LatestDateRegistered, PersonID, CompetitionID FROM Events_Routines GROUP BY PersonID, CompetitionID) alias
					ON
						Events_Routines.CompetitionID = alias.CompetitionID AND
						Events_Routines.PersonID = alias.PersonID
					WHERE
						Identifiers_Institutions.ID = Events_Routines.ClubID AND
						Events_Routines.PersonID = Identifiers_People.ID AND ";
				
			if($institutionID == "false")
				$sql .=	"Events_Routines.ClubID IN (".$newInstitutionID.") AND ";
			else
				$sql .=	"Events_Routines.ClubID IN (?) AND ";
				
				$sql .=	"Events_Routines.CompetitionID = Events_Competitions.ID AND
						Events_Competitions.Division = Constraints_MeetDivisions.ID AND
						Events_Competitions.Level = Constraints_MeetLevels.ID AND
						Events_Competitions.Discipline = Constraints_Disciplines.ID AND
						Events_Routines.CompetitionID IN (Select ID From Events_Competitions WHERE MeetID = ? AND Discipline = ?) AND
						Events_Routines.RegisteredBy = alias2.ID
					GROUP BY
						Events_Routines.PersonID,
						Events_Routines.CompetitionID
					ORDER BY
						Constraints_MeetDivisions.Name,
						Constraints_MeetLevels.DisplayName,
						Concat(LastName, ', ', FirstName)
					;";			

			$stmt = $conn->prepare($sql);
			
			
			if($institutionID != "false")
			{
				$stmt->bindParam(1, $institutionID, PDO::PARAM_INT, 5);	
				$stmt->bindParam(2, $meetID, PDO::PARAM_INT, 5);
				$stmt->bindParam(3, $Discipline, PDO::PARAM_INT, 1);
			}
			else
			{
				$stmt->bindParam(1, $meetID, PDO::PARAM_INT, 5);
				$stmt->bindParam(2, $Discipline, PDO::PARAM_INT, 1);
			}
			
			
			$stmt->execute();
			
			$count = 0;
			
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$competitionID = $row['CompetitionID'];
				$person = $row['PersonID'];
				$minor = $row['Minor'];
				
				if($Discipline == 3)
				{
					$menLecture = $this->isPersonRegisteredForEvent($person,12,$competitionID);
					$womenLecture = $this->isPersonRegisteredForEvent($person,13,$competitionID);
					$social = $this->isPersonRegisteredForEvent($person,14,$competitionID);
					$workout = $this->isPersonRegisteredForEvent($person,15,$competitionID);
					$meetLecture = $this->isPersonRegisteredForEvent($person,16,$competitionID);
					
					$theArray[$count] = array(
						'ID'=>$person,
						'Name'=>$row['PersonName'],
						'Minor'=>$minor,
						'CompetitionID'=>$competitionID,
						'DisciplineID'=>$Discipline,
						'Team'=>$row['CompetitionName'],
						'MenLecture'=>$menLecture,
						'WomenLecture'=>$womenLecture,
						'MeetLecture'=>$meetLecture,
						'Social'=>$social,
						'Workout'=>$workout,
						'Fee'=>$row['theFee'],
						'RegDate'=>$row['LatestDateRegistered'],
						'RegBy'=>$row['RegPersonName'],
						'Institution'=>$row['Institution'],
						'Designation'=>$row['TeamDesignation']
					);
				}		
				
				if($Discipline == 2)
				{
					$FXCount = $this->doesPersonCountForEvent($person,1,$competitionID);
					$PHCount = $this->doesPersonCountForEvent($person,2,$competitionID);
					$SRCount = $this->doesPersonCountForEvent($person,3,$competitionID);
					$VTCount = $this->doesPersonCountForEvent($person,4,$competitionID);
					$PBCount = $this->doesPersonCountForEvent($person,5,$competitionID);
					$HBCount = $this->doesPersonCountForEvent($person,6,$competitionID);
					
					$FX = $this->isPersonRegisteredForEvent($person,1,$competitionID);
					$PH = $this->isPersonRegisteredForEvent($person,2,$competitionID);
					$SR = $this->isPersonRegisteredForEvent($person,3,$competitionID);
					$VT = $this->isPersonRegisteredForEvent($person,4,$competitionID);
					$PB = $this->isPersonRegisteredForEvent($person,5,$competitionID);
					$HB = $this->isPersonRegisteredForEvent($person,6,$competitionID);
					$AA = ($FX&&$PH&&$SR&&$VT&&$PB&&$HB);
					$theArray[$count] = array(
												'ID'=>$person,
												'Name'=>$row['PersonName'],
												'Minor'=>$minor,
												'CompetitionID'=>$competitionID,
												'DisciplineID'=>$Discipline,
												'Team'=>$row['CompetitionName'],
												'MFX'=>$FX,
												'MPH'=>$PH,
												'MSR'=>$SR,
												'MVT'=>$VT,
												'MPB'=>$PB,
												'MHB'=>$HB,
												'MAA'=>$AA,
												'MFXCount'=>$FXCount,
												'MPHCount'=>$PHCount,
												'MSRCount'=>$SRCount,
												'MVTCount'=>$VTCount,
												'MPBCount'=>$PBCount,
												'MHBCount'=>$HBCount,
												'Fee'=>$row['theFee'],
												'RegDate'=>$row['LatestDateRegistered'],
												'RegBy'=>$row['RegPersonName'],
												'Institution'=>$row['Institution'],
												'Designation'=>$row['TeamDesignation']
											);
				}
				if($Discipline == 1)
				{
					$VTCount = $this->doesPersonCountForEvent($person,8,$competitionID);
					$UBCount = $this->doesPersonCountForEvent($person,9,$competitionID);
					$BBCount = $this->doesPersonCountForEvent($person,10,$competitionID);
					$FXCount = $this->doesPersonCountForEvent($person,11,$competitionID);
					
					$VT = $this->isPersonRegisteredForEvent($person,8,$competitionID);
					$UB = $this->isPersonRegisteredForEvent($person,9,$competitionID);
					$BB = $this->isPersonRegisteredForEvent($person,10,$competitionID);
					$FX = $this->isPersonRegisteredForEvent($person,11,$competitionID);
					//something about this resets it for the women but not the men. I don't fucking understand this stupid shit.
					$AA = ($VT&&$UB&&$BB&&$FX);
					$theArray[$count] = array(
												'ID'=>$person,
												'Name'=>$row['PersonName'],
												'Minor'=>$minor,
												'CompetitionID'=>$competitionID,
												'DisciplineID'=>$Discipline,
												'Team'=>$row['CompetitionName'],
												'WVT'=>$VT,
												'WUB'=>$UB,
												'WBB'=>$BB,
												'WFX'=>$FX,
												'WAA'=>$AA,
												'WVTCount'=>$this->doesPersonCountForEvent($person,8,$competitionID),
												'WUBCount'=>$this->doesPersonCountForEvent($person,9,$competitionID),
												'WBBCount'=>$this->doesPersonCountForEvent($person,10,$competitionID),
												'WFXCount'=>$this->doesPersonCountForEvent($person,11,$competitionID),
												'Fee'=>$row['theFee'],
												'RegDate'=>$row['LatestDateRegistered'],
												'RegBy'=>$row['RegPersonName'],
												'Institution'=>$row['Institution'],
												'Designation'=>$row['TeamDesignation']
											);
				}
				//echo $count;
				//print_r($row);
				$count++;
			}
		}
		return $theArray;
	}

	public function getTeamTableData($institutionID, $meetID)
	{
		$teamfee = 10;
		$theArray = array();
		if($this->getInstitutionsInMeet($meetID) != "")
		{
			if($institutionID == "false")
			{
				$newInstitutionID = $this->getInstitutionsInMeet($meetID);
			}
			global $conn;
				$sql = "
					SELECT
						Events_Teams.ID,
						Events_Teams.CompetitionID,
						Events_Teams.InstitutionID,
						Events_Teams.Rotation,
						Identifiers_Institutions.Name AS InstitutionName,
						concat(
							Constraints_MeetDivisions.Name, ' ',
							Constraints_MeetLevels.DisplayName, ' ',
							Constraints_Disciplines.DisciplineShortName
						 ) AS Name,
						Events_Teams.TeamDesignation,
						Events_Teams.TeamScore,
						Events_Teams.TeamFee,
						Events_Teams.LastModified,
						Concat(FirstName, ' ', LastName) AS personLastModified
					FROM
						Events_Competitions,
						Identifiers_Institutions,
						Constraints_Disciplines,
						Constraints_MeetDivisions,
						Constraints_MeetLevels,
						Events_Teams
					LEFT JOIN
						Identifiers_People
					ON
						Identifiers_People.ID = Events_Teams.ModifiedBy
					WHERE
						Events_Teams.CompetitionID = Events_Competitions.ID AND
						Events_Competitions.Division = Constraints_MeetDivisions.ID AND
						Events_Competitions.Level = Constraints_MeetLevels.ID AND
						Events_Competitions.Discipline = Constraints_Disciplines.ID AND
						Identifiers_Institutions.ID = Events_Teams.InstitutionID AND
					
						Events_Teams.CompetitionID IN (SELECT ID From Events_Competitions WHERE MeetID = ?) AND ";
			
			if($institutionID == "false")
				$sql .=	"Events_Teams.InstitutionID IN (".$newInstitutionID.") ";
			else		
				$sql .=	"Events_Teams.InstitutionID IN (?) ";
			
				$sql .= "
						
					;";
			$stmt = $conn->prepare($sql);
			
			if($institutionID == "false")
			{
				$stmt->bindParam(1, $meetID, PDO::PARAM_INT, 5);
			}
			else
			{
				$stmt->bindParam(1, $meetID, PDO::PARAM_INT, 5);
				$stmt->bindParam(2, $institutionID, PDO::PARAM_INT, 5);
			}
			
			$stmt->execute();
			
			$count = 0;
			
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{		
				$teamScore = false; //convet T/F to 1/0
				if($row['TeamScore']==1)
					$teamScore = 1;
				
				//I'm not sure that the pointer isnt moving to the next row
				$currentInst = $row['InstitutionID'];
				$currentComp = $row['CompetitionID'];
				$currentDesignation = $row['TeamDesignation'];
				
				$floorTotal = ($this->getTeamCountForEvent($currentInst,$currentComp,1,$currentDesignation) + $this->getTeamCountForEvent($currentInst,$currentComp,11,$currentDesignation));
				$vaultTotal = ($this->getTeamCountForEvent($currentInst,$currentComp,4,$currentDesignation) + $this->getTeamCountForEvent($currentInst,$currentComp,8,$currentDesignation));
				
				$theArray[$count] = array(
												'ID'=>$row['ID'],
												'MeetID'=>$meetID,
												'CompetitionID'=>$currentComp,
												'Rotation'=>$row['Rotation'],
												'InstitutionID'=>$currentInst,
												'InstitutionName'=>$row['InstitutionName'],
												'TeamName'=>$row['Name'],
												'Designation'=>$row['TeamDesignation'],
												'TeamScore'=>$teamScore,
												'TeamFee'=>$teamfee*$row['TeamScore'],
												'LastModifiedDate'=>$row['LastModified'],
												'LastModifiedPerson'=>$row['personLastModified'],
												//this should be replaced by foreach getEventsForDiscipline($Discipline)??
												'FX'=>$floorTotal,
												'MFX'=>$this->getTeamCountForEvent($currentInst,$currentComp,1,$currentDesignation),
												'WFX'=>$this->getTeamCountForEvent($currentInst,$currentComp,11,$currentDesignation),
												'PH'=>$this->getTeamCountForEvent($currentInst,$currentComp,2,$currentDesignation),
												'SR'=>$this->getTeamCountForEvent($currentInst,$currentComp,3,$currentDesignation),
												'VT'=>$vaultTotal,
												'MVT'=>$this->getTeamCountForEvent($currentInst,$currentComp,4,$currentDesignation),
												'WVT'=>$this->getTeamCountForEvent($currentInst,$currentComp,8,$currentDesignation),
												'PB'=>$this->getTeamCountForEvent($currentInst,$currentComp,5,$currentDesignation),
												'HB'=>$this->getTeamCountForEvent($currentInst,$currentComp,6,$currentDesignation),
												'UB'=>$this->getTeamCountForEvent($currentInst,$currentComp,9,$currentDesignation),
												'BB'=>$this->getTeamCountForEvent($currentInst,$currentComp,10,$currentDesignation),
												'MenLecture'=>$this->getTeamCountForEvent($currentInst,$currentComp,12,$currentDesignation),
												'WomenLecture'=>$this->getTeamCountForEvent($currentInst,$currentComp,13,$currentDesignation),
												'Social'=>$this->getTeamCountForEvent($currentInst,$currentComp,14,$currentDesignation),
												'Workout'=>$this->getTeamCountForEvent($currentInst,$currentComp,15,$currentDesignation),
												'MeetLecture'=>$this->getTeamCountForEvent($currentInst,$currentComp,16,$currentDesignation)
											);
				$count++;
			}
		}
		return $theArray;
	}
	
	public function getTeamCountForEvent($institutionID,$competitionID,$event,$designation)
	{
		global $conn;
		$error = false;
		$count = 0;
		
		try
		{
			//$conn->beginTransaction();
			$sql2 = "
					SELECT 
						Sum(Registered) As theCount
					FROM
						Events_Routines
					WHERE
						ClubID = ? AND
						CompetitionID = ? AND
						Apparatus = ? AND
						TeamDesignation = ?
					;";
					
			$stmt2 = $conn->prepare($sql2);
			
			$stmt2->bindParam(1, $institutionID, PDO::PARAM_INT, 5);
			$stmt2->bindParam(2, $competitionID, PDO::PARAM_INT, 5);
			$stmt2->bindParam(3, $event, PDO::PARAM_INT, 5);
			$stmt2->bindParam(4, $designation, PDO::PARAM_STR);
			
			$stmt2->execute();
			
			while($row = $stmt2->fetch(PDO::FETCH_ASSOC))
			{
				$count += $row['theCount'];
			}
		}
		
		catch(PDOException $e)
		{
			$error = true;
			//$conn->rollBack();
			return 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		}

		if(!$error)
			$foo = "bar";//$conn->commit();
		
		return $count;
		//
	}

	
///////to be replaced///////////////
	function updatePersonEvents($personID,$institutionID,$competitionID,$events,$designation) //this stays and I utilize the registerPersonForEvent and unRegisterPersonForEvent functions in the class are for.
	{
		global $conn;
		$eventID = 0;
		$registered = 1;
		$sql = "
				UPDATE
					Events_Routines
				SET
					Registered = ?,
					RegisteredBy = ?
				WHERE
					PersonID = ? AND
					CompetitionID = ? AND
					Apparatus = ?
				;";
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $registered, PDO::PARAM_INT,10);
		$stmt->bindParam(2, $_SESSION['userID'], PDO::PARAM_INT, 6);
		$stmt->bindParam(3, $personID, PDO::PARAM_INT, 5);
		$stmt->bindParam(4, $competitionID, PDO::PARAM_INT, 5);	
		$stmt->bindParam(5, $eventID, PDO::PARAM_INT, 3);	
		
		foreach ($events AS $eventID => $tregistered) //check how this handles the events array
		{
			if($tregistered=="false") //because of this bullshit https://bugs.php.net/bug.php?id=38546 https://stackoverflow.com/questions/10242312/pdo-bindvalue-with-pdoparam-bool-causes-statement-execute-to-fail-silently
				$registered = 0;
			else
			{
				$registered = 1;
				
				$max = $this->maxNumberOfCompetitorsPerEvent($competitionID);
				$numCurrentlyRegistered = $this->howManyRegisteredOnEvent($competitionID,$institutionID,$eventID,$designation);
				
				if($numCurrentlyRegistered < $max)
				{
					$stmt->execute();
					$this->addToRegistrationLog($competitionID,$personID,$designation,"Register Person for Event",$eventID,"");
				}
			}
		}
	}

	public function updatePersonEventsCounts($personID,$competitionID,$eventCountFlags,$teamID)
	{
		global $conn;
		$eventID = 0;
		$counts = 1;
		$sql = "
				UPDATE
					Events_Routines
				SET
					ApparatusTeamScore = ?,
					RegisteredBy = ?
				WHERE
					PersonID = ? AND
					CompetitionID = ? AND
					Apparatus = ?
				;";
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $counts, PDO::PARAM_INT,10);
		$stmt->bindParam(2, $_SESSION['userID'], PDO::PARAM_INT, 6);
		$stmt->bindParam(3, $personID, PDO::PARAM_INT, 5);
		$stmt->bindParam(4, $competitionID, PDO::PARAM_INT, 5);	
		$stmt->bindParam(5, $eventID, PDO::PARAM_INT, 3);	
		
		foreach ($eventCountFlags AS $eventID => $tcounts) //check how this handles the events array
		{
			if(true) //if numcountperevent + 1 > maxAllowedToCountOnEvent($competition)
			{
				if($tcounts=="false") //because of this bullshit https://bugs.php.net/bug.php?id=38546 https://stackoverflow.com/questions/10242312/pdo-bindvalue-with-pdoparam-bool-causes-statement-execute-to-fail-silently
					$counts = 0;
				else
				{
					$counts = 1;
					$stmt->execute();
					//$this->addToRegistrationLog($competitionID,$personID,$teamID,"Person Counts for Team on Event",$eventID,"");
					//^^so lets skip because this function is being deprecated.
				}
			}
		}
	}
	
	public function howManyCountOnEvent($competition,$institution,$event)
	{
		global $conn;
		$counting = 0;
		
		$sql = "
				SELECT
					count(*) As Num
				FROM
					Events_Routines
				WHERE
					InstitutionID = ? AND
					Apparatus = ? AND
					CompetitionID = ? AND
					ApparatusTeamScore = 1
				;";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $institution, PDO::PARAM_INT, 5);
		$stmt->bindParam(2, $eventID, PDO::PARAM_INT, 3);	
		$stmt->bindParam(3, $competitionID, PDO::PARAM_INT, 5);	
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$counting = $row['Num'];
		}
		
		return $counting;
	}
	
	public function updateEventForPerson($personID,$institutionID,$competitionID,$eventID,$registered,$designation)
	{
		global $conn;
		$error = false;
		$conn->beginTransaction();
		
		if($registered=="false") //because of this bullshit https://bugs.php.net/bug.php?id=38546 https://stackoverflow.com/questions/10242312/pdo-bindvalue-with-pdoparam-bool-causes-statement-execute-to-fail-silently
		{
			$registered = 0;
			$booleanRegistered = false;
			$msg = "Person Removed from Event";
		}
		else
		{
			$registered = 1;
			$booleanRegistered = true;
			$msg = "Person Added to Event";
		}
		
		$anActualUpdate = true;
		if($this->isPersonRegisteredForEvent($personID,$eventID,$competitionID) == $booleanRegistered) //then this isn't a real update.
			$anActualUpdate = false;
			
		try
		{
			//if($this->checkIfPersonIsRegisteredForCompetition($personID,$competitionID))
			if(true) //for now because if zero rows updated then w/e.
			{
				$sql = "
						UPDATE
							Events_Routines
						SET
							Registered = ?,
							RegisteredBy = ?
						WHERE
							PersonID = ? AND
							CompetitionID = ? AND
							Apparatus = ?
						;";
				$stmt = $conn->prepare($sql);
				
				$stmt->bindParam(1, $registered, PDO::PARAM_INT,10);
				$stmt->bindParam(2, $_SESSION['userID'], PDO::PARAM_INT, 6);
				$stmt->bindParam(3, $personID, PDO::PARAM_INT, 5);
				$stmt->bindParam(4, $competitionID, PDO::PARAM_INT, 5);	
				$stmt->bindParam(5, $eventID, PDO::PARAM_INT, 3);	
				
				$max = $this->maxNumberOfCompetitorsPerEvent($competitionID);
				$numCurrentlyRegistered = $this->howManyRegisteredOnEvent($competitionID,$institutionID,$eventID,$designation);
				//echo "max is: " . $max . " and num is: " . $numCurrentlyRegistered;
				
				if((($numCurrentlyRegistered < $max) && ($registered == 1)) || ($registered == 0))
				{
					$stmt->execute();
					if($anActualUpdate)
					{
						$this->addToRegistrationLog($competitionID,$personID,"",$msg,$eventID,"");
					}
				}
				elseif($numCurrentlyRegistered == $max)
				{
					$error = true;
					$conn->rollBack();
					$return_arr = array(
						'Error' => $error,
						'Message'=>"This competition has a limit of " . $max . " people per event per level."
						);
				}
			}
		}
		catch (PDOException $e)
		{
			$error = true;
			$conn->rollBack();
			 
			$details = 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
			 
			$return_arr = array(
					'Error' => $error,
					'Message'=>"Error in updateEventForPerson",
					'Details'=>$details
					);
		}
		if(!$error)
		{
			$conn->commit();
			$return_arr = array(
					'Error' => $error,
					'Message'=>"No Error"
					);
		}
		return json_encode($return_arr);
	}
	
	public function updateEventCountsForPerson($personID,$institutionID,$competitionID,$eventID,$counts,$designation)
	{
		global $conn;
		$error = false;
		$conn->beginTransaction();
		
		if($counts=="false") //because of this bullshit https://bugs.php.net/bug.php?id=38546 https://stackoverflow.com/questions/10242312/pdo-bindvalue-with-pdoparam-bool-causes-statement-execute-to-fail-silently
		{
			$counts = 0;
			$booleanRegistered = false;
			$msg = "Person Doesn't counts For Team Score on Event";
		}
		else
		{
			$counts = 1;
			$booleanRegistered = true;
			$msg = "Person Counts For Team Score on Event";
		}
		
		$anActualUpdate = true;
		if($this->doesPersonCountForEvent($personID,$eventID,$competitionID) == $booleanRegistered) //then this isn't a real update.
			$anActualUpdate = false;
			
		try
		{
			//if($this->checkIfPersonIsRegisteredForCompetition($personID,$competitionID))
			if(true) //for now because if zero rows updated then w/e.
			{
				$sql = "
						UPDATE
							Events_Routines
						SET
							ApparatusTeamScore = ?,
							RegisteredBy = ?
						WHERE
							PersonID = ? AND
							CompetitionID = ? AND
							Apparatus = ?
						;";
				$stmt = $conn->prepare($sql);
				
				$stmt->bindParam(1, $counts, PDO::PARAM_INT,10);
				$stmt->bindParam(2, $_SESSION['userID'], PDO::PARAM_INT, 6);
				$stmt->bindParam(3, $personID, PDO::PARAM_INT, 5);
				$stmt->bindParam(4, $competitionID, PDO::PARAM_INT, 5);	
				$stmt->bindParam(5, $eventID, PDO::PARAM_INT, 3);	
				
				$max = $this->maxNumberOfCompetitorsPerEvent($competitionID); //TODO: eh this is different than how may count. Need a new function.
				$numCurrentlyRegistered = $this->howManyRegisteredOnEvent($competitionID,$institutionID,$eventID,$designation);
				//echo "max is: " . $max . " and num is: " . $numCurrentlyRegistered;
				if((($numCurrentlyRegistered < $max) && ($counts == 1)) || ($counts == 0))
				{
					$stmt->execute();
					if($anActualUpdate)
					{
						$this->addToRegistrationLog($competitionID,$personID,"",$msg,$eventID,"");
					}
				}
			}
		}
		catch (PDOException $e)
		{
			$error = true;
			$conn->rollBack();
			 
			$details = 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
			 
			$return_arr = array(
					'Error' => $error,
					'Message'=>"Error in updateEventCountsForPerson",
					'Details'=>$details
					);
		}
		if(!$error)
		{
			$conn->commit();
			$return_arr = array(
					'Error' => $error,
					'Message'=>"No Error"
					);
		}
		return json_encode($return_arr);
	}
	
}

?>