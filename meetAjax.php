<?php
session_start();
require_once("globals.php");
require_once("auth.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_REQUEST['addNewCompetition']))
{
	$myMeet = $_REQUEST['addNewCompetition'];
	$myDivision = $_REQUEST['addDivision'];
	$myLevel = $_REQUEST['addLevel'];
	$myDiscipline = $_REQUEST['addDiscipline'];
	$maxPerEvent = $_REQUEST['eventMax'];
	
	$stmtCompetitions= $conn->prepare("
		INSERT INTO
			Events_Competitions(MeetID,Leauge,Division,Level,Gender,Part,Session,TeamMaxOnEvent)
		VALUES(?,2,?,?,?,3,1,?)
		;");
	
	$stmtCompetitions->bindParam(1, $myMeet, PDO::PARAM_INT, 5);
	$stmtCompetitions->bindParam(2, $myDivision, PDO::PARAM_INT, 5);
	$stmtCompetitions->bindParam(3, $myLevel, PDO::PARAM_INT, 5);
	$stmtCompetitions->bindParam(4, $myDiscipline, PDO::PARAM_INT, 5);
	$stmtCompetitions->bindParam(5, $maxPerEvent, PDO::PARAM_INT, 5);
	
	$stmtCompetitions->execute();
	
}

if(isset($_REQUEST['addNewMeet']))
{
	
	$meetName = $_REQUEST['meetName'];
	$meetDate = $_REQUEST['meetDate'];
	$meetHost = $_REQUEST['hostClub'];
	$meetSeason = substr($meetDate,0,4);

	$stmtCompetitions= $conn->prepare("
		INSERT INTO
			Events_Meets(Season,Date,MeetName,HostClub)
		VALUES(?,?,?,?)
		;");
	
	$stmtCompetitions->bindParam(1, $meetSeason, PDO::PARAM_INT, 4);
	$stmtCompetitions->bindParam(2, $meetDate, PDO::PARAM_STR, 20);
	$stmtCompetitions->bindParam(3, $meetName, PDO::PARAM_STR, 50);
	$stmtCompetitions->bindParam(4, $meetHost, PDO::PARAM_INT, 5);
	
	$returnArray = array();
	
	if($stmtCompetitions->execute())
		echo json_encode($returnArray);	
}

if(isset($_REQUEST['getDivisions']))
{	
	echo json_encode(getDivisions());
}

function getDivisions()
{
	global $conn;
	$stmtDivisions= $conn->prepare("
		SELECT
			ID, 
			Name
		FROM
			Constraints_MeetDivisions
		;");
	$stmtDivisions->execute();
	
	if ($stmtDivisions->rowCount() > 0)
	{
		$count = 0;
		$returnArray = array();
	
		while($row = $stmtDivisions->fetch(PDO::FETCH_ASSOC))
		{
			$returnArray[$count] = array(
											'ID'=>$row['ID'],
											'Name'=>$row['Name']
										);
			$count++;
		}
		return $returnArray;
	}
	else
	{
		return false;
	}
}

if(isset($_REQUEST['getLevels']))
{	
	$discipline = $_REQUEST['discipline'];
	echo json_encode(getLevels($discipline));
}

function getLevels($discipline)
{
	global $conn;
	$stmtLevels= $conn->prepare("
		SELECT
			Constraints_MeetLevels.ID,
			concat(Constraints_Genders.GenderName,' ',Constraints_MeetLevels.DisplayName)AS Name
		FROM
			Constraints_MeetLevels,
			Constraints_Genders
		WHERE
			Constraints_Genders.ID = Constraints_MeetLevels.Gender AND
			Constraints_MeetLevels.Gender = ?
		Order By 
			concat(Constraints_Genders.GenderName,' ',Constraints_MeetLevels.DisplayName) ASC
		;");
		
	$stmtLevels->bindParam(1, $discipline, PDO::PARAM_INT, 5);
	$stmtLevels->execute();
	
	if ($stmtLevels->rowCount() > 0)
	{
		$count = 0;
		$returnArray = array();
	
		while($row = $stmtLevels->fetch(PDO::FETCH_ASSOC))
		{
			$returnArray[$count] = array(
											'ID'=>$row['ID'],
											'Name'=>$row['Name']
										);
			$count++;
		}
		return $returnArray;
	}
	else
	{
		return false;
	}
}

if(isset($_REQUEST['getDisciplines']))
{	
	echo json_encode(getDisciplines());
}


function getDisciplines()
{
	global $conn;
	$stmtDisciplines = $conn->prepare("
		SELECT
			ID,
			GenderName AS Name
		FROM
			Constraints_Genders
		;");
	$stmtDisciplines->execute();
	
	if ($stmtDisciplines->rowCount() > 0)
	{
		$count = 0;
		$returnArray = array();
	
		while($row = $stmtDisciplines->fetch(PDO::FETCH_ASSOC))
		{
			$returnArray[$count] = array(
											'ID'=>$row['ID'],
											'Name'=>$row['Name']
										);
			$count++;
		}
		return $returnArray;
	}
	else
	{
		return false;
	}
}

if(isset($_REQUEST['getCompetitionsForMeet']))
{
	$meet = $_REQUEST['meetID'];
	
	echo json_encode(getCompetitionsFor($meet));
}

function getCompetitionsFor($meet) //FUNCTION DUPLICATED
{
	global $conn;
	$stmtCompetitions = $conn->prepare("
		SELECT
			Events_Competitions.ID,
			Constraints_Leauges.ShortName AS Leauge,
			Constraints_Leauges.ID AS LeaugeID,
			Constraints_MeetDivisions.Name AS Division,
			Constraints_MeetDivisions.ID AS DivisionID,
			Constraints_MeetLevels.DisplayName AS Level,
			Constraints_MeetLevels.ID AS LevelID,
			Constraints_Genders.GenderName AS Gender,
			Constraints_Genders.ID AS GenderID,
			Events_Competitions.TeamMaxOnEvent
		FROM
			Events_Competitions, 
			Constraints_Leauges,
			Constraints_Genders,
			Constraints_MeetDivisions,
			Constraints_MeetLevels
		WHERE
			Events_Competitions.Leauge = Constraints_Leauges.ID AND
			Events_Competitions.Division = Constraints_MeetDivisions.ID AND
			Events_Competitions.Level = Constraints_MeetLevels.ID AND
			Events_Competitions.Gender = Constraints_Genders.ID AND
			Events_Competitions.MeetID = ?
		;");
	
	$stmtCompetitions->bindParam(1, $meet, PDO::PARAM_INT, 5);
	$stmtCompetitions->execute();
	
	$count = 0;
	$returnArray = array();
	
	while($row = $stmtCompetitions->fetch(PDO::FETCH_ASSOC))
	{
		$returnArray[$count] = array(
										'ID'=>$row['ID'],
										'Leauge'=>$row['Leauge'],
										'LeaugeID'=>$row['LeaugeID'],
										'Division'=>$row['Division'],
										'DivisionID'=>$row['DivisionID'],
										'Level'=>$row['Level'],
										'LevelID'=>$row['LevelID'],
										'Gender'=>$row['Gender'],
										'GenderID'=>$row['GenderID'],
										'TeamMaxOnEvent'=>$row['TeamMaxOnEvent']
									);
		$count++;
	}
	return $returnArray;
}

if(isset($_REQUEST['getMeetsForWhichUserIsAdmin']))
{
	echo json_encode(getMeetsForWhichUserIsAdmin());
}

function getMeetsForWhichUserIsAdmin()
{
	global $conn;
	
	if(userIsExecutiveAdministrator())
	{
		$season = "";
		$hostClub = "";
	}
	else
	{
		$season = "Season >= " . getCurrentSeason() . " AND ";
		$hostClub = "AND HostClub IN (Select InstitutionID From Identifiers_Affiliations Where PersonID = ? And Year = " . getCurrentSeason() . ")";
	}
	
	$stmtMeets= $conn->prepare("
		SELECT
			ID, 
			Concat(MeetName, '(', Date,')') As Name
		FROM
			Events_Meets
		Where 
			".$season."
			scored = 1
			".$hostClub." 
		Order By
			Date Desc
		;");
		
	$stmtMeets->execute();
	
	$count = 0;
	$returnArray = array();
	
	if ($stmtMeets->rowCount() > 0)
	while($row = $stmtMeets->fetch(PDO::FETCH_ASSOC))
	{
		$returnArray[$count] = array(
										'ID'=>$row['ID'],
										'Name'=>$row['Name']
									);
		$count++;
	}
	return $returnArray;
}

function deleteCompetition($competitionID)
{
	global $conn;
	
	if(isCompetitionEmptyOfGymnasts($competitionID) && isCompetitionEmptyOfTeams($competitionID))
	{
		$stmt = $conn->prepare("
				Delete 
				FROM
					Events_Competitions
				Where 
					CompetitionID = ?				
				Limit 1
				");
		$stmt->execute();
	}
	else
	{
		//return a message telling user to empty the competition first.
	}
}

function isCompetitionEmptyOfGymnasts($competitionID)
{
	global $conn;
	
	$stmt = $conn->prepare("
				Select 
					count(*)
				From 
					Events_Routines
				Where
					CompetitionID = ?
				");
				
	$stmt->bindParam(1, $competitionID, PDO::PARAM_INT, 6);
	$stmt->execute();
	
	$count = 0;
	$exists = 0;
	
	if ($stmt->rowCount() > 0)
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$exists += $row['Count'];
		$count++;
	}
	
	if($exists > 0)
		return false;
	else
		return true;
}
function isCompetitionEmptyOfTeams($competitionID)
	global $conn;

	$stmt = $conn->prepare("
				Select
					count(*) as Count
				From
					Events_Teams
				Where
					CompetitionID = ?
				");

	$stmt->bindParam(1, $competitionID, PDO::PARAM_INT, 6);
	$stmt->execute();
	
	$count = 0;
	$exists = 0;
	
	if ($stmt->rowCount() > 0)
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$exists += $row['Count'];
		$count++;
	}
	
	if($exists > 0)
		return false;
	else
		return true;
}

?>