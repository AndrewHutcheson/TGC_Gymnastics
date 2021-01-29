<?php
session_start();
require_once("globals.php");
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

function getScoresForPerson($personID,$DisciplineID)
{
	global $conn;
	$sql = "
			SELECT 
				Identifiers_People.FirstName,
				Identifiers_People.LastName,
				Identifiers_Institutions.Name As Team,
				i.Name AS Meet,
				concat(
									Constraints_MeetDivisions.Name, ' ',
									Constraints_MeetLevels.DisplayName, ' ',
									Constraints_Disciplines.DisciplineShortName
								 ) AS CompetitionName,
				Constraints_Apparatus.Initials,
				Events_Routines.Score
			FROM 
				Identifiers_People,
				Events_Routines,
				Constraints_Apparatus,
				Constraints_MeetDivisions,
				Constraints_MeetLevels,
				Constraints_Disciplines,
				Identifiers_Institutions,
				Identifiers_Institutions i,
				Events_Competitions,
				Events_Meets
			WHERE
				Identifiers_People.ID = Events_Routines.PersonID AND
				Constraints_Apparatus.ID = Events_Routines.Apparatus AND
				Events_Routines.ClubID = Identifiers_Institutions.ID AND
				Events_Routines.CompetitionID = Events_Competitions.ID AND
				Events_Competitions.MeetID = Events_Meets.ID AND
				Events_Competitions.Division = Constraints_MeetDivisions.ID AND
				Events_Competitions.Level = Constraints_MeetLevels.ID AND
				Events_Competitions.Discipline = Constraints_Disciplines.ID AND
				Events_Meets.HostClub = i.ID AND
				Events_Competitions.Discipline = ?
				PersonID = ?
			Order BY
				CompetitionID, Apparatus
	";
	
	
}	
?>