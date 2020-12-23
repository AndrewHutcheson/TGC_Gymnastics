<?php
require_once("globals.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	$core = Core::getInstance();
	$conn = $core->dbh;

	if(isset($_REQUEST['addSkill']))
	{
		$event = $_REQUEST['eventID'];
		$name = $_REQUEST['name'];
		$description = $_REQUEST['description'];
		$altNames = $_REQUEST['altNames'];
		$photo = $_REQUEST['photo'];
		$video = $_REQUEST['video'];
		
		echo json_encode(addSkillToEvent($event,$name,$description,$altNames,$photo,$video));
	}	

	if(isset($_REQUEST['editSkill']))
	{
		$skillId = $_REQUEST['editSkill'];
		$event = $_REQUEST['eventID'];
		$name = $_REQUEST['name'];
		$description = $_REQUEST['description'];
		$altNames = $_REQUEST['altNames'];
		$photo = $_REQUEST['photo'];
		$video = $_REQUEST['video'];
		
		echo json_encode(editSkill($skillId, $name, $event, $description, $altNames, $photo, $video));
	}

	if(isset($_REQUEST['deleteSkill']))
	{	
		$skillId = $_REQUEST['deleteSkill'];
		
		echo json_encode(deleteSkill($skillId));
	}

	if(isset($_REQUEST['loadSkills']))
	{
		$event = $_REQUEST['eventID'];
		
		echo json_encode(getSkillsForEvent($event));
	}			
		
	function addSkillToEvent($event, $name, $description, $altNames, $photo, $video)
	{
		global $conn;
		$sql = "
				Insert INTO 
					Skills_Physical(SkillName, Event, SkillDescription, AltNames, Photo, Video)
				Values
					(?,?,?,?,?,?)
				;";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $name, PDO::PARAM_INT, 2);	
		$stmt->bindParam(2, $event, PDO::PARAM_STR, 254);	
		$stmt->bindParam(3, $description, PDO::PARAM_STR, 254);	
		$stmt->bindParam(4, $altNames, PDO::PARAM_STR, 254);	
		$stmt->bindParam(5, $photo, PDO::PARAM_STR, 254);	
		$stmt->bindParam(6, $video, PDO::PARAM_STR, 254);	
		
		if($stmt->execute())
			return array(); //yay
		else
			return false;//throw error
		
	}	

	function editSkill($skillId, $name, $event, $description, $altNames, $photo, $video)
	{
		global $conn;
		$sql = "
				Update 
					Skills_Physical
				SET
					Name = ?,
					Event = ?,
					SkillDescription = ?,
					AltNames = ?,
					Photo = ?, 
					Video = ?
				Where
					ID = ?
				;";
				
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $name, PDO::PARAM_STR, 254);	
		$stmt->bindParam(2, $event, PDO::PARAM_INT, 2);	
		$stmt->bindParam(3, $description, PDO::PARAM_STR, 254);	
		$stmt->bindParam(4, $altNames, PDO::PARAM_STR, 254);	
		$stmt->bindParam(5, $photo, PDO::PARAM_STR, 254);	
		$stmt->bindParam(6, $video, PDO::PARAM_STR, 254);	
		$stmt->bindParam(7, $skillId, PDO::PARAM_INT, 2);	
		
		if($stmt->execute())
			return array(); //yay
		else
			return false;//throw error
	}

	function deleteSkill($skillId)
	{
		global $conn;
		
		//ok and check that it isn't used in routines or in a code first.
		
		$sql = "
				Delete From
					Skills_Physical
				Where
					ID = ?
				LIMIT 1
				;";
				
		$stmt = $conn->prepare($sql);

		$stmt->bindParam(1, $skillId, PDO::PARAM_INT, 2);	
		
		if($stmt->execute())
			return array(); //yay
		else
			return false;//throw error
	}

	function getSkillsForEvent($event)
	{
		global $conn;
		
		$sql = "
				Select
					ID,
					Name,
					Description,
					Event,
					EventID,
					AltNames,
					JOProhibited,
					Photo,
					Video
				From
					Skills_Physical
				Where
					EventID = ?
				;";
				
		$stmt = $conn->prepare($sql);

		$stmt->bindParam(1, $event, PDO::PARAM_INT, 2);
		
		if($stmt->execute())
		{
			$count = 0;
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				//print_r($row);	
				$returnArray[$count] = array(
												'ID'=>$row['ID'],
												'SkillName'=>htmlspecialchars($row['Name']), //fuck off with the encoding bs php should be smarter than this... specialchars needed because of slashes and apostrophies in the data. Will the php setting for magic_quotes fix this?
												'Description'=>htmlspecialchars($row['Description']),
												'Event'=>htmlspecialchars($row['Event']),
												'EventID'=>htmlspecialchars($row['EventID']),
												'AltNames'=>htmlspecialchars($row['AltNames']),
												'JOProhibited'=>htmlspecialchars($row['JOProhibited']),
												'Photo'=>htmlspecialchars($row['Photo']),
												'Video'=>htmlspecialchars($row['Video'])
											);
				$count++;
			}
		}
		else
		{
			echo "statement did not execute";//throw error
		}
		
		if($count == 0)
		{
			$returnArray = array();
		}
		
		return $returnArray;
	}

	function assignEventSkillToCodeVersion($eventSkill,$value,$group,$flags,$codeName,$codeVersion)
	{
		global $conn;
		
		$sql = "
				Insert Into 
					
				Values
					(last_insert_id(),?,?,?,?,?,?,?)
				";
	}

?>