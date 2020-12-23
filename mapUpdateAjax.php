<?php
session_start();

require_once("auth.php");
require_once("globals.php"); 


$year = DATE("Y");

//write function to get contact history from ID
function getHistory($institutionId)
{
	global $conn;
	global $year;
	try
	{
		$stmt= $conn->prepare("SELECT 
									contactedDate,
									contactedBy,
									notes,
									status,
									callLength
								FROM 
									Recruit_Contacts
								WHERE 
									institutionID = ?
								ORDER BY 
									ID DESC
									");
		$stmt->execute(array(
								$institutionId
							));
		
		if ($stmt->rowCount() > 0)
		{
			$log .= "<br/>Most Recent First: </br></br>";
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$date = $row['contactedDate'];
				$person = $row['contactedBy'];
				$notes = $row['notes'];
				$status = $row['status'];
				$length = $row['callLength'];
				
				if($status == 'contacted')
					$status = '<font color = "green">contacted</font>';
				if($status == 'uncontacted')
					$status = '<font color = "red">not contacted</font>';
				if($status == 'followup')
					$status = '<font color = "#D4A017">will follow up</font>';
				
				$log .= "<b>". userIdToUserName($person) . " on ".$date.": ".$status."</b> (".$length." min)</br>
					<p>".$notes."</p>
					<hr></br>
				";
			}
		}
		else
		{
			$log = 'There is no contact history for this gym.<br/>';
		}
	}
	catch (PDOException $e){
		$log = "PDO object error: " . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
	}
	return nl2br($log);
}

//write function to delete a history item
$sql = ""; 
	
if(userIsLoggedIn())
{
	//write function to save form data
	if($_POST['action']=='insertLog')
	{
		$exceptionThrown = false;
		try
		{		
			$stmt= $conn->prepare("
					INSERT INTO
						Recruit_Contacts(institutionID,contactedDate,contactedBy,notes,year,status,callLength)
					VALUES
						(:institution, :date, :person, :notes, :year, :status, :length)
					");

			$stmt -> execute(array(
				":institution" => $_POST['institutionID'],
				":date" => $_POST['contactedDate'],
				":person" => $_POST['contactedBy'],
				":notes" => $_POST['notes'],
				":year" => $year,
				":status" => $_POST['statusSelect'],
				":length" => $_POST['length'],
			));
		}
		catch (PDOException $e){
			echo json_encode(array($_POST['institutionID'], $_POST['statusSelect'], "update failed. Take a screenshot of this message and tell Andrew. PDO object error: " . $e->getMessage()));
			$exceptionThrown = true;
		}
		if(!$exceptionThrown)
			echo json_encode(array($_POST['institutionID'], $_POST['statusSelect'], "update sucessful"));
	}
	
	if($_POST['action']=='history')
	{
		echo getHistory($_POST['id']);
	}
	if($_POST['action']=='getform')
	{
	//echo "test is good, your id is " . $_POST['id'];
	?>
		<form id="updateForm" action="mapUpdateAjax.php" method="POST">
			<? echo $_POST['name'];?><br/><br/>
			<input type = 'hidden' name = 'action' value = 'insertLog' />
			<input type = 'hidden' name = 'institutionID' value = '<? echo $_POST['id']?>' />
			Who are you?<br/>
			<input type = "hidden" name = 'contactedBy' value = "<?php echo getUserID(); ?>">
			When did you make contact?<br/>
			<input id = "thedate" type = 'date' <?php // pattern = '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ?> required name = 'contactedDate'/> <button type = "button" onclick = "document.getElementById('thedate').valueAsDate = new Date();">now</button><br/>
			Appr. time spent (min in decimal, i.e. 1m30s = "1.5"):<br/>
			<input required name = "length" type = "number" step = "0.1" /><br/>
			What is the status?<br/>
			<select required name = 'statusSelect'>
				<option selected value = ''>Select Status</option>
				<option value = 'contacted'>They have been contacted</option>
				<option value = 'uncontacted'>They have not been contacted</option>
				<option value = 'followup'>They need a follow up (explain below)</option>
			</select><br/>
			Notes?<br/>
			<textarea required name = 'notes'></textarea><br/>
			<input type = 'submit' />
		</form>
	<?
	}
}
?>