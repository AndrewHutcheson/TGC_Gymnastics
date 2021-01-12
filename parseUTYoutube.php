<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("globals.php");
?>
<html>
Enter the number of videos you just uploaded and then press the button to load the DB.<br/>
<br/>

<input type = "text" id = "vidNumber" name = "vidNumber"></input>
<input type = "button" value = "Submit" onclick = "buttonPress();" id = "buttonSet" name = "buttonSet"></input><br>
<br/>
All the videos you just uploaded should have been named in the following format:<br/>
<br/>
YYYY_MEET_FIRSTNAME-LASTNAME_EVENT#.ext<br/>
Where <br/>
<ul>
    <li>YYYY is the 4 digit year</li>
    <li>MEET is one of the following short codes (caps dont matter):</li>
    <ul>
    	<li><b>Abilene Christian University</b></li>
    	<ul>
    		<li>abilene</li>
        	<li>ab</li>
        </ul>
        <li><b>Baylor University</b></li>
        <ul>
      	        <li>bu</li>
        </ul>
		<li><b>Jazz Invite in New Orleans</b></li>
        <ul>
      	        <li>NOLA</li>
      	        <li>JAZZ</li>
        </ul>
        <li><b>UT Austin</b></li>
        <ul>
	        <li>ut</li>
	</ul>
	<li><b>UT Dallas</b></li>
        <ul>
	        <li>utd</li>
	</ul>
	<li><b>U Houston</b></li>
        <ul>
	        <li>uh</li>
	</ul>
        <li><b>Texas A&M</b></li>
        <ul>
	        <li>tamu</li>
	</ul>
        <li><b>Texas Tech University</b></li>
        <ul>
        	<li>tech</li>
        	<li>ttu</li>
        	<li>txtech</li>
        </ul>
        <li><b>Texas State University</b></li>
        <ul>
        	<li>txstate</li>
        	<li>tsu</li>
        	<li>txstate</li>
        </ul>
        <li><b>NAIGC Nationals (prelims or finals)</b></li>
        <ul>
	        <li>naigc-prelim</li>
        	<li>naigc-final</li>
	        <li>naigc-prelims</li>
	        <li>naigc-finals</li>
	</ul>
        <li><b>DIV II Collegiates</b></li>
        <ul>
	        <li>usag</li>
	</ul>
	<li><b>Metroplex (Dallas)</b></li>
        <ul>
	        <li>metro</li>
	</ul>
	<li><b>Houston National Invitational</b></li>
        <ul>
	        <li>hni</li>
	</ul>
    </ul>
    <li>Event is one of the following (caps dont matter):</li>
    <ul>
    	<li>M-FX</li>
        <li>M-PH</li>
        <li>M-SR</li>
        <li>M-VT</li>
        <li>M-V</li>
        <li>M-PB</li>
        <li>M-HB</li>
        <li>W-V</li>
        <li>W-VT</li>                
        <li>W-UB</li>
        <li>W-BB</li>
        <li>W-FX</li>
    </ul>  
    <li># is an optional parameter. It can be a 2 or 3 or non-existent. For women's second vaults, men's bonus vaults, or if a video was separated into multiple parts.</li>  
    <li>In the early days videos were uploaded as broken up files. At the beginning we uploaded like 5 years in one week and didn't feel like stitching pieces together. Now days you should try and avoid using part numbers except for a second vault.</li>
    <li>EXT is the file extension. Unless you removed it after you uploaded, youtube doesn't always remove the extension from the title. Usually you will be too lazy to do so. Acceptable extensions are (caps dont matter):</li>
    <ul>
    	<li>.mov</li>
        <li>.avi</li>
        <li>.wmv</li>
    </ul>
</ul>

Should anything need to be changed contact the webmaster or Andrew Hutcheson. Examples may be:<br />
-Adding new meet codes for new schools<br />
-Adding a 4 or 5 to the acceptable parts<br />
<br />


<script type="text/javascript">
		function buttonPress(){
			window.location='http://<? echo $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];?>?buttonSet=1&number=' + document.getElementById('vidNumber').value;
		}
</script>

<br/>
<hr><br/>
<br/>
<?
	$numberOfNonLinkedVids = 0;
	#connect to DB
			/*$user = "texacpnq";
			$password = "eoikNcx2j18b";
			$database = "texacpnq_utaustin1";
			$url = "localhost";
			$con = mysqli_connect($url,$user,$password);
			mysqli_select_db($database, $con) or die( "Unable to select database");
			*/
	
			$sql_login_host = "localhost"; # MySQL Host
			$sql_login_user = "texacpnq"; # MySql UserName
			$sql_login_pass = "eoikNcx2j18b"; # MySql Password
			$sql_login_db = "texacpnq_utaustin1"; # MySql Database

			//old, deprecate this in favor of PDO object.
				$con= new mysqli($sql_login_host, $sql_login_user, $sql_login_pass, $sql_login_db);
				
				if($con->connect_error){
					die("Connection Problem: ". $con->connect_error);
				}
	
	//check if link already exists in the database - do outside the loop for speed
			$validLinks = array();
			$sql = "SELECT link FROM meetVids";
			if($checkLink_query = mysqli_query($con,$sql))
				{
					while($result = mysqli_fetch_assoc($checkLink_query)){
						$validLinks[] = $result['link'];
					}
				}
			else
			{
				echo "An error has occurred while looking up all the urls.";
			}
	
	
		$stmtYearNames= $conn->prepare("
					SELECT 
						Distinct FirstName, 
						LastName 
					FROM 
						Identifiers_People, 
						Identifiers_Affiliations
					WHERE 
						Identifiers_People.ID = Identifiers_Affiliations.PersonID
						AND Identifiers_Affiliations.Season = ?
					;");


		$allValidNames = array();		
		$stmtAllNames= $conn->prepare("
					SELECT 
						Distinct FirstName, 
						LastName 
					FROM 
						Identifiers_People, 
						Identifiers_Affiliations
					WHERE 
						Identifiers_People.ID = Identifiers_Affiliations.PersonID
					;");
			$stmtAllNames->execute();
			
			while($row = $stmtAllNames->fetch(PDO::FETCH_ASSOC))
			{
				$allValidNames[] = $row['FirstName']. " " . $row['LastName'];
			}	

	
	$startIndex = 1;

if (isset($_REQUEST['buttonSet'])){
	
	//Do all the work of getting shit from youtube.
	$DEVELOPER_KEY = 'AIzaSyAZGxgeISiS5LTO8rdNPhBAe9iOzbKjWbk';
	//$channelId = 'UC2Hfi8KG87K06ONq29eZ28Q';
	$playlistId = 'UU2Hfi8KG87K06ONq29eZ28Q'; //for uploads
	$API_key = $DEVELOPER_KEY;

	$wantedNumber = $_REQUEST['number'];

	$anotherPage = false;
	$myarray = array();
	$arrayIndex = 0;
	
	do
	{
		//lets not get more than we need
		if($wantedNumber > 50)
		{
			$maxResults = 50;
		}
		else
		{
			$maxResults = $wantedNumber;
		}
		
		$url = "";
		//check if first or last page
		if(!$anotherPage)
		{
			//$url = 'https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId='.$channelId.'&key='.$API_key.'&maxResults='.$maxResults;
			$url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId='.$playlistId.'&key='.$API_key.'&maxResults='.$maxResults;
		}
		else
		{
			$url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId='.$playlistId.'&pageToken='.$nextPage.'&key='.$API_key.'&maxResults='.$maxResults;
		}
		$video_list = json_decode(file_get_contents($url),true);
		echo $url."<br>";
		//$video_list = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId=UC2Hfi8KG87K06ONq29eZ28Q&key=AIzaSyC1Q9jTH7r6O_CEVASSYwpOZ-vWVW1Hz_Y&maxResults=50'),true);
		//echo "<pre>".print_r($video_list)."</pre>";
		//see if there is another page
		if(isset($video_list['nextPageToken']))
		{
			$nextPage = $video_list['nextPageToken'];
			$anotherPage = true;
		}
		else
		{
			$anotherPage = false;
		}

		$video_list = $video_list['items'];

		foreach($video_list as $key)
		{
			//$myArray[$arrayIndex]['link'] = $key['id']['videoId'];
			$myArray[$arrayIndex]['link'] = $key['snippet']['resourceId']['videoId'];
			$myArray[$arrayIndex]['title'] = $key['snippet']['title'];
			$arrayIndex += 1;
		}
		
		$wantedNumber -= $maxResults;
	}while($anotherPage && ($wantedNumber > 0));
	/*echo "<pre>";
	print_r($myArray);
	echo "</pre>";*/
############## NOW I LOOP THROUGH $MYARRAY #####################	
	$inputNumber = count($myArray);
	$videosRemaining = $inputNumber;				
	$i = 0;
		while($videosRemaining > 0)
		//while ($i == 0)
		{
			$link = $myArray[$i]['link'];
			$title = $myArray[$i]['title'];
			//$title = "2019 Texas Tech Chris Kujalowicz M HB";

			$i++;
			//in the future, some code goes right here to check the permalink against the database. If that primary key exists, you can skip. This will be necessary when this script becomes automated. 
			//We may try this for the first time in 2013 if we are feeling brave
			//e.g. 
			//$link = urldecode($item->get_permalink());
			//now perform a sql search - WHERE link = $link
			//then check if there was a result or not
			//if so, CONTINUE on the loop and go to the next result
			
			//2019 change: we sometimes have added alumni and people from other teams, or some kind of note in parenthesis. We will strip anything in parenthesis.
			$pos = strpos($title,"(");
			if($pos == true)
			{
				$posEnd = strpos($title,")") + 1;
				$title = substr($title,0,$pos - 1) . substr($title,$posEnd,strlen($title)-$posEnd);
			}

############//Parse Video Title
			//format ia always
			//YYYY_MEET_FirstName-LastName_Event
			//Ex: 2011_UT_Rachel-Zarosky_W-FX
			
############//step1: Year is always first 4 Characters
			
			$year = substr($title,0,4);
			
############//step2: find start characters of everything else
			$currentIndex = 4; //start after the year
			
			############find the meet name############
			while($currentIndex < strlen($title)){
				if(($title[$currentIndex] == "_")||($title[$currentIndex] == " ")){
					$startIndexMeet = $currentIndex + 1;//first instance of _
					$currentIndex = $startIndexMeet;
					break;
				}
				else{
					$currentIndex++;
				}
			}
			
			//fix cause I used a dash to differentiate NAIGC prelim from finals
			$pos = strpos($title,"NAIGC");

				if($pos == true) {
					$currentIndex = $currentIndex+7;
				}
			
			//fix cause I in 2014 used "tamu-state" as meet name since they had two meets
			$pos = strpos($title,"TAMU State");

				if($pos == true) {
					$currentIndex = $currentIndex+6;
				}
				
			//fix cause 2019 webmaster didn't follow the naming schema
			$pos = strpos($title,"UT Austin");
			if($pos == true) {
				$currentIndex = $currentIndex+7;
			}
			
			$pos = strpos($title,"Texas Tech");
			if($pos == true) {
				$currentIndex = $currentIndex+6; //dunno why its 6 it should be 5
			}
			
			//fix cause in 2019 "NAIGC Nationals Daytona" was added
			$pos = strpos($title,"NAIGC Nationals Daytona");

				if($pos == true) {
					$currentIndex = $currentIndex+10;
				}
				
			############find the first name############
			while($currentIndex < strlen($title)){
				if(($title[$currentIndex] == "_")||($title[$currentIndex] == " ")){
					$startIndexName = $currentIndex + 1; //second instance of _
					$currentIndex = $startIndexName;
					break;
				}
				else{
					$currentIndex++;
				}	
			}
			############find the last name############
			while($currentIndex < strlen($title)){
				if(($title[$currentIndex] == "_")||($title[$currentIndex] == " ")){
					$startIndexName2 = $currentIndex + 1; //third instance of _
					$currentIndex = $startIndexName2;
					break;
				}
				else{
					$currentIndex++;
				}	
			}
			############find the event name############
			while($currentIndex < strlen($title)){
				if(($title[$currentIndex] == "_")||($title[$currentIndex] == " ")){
					$startIndexEvent = $currentIndex + 1; //fourth instance of _
					$currentIndex = $startIndexEvent;
					break;
				}
				else{
					$currentIndex++;
				}	
			}
			
############//now put in vars
			$meet = substr($title,$startIndexMeet,$startIndexName-$startIndexMeet-1);
			//$name = substr($title,$startIndexName,$startIndexEvent-$startIndexName-1);
			//the ucfirst function capitalizes the first letter of the string (the first letter of someone's name)
			$firstName = substr($title,$startIndexName,$startIndexName2-$startIndexName-1);
			$firstName = ucfirst($firstName);

			$lastName = substr($title,$startIndexName2,$startIndexEvent-$startIndexName2-1);
			$lastName = ucfirst($lastName);			
			
			$name = $firstName . " " . $lastName;

			$event = substr($title,$startIndexEvent);
			
			
			//Sometimes I screwed this up. I think it's fixed now. But this purpose of these lines of code should be obvious. Add extensions as necessary.
			$event = strtoupper($event);
			if((substr($event,-4,strlen($event)) == ".MOV")||(substr($event,-4,strlen($event)) == ".AVI")||(substr($event,-4,strlen($event)) == ".WMV")){
				$event = substr($event,0,strlen($event)-4);
			}
			
			/*ADJUST FOR VARIANCES IN NAMING SCHEMA*/
			$meet = strtolower($meet);
			switch ($meet){
				//default : $meet = 'TheMeetHasBeenSetToDefault'; break;}
				case 'tech' : {$meet = 'ttu'; break;}
				case 'txtech' : {$meet = 'ttu'; break;}
				
				case 'A&M' : {$meet = 'tamu'; break;}
				
				case 'baylor' : {$meet = 'BU'; break;}
				
				case 'txstate' : {$meet = 'tsu'; break;}
				case 'state': {$meet = 'tsu'; break;}
				
				case 'nola': {$meet = 'jazz'; break;}
				case 'jazz': {$meet = 'jazz'; break;}
				
				case 'abilene': {$meet = 'ab'; break;};

				//get rid of s
				case 'naigc prelims': {$meet = 'naigc-prelim'; break;};
				case 'naigc finals': {$meet = 'naigc-final'; break;};
				case 'naigc-prelims': {$meet = 'naigc-prelim'; break;};
				case 'naigc-finals': {$meet = 'naigc-final'; break;};
				
				
								
				//becasue now youtube strips the dash on upload...
				case 'naigc prelim': {$meet = 'naigc-prelim'; break;};
				case 'naigc final': {$meet = 'naigc-final'; break;};
				//2013 tamu hosting the state meet along with a second meet
				case 'tamu state': {$meet = 'tamu-state'; break;};
				
				//adjust for Jared the vieo chair's changes to my schema...
				//2019 fixes
				case 'naigc nationals daytona': {$meet = 'naigc-prelim'; break;};
				case 'ut austin': {$meet = 'ut'; break;};
				case 'texas tech': {$meet = 'ttu'; break;};
			}
			
			$meet = strtoupper($meet);
			
############//NOW ADJUST FOR OTHER THINGS::::
			
			
			//check the name capitalization again
			$firstName = ucfirst($firstName);
			$lastName = ucfirst($lastName);
			
			//Casues the second Vault to be listed after the event has been selected:
			//e.g. 2007_Tech_Andrew_V2 -> 2007 TTU M-V Andrew2

			$event = strtolower($event);
						
			if(substr($event,-2,1) == ' '){
				//here the count was off because V is one character shorter than everything else. Should used VT instead
				if(substr($event,-1,1) != 'v')
				{
					$event = substr($event,0,strlen($event)-2) + substr($event,strlen($event),1);
				}
			}
			$part = 1;
			if(substr($event,-1,1) == '2'){
				$event = substr($event,0,strlen($event)-1);
				$part = 2;
			}
			if(substr($event,-1,1) == '3'){
				$event = substr($event,0,strlen($event)-1);
				$part = 3;
			}
			
			//youtube strips the dashes out of the name now on upload >:(
			//so add them back in
			if(true){
				switch($event){
					case 'w v' : $event = 'W-V'; break;
					case 'w v1' : $event = 'W-V'; break;
					case 'w v2' : $event = 'W-V'; break;
					case 'w vt' : $event = 'W-V'; break;
					case 'w vt1' : $event = 'W-V'; break;
					case 'w vault' : $event = 'W-V';  break;
					case 'w ub' : $event = 'W-UB'; break;
					case 'w bb' : $event = 'W-BB'; break;
					case 'w beam' : $event = 'W-BB'; break;
					case 'w fx' : $event = 'W-FX'; break;
					case 'm fx' : $event = 'M-FX'; break;
					case 'm ph' : $event = 'M-PH'; break;
					case 'm sr' : $event = 'M-SR'; break;
					case 'm r' : $event = 'M-SR'; break;
					case 'm v' : $event = 'M-V'; break;
					case 'm v1' : $event = 'M-V'; break;
					case 'm v bonus' : $event = 'M-V'; break;
					case 'm v2' : $event = 'M-V'; break;
					case 'm vault' : $event = 'M-V';  break;
					case 'm vt' : $event = 'M-V'; break;
					case 'm vt1' : $event = 'M-V'; break;
					case 'm pb' : $event = 'M-PB'; break;
					case 'm hb' : $event = 'M-HB'; break;
				}	
			}
			
			//$link = urldecode($link);
			
			//echo "Display: Year" . $year . " Meet " . $meet . " Name " . $name  . " Event " . $event . "<br/>";
			//echo "Link: ";
			//echo '<a target="_blank" href="' . urldecode($item->get_permalink()) . '">' . $item->get_title() . '</a>';
			
			$link = $link;
			
############ADJUST FOR MULTI PART ROUTINES OR MULTIPLE COPIES/ANGLES###############			
			//$counter = $counter + 1;
			
			$event = strtoupper($event);
			
			//validation
			//check year 
			$currentYear = date("Y");
			if(($year <= 1950) || ($year > $currentYear))
			{
				$year = '<span style="color:red;">'.$year.'</span>';
			}
			elseif($year == ++$currentYear){
				$year = '<span style="color:c58917;">'.$year.'</span>';
			}
			
			//now check that the event given is the same gender as the person
			//currently this is ignoring the year check for when two different people have the same name. it shoud probably be added in.
			$gender = "0";
			$sqlGender = "SELECT gender FROM memberBiosInfo i WHERE i.firstName =  '" . $firstName . "' AND i.lastName = '" . $lastName . "';";
			if($gender_query = mysqli_query($con,$sqlGender))
				{
					while($result = mysqli_fetch_assoc($gender_query)){
						$gender =$result['gender'];
					}
				}
			//echo $gender;
			if($gender=="M")
			{
				$menEvents = array("M-FX","M-PH","M-SR","M-V","M-VT","M-PB","M-HB");
				if(!(in_array($event,$menEvents)))
				{
					$event = '<span style="color:#c58917; font-weight:bold;">'.$event.'</span>';
				}
			}
			elseif($gender=="F")
			{
				$womenEvents = array("W-V","W-VT","W-UB","W-BB","W-FX");
				if(!(in_array($event,$womenEvents)))
				{
					$event = '<span style="color:#c58917; font-weight:bold;">'.$event.'</span>';
				}
			}
			else{
				//check for all events
				$validEvents = array("M-FX","M-PH","M-SR","M-V","M-VT","M-PB","M-HB","W-V","W-VT","W-UB","W-BB","W-FX");
				if(!(in_array($event,$validEvents)))
				{
					$event = '<span style="color:red;">'.$event.'</span>';
				}
			}
					
			//check meet
			$validMeets = array("AB","BU","METRO","UT","UTD","UH","A&M","TAMU","TAMU2","TTU","TSU","OU","PRAC","PRAC1","PRAC2","NAIGC-PRELIM","NAIGC-FINAL","USAG","HNI","JAZZ");
			
			if($year==2014)
			{
				$validMeets = array("AB","BU","METRO","UT","UTD","TAMU","TTU","TSU","OU","PRAC","NAIGC-PRELIM","NAIGC-FINAL","USAG","TAMU-STATE","HNI");
			}
			
			if(!(in_array($meet,$validMeets)))
			{
				$meet = '<span style="color:red;">'.$meet.'</span>';
			}
			
			//check if name	is in that year		
			$validNames = array();
			//$sql = "SELECT firstName, lastName FROM memberBiosYear y, memberBiosInfo i WHERE i.eid=y.eid AND y.year = " . $year . ";";
			/*$sql = "
					SELECT 
						Distinct FirstName, 
						LastName 
					FROM 
						Identifiers_People, 
						Identifiers_Affiliations
					WHERE 
						Identifiers_People.ID = Identifiers_Affiliations.PersonID
						AND Identifiers_Affiliations.Season = " . $year . "
				;";
			if($name_query = mysqli_query($con,$sql))
			{
				while($result = mysqli_fetch_assoc($name_query)){
					$validNames[] = $result['FirstName']. " " . $result['LastName'];
					//$test = $result['firstName']. " " . $result['lastName'];
					//echo $test;
				}
			else
			{
				echo "An error has occurred while looking up this years names.";
			}
			}*/
			
			$stmtYearNames->bindParam(1, $year, PDO::PARAM_INT, 4);	
			$stmtYearNames->execute();
			
			while($row = $stmtYearNames->fetch(PDO::FETCH_ASSOC))
			{
				$validNames[] = $row['FirstName']. " " . $row['LastName'];
			}	
			
			
			
			//check if name in any year - for hosting alum vids
			//$allValidNames = array();
			//$sql = "SELECT firstName, lastName FROM memberBiosYear y, memberBiosInfo i WHERE i.eid=y.eid;";
			/*$sql = "
					SELECT 
						Distinct FirstName, 
						LastName 
					FROM 
						Identifiers_People a, 
						Identifiers_Affiliations b
					WHERE 
						a.ID = b.PersonID
				;";
				
			if($all_name_query = mysqli_query($con,$sql))
				{
					while($result = mysqli_fetch_assoc($all_name_query)){
						$allValidNames[] = $result['FirstName']. " " . $result['LastName'];
						//$test = $result['firstName']. " " . $result['lastName'];
						//echo $test;
					}
				}	
			else
			{
				echo "An error has occurred while looking up all names.";
			}*/
			
			//echo "<pre>"; print_r($allValidNames); echo "</pre>";
			
			//if not found in any year it becomes red
			if(!(in_array($name,$allValidNames)))
			{
				$name = '<span style="color:red;">'.$name.'</span>';
			}
			//if found but not in current year it becomes yellow
			elseif(!(in_array($name,$validNames)))
			{
				$name = '<span style="color:#c58917; font-weight:bold;">'.$name.'</span>';	
			}			
			
			//check if link is already in the database
			//$linkCheck = mysqli_real_escape_string($con,stripslashes($link));
			$linkCheck = html_entity_decode($link);
			if(!(in_array($linkCheck,$validLinks)))
			{
				//set link to an actual link for finding the video easily on youtube to change name or whatever	
				//$link = '<a href ="http://youtu.be/' . $link . '"><span style="color:red;">' . $link . '</span></a>';
				$link = '<a href ="https://studio.youtube.com/video/' . $link . '/edit" target = "_new"><span style="color:red;">' . $link . '</span></a>';
				$numberOfNonLinkedVids++;
			}
			else{
				//set link to an actual link for finding the video easily on youtube to change name or whatever	
				//$link = '<a href ="http://youtu.be/' . $link . '"><span style="color:green;">' . $link . '</span></a>';
				$link = '<a href ="https://studio.youtube.com/video/' . $link . '/edit" target = "_new"><span style="color:green;">' . $link . '</span></a>';
			}
			
			
			//$sql = "INSERT INTO meetVids(year, meet, event, gymnast, link, selectByGymnast, part, visibility) values ('".mysqli_real_escape_string(stripslashes($year))."','".mysqli_real_escape_string(stripslashes($meet))."','".mysqli_real_escape_string(stripslashes($event))."','".mysqli_real_escape_string(stripslashes($name))."','".mysqli_real_escape_string(stripslashes($link))."','".mysqli_real_escape_string(stripslashes($selectByName))."','".mysqli_real_escape_string(stripslashes($part))."','1');";
			$sql = "INSERT INTO meetVids(year, meet, event, gymnast, link, part, visibility) values ('".$year."','".$meet."','".$event."','".$name."','".$link."','".mysqli_real_escape_string($con,stripslashes($part))."','1');";
			echo $sql;
			/*echo " ";
			if($link_already_exists)
				echo "true";
			else
				echo "false";*/
			echo "<br/>";	
			/*if ($feed->error())
					{
						echo $feed->error();
					}			

/*			
########################### VIDEO ENTRY#################
			
			{
				# THIS CODE TELL MYSQL TO INSERT THE DATA FROM THE FORM INTO YOUR MYSQL TABLE
				#For Meet Videos Table, DB Schema: Year - int(4), meet - varchar(60), event - varchar(30), gymnast - varchar(50), link - text
			
				$sql = "INSERT INTO meetVids(year, meet, event, gymnast, link) values ('".mysqli_real_escape_string(stripslashes($year))."','".mysqli_real_escape_string(stripslashes($meet))."','".mysqli_real_escape_string(stripslashes($event))."','".mysqli_real_escape_string(stripslashes($name))."','".mysqli_real_escape_string(stripslashes($link))."')";
				if($result = mysqli_query($sql ,$con))
				{
					;
				} 
				else 
				{
					echo "ERROR: ".mysqli_error();
				}
				
				exit;
			} 
			*/
			
			
			$videosRemaining = $videosRemaining - 1;	
		} //end of foreach($myarray)		
}	//end of if isset button

//var_dump($validLinks);

//count the number of vids in the db
$sqlCount = "SELECT COUNT(*) AS count FROM `meetVids`;";
if($count_query = mysqli_query($con,$sqlCount))
{
	while($result = mysqli_fetch_assoc($count_query)){
		$numberOfTotalLinkedVids = $result['count'];
	}
}
				

echo "<br/><hr/><br/>The number of non-linked videos listed above is : " . $numberOfNonLinkedVids;
echo "<br/><br/>The number of videos in the database is : " . $numberOfTotalLinkedVids;
echo "<br/>The number of videos in the youtube channel is : uh look it up on youtube";
echo "<br/>So the difference between the two, #youtube - " . $numberOfTotalLinkedVids . " = the number missing. This should equal the number of unlisted+private videos.<br/>";
echo "<br/>If not, then something is missing from the db.";
?>
</html>