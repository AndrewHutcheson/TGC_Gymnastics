<?php

require_once("globals.php");
require_once("auth.php");

if(userIsLoggedIn()) //quick way of parsing input to prevent sql injections since I post code to github
{
	if (isset($_REQUEST['getProgramDetails']))
	{
		$InstitutionID = $_REQUEST['institutionID'];
		getProgramDetails($InstitutionID);
	}

	if (isset($_REQUEST['updateProgramID']))
	{
		$field = "ClubType";
		$value = $_REQUEST['newType'];
		$row = $_REQUEST['programID'];
		saveProgramProperty($field,$value,$row);
	}

	if (isset($_REQUEST['updateProgramEmail']))
	{
		$field = "Email";
		$value = $_REQUEST['email'];
		$row = $_REQUEST['programID'];
		saveProgramProperty($field,$value,$row);
	}

	if (isset($_REQUEST['updateProgramPhone']))
	{
		$field = "Phone";
		$value = $_REQUEST['phone'];
		$row = $_REQUEST['programID'];
		saveProgramProperty($field,$value,$row);
	}

	if (isset($_REQUEST['term'])){
		$return_arr = array();

		try {
			$search = '%'.$_GET['term'].'%';
			$stmt = $conn->prepare('
									SELECT 
										Concat(Name, ", ", State, " - ", City) As label,
										Name,
										State, 
										City, 
										ID,
										Lat,
										Lng
									FROM 
										Identifiers_Institutions
									WHERE 
										Name LIKE ? OR
										State LIKE ? OR
										City LIKE ?
									');
			//add WHERE not already registered NOT IN ()
			$stmt->bindParam(1, $search, PDO::PARAM_STR, 150);
			$stmt->bindParam(2, $search, PDO::PARAM_STR, 150);
			$stmt->bindParam(3, $search, PDO::PARAM_STR, 150);
			$stmt->execute();
			
			$count=0;
			if ($stmt->rowCount() > 0)
			{
				//$log .= "<br/>Most Recent First: </br></br>";
				while($row = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					$return_arr[$count] = array(
												"ID" => $row['ID'], 
												"label" => addslashes($row['label']),
												"Name" => $row['Name'],
												"State" => $row['State'],
												"City" => $row['City'],
												"Lat" => $row['Lat'],
												"Lng" => $row['Lng']
												);
					$count++;
				}
			}
			
		} catch(PDOException $e) {
			echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		}
		/* Toss back results as json encoded array. */
		echo json_encode($return_arr);
	}
}

function saveInstitution()
{
	
}

function getProgramDetails($InstitutionID)
{
	global $conn;
	
	try {
		$stmt = $conn->prepare('
								Select 
									Identifiers_Programs.ID,
									Identifiers_Programs.Name,
									Identifiers_Programs.ClubType,
									Constraints_ClubTypes.TypeOfClub,
									Identifiers_Programs.Gender,
									Identifiers_Programs.ClubDivision,
									Identifiers_Programs.Phone,
									Identifiers_Programs.Email,
									Identifiers_Programs.Website,
									Identifiers_Programs.Twitter,
									Identifiers_Programs.Instagram,
									Identifiers_Programs.Facebook,
									Identifiers_Programs.YouTube,
									Identifiers_Programs.Snapchat,
									Identifiers_Programs.Inactive
								From 
									Identifiers_Programs, 
									Constraints_ClubTypes
								Where 
									InstitutionID = ? AND
									Identifiers_Programs.ClubType = Constraints_ClubTypes.ID
								Order By 
									ClubType, 
									Gender, 
									ClubDivision
							');
		$stmt->bindParam(1, $InstitutionID, PDO::PARAM_INT, 5);
		$stmt->execute();
			
		$count=0;
		if ($stmt->rowCount() > 0)
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$return_arr[$count] = array(
											"ProgramID" => $row['ID'],
											"Name" => $row['Name'],
											"ClubTypeID" => $row['ClubType'],
											"ClubTypeName" => $row['TypeOfClub'],
											"Gender" => $row['Gender'],
											"Division" => $row['ClubDivision'],
											"Phone" => $row['Phone'],
											"Email" => $row['Email'],
											"Website" => $row['Website'],
											"Twitter" => $row['Twitter'],
											"Instagram" => $row['Instagram'],
											"Facebook" => $row['Facebook'],
											"YouTube" => $row['YouTube'],
											"Snapchat" => $row['Snapchat'],
											"Inactive" => $row['Inactive']
											);
				$count++;
			}
		}
	} catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
    }
	echo json_encode($return_arr);
}

function saveProgramProperty($field,$value,$row)
{
	global $conn;
	
	$return_arr = array();
	
	try 
	{
		$stmt = $conn->prepare("Update Identifiers_Programs
				Set " . $field . " = ? 
				Where ID = ?");
		
		$stmt->bindParam(1, $value, PDO::PARAM_STR, 5);
		$stmt->bindParam(2, $row, PDO::PARAM_INT, 5);
		$stmt->execute();
	} 
	catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
    }
	echo json_encode($return_arr);
}


?>