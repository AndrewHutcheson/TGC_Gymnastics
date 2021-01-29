<?php
session_start();
require_once("auth.php"); 
require_once("globals.php"); 
require_once("scoreAjax.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

////////////////////////////////////////////////////////////////////////////////////
///////////Preparing statements outside of loops and functions for efficiency//////
////////////////////////////////////////////////////////////////////////////////////

$sqlCheckIfAnyoneCompetedInCompetition = "
	Select 
		sum(Score) as total
	From
		Events_Routines
	Where
		CompetitionID = ?
	;";
$stmtcheckIfAnyoneCompetedInCompetition = $conn->prepare($sqlCheckIfAnyoneCompetedInCompetition);


$sqlCheckIfAnyoneCompetedOnEvent = "
		Select 
			sum(Score) as total
		From
			Events_Routines
		Where
			CompetitionID = ? AND
			Apparatus = ?
		;";
$stmtcheckIfAnyoneCompetedOnEvent = $conn->prepare($sqlCheckIfAnyoneCompetedOnEvent);


$sqlgetEventsForCompetition = "
		Select 
			ID,
			Name,
			ShortName,
			CompOrder
		From
			Constraints_Apparatus
		Where
			Discipline = (Select Discipline From Events_Competitions Where ID = ?)
		Order By 
			CompOrder
		;";
$stmtgetEventsForCompetition = $conn->prepare($sqlgetEventsForCompetition);

$sqlgetDisciplineForCompetition = "
		Select 
			Discipline
		From
			Events_Competitions
		Where
			ID = ?
		;";
$stmtgetDisciplineForCompetition = $conn->prepare($sqlgetDisciplineForCompetition);


$sqlGetCompetitions = "
		Select 
			Events_Competitions.ID,
			Concat(Constraints_Disciplines.DisciplineShortName, ' ', Constraints_MeetDivisions.Name,' ', Constraints_MeetLevels.DisplayName) as DisplayName
		From
			Events_Competitions,
			Constraints_MeetDivisions,
			Constraints_MeetLevels,
			Constraints_Disciplines
		Where
			Events_Competitions.MeetID = ? AND
			Events_Competitions.Level = Constraints_MeetLevels.ID AND
			Events_Competitions.Division = Constraints_MeetDivisions.ID AND
			Events_Competitions.Discipline = Constraints_Disciplines.ID
		Order By
			Events_Competitions.Discipline ASC,
			Events_Competitions.Division DESC,
			Events_Competitions.Level DESC
		;";
$stmtgetCompetitions = $conn->prepare($sqlGetCompetitions);


$sqlGetTopXScoresOnEvent = "
			Select 
				Score as theScore
			From
				Events_Routines
			Where
				Events_Routines.CompetitionID = ? AND
				Events_Routines.Apparatus = ? AND
				Score > 0
			ORDER BY
				Score Desc
			LIMIT 
				". intval($_REQUEST['maxPlaces']) ."
			";
$stmtGetTopXScoresOnEvent = $conn->prepare($sqlGetTopXScoresOnEvent);


$sqlGetTopXAAScoresOnEvent = "
			Select 
				sum(Score) As theScore
			From
				Events_Routines
			Where
				Events_Routines.CompetitionID = ?
			GROUP BY
				Events_Routines.PersonID
			HAVING
				sum(Score) > 0
			ORDER BY
				theScore Desc
			LIMIT 
				". intval($_REQUEST['maxPlaces']) ."
			";
$stmtGetTopXAAScoresOnEvent = $conn->prepare($sqlGetTopXAAScoresOnEvent);
			
///////////////////////////////////////////////////////////
///////////////// End Prepared Statements ////////////////
///////////////////////////////////////////////////////////

class teamScore
{
	public $ID = 0;
	public $institution = "";
	public $competition = "";
	public $competitionID = 0;
	public $placementText = "";
	
	public $teamScoreAA = 0;
	
	public $firstScore = 0;
	public $secondScore = 0;
	public $thirdScore = 0;
	public $fourthScore = 0;
	public $fifthScore = 0;
	public $sixthScore = 0;
}

class personWAG
{
	public $ID = 0;
	public $Name = "";
	public $institution = "";
	public $competition = "";
	public $competitionID = 0;
	
	public $placementText = "";
	
	public $VT = 0;
	public $UB = 0;
	public $BB = 0;
	public $FX = 0;
	public $AA = 0;		//is zero when they didn't actually do AA. bc not eligible.
	public $AATie = 0;  //used for tiebreaker
	
	public $VTSV = 0;
	public $UBSV = 0;
	public $BBSV = 0;
	public $FXSV = 0;
	public $AASV = 0;
	
	public $firstScore = 0;
	public $secondScore = 0;
	public $thirdScore = 0;
	public $fourthScore = 0;
	
	public function getScoreForEvent($event)
	{
		if($event == 8)
			return $this->VT;
		if($event == 9)
			return $this->UB;
		if($event == 10)
			return $this->BB;
		if($event == 11)
			return $this->FX;
		if($event == 0)
			return $this->AATie;
		
	}
	
	public function test()
	{
		echo "Person is: " . $this->Name . "Vault is: " . $this->VT . " Bars is: " . $this->UB . " Beam is: " . $this->BB . " Floor is: " . $this->FX . " AA is: " . $this->AATie . "<br/>";
	}
}

class personMAG
{
	public $ID = 0;
	public $Name = "";
	public $institution = "";
	public $competition = "";
	public $competitionID = 0;
	
	public $placementText = "";
	
	public $FX = 0;
	public $PH = 0;
	public $SR = 0;
	public $VT = 0;
	public $PB = 0;
	public $HB = 0;
	public $AA = 0;		//is zero when they didn't actually do AA. bc not eligible.
	public $AATie = 0;  //used for tiebreaker
	
	public $FXSV = 0;
	public $PHSV = 0;
	public $SRSV = 0;
	public $VTSV = 0;
	public $PNSV = 0;
	public $HBSV = 0;
	public $AASV = 0;
	
	public $firstScore = 0;
	public $secondScore = 0;
	public $thirdScore = 0;
	public $fourthScore = 0;
	public $fifthScore = 0;
	public $sixthScore = 0;
	
	public function getScoreForEvent($event)
	{
		if($event == 1)
			return $this->FX;
		if($event == 2)
			return $this->PH;
		if($event == 3)
			return $this->SR;
		if($event == 4)
			return $this->VT;
		if($event == 5)
			return $this->PB;
		if($event == 6)
			return $this->HB;
		if($event == 0)
			return $this->AATie;
	}
}

function sortTeamTies($a, $b)
{
	if($a->teamScoreAA < $b->teamScoreAA)
		return true;
	elseif($b->teamScoreAA < $a->teamScoreAA)
		return false;
	else
	{
		if($a->firstScore < $b->firstScore)
			return true;
		elseif($b->firstScore < $a->firstScore)
			return false;
		else
		{
			if($a->secondScore < $b->secondScore)
				return true;
			elseif($b->secondScore < $a->secondScore)
				return false;
			else
			{
				if($a->thirdScore < $b->thirdScore)
					return true;
				elseif($b->thirdScore < $a->thirdScore)
					return false;
				else
				{
					if($a->fourthScore < $b->fourthScore)
						return true;
					elseif($b->fourthScore < $a->fourthScore)
						return false;
					else //A TRUE TIE. TBD.
						return true;
				}
			}
		}
	}
}

function sortWagTies($a, $b)
{
	if($a->AATie < $b->AATie)
		return true;
	elseif($b->AATie < $a->AATie)
		return false;
	else
	{
		if($a->firstScore < $b->firstScore)
			return true;
		elseif($b->firstScore < $a->firstScore)
			return false;
		else
		{
			if($a->secondScore < $b->secondScore)
				return true;
			elseif($b->secondScore < $a->secondScore)
				return false;
			else
			{
				if($a->thirdScore < $b->thirdScore)
					return true;
				elseif($b->thirdScore < $a->thirdScore)
					return false;
				else
				{
					if($a->fourthScore < $b->fourthScore)
						return true;
					elseif($b->fourthScore < $a->fourthScore)
						return false;
					else
					{
						if($a->fifthScore < $b->fifthScore)
							return true;
						elseif($b->fifthScore < $a->fifthScore)
							return false;
						else
						{
							if($a->sixthScore < $b->sixthScore)
								return true;
							elseif($b->sixthScore < $a->sixthScore)
								return false;
							else //TBD, A TRUE TIE
							{
								return true;
							}
						}
					}
				}
			}
		}
	}
}

function sortMagTies($a, $b)
{
	if($a->AATie < $b->AATie)
		return true;
	elseif($b->AATie < $a->AATie)
		return false;
	else
	{
		if($a->firstScore < $b->firstScore)
			return true;
		elseif($b->firstScore < $a->firstScore)
			return false;
		else
		{
			if($a->secondScore < $b->secondScore)
				return true;
			elseif($b->secondScore < $a->secondScore)
				return false;
			else
			{
				if($a->thirdScore < $b->thirdScore)
					return true;
				elseif($b->thirdScore < $a->thirdScore)
					return false;
				else
				{
					if($a->fourthScore < $b->fourthScore)
						return true;
					elseif($b->fourthScore < $a->fourthScore)
						return false;
					else
					{
						if($a->fifthScore < $b->fifthScore)
							return true;
						elseif($b->fifthScore < $a->fifthScore)
							return false;
						else
						{
							if($a->sixthScore < $b->sixthScore)
								return true;
							elseif($b->sixthScore < $a->sixthScore)
								return false;
							else //TBD, A TRUE TIE
							{
								return true;
							}
						}
					}
				}
			}
		}
	}
}

function ordinal($number) 
{
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

function getTopXScoresOnEvent($competitionID,$eventID,$X)
{
	global $conn;
	global $stmtGetTopXScoresOnEvent;
	global $stmtGetTopXAAScoresOnEvent;
	
	$stmt = $stmtGetTopXScoresOnEvent;

	if($eventID == 0)
	{
		$stmt = $stmtGetTopXAAScoresOnEvent;
	}
	
	$stmt->bindParam(1, $competitionID, PDO::PARAM_INT, 6);	
	if($eventID != 0)
	{
		$stmt->bindParam(2, $eventID, PDO::PARAM_INT, 6);	
	}
	$stmt->execute();
	
	$returnArray = [];
	
	if ($stmt->rowCount() > 0)
	{
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$returnArray[] = $row['theScore'];
		}
	}
	//if($eventID == 0)
		//print_r($returnArray);
	return $returnArray;
}

function findWomenWithEventScore($competitionID,$event,$score)	//now this follows an implicit constraint of one event registration per person-meet-Discipline
{
	global $globalWAG;
	
	$returnArray = array();
	foreach($globalWAG AS $ID=>$gymnast)
	{
		if($competitionID == $gymnast->competitionID)
		{
			if($gymnast->getScoreForEvent($event) == $score)// && ($gymnast->competitionID == $competitionID))
			{
				$returnArray[] = $globalWAG[$ID];
			}
		}
	}
	return $returnArray;
}

function findMenWithEventScore($competitionID,$event,$score)	//now this follows an implicit constraint of one event registration per person-meet-Discipline
{
	global $globalMAG;
	
	$returnArray = array();
	foreach($globalMAG AS $ID=>$gymnast)
	{
		if($competitionID == $gymnast->competitionID)
		{
			if($gymnast->getScoreForEvent($event) == $score)// && ($gymnast->competitionID == $competitionID))
			{
				$returnArray[] = $globalMAG[$ID];
			}
		}
	}
	return $returnArray;
}

/*function createScoreTieArray($scores)
{
	global $scoreTieArray;
	global $scoresForTieArray;
	$n=1;
	foreach($scoresForTieArray as $score=>$numTies)
	{
		if($numTies > 1)
			$scoreTieArray[$score] = "Tied for " . ordinal($n) . ", receiving " . ordinal($scoresForTieArray[$score]) . " from " . "" . " with a " . $score;
		else
			$scoreTieArray[$score] = "In " . ordinal($n) . " place from " . "" . " with a " . $score;
		
		$n++;
	}
	
}*/

function populateTeams($meetID)
{
	global $globalTeam;
}

function populatePeople($meetID)
{
	global $globalMAG;
	global $globalWAG;
	
	$wag = getScores($meetID, "false", 1);
	$mag = getScores($meetID, "false", 2);

	foreach ($wag as $person)
	{
		$woman = new personWAG;
		$personID = $person['ID'];
		
		$woman->ID = $person['ID'];
		if($person['Phonetic'] != "")
			$woman->Name = $person['FirstName'] . " " . $person['LastName'] . " (" . $person['Phonetic'] . ")" ;
		else
			$woman->Name = $person['FirstName'] . " " . $person['LastName'];
		$woman->competitionID = $person['CompetitionID'];
		//$woman-> = $person['Team'];
		$woman->VT = $person['WVT'];
		$woman->UB = $person['WUB'];
		$woman->BB = $person['WBB'];
		$woman->FX = $person['WFX'];
		
		$woman->VTSV = $person['WVTSV'];
		$woman->UBSV = $person['WUBSV'];
		$woman->BBSV = $person['WBBSV'];
		$woman->FXSV = $person['WFXSV'];
		
		if(((double)$person['WVTSV'] * (double)$person['WUBSV'] * (double)$person['WBBSV'] * (double)$person['WFXSV']) > 0)
		//if(($person['WVTSV'] > 0) && ($person['WUBSV'] > 0) && ($person['WBBSV'] > 0) && ($person['WFXSV'] > 0))	
		{
			$woman->AA = $person['WAA'];
		}
		
		$woman->AATie = $person['WAA'];
		
		$woman->institution = $person['Institution'];
		$woman->competition = $person['Team'];
		
		$scores = array($person['WVT'],$person['WUB'],$person['WBB'],$person['WFX']);
		rsort($scores);
		
		$woman->firstScore = $scores[0];
		$woman->secondScore = $scores[1];
		$woman->thirdScore = $scores[2];
		$woman->fourthScore = $scores[3];
		
		$globalWAG[$personID] = $woman;
	}

	foreach ($mag as $person)
	{
		$man = new personMAG;
		$personID = $person['ID'];
		
		$man->ID = $person['ID'];
		
		if($person['Phonetic'] != "")
			$man->Name = $person['FirstName'] . " " . $person['LastName'] . " (" . $person['Phonetic'] . ")" ;
		else
			$man->Name = $person['FirstName'] . " " . $person['LastName'];
		$man->competitionID = $person['CompetitionID'];
		//$man-> = $person['Team'];
		$man->FX = $person['MFX'];
		$man->PH = $person['MPH'];
		$man->SR = $person['MSR'];
		$man->VT = $person['MVT'];
		$man->PB = $person['MPB'];
		$man->HB = $person['MHB'];
		
		$man->FXSV = $person['MFXSV'];
		$man->PHSV = $person['MPHSV'];
		$man->SRSV = $person['MSRSV'];
		$man->VTSV = $person['MVTSV'];
		$man->PBSV = $person['MPBSV'];
		$man->HBSV = $person['MHBSV'];
		
		if(((double)$person['MFXSV'] * (double)$person['MPHSV'] * (double)$person['MSRSV'] * (double)$person['MVTSV'] * (double)$person['MPBSV'] * (double)$person['MHBSV']) > 0)
		{
			$man->AA = $person['MAA'];
		}
		
		$man->AATie = $person['MAA'];
		
		$man->institution = $person['Institution'];
		$man->competition = $person['Team'];
		
		$scores = array($person['MFX'],$person['MPH'],$person['MSR'],$person['MVT'],$person['MPB'],$person['MHB']);
		rsort($scores);
		
		$man->firstScore = $scores[0];
		$man->secondScore = $scores[1];
		$man->thirdScore = $scores[2];
		$man->fourthScore = $scores[3];
		$man->fifthScore = $scores[4];
		$man->sixthScore = $scores[5];
		
		$globalMAG[$personID] = $man;
	}
}

////////////////////////////////////////////////
////////////////////////////////////////////////
////////////////////////////////////////////////

function checkIfAnyoneCompetedInCompetition($competitionID)
{
	global $conn; 
	global $stmtcheckIfAnyoneCompetedInCompetition;
	
	$stmtcheckIfAnyoneCompetedInCompetition->bindParam(1, $competitionID, PDO::PARAM_INT, 6);		
	$stmtcheckIfAnyoneCompetedInCompetition->execute();
	
	$total = 0;
	
	if ($stmtcheckIfAnyoneCompetedInCompetition->rowCount() > 0)
	{
		while($row = $stmtcheckIfAnyoneCompetedInCompetition->fetch(PDO::FETCH_ASSOC))
		{
			$total = $row['total'];
		}
	}
	
	if($total > 1)
		return true;
	else
		return false;
}

function checkIfAnyoneCompetedOnEvent($competitionID,$event)
{
	global $conn;
	global $stmtcheckIfAnyoneCompetedOnEvent;
	
	$stmtcheckIfAnyoneCompetedOnEvent->bindParam(1, $competitionID, PDO::PARAM_INT, 6);		
	$stmtcheckIfAnyoneCompetedOnEvent->bindParam(2, $event, PDO::PARAM_INT, 6);		
	$stmtcheckIfAnyoneCompetedOnEvent->execute();
	
	$total = 0;
	
	if ($stmtcheckIfAnyoneCompetedOnEvent->rowCount() > 0)
	{
		while($row = $stmtcheckIfAnyoneCompetedOnEvent->fetch(PDO::FETCH_ASSOC))
		{
			$total = $row['total'];
		}
	}
	
	if($total > 1)
		return true;
	else
		return false;
}

$globalWAG = array();
$globalMAG = array();
$globalTeam = array();

$scoreTieArray = array();
$scoresForTieArray = array();

displayScript($_REQUEST['meetID'],$_REQUEST['maxPlaces']);

function displayScript($meetID,$places)
{
	populatePeople($meetID);
	//foreach competition
		//iterate over events
		//then do AA
	$competitions = getCompetitions($meetID);
	foreach($competitions as $competition)
	{
		if(checkIfAnyoneCompetedInCompetition($competition['ID']))
		{
			echo "<h1>In the " . $competition['Name'] . " competition :</h1>";
			
			$events = getEventsForCompetition($competition['ID']);
			
			foreach($events as $event)
			{
				if(checkIfAnyoneCompetedOnEvent($competition['ID'],$event['ID']))
				{
					echo "<h2>On " . $event['DisplayName'] . ": </h2>";
					sortPeopleOnAnEvent($event['ID'],$event['DisplayName'],$competition['ID'],$places);
					echo "These are your " . $event['DisplayName'] . " champions!<br/>";
					echo "<span style = 'color:red'>----pause, and let a picture be taken.----</span><br/>";
				}
			}
			
			echo "<h1>In the All-Around competition:</h1>";
			sortPeopleOnAnEvent(0,"All-Around",$competition['ID'],$places);
			echo "These are your All Around champions!<br/>";
			echo "<span style = 'color:red'>----pause, and let a picture be taken.----</span><br/>";
		}
	}
	
}

function getEventsForCompetition($competitionID)
{
	global $conn;
	global $stmtgetEventsForCompetition;
	
	$stmtgetEventsForCompetition->bindParam(1, $competitionID, PDO::PARAM_INT, 6);		
	$stmtgetEventsForCompetition->execute();
	
	$returnArray = [];
	
	if ($stmtgetEventsForCompetition->rowCount() > 0)
	{
		while($row = $stmtgetEventsForCompetition->fetch(PDO::FETCH_ASSOC))
		{
			$returnArray[] = array(
								"ID"=>$row['ID'],
								"DisplayName" => $row['Name']
								);
		}
	}
	return $returnArray;
}

function getCompetitions($meet)
{
	global $conn;
	global $stmtgetCompetitions;

	$stmtgetCompetitions->bindParam(1, $meet, PDO::PARAM_INT, 6);		
	$stmtgetCompetitions->execute();
	
	$returnArray = [];
	
	if ($stmtgetCompetitions->rowCount() > 0)
	{
		while($row = $stmtgetCompetitions->fetch(PDO::FETCH_ASSOC))
		{
			$returnArray[] = array(
								"ID"=>$row['ID'],
								"Name" => $row['DisplayName']
								);
		}
	}
	return $returnArray;	
}

function getDisciplineForCompetition($competitionID)
{
	global $conn;
	global $stmtgetDisciplineForCompetition;
	
	$stmtgetDisciplineForCompetition->bindParam(1, $competitionID, PDO::PARAM_INT, 6);		
	$stmtgetDisciplineForCompetition->execute();
	
	if ($stmtgetDisciplineForCompetition->rowCount() > 0)
	{
		while($row = $stmtgetDisciplineForCompetition->fetch(PDO::FETCH_ASSOC))
		{
			$Discipline = $row['Discipline'];
		}
	}
	return $Discipline;
}

function sortPeopleOnAnEvent($event,$EventName,$competitionID,$placesToGo)
{	
	global $scoresForTieArray;
	global $scoreTieArray;
	
	$scores = array_unique(getTopXScoresOnEvent($competitionID,$event,$placesToGo));
	
	$Discipline = getDisciplineForCompetition($competitionID);
	
	foreach($scores as $index=>$score)
	{		
		//get the people.
		if($Discipline == 1) //men
			$people[$score] = findWomenWithEventScore($competitionID,$event,$score);
		elseif($Discipline == 2) //women
			$people[$score] = findMenWithEventScore($competitionID,$event,$score);
		$n = sizeof($people[$score]);
		$scoresForTieArray[$score] = $n;
		$tempArray = array();
		
		if(($n > 1) && ($Discipline == 2))
		{
			usort($people[$score],"sortMagTies");
		}
		elseif(($n > 1) && ($Discipline == 1))
		{
			usort($people[$score],"sortWagTies");
		}
	}
	
	/*echo "<pre>";
	echo print_r($people);
	echo "</pre>"; */ // ok looks good so far
	
	
	$count = 0;
	
	$finalArrayInReverseOrder = array();
	$currentNumber = 0;
	$currentPlace = 0;
	
	if(isset($people))
	foreach($people as $person)
	{
		if(sizeof($person) > 1) //then it's a tie and we go one more level deep.
		{
			//todo: when AA, don't increment CurrentNumber or currentplace if they didnt do AA
			$currentNumber++;
			foreach($person as $tied)
			{
				if((($event == 0)&&($tied->AA != 0)) || ($event !=0))
				{
					$currentPlace++;
					$tied->placementText = "Tied for " . ordinal($currentNumber) . " receivng " . ordinal($currentPlace);
					array_unshift($finalArrayInReverseOrder,$tied);
					//$count++;
					//if($count == $placesToGo)
						//exit;
				}
			}
			$currentNumber = $currentPlace;
		}	
		elseif(sizeof($person) == 1)
		{
			if((($event == 0)&&($person[0]->AA != 0)) || ($event !=0))
			{
				$currentNumber++;
				$currentPlace++;
				$person[0]->placementText = "In " . ordinal($currentNumber) . " place ";
				//if((($event == 0)&&($person[0]->AA != 0)) || ($event !=0))
					array_unshift($finalArrayInReverseOrder,$person[0]); 
				//$count++;
				//if($count == $placesToGo)
					//exit;
			}
		}
		else
		{
			print_r($person);
		}
	}
	
	/*echo "<pre>";
	echo print_r($finalArrayInReverseOrder);
	echo "</pre>"; */ 
	
	// ok looks good so far
	
	$count = 1;
	$end = sizeof($finalArrayInReverseOrder);
	
	foreach($finalArrayInReverseOrder as $index=>$person)
	{
		$placement = $person->placementText;
		$institution = $person->institution;
		$name = $person->Name;
		
		$score = $person->getScoreForEvent($event);
		
		if((($event == 0)&&($person->AA != 0)) || ($event !=0)) //24 FEB MOVE THIS TO EARLIER. i'm getting 8th, 5th, 4th, 3,2,1 bc 6 and 7 didnt do AA
		{
			if($count == $end)
				echo "And your " . $EventName . " champion, from " . $institution . " with a " . $score . ", " . $name . "<br/>";
				//echo $placement . " from " . $institution . ", " . $name . " with a " . $score . "<br/>";
			else
				echo $placement . " from " . $institution . ", " . "<span style='color:#dddddd'> with a " . $score . "</span>, " . $name . "<br/>";
		}
		
		$count++;
	}	
}
?>