<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>
<?php require_once("fpdf/fpdf.php"); ?>
<?php require_once("registrationAjax.php"); ?>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	
	$pageHalf = 1;
	
	$pdf = new FPDF('P','pt','Letter');
	$pdf->AddPage();



	function getInstitutionNameFromID($institutionID)
	{
		global $conn;
		$return['Name'] = "Institution not found";
		$stmt= $conn->prepare("
						SELECT 
							ID, 
							coalesce(Identifiers_Institutions.AltName,Identifiers_Institutions.Name) As Name,
							concat(Address1, ' ', Address2) as Address,
							concat(City, ' ', State, ', ', Zip) As cityStZip
						FROM 
							Identifiers_Institutions
						WHERE 
							ID = ?
					;");
					
		$stmt->bindParam(1, $institutionID, PDO::PARAM_INT, 6);	
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$return['Name'] = $row['Name'];
			$return['Address'] = $row['Address'];
			$return['cityStZip'] = $row['cityStZip'];
			
		}
		return $return;
	}
	
	if(userIsLoggedIn()) //quick way of parsing input to prevent sql injections since we control who has login permissions
	{
		$meetID = $_REQUEST['meetID'];
		$Reg = new meetRegistration("byMeet",$meetID);
	}
	else
	{
		"ERROR: YOU ARE NOT LOGGED IN";
	}
	
	function drawInvoice($institutionID, $meetID, $everyone)
	{
		global $pdf, $Reg;
		$counter = 0;
		$offset = 85;
		$total = 0;
		//draw header
		$year = getCurrentSeason();
		$pdf->setX(40);
		$pdf->setY(0,false);
		$pdf->SetFont('Arial','B',16);
		$pdf->Cell(50,100,"TGC Invoice for " . $year ,0,0);
		$pdf->setX(450);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(10,100,$Reg->meetDate,0,0);

		$pdf->setX(450);
		$pdf->setY(20,false);
		$array = array(6,7);
		if(in_array($institutionID,$array))
			$pdf->Cell(50,100,"Invoice # " . str_replace("-","",$Reg->meetDate) . $institutionID ,0,0);
		
		$pdf->SetFont('Arial','',14);
		$pdf->setX(40);
		$pdf->setY(20,false);
		
		$addressee =  getInstitutionNameFromID($institutionID);
		$pdf->Cell(50,100,"Billed to " . $addressee['Name'],0,0);
		
		$pdf->setX(40,false);
		$pdf->setY(35,false);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(50,100,$addressee['Address'],0,0);
		$pdf->setX(40,false);
		$pdf->setY(45,false);
		$pdf->Cell(50,100,$addressee['cityStZip'],0,0);
		
		//for each person display their name, reg/late and the fee
		foreach($everyone AS $person)
		{
			$counter++;
			if($counter > 42)
			{
				$pdf->AddPage();
				$offset = 85; 
				$counter = 0;
				
				//draw header
				$pdf->SetFont('Arial','B',16);
				$pdf->setX(40);
				$pdf->setY(0,false);
				$pdf->Cell(50,100,"TGC Invoice for " . $year,0,0);
				$pdf->setX(450);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(10,100,$Reg->meetDate,0,0);

				$pdf->setX(450);
				$pdf->setY(20,false);
				$array = array([6,7]);
				if(in_array($institutionID,$array))
					$pdf->Cell(50,100,"Invoice # " . stripslashes($Reg->meetDate) . $institutionID ,0,0);
				
				$pdf->SetFont('Arial','',14);
				$pdf->setX(40);
				$pdf->setY(20,false);
				
				$addressee =  getInstitutionNameFromID($institutionID);
				$pdf->Cell(50,100,"Billed to " . $addressee['Name'],0,0);
				
				$pdf->setX(40,false);
				$pdf->setY(35,false);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(50,100,$addressee['Address'],0,0);
				$pdf->setX(40,false);
				$pdf->setY(45,false);
				$pdf->Cell(50,100,$addressee['cityStZip'],0,0);
			}
			$name = $person['Name'];
			$fee = 10;
			$total += $fee;
			
			$pdf->setX(40);
			$pdf->setY($offset+12,false); $offset += 12;
			$pdf->Cell(300,40,"TGC Membership fee for " . $name,0,0);
			$pdf->setX(-150);
			$pdf->Cell(40,40,"$" . $fee,0,0);
		}
		
		//display running total
		$pdf->SetFont('Arial','',16);
		$offset += 30;
		$pdf->setX(40);
		$pdf->setY($offset,false); $offset += 12;
		$pdf->Cell(300,40,"Total Due: ",0,0);
		$pdf->setX(-150);
		$pdf->Cell(40,40,"$" . $total,0,0);
		
		$pdf->SetFont('Arial','',12);
		$pdf->setX(40);
		$pdf->setY($offset+25,false); $offset += 25;
		
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(12,30,"Make checks payable to: ",0,0);
		$pdf->setX(40);
		$pdf->setY($offset+20, false); $offset += 20;
		$pdf->Cell(12,12,"Texas Gymnastics Conference",0,1);
		$pdf->setX(40);
		$pdf->setY($offset+20, false); $offset += 20;
		$pdf->Cell(12,12,"Mailing Address:",0,1);
		$pdf->setX(40);
		$pdf->Cell(12,12,"3201 Quarry Creek",0,1);
		$pdf->setX(40);
		$pdf->Cell(12,12,"Round Rock, TX, 78681",0,1);
		
		$pdf->AddPage();		
	}
	
	$clubs = explode(",",$Reg->getInstitutionsInMeet($meetID));
	
	$crossClubCompetitorCheckList = array();

	foreach($clubs AS $club)
	{
		$thisClub = array();
		$womenArray = $Reg->getPeopleInTeam($meetID, $club, 1);
		$menArray = $Reg->getPeopleInTeam($meetID, $club, 2);

		foreach($menArray as $key=>$magPerson)
		{
			if(array_key_exists($magPerson['ID'],$crossClubCompetitorCheckList))
				;//skip
			else
			{
				$thisClub[$magPerson['ID']] = $magPerson;
				$crossClubCompetitorCheckList[$magPerson['ID']] = true;
			}
		}

		foreach($womenArray as $key2=>$wagPerson)
		{
			if(array_key_exists($wagPerson['ID'],$crossClubCompetitorCheckList))
				;//skip\
			else
			{
				$thisClub[$wagPerson['ID']] = $wagPerson;
				$crossClubCompetitorCheckList[$wagPerson['ID']] = true;
			}
		}
		
		drawInvoice($club,$meetID,$thisClub);
		
	}
	
	$pdf->Output();
?>
