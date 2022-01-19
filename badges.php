<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>
<?php require_once("fpdf/code128.php"); ?>
<?php require_once("registrationAjax.php"); ?>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class nametag
{
	public $quadrant = 0;
	public $isAdvisor = 0;
	public $isDirector = 0;
	public $isJudge = 0;
	public $isGymnast = 0;
	public $isCoach = 0;
	public $isVendor = 0;
	public $isOfficial = 0;
	public $schoolName = 0;
	public $personName = 0;
	public $personID = 0;
	public $teamID = 0;
	public $assignedSession = 0;
}


$pdf = new PDF_Code128('P','in',array(11.0,8.5));  //inches: http://www.fpdf.org/en/doc/__construct.htm
$nameTags = array();

$meetID = $_REQUEST['MeetID'];
global $conn;

	$stmtGymnasts= $conn->prepare("
	SELECT Distinct 
		Identifiers_People.ID,
		Identifiers_People.Firstname,
		Identifiers_People.Lastname,
		Identifiers_Institutions.Name
	FROM
		Events_Routines,
		Identifiers_People,
		Identifiers_Institutions
	WHERE
		CompetitionID IN (Select ID From Events_Competitions Where MeetID = ?) AND
		Events_Routines.PersonID = Identifiers_People.ID AND
		Events_Routines.ClubID = Identifiers_Institutions.ID
	ORDER BY
		Identifiers_Institutions.Name,
		Identifiers_People.Lastname,
		Identifiers_People.Firstname
	");
	
	$stmtGymnasts->bindParam(1, $meetID, PDO::PARAM_INT, 5);	
	
	$stmtGymnasts->execute();
	
	$quadrant = 1;	
	$temp = 0;
	$nametag1 = new nametag();
	$nametag2 = new nametag();
	$nametag3 = new nametag();
	$nametag4 = new nametag();
	$nametag5 = new nametag();
	$nametag6 = new nametag();
	$curNameTag = new nametag();
	
	$curPage = array();
	$curPage[1] = $nametag1;
	$curPage[2] = $nametag2;
	$curPage[3] = $nametag3;
	$curPage[4] = $nametag4;
	$curPage[5] = $nametag5;
	$curPage[6] = $nametag6;
	
	while($row = $stmtGymnasts->fetch(PDO::FETCH_ASSOC))
	{
		$curNameTag = $curPage[$quadrant];
		
		$curNameTag->quadrant = $quadrant;
		$curNameTag->isAdvisor = 0 ;
		$curNameTag->isDirector = 0;
		$curNameTag->isJudge = 0;
		$curNameTag->isGymnast = 1;
		$curNameTag->isCoach = 0;
		$curNameTag->isVendor = 0;
		$curNameTag->isOfficial = 0;
		$curNameTag->schoolName = $row['Name'];
		$curNameTag->personName = $row['Firstname'] . " " . $row['Lastname'];
		$curNameTag->personID = $row['ID'];
		$curNameTag->teamID = 0;
		$curNameTag->assignedSession = "";
		
		$quadrant++;
		if($quadrant == 7)
		{
			writePages($curPage);
			
			$quadrant = 1;
		}
		//because during devekopment I don't want to load everything every time.
		$temp++;
		if($temp == 10)
			;//break;
	}
	$pdf->Output();

function writePages($c)
{
	global $pdf;
	$pdf->AddPage();
	for($i = 1; $i < 7; $i++)
	{
		createNametag($c[$i]->quadrant,$c[$i]->isAdvisor,$c[$i]->isDirector,$c[$i]->isJudge,$c[$i]->isGymnast,$c[$i]->isCoach,$c[$i]->schoolName,$c[$i]->personName,$c[$i]->personID,$c[$i]->teamID,$c[$i]->assignedSession,$c[$i]->isVendor,$c[$i]->isOfficial);
	}
	$pdf->AddPage();
	for($i = 1; $i < 7; $i++)
	{
		makeNameTagBackPage($c[$i]->quadrant,$c[$i]->personID);
	}
}
		
function createNametag($quadrant,$isAdvisor,$isDirector,$isJudge,$isGymnast,$isCoach,$schoolName,$personName,$personID,$teamID,$assignedSession,$isVendor,$isOfficial) 
{
	/*first let's figure out their colors.
	some assumptions:
		1. A person can have one, two or three properties. The possible combinations are:
			-One (solid blue): Athlete
			-*proposed* One (solid green): Athlete who happens to be labeled a club officer or club admin. or we can place their club name in asterisks or something
			-One (solid yellow): Coach
			-One (solid red): Director, Advisor, Judge
			-Two (half blue and yellow): Athlete/Coach 
			
			-Three: Any director or advisor with multiple classifications  (director/advisor, coach and athlete) will only be solid red. 
			Furthermore "Athlete/Coach/Director" takes up more textual space so I think they will simply be kept solid red and only say Director/Advisor. We don't really care if they are competing.
			Any desire to do otherwise is vain, keeping director badges simple makes it easier to pick them out in a crowd. 
			Since directors can go anywhere they don't need athlete or coach on it.
	*/
	
	$meetName = "NAIGC NATIONALS 2018";
	
	$color = "#00205F"; //blue
	$type = "Athlete";
	$txtColor = "#ffffff";
	if($isCoach)
	{
		$color = "#FFA600";
		$txtColor = "#000000";
		$type = "Coach";
	}
	if($isCoach && $isGymnast)
	{
		$color = "#FFA600";
		$txtColor = "#000000";
		$type = "Coach / Athlete";
	}
	if(getPersonIfGreen($personID))
	{
		$color = "#40FF00";
		$txtColor = "#000000";
		$type = "Athlete";
	}
	if($isDirector)
	{
		$color = "#C30017";
		$txtColor = "#ffffff";
		$type = "Director";
	}
	if($isAdvisor)
	{
		$txtColor = "#ffffff";
		$color = "#C30017";
		$type = "Advisor";
	}
	if($isJudge)
	{
		$txtColor = "#ffffff";
		$color = "#C30017";
		$type = "Official Judge";
	}
	if($isOfficial)
	{
		$txtColor = "#ffffff";
		$color = "#C30017";
		if($schoolName != "Medical / Trainer")
			$type = "Event Official";
		else
			$type = "Medical / Trainer";
	}
	if($isVendor)
	{
		$txtColor = "#ffffff";
		$color = "#45227b";
		$type = "Vendor";
	}
	//todo: maybe add naigc logo to the front, transparent and in the background.
	
		//aaagh ok so this is an array.
		$temp = $color;
		$bgColor = array();
		$bgColor[0] = hexdec(substr($temp,1,2));
		$bgColor[1] = hexdec(substr($temp,3,2));
		$bgColor[2] = hexdec(substr($temp,5,2));
		
		$temp = $txtColor;
		$txtColor = array();
		$txtColor[0] = hexdec(substr($temp,1,2));
		$txtColor[1] = hexdec(substr($temp,3,2));
		$txtColor[2] = hexdec(substr($temp,5,2));
		
	makeNameTagFrontPage($quadrant,$bgColor,$txtColor,$type,$personName,$schoolName,$meetName,$personID);
}

function getPersonIfGreen($person)
{
	return false;
}
	
function makeNameTagFrontPage($quadrant,$bgColor,$txtColor,$type,$name,$club,$meetName,$compNum)
{
	global $pdf;
	$x = 0; //we will notice if quadrant wasn't set
	$y = 0;

		switch ($quadrant)
		{
			case 1:
				$x = 0.0;
				$y = 0.0;
				break;
			case 2:
				$x = 4.5;
				$y = 0.0;
				break;
			case 3:
				$x = 0.0;
				$y = 3.05;
				break;
			case 4:
				$x = 4.5;
				$y = 3.05;
				break;
			case 5:
				$x = 0.0;
				$y = 8;
				break;
			case 6:
				$x = 4.5;
				$y = 8;
				break;
		}
		
	//front of nametag
		//set background color and middle white frame
			$pdf->SetFillColor($bgColor[0],$bgColor[1],$bgColor[2]);
			$pdf->Rect($x+0,$y+0,4.0,3.0,"F");
			$pdf->SetFillColor(255,255,255);
			$pdf->Rect($x+3/32,$y+3/4,4.0-3/32-3/32,2+(34/64)-3/4,"F");
		//now fill in text stuff.
			//type of badge
				$pdf->SetXY($x+0,$y+0);
				$pdf->SetFont('Arial','','24');
				$pdf->SetTextColor($txtColor[0],$txtColor[1],$txtColor[2]);
				$pdf->Cell(4.0,3/4,$type,0,0,'C'); //it will auto valign.
			//person name and club
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B','24'); //ah fuck people with really long names get screwed here I'll have to scale this per person, damnit. same for school.
				//probably the easiest thing to do is to create brackets. So we check:
					$someUpperCharacterLimit = 29; //"international gymnastics camp" is 29
					if((strlen($name) >= 24) && ((strlen($name) < $someUpperCharacterLimit)))
						$pdf->SetFont('Arial','B','20');
					if((strlen($name) >= $someUpperCharacterLimit))
						$pdf->SetFont('Arial','B','16');
				$pdf->SetXY($x+0,$y+1.25);
				$pdf->Cell(4.0,0,$name,0,0,'C');
				$pdf->SetFont('Arial','','24');
				$pdf->SetXY($x+0,$y+1.25+5/8);
					if((strlen($club) >= 24) && ((strlen($club) < $someUpperCharacterLimit)))
						$pdf->SetFont('Arial','','20');
					if((strlen($club) >= $someUpperCharacterLimit))
						$pdf->SetFont('Arial','','16');
					//"gymnastics at the university of houston"
					if(strlen($club) >= 35)
						$pdf->SetFont('Arial','','13.5');
					//fuckin CU boulder is 48 characters long. Instead should break the string in half and do two lines...
					if(strlen($club) >= 48)
						$pdf->SetFont('Arial','','11');
				$pdf->Cell(4.0,0,$club,0,0,'C');
			//and the bottom, the meet name.
				$pdf->SetFont('Arial','B','22');
				$pdf->SetTextColor($txtColor[0],$txtColor[1],$txtColor[2]);
				$pdf->SetAutoPageBreak(false,0);
				//2+(34/64) is the position of the top of the bottom blue border.
				$pdf->SetXY($x+0,$y+2+(34/64));
				$pdf->Cell(4.0,3-(2+(34/64)),$meetName,0,0,'C');		
}	
	
function makeNameTagBackPage($quadrant,$compNum)
{
	global $pdf;

			//THESE ARE REVERSED ON BACK PAGE
			// 	1<-->2
			//	3<-->4
			//	5<-->6
		switch ($quadrant)
		{
			case 2:
				$x = 0.0;
				$y = 0.0;
				break;
			case 1:
				$x = 4.5;
				$y = 0.0;
				break;
			case 4:
				$x = 0.0;
				$y = 3.05;
				break;
			case 3:
				$x = 4.5;
				$y = 3.05;
				break;
			case 6:
				$x = 0.0;
				$y = 8;
				break;
			case 5:
				$x = 4.5;
				$y = 8;
				break;
		}
		
		//otherwise keep the same page and do new quadrant
			//Place static schedule image
			$pdf->Image('2018 FtWorth NametagBack.jpg',$x,$y,4.0,3.0);
			//Create and place barcode
			$pdf->SetFillColor(0,0,0);
			$pdf->SetFont('Arial','','10');
			if($compNum != 0) //the judges and other special people don't need barcodes. This will skip it.
			{
				$pdf->Code128($x+3.25,$y+2.2,$compNum,.5,.2);
			}
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('Arial','','6');
			$pdf->SetXY($x+3.25,$y+2.4);
			$pdf->Cell(0.5,0.2,$compNum,0,0,'C');
}
?>