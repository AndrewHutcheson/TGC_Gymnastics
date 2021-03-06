<?php

require_once("globals.php");

if (isset($_REQUEST['selectedClub'])){
    $return_arr = array();

    try {
        $search = $_REQUEST['term'].'%';
		$club = $_REQUEST['selectedClub'];
        $stmt = $conn->prepare('
								SELECT 
									Concat(LastName, ", ", FirstName, " ", MiddleName, " -", Schools) As Name,
									FirstName, 
									LastName, 
									MiddleName, 
									ID
								FROM 
									Identifiers_People,
									(
										SELECT
											Identifiers_Affiliations.PersonID,
											GROUP_CONCAT(" ",Identifiers_Affiliations.Season,ifnull(Identifiers_Institutions.Abbr,Identifiers_Institutions.Name) ORDER BY Season Desc) As Schools
										FROM
											Identifiers_Affiliations,
											Identifiers_Institutions
										WHERE
											Identifiers_Institutions.ID = Identifiers_Affiliations.ClubID AND
											Identifiers_Affiliations.GymnastPermission = 1 AND
											Identifiers_Affiliations.ClubID = ?
										GROUP BY
											PersonID
										ORDER BY 
											Season DESC										
									) Affiliations
								WHERE
									Identifiers_People.ID = Affiliations.PersonID AND
									(Concat(LastName, ", ", FirstName, " ", MiddleName) LIKE ? OR
									Concat(LastName, ",", FirstName, " ", MiddleName) LIKE ? ) AND
									ID > 100
								');
		//add WHERE not already registered NOT IN ()
		$season = getCurrentSeason();
        $stmt->bindParam(1, $club, PDO::PARAM_INT, 20);
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
											"value" => $row['ID'], 
											"label" =>  substr($row['Name'],0,strlen($row['Name'])),
											"name" => $row['Name'],
											"firstName" => $row['FirstName'],
											"lastName" => $row['LastName'],
											"middleName" => $row['MiddleName']
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
elseif (isset($_REQUEST['term'])){
    $return_arr = array();

    try {
        $search = $_REQUEST['term'].'%';
        $stmt = $conn->prepare('
								SELECT 
									Concat(LastName, ", ", FirstName, " ", MiddleName, " -", Schools) As Name,
									FirstName, 
									LastName, 
									MiddleName, 
									ID
								FROM 
									Identifiers_People
								LEFT JOIN
									(
										SELECT
											Identifiers_Affiliations.PersonID,
											GROUP_CONCAT(" ",Identifiers_Affiliations.Season,ifnull(Identifiers_Institutions.Abbr,Identifiers_Institutions.Name) ORDER BY Season Desc) As Schools
										FROM
											Identifiers_Affiliations,
											Identifiers_Institutions
										WHERE
											Identifiers_Institutions.ID = Identifiers_Affiliations.ClubID AND
											Identifiers_Affiliations.GymnastPermission = 1
										GROUP BY
											PersonID
										ORDER BY 
											Season DESC										
									) As Affiliations
								ON
									Identifiers_People.ID = Affiliations.PersonID
								WHERE 
									(Concat(LastName, ", ", FirstName, " ", MiddleName) LIKE ? OR
									Concat(LastName, ",", FirstName, " ", MiddleName) LIKE ? ) AND
									ID > 100
								');
		//add WHERE not already registered NOT IN ()
        $stmt->bindParam(1, $search, PDO::PARAM_STR, 150);
        $stmt->bindParam(2, $search, PDO::PARAM_STR, 150);
		$stmt->execute();
		
		$count=0;
		if ($stmt->rowCount() > 0)
		{
			//$log .= "<br/>Most Recent First: </br></br>";
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$return_arr[$count] = array(
											"value" => $row['ID'], 
											"label" =>  substr($row['Name'],0,strlen($row['Name'])),
											"name" => $row['Name'],
											"firstName" => $row['FirstName'],
											"lastName" => $row['LastName'],
											"middleName" => $row['MiddleName']
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
elseif (isset($_REQUEST['club'])){
    $return_arr = array();

    try {
        $club = $_REQUEST['club'];
        $stmt = $conn->prepare('
								SELECT 
									Concat(LastName, ", ", FirstName, " ", MiddleName, " -", Schools) As Name,
									FirstName, 
									LastName, 
									MiddleName, 
									ID
								FROM 
									Identifiers_People,
									(
										SELECT
											Identifiers_Affiliations.PersonID,
											GROUP_CONCAT(" ",Identifiers_Affiliations.Season,ifnull(Identifiers_Institutions.Abbr,Identifiers_Institutions.Name) ORDER BY Season Desc) As Schools
										FROM
											Identifiers_Affiliations,
											Identifiers_Institutions
										WHERE
											Identifiers_Institutions.ID = Identifiers_Affiliations.ClubID AND
											Identifiers_Affiliations.GymnastPermission = 1 AND
											Identifiers_Affiliations.ClubID = ? AND
											Season = ?
										GROUP BY
											PersonID
										ORDER BY 
											Season DESC										
									) Affiliations
								WHERE
									Identifiers_People.ID = Affiliations.PersonID AND
									ID > 100
								');
		//add WHERE not already registered NOT IN ()
		$season = getCurrentSeason();
        $stmt->bindParam(1, $club, PDO::PARAM_INT, 20);
        $stmt->bindParam(2, $season, PDO::PARAM_INT, 4);
		$stmt->execute();
		
		$count=0;
		if ($stmt->rowCount() > 0)
		{
			//$log .= "<br/>Most Recent First: </br></br>";
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$return_arr[$count] = array(
											"ID" => $row['ID'], 
											"label" =>  substr($row['Name'],0,strlen($row['Name'])),
											"name" => $row['Name'],
											"firstName" => $row['FirstName'],
											"lastName" => $row['LastName'],
											"middleName" => $row['MiddleName']
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

?>