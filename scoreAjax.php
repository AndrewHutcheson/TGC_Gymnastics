<?php
session_start();
require_once("globals.php");
require_once("auth.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getInstitutionsInMeet($meet)
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

//if(userIsLoggedIn()) //quick way of parsing input to prevent sql injections since we control who has login permissions
if(true)
{
	if(isset($_REQUEST['getLiveScores']))
	{
		$meet = $_REQUEST['meet'];
		$numLimit = $_REQUEST['numberLimit'];
		$Discipline = html_entity_decode($_REQUEST['Discipline']);
		//$numWomen = $_REQUEST['numPerWomen'];
		//$numMen = $_REQUEST['numPerMen'];
		//$womenEvents = $_REQUEST['womenEvents'];
		//$menEvents = $_REQUEST['menEvents'];
		
		//return getliveScores($competitions,$numWomen,$numMen,$womenEvents,$menEvents);
		echo json_encode(getliveScores($meet,$numLimit,$Discipline));
	}
}

//function getliveScores($competitions,$numWomen,$numMen,$womenEvents,$menEvents)
function getliveScores($meet,$numLimit,$Discipline)
{
	global $conn;
	
	$sql = "
							Select 
								Events_Routines.ID,
								Events_Routines.Score,
								Events_Routines.lastScoreChange,
								Constraints_Apparatus.ShortName as Event,
								Concat(Identifiers_People.FirstName, ' ', Identifiers_People.LastName) AS Person,
								Identifiers_Institutions.Name as Club,
								concat(
									Constraints_MeetDivisions.Name, ' ',
									Constraints_MeetLevels.DisplayName
								 ) AS CompetitionName
							FROM
								Events_Routines,
								Constraints_Apparatus,
								Identifiers_Institutions,
								Identifiers_People,
								Constraints_MeetDivisions,
								Constraints_MeetLevels,
								Constraints_Disciplines,
								Events_Competitions
							WHERE
								Events_Routines.CompetitionID IN (Select ID From Events_Competitions Where MeetID = " . $meet . ") AND
								Events_Routines.lastScoreChange IS NOT NULL AND
								Events_Routines.PersonID = Identifiers_People.ID AND
								Events_Routines.Apparatus = Constraints_Apparatus.ID AND
								Events_Routines.ClubID = Identifiers_Institutions.ID AND
								Events_Routines.CompetitionID = Events_Competitions.ID AND
								Events_Competitions.Division = Constraints_MeetDivisions.ID AND
								Events_Competitions.Level = Constraints_MeetLevels.ID AND
								Events_Competitions.Discipline = Constraints_Disciplines.ID AND
								Events_Competitions.Discipline IN (" . $Discipline . ") AND
								Events_Routines.Score > 0
							Order BY
								lastScoreChange Desc
							Limit
								" . $numLimit . "
						";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	
	//echo $sql;
	
	$returnArray = array();
	
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		//$returnArray[$row['ID']] = array(
		$returnArray[] = array(
										"Competition"=>$row['CompetitionName'],
										"Event"=>$row['Event'],
										"Team"=>$row['Club'],
										"Gymnast"=>$row['Person'],
										"Score"=>number_format($row['Score'],2)
								);
	}
	
	return $returnArray;
}

function isScoreVerified($person, $competition, $event)
{
	global $conn;
	$sql = "Select 
				Verified
			From 
				Events_Routines
			WHERE
				PersonID = ? AND
				CompetitionID = ? AND
				Apparatus = ?
			";

	$stmt = $conn->prepare($sql);

	$stmt->bindParam(1, $person, PDO::PARAM_INT);	
	$stmt->bindParam(2, $competition, PDO::PARAM_INT);		
	$stmt->bindParam(3, $event, PDO::PARAM_INT);

	$stmt->execute();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		if($row['Verified'] == 1)
			return true;
	}

	return false;
}

if(isset($_REQUEST['updatePersonScore']))
{
	$person = $_REQUEST['person'];
	$score = $_REQUEST['score'];
	$event = $_REQUEST['event'];
	$competition = $_REQUEST['competition'];
	
	if(!isScoreVerified($person, $competition, $event))
	{
		updatePersonScore($person, $score, $competition, $event);

		if(isset($_REQUEST['startValue']))
		{
			$startValue = $_REQUEST['startValue'];
			updatePersonStartValue($person, $startValue, $competition, $event); //todo: re-enable return array and combine with this one.
		}
	}
	else
	{
		$return_arr = array(
			'Error' => true,
			'Message'=>"Error, score already verified."
			);

		echo json_encode($return_arr);
	}
}

function updatePersonScore($personID, $score, $competition, $event)
{
	global $conn;
	$error = false;
	
	try
	{
		$conn->beginTransaction();
		
		$sql = "
				UPDATE
					Events_Routines
				SET
					Score = ?,
					lastScoreChange = NOW()
				WHERE
					PersonID = ? AND
					CompetitionID = ? AND
					Apparatus = ?
				;";
				
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $score, PDO::PARAM_STR, 6);	
		$stmt->bindParam(2, $personID, PDO::PARAM_INT, 5);	
		$stmt->bindParam(3, $competition, PDO::PARAM_INT, 5);		
		$stmt->bindParam(4, $event, PDO::PARAM_INT, 5);		
		
		if(true)
		{
			$stmt->execute();
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
	}

		$return_arr = array(
				'Error' => false,
				'Message'=>"saved."
				);

	echo json_encode($return_arr);
}

function updatePersonStartValue($personID, $startValue, $competition, $event)
{
	global $conn;
	$error = false;
	
	try
	{
		$conn->beginTransaction();
		
		$sql = "
				UPDATE
					Events_Routines
				SET
					StartValue = ?
				WHERE
					PersonID = ? AND
					CompetitionID = ? AND
					Apparatus = ?
				;";
				
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $startValue, PDO::PARAM_STR, 6);	
		$stmt->bindParam(2, $personID, PDO::PARAM_INT, 5);	
		$stmt->bindParam(3, $competition, PDO::PARAM_INT, 5);		
		$stmt->bindParam(4, $event, PDO::PARAM_INT, 5);		
		
		if(true)
		{
			$stmt->execute();
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
	}

		$return_arr = array(
				'Error' => false,
				'Message'=>"saved."
				);

	//echo json_encode($return_arr);
}

if(isset($_REQUEST['getTeamScoreResults']))
{
	$meetID = $_REQUEST['meetID'];
	$DisciplineID = $_REQUEST['DisciplineID'];
	echo json_encode(getTeamScoreSummary($meetID,$DisciplineID));
}

function getTeamScoreSummary($meetID,$DisciplineID)
{
	global $conn;
	$error = false;
	$returnStuff = array();
	
	try
	{
		$conn->beginTransaction();
		$sql = "
				SELECT
					coalesce(Identifiers_Institutions.AltName,Identifiers_Institutions.Name) As InstitutionName,
					Identifiers_Institutions.ID As InstitutionID,
					Events_Teams.Name As CompetitionName,
					Events_Competitions.ID As CompetitionID,
					Events_Competitions.numPerTeamScore,
					TeamDesignation
				FROM
					Events_Teams,
					Identifiers_Institutions,
					Events_Competitions
				WHERE
					CompetitionID IN (Select ID From Events_Competitions Where MeetID = ?) AND
					Events_Teams.InstitutionID = Identifiers_Institutions.ID AND
					Events_Competitions.ID = Events_Teams.CompetitionID AND
					Events_Competitions.Discipline = ? AND
					Events_Teams.TeamScore = 1
				;";
				
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $meetID, PDO::PARAM_INT, 5);
		$stmt->bindParam(2, $DisciplineID, PDO::PARAM_INT, 5);
		
		$stmt->execute();
		
		$count = 0;
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$compID = $row['CompetitionID'];
			$instID = $row['InstitutionID'];
			$compName = $row['CompetitionName'];
			$competitionLimit = $row['numPerTeamScore'];
			$designation = $row['TeamDesignation'];
			
			//do not remove this comment until the db has manually been retroactively updated for years 2019 and prior.
			/*$competitionLimit = 3;
			if(strpos($compName, 'Level 8') != false)
			{
				$competitionLimit = 4;
			}*/
			
			$MFX = getTeamScoreForEvent($instID, $designation, $compID, 1, $competitionLimit);
			$MPH = getTeamScoreForEvent($instID, $designation, $compID, 2, $competitionLimit);
			$MSR = getTeamScoreForEvent($instID, $designation, $compID, 3, $competitionLimit);
			$MVT = getTeamScoreForEvent($instID, $designation, $compID, 4, $competitionLimit);
			$MPB = getTeamScoreForEvent($instID, $designation, $compID, 5, $competitionLimit);
			$MHB = getTeamScoreForEvent($instID, $designation, $compID, 6, $competitionLimit);
			$WVT = getTeamScoreForEvent($instID, $designation, $compID, 8, $competitionLimit);
			$WUB = getTeamScoreForEvent($instID, $designation, $compID, 9, $competitionLimit);
			$WBB = getTeamScoreForEvent($instID, $designation, $compID, 10, $competitionLimit);
			$WFX = getTeamScoreForEvent($instID, $designation, $compID, 11, $competitionLimit);
			
			$MAA = $MFX + $MPH + $MSR + $MVT + $MPB + $MHB;
			$WAA = $WVT + $WUB + $WBB + $WFX;
			$AAA = $MAA + $WAA;
			
			$returnStuff[$count] = array(
									'InstitutionName'=>$row['InstitutionName'],
									'Designation'=>$row['TeamDesignation'],
									'CompetitionName'=>$compName,
									'CompetitionID'=>$compID,
									'MFX'=>$MFX,
									'MPH'=>$MPH,
									'MSR'=>$MSR,
									'MVT'=>$MVT,
									'MPB'=>$MPB,
									'MHB'=>$MHB,
									'WVT'=>$WVT,
									'WUB'=>$WUB,
									'WBB'=>$WBB,
									'WFX'=>$WFX,
									'WAA'=>$WAA,
									'MAA'=>$MAA,
									'AAA'=>$AAA
									);
			$count++;
		}
	}
	
	catch(PDOException $e)
	{
		$error = true;
		$conn->rollBack();
		return 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
	}
	
	if(!$error)
		$conn->commit();
	
	return $returnStuff;
}

function getTeamScoreForEvent($institutionID, $designation, $competitionID, $eventID, $competitionLimit)
{
	global $conn;
	$error = false;
	$score = 0;
	
		$sql2 = "
				SELECT 
					Sum(Score) As EventScore
				FROM
					(
						Select 
							Score 
						From 
							Events_Routines 
						Where 
							ClubID = ? AND
							TeamDesignation = ? AND
							CompetitionID = ? AND
							Apparatus = ? AND
							ApparatusTeamScore = 1
						Order By 
							Score Desc Limit ?
					) alias						
				;";
				
		$stmt2 = $conn->prepare($sql2);
		
		$stmt2->bindParam(1, $institutionID, PDO::PARAM_INT, 5);
		$stmt2->bindParam(2, $designation, PDO::PARAM_STR, 5);
		$stmt2->bindParam(3, $competitionID, PDO::PARAM_INT, 5);
		$stmt2->bindParam(4, $eventID, PDO::PARAM_INT, 3);
		$stmt2->bindParam(5, $competitionLimit, PDO::PARAM_INT, 2);
		
		$stmt2->execute();
		
		while($row = $stmt2->fetch(PDO::FETCH_ASSOC))
		{
			$score = $row['EventScore'];
		}
	
	return $score;
}

if(isset($_REQUEST['getAllScoresForMeetDiscipline']))
{
	$iInstitution = $_REQUEST['institutionID'];
	$iMeet = $_REQUEST['meetID'];
	$iDiscipline = $_REQUEST['DisciplineID'];
	
	$return_arr = getScores($iMeet, $iInstitution, $iDiscipline);
	
	echo json_encode($return_arr);
}

function getScores($meetID, $institutionID, $Discipline)
{
	
	$theArray = array();
	if(getInstitutionsInMeet($meetID) != "")
	{
		if(($institutionID == "false") || ($institutionID == false))
		{
			$newInstitutionID = getInstitutionsInMeet($meetID);
			//echo $institutionID;
		}
		
		global $conn;
		//ughh I need a left join in case something is ever missing.
		$sql = "SELECT
					DISTINCT Events_Routines.PersonID,
					Concat(LastName, ', ', FirstName) AS PersonName,
					FirstName, LastName, Phonetic,
					Events_Routines.CompetitionID,
					concat(
						Constraints_MeetDivisions.Name, ' ',
						Constraints_MeetLevels.DisplayName, ' ',
						Constraints_Disciplines.DisciplineShortName
					 ) AS CompetitionName,
					LatestDateRegistered,
					Events_Routines.TeamDesignation,
					RegPersonName,
					MAX(Fee) As theFee,
					coalesce(Identifiers_Institutions.AltName,Identifiers_Institutions.Name) AS Institution
				FROM
					Identifiers_People,
					Identifiers_Institutions,
					Events_Routines,
					Events_Competitions,
					Constraints_MeetDivisions,
					Constraints_MeetLevels,
					Constraints_Disciplines,
					(Select MAX(RegDate) AS LatestDateRegistered, PersonID, CompetitionID FROM Events_Routines GROUP BY PersonID, CompetitionID) alias,
					(Select ID, Concat(LastName, ', ', FirstName) AS RegPersonName FROM Identifiers_People) alias2
				WHERE
					Identifiers_Institutions.ID = Events_Routines.ClubID AND
					Events_Routines.CompetitionID = alias.CompetitionID AND
					Events_Routines.PersonID = alias.PersonID AND
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
			if($Discipline == 2)
			{
				$FX = getPersonScoreForEvent($person,1,$competitionID);
				$PH = getPersonScoreForEvent($person,2,$competitionID);
				$SR = getPersonScoreForEvent($person,3,$competitionID);
				$VT = getPersonScoreForEvent($person,4,$competitionID);
				$PB = getPersonScoreForEvent($person,5,$competitionID);
				$HB = getPersonScoreForEvent($person,6,$competitionID);
				$FXSV = getPersonSVForEvent($person,1,$competitionID);
				$PHSV = getPersonSVForEvent($person,2,$competitionID);
				$SRSV = getPersonSVForEvent($person,3,$competitionID);
				$VTSV = getPersonSVForEvent($person,4,$competitionID);
				$PBSV = getPersonSVForEvent($person,5,$competitionID);
				$HBSV = getPersonSVForEvent($person,6,$competitionID);
				
				$AA = ($FX+$PH+$SR+$VT+$PB+$HB);
				//$theArray[$count] = array($person,$row['PersonName'],$row['CompetitionID'],$row['CompetitionName'],$FX,$PH,$SR,$VT,$PB,$HB,$AA);
				$theArray[$count] = array(
											'ID'=>$person,
											'Name'=>$row['PersonName'],
											'FirstName'=>$row['FirstName'],
											'LastName'=>$row['LastName'],
											'Phonetic'=>$row['Phonetic'],
											'CompetitionID'=>$row['CompetitionID'],
											'DisciplineID'=>$Discipline,
											'Team'=>$row['CompetitionName'],
											'Designation'=>$row['TeamDesignation'],
											'MFX'=>$FX,
											'MPH'=>$PH,
											'MSR'=>$SR,
											'MVT'=>$VT,
											'MPB'=>$PB,
											'MHB'=>$HB,
											'MFXSV'=>$FXSV,
											'MPHSV'=>$PHSV,
											'MSRSV'=>$SRSV,
											'MVTSV'=>$VTSV,
											'MPBSV'=>$PBSV,
											'MHBSV'=>$HBSV,
											'MAA'=>$AA,
											'Fee'=>$row['theFee'],
											'Institution'=>$row['Institution']
										);
			}
			elseif($Discipline == 1)
			{
				$VT = getPersonScoreForEvent($person,8,$competitionID);
				$UB = getPersonScoreForEvent($person,9,$competitionID);
				$BB = getPersonScoreForEvent($person,10,$competitionID);
				$FX = getPersonScoreForEvent($person,11,$competitionID);
				$VTSV = getPersonSVForEvent($person,8,$competitionID);
				$UBSV = getPersonSVForEvent($person,9,$competitionID);
				$BBSV = getPersonSVForEvent($person,10,$competitionID);
				$FXSV = getPersonSVForEvent($person,11,$competitionID);
				
				$AA = ($VT+$UB+$BB+$FX);
				$theArray[$count] = array(
											'ID'=>$person,
											'Name'=>$row['PersonName'],
											'FirstName'=>$row['FirstName'],
											'LastName'=>$row['LastName'],
											'Phonetic'=>$row['Phonetic'],
											'CompetitionID'=>$row['CompetitionID'],
											'DisciplineID'=>$Discipline,
											'Team'=>$row['CompetitionName'],
											'Designation'=>$row['TeamDesignation'],
											'WVT'=>$VT,
											'WUB'=>$UB,
											'WBB'=>$BB,
											'WFX'=>$FX,
											'WVTSV'=>$VTSV,
											'WUBSV'=>$UBSV,
											'WBBSV'=>$BBSV,
											'WFXSV'=>$FXSV,
											'WAA'=>$AA,
											'Fee'=>$row['theFee'],
											'Institution'=>$row['Institution']
										);
			}
			//echo $count;
			//print_r($row);
			$count++;
		}
	}
	return $theArray;
}

	

function getPersonScoreForEvent($personID,$eventID,$competitionID)
{
	global $conn;
	
	$sqlGetPersonScore = "
			SELECT
				Score
			FROM
				Events_Routines
			WHERE
				PersonID = ? AND
				Apparatus = ? AND
				CompetitionID = ?
			;";
	
	$stmtGetPersonScore = $conn->prepare($sqlGetPersonScore);
	
	$score = 0;
	
	$stmtGetPersonScore->bindParam(1, $personID, PDO::PARAM_INT, 5);
	$stmtGetPersonScore->bindParam(2, $eventID, PDO::PARAM_INT, 3);	
	$stmtGetPersonScore->bindParam(3, $competitionID, PDO::PARAM_INT, 5);	
	$stmtGetPersonScore->execute();
	
	while($row = $stmtGetPersonScore->fetch(PDO::FETCH_ASSOC))
	{
		$score += $row['Score'];
	}
	
	return $score;
}

if(isset($_REQUEST['getRegistrationMetricsForMeetDiscipline']))
{
	$iInstitution = $_REQUEST['institutionID'];
	$iMeet = $_REQUEST['meetID'];
	$iDiscipline = $_REQUEST['DisciplineID'];
	
	$return_arr = getScoreMetrics($iMeet, $iInstitution, $iDiscipline);
	
	echo json_encode($return_arr);
}

function getScoreMetrics($meetID, $institutionID, $Discipline)
{
	
	$theArray = array();
	if(getInstitutionsInMeet($meetID) != "")
	{
		if($institutionID == "false")
		{
			$newInstitutionID = getInstitutionsInMeet($meetID);
			//echo $institutionID;
		}
		
		global $conn;
		//ughh I need a left join in case something is ever missing.
		$sql = "SELECT
					DISTINCT Events_Routines.PersonID,
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
					coalesce(Identifiers_Institutions.AltName,Identifiers_Institutions.Name) AS Institution
				FROM
					Identifiers_People,
					Identifiers_Institutions,
					Events_Routines,
					Events_Competitions,
					Constraints_MeetDivisions,
					Constraints_MeetLevels,
					Constraints_Disciplines,
					(Select MAX(RegDate) AS LatestDateRegistered, PersonID, CompetitionID FROM Events_Routines GROUP BY PersonID, CompetitionID) alias,
					(Select ID, Concat(LastName, ', ', FirstName) AS RegPersonName FROM Identifiers_People) alias2
				WHERE
					Identifiers_Institutions.ID = Events_Routines.ClubID AND
					Events_Routines.CompetitionID = alias.CompetitionID AND
					Events_Routines.PersonID = alias.PersonID AND
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
			if($Discipline == 2)
			{
				$FX = getPersonCompetitionVsRegistration($person,1,$competitionID);
				$PH = getPersonCompetitionVsRegistration($person,2,$competitionID);
				$SR = getPersonCompetitionVsRegistration($person,3,$competitionID);
				$VT = getPersonCompetitionVsRegistration($person,4,$competitionID);
				$PB = getPersonCompetitionVsRegistration($person,5,$competitionID);
				$HB = getPersonCompetitionVsRegistration($person,6,$competitionID);
				$AA = "";
				//$theArray[$count] = array($person,$row['PersonName'],$row['CompetitionID'],$row['CompetitionName'],$FX,$PH,$SR,$VT,$PB,$HB,$AA);
				$theArray[$count] = array(
											'ID'=>$person,
											'Name'=>$row['PersonName'],
											'CompetitionID'=>$row['CompetitionID'],
											'DisciplineID'=>$Discipline,
											'Team'=>$row['CompetitionName'],
											'MFX'=>$FX,
											'MPH'=>$PH,
											'MSR'=>$SR,
											'MVT'=>$VT,
											'MPB'=>$PB,
											'MHB'=>$HB,
											'MAA'=>$AA,
											'Fee'=>$row['theFee'],
											'Institution'=>$row['Institution']
										);
			}
			elseif($Discipline == 1)
			{
				$VT = getPersonCompetitionVsRegistration($person,8,$competitionID);
				$UB = getPersonCompetitionVsRegistration($person,9,$competitionID);
				$BB = getPersonCompetitionVsRegistration($person,10,$competitionID);
				$FX = getPersonCompetitionVsRegistration($person,11,$competitionID);
				$AA = "";
				$theArray[$count] = array(
											'ID'=>$person,
											'Name'=>$row['PersonName'],
											'CompetitionID'=>$row['CompetitionID'],
											'DisciplineID'=>$Discipline,
											'Team'=>$row['CompetitionName'],
											'WVT'=>$VT,
											'WUB'=>$UB,
											'WBB'=>$BB,
											'WFX'=>$FX,
											'WAA'=>$AA,
											'Fee'=>$row['theFee'],
											'Institution'=>$row['Institution']
										);
			}
			//echo $count;
			//print_r($row);
			$count++;
		}
	}
	return $theArray;
}

function getPersonCompetitionVsRegistration($personID,$eventID,$competitionID)
{
	global $conn;
	$score = "";
	
	/*
	registered and competed = good (green)
	not registered and not competed = good (green)
	registered and not competed = bad (yellow)
	not registered and competed = bad (red)
	*/
	
	$sql = "
			SELECT
				if(((Score > 0 And Registered = 1) OR (Score <= 0 And Registered = 0)),true,false) As Green,
				if(Registered = 1 And Score = 0, true, false)  As Yellow,
				if(Registered = 0 And Score > 0, true, false) AS Red
			FROM
				Events_Routines
			WHERE
				PersonID = ? AND
				Apparatus = ? AND
				CompetitionID = ?
			;";
	
	$stmt = $conn->prepare($sql);
	
	$stmt->bindParam(1, $personID, PDO::PARAM_INT, 5);
	$stmt->bindParam(2, $eventID, PDO::PARAM_INT, 3);	
	$stmt->bindParam(3, $competitionID, PDO::PARAM_INT, 5);	
	$stmt->execute();
	
	//SELECT CompetitionID, concat(FirstName," ",LastName) AS Gymnast, ItemKey FROM Log_Registration, Identifiers_People WHERE CompetitionID IN (Select ID From Events_Competitions Where MeetID = 110) AND Timestamp >= (Select Date From Events_Meets WHERE ID = 110) AND Action = "Person Added to Event" AND Identifiers_People.ID = Log_Registration.PersonID 
	
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		If ($row['Green'])
			$score = "==";
		Else If ($row['Yellow'])
			$score = "xxx";
		Else If ($row['Red'])
			$score = "+++";
	}
	
	return $score;
}

	

function getPersonSVForEvent($personID,$eventID,$competitionID)
{
	global $conn;
	$startValue = 0;
	
	$sqlGetPersonSV = "
			SELECT
				StartValue
			FROM
				Events_Routines
			WHERE
				PersonID = ? AND
				Apparatus = ? AND
				CompetitionID = ?
			;";
	
	$stmtGetPersonSV = $conn->prepare($sqlGetPersonSV);
	
	$stmt = $stmtGetPersonSV;
	
	$stmt->bindParam(1, $personID, PDO::PARAM_INT, 5);
	$stmt->bindParam(2, $eventID, PDO::PARAM_INT, 3);	
	$stmt->bindParam(3, $competitionID, PDO::PARAM_INT, 5);	
	$stmt->execute();
	
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$startValue += $row['StartValue'];
	}
	
	if($startValue == 0)
		$startValue = null;
	
	return $startValue;
}

if(isset($_REQUEST['getAllScoresForMeetEventVerification']))
{
	$iInstitution = $_REQUEST['institutionID'];
	$iMeet = $_REQUEST['meetID'];
	$iEvent = $_REQUEST['eventID'];
	$iDiscipline = $_REQUEST['DisciplineID'];
	
	$return_arr = getEventScores($iMeet, $iInstitution, $iEvent, $iDiscipline,true);
	
	echo json_encode($return_arr);
}

if(isset($_REQUEST['getAllScoresForMeetEvent']))
{
	$iInstitution = $_REQUEST['institutionID'];
	$iMeet = $_REQUEST['meetID'];
	$iEvent = $_REQUEST['eventID'];
	$iDiscipline = $_REQUEST['DisciplineID'];
	
	$return_arr = getEventScores($iMeet, $iInstitution, $iEvent, $iDiscipline, false);
	
	echo json_encode($return_arr);
}

function getEventScores($meetID, $institutionID, $event, $Discipline, $verified)
{
	
	$theArray = array();
	if(getInstitutionsInMeet($meetID) != "")
	{
		if($institutionID == "false")
		{
			$newInstitutionID = getInstitutionsInMeet($meetID);
			//echo $institutionID;
		}
		
		global $conn;
		//ughh I need a left join in case something is ever missing.
		$sql = "SELECT
					DISTINCT Events_Routines.PersonID,
					Concat(LastName, ', ', FirstName) AS PersonName,
					Events_Routines.CompetitionID,
					concat(
						Constraints_MeetDivisions.Name, ' ',
						Constraints_MeetLevels.DisplayName, ' ',
						Constraints_Disciplines.DisciplineShortName, ' ',
						Constraints_MeetParts.DisplayName
					 ) AS CompetitionName,
					coalesce(Identifiers_Institutions.Abbr,Identifiers_Institutions.AltName,Identifiers_Institutions.Name) AS Institution,
					Verified,
					ApparatusTeamScore
				FROM
					Identifiers_People,
					Identifiers_Institutions,
					Events_Routines,
					Events_Competitions,
					Constraints_MeetDivisions,
					Constraints_MeetParts,
					Constraints_MeetLevels,
					Constraints_Disciplines
				WHERE
					Identifiers_Institutions.ID = Events_Routines.ClubID AND
					Events_Routines.Apparatus = ? AND
					Events_Routines.PersonID = Identifiers_People.ID AND 
					Events_Competitions.Part IN (1,2,3) AND ";
			
		if($institutionID == "false")
			$sql .=	"Events_Routines.ClubID IN (".$newInstitutionID.") AND ";
		else
			$sql .=	"Events_Routines.ClubID IN (?) AND ";

		if($verified)
			$sql .= "Events_Routines.Verified >= 0 AND ";
		else
			$sql .= "Events_Routines.Verified = 0 AND Events_Routines.Registered = 1 AND ";
			
			$sql .=	"Events_Routines.CompetitionID = Events_Competitions.ID AND
					Events_Competitions.Division = Constraints_MeetDivisions.ID AND
					Events_Competitions.Level = Constraints_MeetLevels.ID AND
					Events_Competitions.Discipline = Constraints_Disciplines.ID AND
					Events_Competitions.Part = Constraints_MeetParts.ID AND
					Events_Routines.CompetitionID IN (Select ID From Events_Competitions WHERE MeetID = ? AND Discipline = ?)
				GROUP BY
					Events_Routines.PersonID,
					Events_Routines.CompetitionID
				;";			

		$stmt = $conn->prepare($sql);
		
		if($institutionID != "false")
		{
			$stmt->bindParam(1, $event, PDO::PARAM_INT, 5);	
			$stmt->bindParam(2, $institutionID, PDO::PARAM_INT, 5);	
			$stmt->bindParam(3, $meetID, PDO::PARAM_INT, 5);
			$stmt->bindParam(4, $Discipline, PDO::PARAM_INT, 1);
		}
		else
		{
			$stmt->bindParam(1, $event, PDO::PARAM_INT, 5);
			$stmt->bindParam(2, $meetID, PDO::PARAM_INT, 5);
			$stmt->bindParam(3, $Discipline, PDO::PARAM_INT, 1);
		}
		
		
		$stmt->execute();
		
		$count = 0;
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$competitionID = $row['CompetitionID'];
			$person = $row['PersonID'];
			if(true)
			{
				$eventScore = getPersonScoreForEvent($person,$event,$competitionID);
				$eventSV = getPersonSVForEvent($person,$event,$competitionID);
				//$theArray[$count] = array($person,$row['PersonName'],$row['CompetitionID'],$row['CompetitionName'],$FX,$PH,$SR,$VT,$PB,$HB,$AA);
				$theArray[$count] = array(
											'ID'=>$person,
											'Name'=>$row['PersonName'],
											'CompetitionID'=>$row['CompetitionID'],
											'Team'=>$row['CompetitionName'],
											"Score"=>$eventScore,
											'SV'=>$eventSV,
											'Institution'=>$row['Institution'],
											'Verified'=>$row['Verified']
										);
			}
			//echo $count;
			//print_r($row);
			$count++;
		}
	}
	return $theArray;
}

if(isset($_REQUEST['updateVerification']))
{
	$person = $_REQUEST['person'];
	$verified = $_REQUEST['verified'];
	$event = $_REQUEST['event'];
	$competition = $_REQUEST['competition'];
	
	updateScoreVerification($person, $verified, $competition, $event);
}

function updateScoreVerification($personID, $verified, $competition, $event)
{
	global $conn;
	$error = false;
	
	//because of this bullshit https://bugs.php.net/bug.php?id=38546 https://stackoverflow.com/questions/10242312/pdo-bindvalue-with-pdoparam-bool-causes-statement-execute-to-fail-silently
	if($verified=="true")
		$verified = 1;
	else
		$verified = 0;

	try
	{
		$conn->beginTransaction();
		
		$sql = "
				UPDATE
					Events_Routines
				SET
					Verified = ?
				WHERE
					PersonID = ? AND
					CompetitionID = ? AND
					Apparatus = ?
				;";
				
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $verified, PDO::PARAM_INT);	
		$stmt->bindParam(2, $personID, PDO::PARAM_INT);	
		$stmt->bindParam(3, $competition, PDO::PARAM_INT);		
		$stmt->bindParam(4, $event, PDO::PARAM_INT);		
		
		if(true)
		{
			$stmt->execute();
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
	}

		$return_arr = array(
				'Error' => false,
				'Message'=>"saved."
				);

	echo json_encode($return_arr);
}

if(isset($_REQUEST['getScoreHistoryForGymnast']))
{
	$personID = $_REQUEST['personID'];
	$Discipline = $_REQUEST['Discipline'];
	
	echo json_encode(getPersonalScoreHistory($personID, $Discipline));
}

function getPersonalScoreHistory($personID, $Discipline)
{
	
	$theArray = array();

	global $conn;
	//ughh I need a left join in case something is ever missing.
	$sql = "SELECT
				DISTINCT Events_Routines.PersonID,
				Concat(LastName, ', ', FirstName) AS PersonName,
				Events_Routines.CompetitionID,
				concat(
					Constraints_MeetDivisions.Name, ' ',
					Constraints_MeetLevels.DisplayName, ' ',
					Constraints_Disciplines.DisciplineShortName
				 ) AS CompetitionName,
				LatestDateRegistered,
				concat(Events_Meets.Date, ' ', Events_Meets.MeetName) AS theMeetName,
				RegPersonName,
				MAX(Fee) As theFee,
				coalesce(Identifiers_Institutions.AltName,Identifiers_Institutions.Name) AS Institution
			FROM
				Identifiers_People,
				Identifiers_Institutions,
				Events_Meets,
				Events_Routines,
				Events_Competitions,
				Constraints_MeetDivisions,
				Constraints_MeetLevels,
				Constraints_Disciplines,
				(Select MAX(RegDate) AS LatestDateRegistered, PersonID, CompetitionID FROM Events_Routines GROUP BY PersonID, CompetitionID) alias,
				(Select ID, Concat(LastName, ', ', FirstName) AS RegPersonName FROM Identifiers_People) alias2
			WHERE
				Identifiers_Institutions.ID = Events_Routines.ClubID AND
				Events_Competitions.MeetID = Events_Meets.ID AND
				Events_Routines.CompetitionID = alias.CompetitionID AND
				Events_Routines.PersonID = alias.PersonID AND
				Events_Routines.PersonID = Identifiers_People.ID AND 
				Events_Routines.CompetitionID = Events_Competitions.ID AND
				Events_Competitions.Division = Constraints_MeetDivisions.ID AND
				Events_Competitions.Level = Constraints_MeetLevels.ID AND
				Events_Competitions.Discipline = Constraints_Disciplines.ID AND
				Events_Routines.CompetitionID IN (Select ID From Events_Competitions WHERE Events_Routines.PersonID = ? AND Discipline = ?) AND
				Events_Routines.RegisteredBy = alias2.ID
			GROUP BY
				Events_Routines.PersonID,
				Events_Routines.CompetitionID
			ORDER BY
				Events_Meets.Date
			;";			

	$stmt = $conn->prepare($sql);
	
	$stmt->bindParam(1, $personID, PDO::PARAM_INT, 5);
	$stmt->bindParam(2, $Discipline, PDO::PARAM_INT, 1);	
	
	$stmt->execute();
	
	$count = 0;
	
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$competitionID = $row['CompetitionID'];
		$person = $row['PersonID'];
		if($Discipline == 2)
		{
			$FX = getPersonScoreForEvent($person,1,$competitionID);
			$PH = getPersonScoreForEvent($person,2,$competitionID);
			$SR = getPersonScoreForEvent($person,3,$competitionID);
			$VT = getPersonScoreForEvent($person,4,$competitionID);
			$PB = getPersonScoreForEvent($person,5,$competitionID);
			$HB = getPersonScoreForEvent($person,6,$competitionID);
			
			$FXSV = getPersonSVForEvent($person,1,$competitionID);
			$PHSV = getPersonSVForEvent($person,2,$competitionID);
			$SRSV = getPersonSVForEvent($person,3,$competitionID);
			$VTSV = getPersonSVForEvent($person,4,$competitionID);
			$PBSV = getPersonSVForEvent($person,5,$competitionID);
			$HBSV = getPersonSVForEvent($person,6,$competitionID);
			
			$AA = ($FX+$PH+$SR+$VT+$PB+$HB);
			//$theArray[$count] = array($person,$row['PersonName'],$row['CompetitionID'],$row['CompetitionName'],$FX,$PH,$SR,$VT,$PB,$HB,$AA);
			$theArray[$count] = array(
										'ID'=>$person,
										'Name'=>$row['PersonName'],
										'CompetitionID'=>$row['CompetitionID'],
										'DisciplineID'=>$Discipline,
										'Team'=>$row['CompetitionName'],
										'Meet'=>$row['theMeetName'],
										'MFX'=>$FX,
										'MPH'=>$PH,
										'MSR'=>$SR,
										'MVT'=>$VT,
										'MPB'=>$PB,
										'MHB'=>$HB,
										'MFXSV'=>$FXSV,
										'MPHSV'=>$PHSV,
										'MSRSV'=>$SRSV,
										'MVTSV'=>$VTSV,
										'MPBSV'=>$PBSV,
										'MHBSV'=>$HBSV,
										'MAA'=>$AA,
										'Fee'=>$row['theFee'],
										'Institution'=>$row['Institution']
									);
		}
		elseif($Discipline == 1)
		{
			$VT = getPersonScoreForEvent($person,8,$competitionID);
			$UB = getPersonScoreForEvent($person,9,$competitionID);
			$BB = getPersonScoreForEvent($person,10,$competitionID);
			$FX = getPersonScoreForEvent($person,11,$competitionID);
			
			$VTSV = getPersonSVForEvent($person,8,$competitionID);
			$UBSV = getPersonSVForEvent($person,9,$competitionID);
			$BBSV = getPersonSVForEvent($person,10,$competitionID);
			$FXSV = getPersonSVForEvent($person,11,$competitionID);
			
			$AA = ($VT+$UB+$BB+$FX);
			$theArray[$count] = array(
										'ID'=>$person,
										'Name'=>$row['PersonName'],
										'CompetitionID'=>$row['CompetitionID'],
										'DisciplineID'=>$Discipline,
										'Team'=>$row['CompetitionName'],
										'Meet'=>$row['theMeetName'],
										'WVT'=>$VT,
										'WUB'=>$UB,
										'WBB'=>$BB,
										'WFX'=>$FX,
										'WVTSV'=>$VTSV,
										'WUBSV'=>$UBSV,
										'WBBSV'=>$BBSV,
										'WFXSV'=>$FXSV,
										'WAA'=>$AA,
										'Fee'=>$row['theFee'],
										'Institution'=>$row['Institution']
									);
		}
		//echo $count;
		//print_r($row);
		$count++;
	}
	return $theArray;
}


if(isset($_REQUEST['getTopAllTimeScores']))
{
	$Apparatus = $_REQUEST['apparatus'];
	$Division = $_REQUEST['division'];
	$Level = $_REQUEST['level'];
	
	echo json_encode(getTop10ScoresOfAllTime($Apparatus,$Division,$Level));
}


function getTop10ScoresOfAllTime($Apparatus,$Division,$Level)
{
	global $conn;
	
	$returnArray = array();
	
	if($division = 1)
		$limit = 10;
	else 
		$limit = 10;
	
	$sql = "
		SELECT 
			Concat (Events_Meets.Season, ' ', Events_Meets.MeetName) As Meet,
			Concat (Identifiers_People.Lastname, ', ', Identifiers_People.Firstname) as Gymnast,
			Identifiers_Institutions.Name As Team,
			Concat (Constraints_Disciplines.DisciplineShortName, ' ', Constraints_MeetDivisions.Name, ' ', Constraints_MeetLevels.DisplayName) as Competition,
			Constraints_Apparatus.Name as Event,
			Events_Routines.Score
		FROM 
			Events_Routines, 
			Events_Competitions, 
			Events_Meets,
			Constraints_Apparatus,
			Constraints_MeetDivisions,
			Constraints_MeetLevels,
			Constraints_Disciplines,
			Identifiers_People,
			Identifiers_Institutions,
			(Select Distinct Score From Events_Routines Where Apparatus = ? AND CompetitionID IN (Select ID From Events_Competitions Where Division = ? And Level = ?) Order By Score Desc Limit ".$limit.") derivedTable
		WHERE
			Events_Routines.CompetitionID = Events_Competitions.ID AND
			Events_Competitions.MeetID = Events_Meets.ID AND
			Events_Routines.Apparatus = Constraints_Apparatus.ID AND
			Events_Competitions.Division = Constraints_MeetDivisions.ID AND
			Events_Competitions.Level = Constraints_MeetLevels.ID AND
			Events_Competitions.Discipline = Constraints_Disciplines.ID AND
			Events_Routines.PersonID = Identifiers_People.ID AND
			Events_Routines.ClubID = Identifiers_Institutions.ID AND
			Events_Routines.Score = derivedTable.Score AND
			Events_Routines.Apparatus = ? AND
			Events_Competitions.Division = ? AND
			Events_Competitions.Level = ? 
		Order By 
			Events_Routines.Score Desc
		";
	
	$stmt = $conn->prepare($sql);
	
	$stmt->bindParam(1, $Apparatus, PDO::PARAM_INT, 3);
	$stmt->bindParam(2, $Division, PDO::PARAM_INT, 3);
	$stmt->bindParam(3, $Level, PDO::PARAM_INT, 3);
	$stmt->bindParam(4, $Apparatus, PDO::PARAM_INT, 3);
	$stmt->bindParam(5, $Division, PDO::PARAM_INT, 3);
	$stmt->bindParam(6, $Level, PDO::PARAM_INT, 3);
	
	$stmt->execute();
	
	$count = 0;
	
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$returnArray[$count] = array(
										'Meet'=>$row['Meet'],
										'Gymnast'=>$row['Gymnast'],
										'Team'=>$row['Team'],
										'Competition'=>$row['Competition'],
										'Event'=>$row['Event'],
										'Score'=>$row['Score']
									);
		$count++;
	}
	return $returnArray;
}

if(isset($_REQUEST['getTopAllTimeAAScores']))
{
	$Division = $_REQUEST['division'];
	$Level = $_REQUEST['level'];
	
	echo json_encode(getTop10AAScoresOfAllTime($Division,$Level));
}

function getTop10AAScoresOfAllTime($Division,$Level)
{
	global $conn;
	
	$returnArray = array();
	
	if($division = 1)
		$limit = 10;
	else 
		$limit = 10;
	
	$sql = "
		Select 
			*
		From
			(
			SELECT 
				Concat (Events_Meets.Season, ' ', Events_Meets.MeetName) As Meet,
				Concat (Identifiers_People.Lastname, ', ', Identifiers_People.Firstname) as Gymnast,
				Identifiers_Institutions.Name As Team,
				Concat (Constraints_Disciplines.DisciplineShortName, ' ', Constraints_MeetDivisions.Name, ' ', Constraints_MeetLevels.DisplayName) as Competition,
				'AA' as Event,
				round(sum(Events_Routines.Score),3) As Score
			FROM 
				Events_Routines, 
				Events_Competitions, 
				Events_Meets,
				Constraints_MeetDivisions,
				Constraints_MeetLevels,
				Constraints_Disciplines,
				Identifiers_People,
				Identifiers_Institutions
			WHERE
				Events_Routines.CompetitionID = Events_Competitions.ID AND
				Events_Competitions.MeetID = Events_Meets.ID AND
				Events_Competitions.Division = Constraints_MeetDivisions.ID AND
				Events_Competitions.Level = Constraints_MeetLevels.ID AND
				Events_Competitions.Discipline = Constraints_Disciplines.ID AND
				Events_Routines.PersonID = Identifiers_People.ID AND
				Events_Routines.ClubID = Identifiers_Institutions.ID AND
				Events_Competitions.Division = ? AND
				Events_Competitions.Level = ? 
			Group By
				Events_Routines.CompetitionID,
				Events_Routines.PersonID
			Order By 
				Events_Routines.Score Desc
			) derivedTable1,
			
			(
			Select
				Distinct round(sum(Score),3) as Score 
			From 
				Events_Routines 
			Where 
				CompetitionID IN (Select 
									ID 
								From 
									Events_Competitions 
								Where Division = ? And Level = ?) 
			Group By 
				CompetitionID, 
				PersonID 
			Order By sum(Score) Desc Limit ".$limit."
			) derivedTable2
		WHERE
			derivedTable1.Score = derivedTable2.Score
			";
	
	$stmt = $conn->prepare($sql);
	
	$stmt->bindParam(1, $Division, PDO::PARAM_INT, 3);
	$stmt->bindParam(2, $Level, PDO::PARAM_INT, 3);
	$stmt->bindParam(3, $Division, PDO::PARAM_INT, 3);
	$stmt->bindParam(4, $Level, PDO::PARAM_INT, 3);
	
	$stmt->execute();
	
	$count = 0;
	
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$returnArray[$count] = array(
										'Meet'=>$row['Meet'],
										'Gymnast'=>$row['Gymnast'],
										'Team'=>$row['Team'],
										'Competition'=>$row['Competition'],
										'Event'=>$row['Event'],
										'Score'=>$row['Score']
									);
		$count++;
	}
	return $returnArray;
}

?>