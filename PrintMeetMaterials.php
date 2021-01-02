<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>
<?php require_once("fpdf/fpdf.php"); ?>
<?php require_once("registrationAjax.php"); ?>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	
	function getHostIDFromMeetID($meetID)
	{
		global $conn;
		$return = "Host institution not found";
		$stmt= $conn->prepare("
						SELECT 
							HostClub
						FROM 
							Events_Meets
						WHERE 
							ID = ?
					;");
					
		$stmt->bindParam(1, $meetID, PDO::PARAM_INT, 6);	
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$return = $row['HostClub'];	
		}
		return $return;
	}
	
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
	/*TODO: Check mens and women arrays are sorted by school-division-level-name*/
	
	$womenEvents = array(
						8=>"Women's Vault",
						9=>"Uneven Bars",
						10=>"Balance Beam",
						11=>"Floor Exercise"
						);
	$menEvents =  array(
						1=>"Floor Exercise",
						2=>"Pommel Horse",
						3=>"Still Rings",
						4=>"Vault",
						5=>"Parallel Bars",
						6=>"Horizontal Bar"
						);
	
	$pageHalf = 1;
	
	$pdf = new FPDF('P','pt','Letter');
	$pdf->SetFont('Arial','B',16);
	$pdf->AddPage();
	
	function drawMenScoreCard($person)
	{
		global $pdf, $pageHalf, $womenEvents, $Reg;
		
		if($pageHalf == 1)
			$offset = 0;
		if($pageHalf == 2)
			$offset = 400;
		
		$pdf->SetFont('Arial','B',16);
		//Add meet name and date maybe add host logo
		$pdf->setX(-40);
		$pdf->setY($offset,false);
		$pdf->Cell(0,100,$Reg->meetName,0,0,'R');
		$pdf->setX(-40);
		$pdf->setY($offset+16,false);
		$pdf->Cell(0,100,$Reg->meetDate,0,0,'R');
		
		//Add school name
		//Add gymnast name, level, division-level-name*/
		$pdf->setX(40);
		$pdf->setY($offset);
		$pdf->Cell(0,100,$person['Name']);
		$pdf->setX(40);
		$pdf->setY($offset+16);
		$pdf->Cell(0,100,$person['Team']);
		$pdf->setX(40);
		$pdf->setY($offset+32);
		$pdf->Cell(0,100,$person['Institution']);
		
		//draw box for scores
		$pdf->setX(30);
		$pdf->setY($offset+100);
		$pdf->Cell(560,225,"",1,2); //at 225 each event is 45 when we divide by 5
		
		$pdf->SetFont('Arial','',16);
		
		$pdf->setX(30);
		$pdf->setY($offset+100);
		$pdf->Cell(80,112,"FX",1,0,"C");
		$pdf->Cell(80,112,"PH",1,0,"C"); 
		$pdf->Cell(80,112,"SR",1,0,"C"); 
		$pdf->Cell(80,112,"VT",1,0,"C"); 
		$pdf->Cell(80,112,"PB",1,0,"C"); 
		$pdf->Cell(80,112,"HB",1,0,"C"); 
		$pdf->Cell(80,112,"All-Around",1,0,"C"); 
		
		$pdf->setX(30);
		$pdf->setY($offset+100+112);
		$pdf->Cell(80,113,"",1,0); 	
		$pdf->Cell(80,113,"",1,0);		
		$pdf->Cell(80,113,"",1,0);		
		$pdf->Cell(80,113,"",1,0);		
		$pdf->Cell(80,113,"",1,0); 
		$pdf->Cell(80,113,"",1,0); 
		$pdf->Cell(80,113,"",1,2); 
		
		//label the box
		$pdf->setX(30);
		$pdf->setY($offset+100+70); 
		$pdf->Cell(80,113,"S.V",0,0,"C"); 
		$pdf->Cell(80,113,"S.V",0,0,"C"); 
		$pdf->Cell(80,113,"S.V",0,0,"C"); 
		$pdf->Cell(80,113,"S.V",0,0,"C"); 
		$pdf->Cell(80,113,"S.V",0,0,"C"); 
		$pdf->Cell(80,113,"S.V",0,0,"C"); 
		$pdf->setY($offset+100+120); 
		$pdf->Cell(80,113,"Score",0,0,"C"); 	
		$pdf->Cell(80,113,"Score",0,0,"C"); 	
		$pdf->Cell(80,113,"Score",0,0,"C"); 	
		$pdf->Cell(80,113,"Score",0,0,"C"); 	
		$pdf->Cell(80,113,"Score",0,0,"C"); 	
		$pdf->Cell(80,113,"Score",0,0,"C"); 	
		
		//insert images for scores
		
		$pageHalf++;
		
		if($pageHalf > 2)
		{
			$pdf->AddPage();
			$pageHalf = 1;
		}
	}
	
	function drawWomenScoreCard($person)
	{
		global $pdf, $pageHalf, $womenEvents, $Reg;
		
		if($pageHalf == 1)
			$offset = 0;
		if($pageHalf == 2)
			$offset = 400;
		
		$pdf->SetFont('Arial','B',16);
		//Add meet name and date maybe add host logo
		$pdf->setX(-40);
		$pdf->setY($offset,false);
		$pdf->Cell(0,100,$Reg->meetName,0,0,'R');
		$pdf->setX(-40);
		$pdf->setY($offset+16,false);
		$pdf->Cell(0,100,$Reg->meetDate,0,0,'R');
		
		//Add school name
		//Add gymnast name, level, division-level-name*/
			$pdf->setX(40);
			$pdf->setY($offset);
			$pdf->Cell(0,100,$person['Name']);
			$pdf->setX(40);
			$pdf->setY($offset+16);
			$pdf->Cell(0,100,$person['Team']);
			$pdf->setX(40);
			$pdf->setY($offset+32);
			$pdf->Cell(0,100,$person['Institution']);
		
		//draw box for scores
		$pdf->setX(30);
		$pdf->setY($offset+100);
		$pdf->Cell(560,225,"",1,2); //at 225 each event is 45 when we divide by 5
		
		$pdf->SetFont('Arial','',16);
		
		$pdf->setX(30);
		$pdf->setY($offset+100);
		$pdf->Cell(112,112,"Vault",1,0,"C");
		$pdf->Cell(112,112,"Bars",1,0,"C"); 
		$pdf->Cell(112,112,"Beam",1,0,"C"); 
		$pdf->Cell(112,112,"Floor",1,0,"C"); 
		$pdf->Cell(112,112,"All-Around",1,0,"C"); 
		
		$pdf->setX(30);
		$pdf->setY($offset+100+112);
		$pdf->Cell(112,113,"",1,0); 	
		$pdf->Cell(112,113,"",1,0);		
		$pdf->Cell(112,113,"",1,0); 
		$pdf->Cell(112,113,"",1,0); 
		$pdf->Cell(112,113,"",1,2); 
		
		//label the box
		$pdf->setX(30);
		$pdf->setY($offset+100+70); 
		$pdf->Cell(112,113,"S.V",0,0,"C"); 
		$pdf->Cell(112,113,"S.V",0,0,"C"); 
		$pdf->Cell(112,113,"S.V",0,0,"C"); 
		$pdf->Cell(112,113,"S.V",0,0,"C"); 
		$pdf->setY($offset+100+120); 
		$pdf->Cell(112,113,"Score",0,0,"C"); 	
		$pdf->Cell(112,113,"Score",0,0,"C"); 	
		$pdf->Cell(112,113,"Score",0,0,"C"); 	
		$pdf->Cell(112,113,"Score",0,0,"C"); 	
		
		//insert images for scores
		
		$pageHalf++;
		
		if($pageHalf > 2)
		{
			$pdf->AddPage();
			$pageHalf = 1;
		}
	}
	
	function drawScoreSheets($team,$discipline)
	{
		global $pdf, $menEvents, $womenEvents, $Reg;
				
		if($discipline == 2)
			$events = $menEvents;
		if($discipline == 1)
			$events = $womenEvents;
		if($discipline == 3)
			$events = array(1=>"");

		$count = 1;
		$max = sizeOf($events);
		
		foreach($events as $event)
		{
			//Add meet name and date maybe add host logo
			$pdf->SetFont('Arial','',10);
			$offset = 0;
			$pdf->setX(40);
			$pdf->setY($offset,false);
			$pdf->Cell(0,100,$Reg->meetName . " - " . date('M d, Y',strtotime($Reg->meetDate)),0,0);
			//$pdf->setX(40);
			//$pdf->setY($offset+16,false);
			//$pdf->Cell(0,100,$Reg->meetDate,0,0);
			
			$pdf->SetFont('Arial','',16);
			$pdf->setX(40);
			$pdf->setY($offset+20,false);
			if($discipline != 3)
				$pdf->Cell(0,100,$team['Institution'] . " - " . $team['Name'],0,0);
			else
				$pdf->Cell(0,100,"_____________________________________",0,0);
			$pdf->setX(40);
			$pdf->setY($offset+40,false);
			$pdf->Cell(0,100,$event,0,0);
			
			//some instructions.
			$pdf->SetFont('Arial','',8);
			$pdf->setX(40);
			$pdf->setY($offset+110,false);
			$pdf->Cell(0,11,"PLEASE TAKE THIS SHEET TO THE SCOREKEEPER AFTER EACH EVENT.",0,1);
			$pdf->setX(40);
			$pdf->Cell(0,11,"(Even if you scratch the event, so we know it isn't missing!!!)",0,0);
			
			//add appropriate number of lines for this competition and no more
			$pdf->SetFont('Arial','',16);
			$competitionID = $team['CompetitionID'];
			
			if($discipline == 3)
				$max = 8;
			else
				$max = $Reg->maxNumberOfCompetitorsPerEvent($competitionID);
			
			$pdf->setX(40);
			$pdf->setY($offset+105,false);
			$pdf->Cell(0,100,"Competitor Name",0,0);
			
			$pdf->SetFont('Arial','',8);
			$pdf->setX(40);
			$pdf->setY($offset+120,false);
			$pdf->Cell(0,100,"Include first and last name!",0,0);
			$pdf->SetFont('Arial','',16);
			
			$pdf->setX(385);
			$pdf->Cell(0,100,"S.V.",0,0);
			$pdf->setX(490);
			$pdf->Cell(0,100,"Score",0,0);
			
			for ($i = 1; $i<=$max; $i++)
			{
				$pdf->setX(40);
				$pdf->setY($offset+170,false);
				$pdf->Cell(300,40,$i . ".","B",0);
				$pdf->setX(385);
				$pdf->Cell(85,40,"","B",0);
				$pdf->setX(490);
				$pdf->Cell(85,40,"","B",0);
				$offset += 40;
			}
			
			if($count <= $max)
				$pdf->AddPage();
			
			$count++;
			//possibly pre-fill names for this event
		}
	}
	
	function drawInvoice($institutionID, $meetID, $womenArray, $menArray, $teams)
	{
		global $pdf, $Reg;
		$counter = 0;
		$offset = 85;
		$total = 0;
		//draw header
		$pdf->setX(40);
		$pdf->setY(0,false);
		$pdf->Cell(50,100,"Invoice for " . $Reg->meetName,0,0);
		$pdf->setX(450);
		$pdf->Cell(10,100,$Reg->meetDate,0,0);
		
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
		foreach($womenArray AS $person)
		{
			$counter++;
			if($counter > 42)
			{
				$pdf->AddPage();
				$offset = 85; 
				$counter = 0;
				
				//draw header
				$pdf->setX(40);
				$pdf->setY(0,false);
				$pdf->Cell(50,100,"Invoice for " . $Reg->meetName,0,0);
				$pdf->setX(450);
				$pdf->Cell(10,100,$Reg->meetDate,0,0);
				
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
			$fee = $person['Fee'];
			$total += $fee;
			
			$pdf->setX(40);
			$pdf->setY($offset+12,false); $offset += 12;
			$pdf->Cell(300,40,"Gymnast registration fee for " . $name,0,0);
			$pdf->setX(-150);
			$pdf->Cell(40,40,"$" . $fee,0,0);
		}
		
			$name = "";
			$fee = "";
			
			
			$pdf->setX(40);
			$pdf->setY($offset+12,false); $offset += 12;
			$pdf->Cell(300,40,"",0,0);
			$pdf->setX(-150);
			$pdf->Cell(40,40,"" . $fee,0,0);
		
		foreach($menArray AS $person)
		{
			$counter++;
			if($counter > 42)
			{
				$pdf->AddPage();
				$offset = 85; 
				$counter = 0;
				//draw header
				$pdf->setX(40);
				$pdf->setY(0,false);
				$pdf->Cell(50,100,"Invoice for " . $Reg->meetName,0,0);
				$pdf->setX(450);
				$pdf->Cell(10,100,$Reg->meetDate,0,0);
				
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
			$fee = $person['Fee'];
			$total += $fee;
			
			$pdf->setX(40);
			$pdf->setY($offset+12,false); $offset += 12;
			$pdf->Cell(300,40,"Gymnast registration fee for " . $name,0,0);
			$pdf->setX(-150);
			$pdf->Cell(40,40,"$" . $fee,0,0);
		}
		
		//display team fees
		$offset += 12;
		foreach($teams AS $team)
		{
			$counter++;
			if($counter > 42)
			{
				$pdf->AddPage();
				$offset = 85; 
				$counter = 0;
				//draw header
				$pdf->setX(40);
				$pdf->setY(0,false);
				$pdf->Cell(50,100,"Invoice for " . $Reg->meetName,0,0);
				$pdf->setX(450);
				$pdf->Cell(10,100,$Reg->meetDate,0,0);
				
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
			$name = $team['TeamName'];
			$fee = $team['TeamFee'];
			$total += $fee;
			
			if($fee > 0)
			{
				$pdf->setX(40);
				$pdf->setY($offset+12,false); $offset += 12;
				$pdf->Cell(300,40,"Team registration fee for " . $name,0,0);
				$pdf->setX(-150);
				$pdf->Cell(40,40,"$" . $fee,0,0);
			}
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
		$addresser = getInstitutionNameFromID(getHostIDFromMeetID($meetID));
		$pdf->setX(40);
		$pdf->setY($offset+20, false); $offset += 20;
		$pdf->Cell(12,12,$addresser['Name'],0,1);
		$pdf->setX(40);
		$pdf->setY($offset+20, false); $offset += 20;
		$pdf->Cell(12,12,"Mailing Address:",0,1);
		$pdf->setX(40);
		$pdf->Cell(12,12,$addresser['Address'],0,1);
		$pdf->setX(40);
		$pdf->Cell(12,12,$addresser['cityStZip'],0,1);
		
		$pdf->AddPage();		
	}
	
	$clubs = explode(",",$Reg->getInstitutionsInMeet($meetID));
	
	foreach($clubs AS $club)
	{
		$womenArray = $Reg->getPeopleInTeam($meetID, $club, 1);
		$menArray = $Reg->getPeopleInTeam($meetID, $club, 2);
		$womenTeamArray = array();
		$menTeamArray = array();
		//$clubNametoIDList = array(); //sigh...
		
		//oh god this is hacky I should put this in the reg object
			$teams = $Reg->getTeamTableData($club,$meetID); ///I can return competitionID and Max # of competitors per event
			foreach($teams as $team)
			{
				if(strpos($team['TeamName'], "Women") !== false)
				{
					$womenTeamArray[$team['TeamName']] = array(
															"Name"=>$team['TeamName'],
															"Institution"=>$team['InstitutionName'],
															"CompetitionID"=>$team['CompetitionID']
															);
															
					//$clubNametoIDList['club'] = $team['InstitutionName'];
				}
				else if(strpos($team['TeamName'], "Men") !== false)
				{
					$menTeamArray[$team['TeamName']] = array(
															"Name"=>$team['TeamName'],
															"Institution"=>$team['InstitutionName'],
															"CompetitionID"=>$team['CompetitionID']
															);
				}
			}
		
		drawInvoice($club,$meetID,$womenArray,$menArray,$teams);
		
		foreach($womenArray AS $person)
		{
			drawWomenScoreCard($person);			
		}
		if($pageHalf > 1)
		{
			$pageHalf = 1;
			$pdf->AddPage();
		}
		
		foreach($womenTeamArray AS $team)
		{
			drawScoreSheets($team,1);
		}
		
		foreach($menArray AS $person)
		{
			drawMenScoreCard($person);
		}
		if($pageHalf > 1)
		{
			$pageHalf = 1;
			$pdf->AddPage();
		}
		
		foreach($menTeamArray AS $team)
		{
			drawScoreSheets($team,2);
		}
			
		$blankPerson = array(
								"Name"=>"Name______________",
								"Institution"=>"Team______________",
								"Team"=>"Level/Div______________"
							);
		$blankTeam = array(
								"Name"=>"",
								"Institution"=>"",
								"CompetitionID"=>""
							);
		
		drawWomenScoreCard($blankPerson);
		drawWomenScoreCard($blankPerson);
		drawMenScoreCard($blankPerson);
		drawMenScoreCard($blankPerson);
		drawScoreSheets($blankTeam,3);
		
	}
	
	$pdf->Output();
?>
