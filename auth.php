<?php
	session_start();
	require_once("database.php"); 

	//UPDATE TO NEW CONN
	$con= new mysqli($sql_login_host, $sql_login_user, $sql_login_pass, $sql_login_db);
	
	/*ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/
	
    if($con->connect_error){
        die("Connection Problem: ". $con->connect_error);
    }
	
	if(isset($_REQUEST['userWantsToLogIn'])){
		getCredentials();
	}
	
	if(isset($_REQUEST['logout'])){
		logout();
	}
	
	function getSalt($user)
	{
		global $con;
		
		$salt = "fail";
		
		$sql = "
				SELECT
					Salt
				From
					Identifiers_People
				Where
					Username = ?
				";
				
		$stmt = $con->prepare($sql);
		//$stmt->bindParam(1, $user, PDO::PARAM_STR, 250);
		$stmt->bind_param("s",$user);
		$stmt->execute();
		
		$count = 0;
	
		$stmt->bind_result($tempSalt);
	
		//while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		while($stmt->fetch())
		{
			$salt = $tempSalt;
			$count++;
		}
		
		return $salt;
	}
	
	function writeToLoginLog($PersonID, $Success)
	{
		global $con;
		$sql = "
				INSERT INTO Log_Login (PersonID, Success, PageID, IPAddress, UserAgent, Browser, Referrer)
				VALUES(?,?,?,?,?,?,?);
				";
		$stmt = $con->prepare($sql);
		$stmt->bind_param("iisssss",$PersonID,$Success,basename($_SERVER['SCRIPT_FILENAME']),$_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT'],get_browser(),$_SERVER['HTTP_REFERER']);
		
		$stmt->execute();
	}
	
	
	function getPermissions()
	{
		//so let's get certain permissions. They have a name, type and value
		/*
			map | view | layer
			map | view |control
			map | edit | etc
			
			clubadmin | clubID | canAddclubadmin
			clubadmin | clubID | caneditclubdata
			
			tgcadmin
			
			gymnast | clubID
			coach | clubID
			
			ugh and then program ID...
		*/
		/*
			db table would be
			personid
			start date
			end date
			name
			type
			value
		*/
	}
	
	function checkPermission($name, $type, $value)
	{
		//get personid from session
		//get current date from systime
	}
	
	
	//ok so this function returns an array of [clubid]=>{array},[clubid]=>{array},[clubid]=>{array}
	//how to do other stuff? I could make clubID not numeric.
	//this array is only gettting the current credentials anyway...
	function getCredentials()
	{
	
		$username = $_REQUEST['username'];
		
		$salt = getSalt($_REQUEST['username']);
		$passwordhash = hash('sha512', $_REQUEST['password'].$salt);
		
		//echo "<pre>";
		//print_r($_REQUEST);
		//echo "</pre>";
		
		if(idate("m")>=8)
			$currentSeason = idate("Y")+1;
		else
			$currentSeason = idate("Y");
			
		global $con;
		$query = "SELECT 
						people.ID, 
						ClubID, 
						coalesce(clubs.AltName,clubs.Name) As Name, 
						CoachPermission, 
						CaptainPermission, 
						InstitutionAdminPermission, 
						ProgramAdminPermission, 
						GymnastPermission, 
						ExecutivePermission,
						Season
					FROM 
						Identifiers_People people, 
						Identifiers_Affiliations permissions, 
						Identifiers_Institutions clubs
					WHERE 
						clubs.ID = permissions.ClubID AND
						people.ID = permissions.PersonID AND
						
						people.Username = ? AND
						people.PasswordHash = ? AND
						permissions.Season = ? 
					";
					
		if($stmt = $con->prepare($query))
		{
			$stmt->bind_param("sss",$username,$passwordhash,$currentSeason);
				
			$stmt->execute();
			$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;
		}
		else
		{
			printf("Errormessage: %s\n", $con->error);
		}
		
		if($stmt->num_rows >= 1)
		{
			$stmt->bind_result($UserID,$ClubID,$ClubName,$UserCoachPermission,$UserCaptainPermission,$UserAdministratorPermission,$ProgramAdminPermission, $UserGymnastPermission,$UserExecutivePermission,$season);
		
			while($stmt->fetch())
			{
				$permissions[$ClubID] = array(
										'ClubName'=>$ClubName,
										'CoachPermission'=>$UserCoachPermission,
										'CaptainPermission'=>$UserCaptainPermission,
										'InstitutionAdminPermission'=>$UserAdministratorPermission,
										'ProgramAdminPermission'=>$ProgramAdminPermission,
										'GymnastPermission'=>$UserGymnastPermission,
										'ExecutivePermission'=>$UserExecutivePermission,
										'Season'=>$season
										);
			}
			
			$_SESSION['permissions'] = $permissions;
			$_SESSION['userIsLoggedIn'] = true;
			writeToLoginLog($UserID, 1);
			$_SESSION['userID'] = $UserID;
		}
		else
		{
			echo "<script>alert('That username/password combination was not found.');</script>";
			writeToLoginLog($UserID, 0);
		}

		mysqli_stmt_close($stmt);
		mysqli_close($con);
	}
	
	function userIsAdminFor($club){
		if($_SESSION['permissions'][$club]['InstitutionAdminPermission']){
			return true;
		}
		else{
			return false;
		}
	}
	
	function userIsCoachFor($club){
		if($_SESSION['permissions'][$club]['CoachPermission']){
			return true;
		}
		else{
			return false;
		}
	}
	
	function userIsCaptainFor($club){
		if($_SESSION['permissions'][$club]['CaptainPermission']){
			return true;
		}
		else{
			return false;
		}
	}
	
	function userIsGymnastFor($club){
		if($_SESSION['permissions'][$club]['GymnastPermission']){
			return true;
		}
		else{
			return false;
		}
	}
	
	function userIsAlumniFor($club){
		//get the years they were in club
	}
	
	function userIsJudge(){
		//todo
	}
	
	function userIsExecutiveAdministrator(){ //e.g. TGC administrator. This is independent of club affiliation.
		$isExecutive = false;

		//Scan through all of their affiliated clubs -> gets anyone dual enrolled
		if(isset($_SESSION['permissions']))
		foreach ($_SESSION['permissions'] as $inner) { //inner is just a number
			//then see if ANY of them have an executive flag
			foreach ($inner as $key=>$value) { //each key is the permission name and the value is true or false
				if(isset($_REQUEST['debugPermissions']))
				{echo "<pre>"; print_r($_SESSION['permissions']); echo "</pre>";}
			   if(($key=='ExecutivePermission')&&($value==1))
			   {
					$isExecutive = true;
			   }
			}
		}
		if($isExecutive){
			return true;
		}
		else{
			return false;
		}
	}
	
	function userIsLoggedIn(){
		if(isset($_SESSION['userIsLoggedIn'])){
			if($_SESSION['userIsLoggedIn'])
				return true;
			else
				return false;
		}
		else{
			return false;
		}
	}
	
	function userLoggedInNameIs(){
		global $con;
		$thename = "nobody";
		
		if(userIsLoggedIn())
		{
			$query = "
					SELECT
						Concat(LastName, ', ', FirstName) As Name
					FROM
						Identifiers_People
					WHERE
						ID = ?
				;";
			if($stmt = $con->prepare($query))
			{
				$stmt->bind_param("i",getUserID());
				$stmt->execute();
				$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;
			}
			else
			{
				printf("Errormessage: %s\n", $con->error);
			}
			//echo $stmt->num_rows;
			if($stmt->num_rows >= 1)
			{
				$stmt->bind_result($Name);
			
				while($stmt->fetch())
				{
					$thename = $Name;
				}
			}	
		}			
		return $thename;
	}
	
	function getListOfUserCaptainPermissions()
	{
		$clubList = array(); //array of club ID's.
		
		if(isset($_SESSION['permissions'])){ //if we got here they are logged in
			foreach ($_SESSION['permissions'] as $inner => $theArray) { //inner is now clubid, array is the array of perms.
				foreach ($theArray as $key => $value) { //key is name of permission, valus is true/false
				   if($key=='CaptainPermission')
				   {
					   if($value==1)
							$clubList[$inner] = $theArray['ClubName'];
				   }
				}
			}
		}
		
		return $clubList;
	}
	
	function getListOfUserCoachPermissions()
	{
		$clubList = array(); //array of club ID's.
		
		if(isset($_SESSION['permissions'])){ //if we got here they are logged in
			foreach ($_SESSION['permissions'] as $inner => $theArray) { //inner is now clubid, array is the array of perms.
				foreach ($theArray as $key => $value) { //key is name of permission, valus is true/false
				   if($key=='CoachPermission')
				   {
					   if($value==1)
							$clubList[$inner] = $theArray['ClubName'];
				   }
				}
			}
		}
		
		return $clubList;
	}
	
	function getListOfUserClubAdministrativePermissions(){
		$clubList = array(); //array of club ID's.
		
		if(userIsExecutiveAdministrator()){ // if they are executive, they are admin for all clubs
			global $con;
			$query = "SELECT
						ID,
						coalesce(Identifiers_Institutions.AltName,Identifiers_Institutions.Name) As Name
					FROM 
						Identifiers_Institutions
					WHERE 
						(ID IN (Select InstitutionID From Identifiers_Programs Where ClubType IN (1,7,14)) OR
						ID IN (78,6203,7023)) AND
						Identifiers_Institutions.State IN ('TX','OK','LA','KS')
					ORDER BY Name ASC
						";
			if($stmt = $con->prepare($query)){
				$stmt->execute();
				$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;
			}
			else{
				printf("Errormessage: %s\n", $con->error);
			}
			
			if($stmt->num_rows >= 1){
				$stmt->bind_result($ClubID,$ClubName);
			
				while($stmt->fetch()){
					$clubList[$ClubID] = $ClubName;
				}
			}
		}
		else{
			if(isset($_SESSION['permissions'])){ //else they are just admin for their own club(s)
				foreach ($_SESSION['permissions'] as $inner => $theArray) { //inner is now clubid, array is the array of perms.
					foreach ($theArray as $key => $value) { //key is name of permission, valus is true/false
					   if($key=='InstitutionAdminPermission')
					   {
						   if($value==1)
								$clubList[$inner] = $theArray['ClubName'];
					   }
					}
				}
			}
		}
		return $clubList;
	}
	
	function getUserID(){
		return $_SESSION['userID'];
	}
	
	//nomenclature here may be misleading. Username doesnt mean crendential, but rather then name *of* the user.
	function userIdToUserName($userID)
	{
		global $con;
		$thename = "nobody";
		
		if(userIsLoggedIn())
		{
			$query = "
					SELECT
						Concat(LastName, ', ', FirstName) As Name
					FROM
						Identifiers_People
					WHERE
						ID = ?
				;";
			if($stmt = $con->prepare($query))
			{
				$stmt->bind_param("i",$userID);
				$stmt->execute();
				$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;
			}
			else
			{
				printf("Errormessage: %s\n", $con->error);
			}
			//echo $stmt->num_rows;
			if($stmt->num_rows >= 1)
			{
				$stmt->bind_result($Name);
			
				while($stmt->fetch())
				{
					$thename = $Name;
				}
			}	
		}			
		return $thename;
	}
	
	function display_login(){
		/* echo "
			<form method = 'post' action = '".htmlspecialchars($_SERVER['PHP_SELF'])."'>
				<input type = 'text' name='username' placeholder='Username' />
				<input type = 'password' name='password' placeholder = 'Password' />
				<input type = 'submit' name = 'userWantsToLogIn' value='Submit' />
			</form><br/>
		"; */
		echo "
			<form method = 'post' action = '".str_replace("logout","",basename($_SERVER['REQUEST_URI']))."'>
				<input type = 'text' name='username' placeholder='Username' />
				<input type = 'password' name='password' placeholder = 'Password' />
				<input type = 'submit' name = 'userWantsToLogIn' value='Submit' />
			</form><br/>
		";
		echo "<p><a href = 'forgotPwd.php'>Forgot Password?</a></p>";
	}
	
	function logout(){
		unset($_SESSION['permissions']);
		unset($_SESSION['userIsLoggedIn']);
		unset($_SESSION['userID']);
	}
	
	if(isset($_REQUEST['debugPermissions']))
		debug_Permissions();
	
	function debug_permissions(){
		
		if(isset($_SESSION['permissions'])){
			echo "Permission session variable is set.<br/><br/>";
		}
		else{
			echo "Permission session variable is NOT set.<br/><br/>";
		}
	
		echo "<pre>";
			print_r($_SESSION['permissions']);
		echo "</pre>";
		
		echo "<br/><hr/><br/>";

		/*$club = 1;

		if(userIsAdminFor($club))
			echo "user is administrator for club " . $club . "<br/>";
		else 
			echo "user is NOT administrator for club " . $club . "<br/>";
			
		if(userIsCoachFor($club))
			echo "user is coach for club " . $club . "<br/>";
		else 
			echo "user is NOT coach for club " . $club . "<br/>";

		if(userIsCaptainFor($club))
			echo "user is captain for club " . $club . "<br/>";
		else 
			echo "user is NOT captain for club " . $club . "<br/>";

		if(userIsGymnastFor($club))
			echo "user is gymnast for club " . $club . "<br/>";
		else 
			echo "user is NOT gymnast for club " . $club . "<br/>";
		
		if(userIsExecutiveAdministrator())
			echo "user is executive administrator<br/>";
		else 
			echo "user is NOT executive administrator for club<br/>";*/
	}
	
?>

