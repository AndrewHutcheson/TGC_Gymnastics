<?php
	session_start();
	require_once("globals.php"); 
	
	if(isset($_REQUEST['userWantsToLogIn'])){
		getCredentials();
	}
	
	if(isset($_REQUEST['logout'])){
		logout();
	}
	
	function getSalt($user)
	{
		global $conn;
		
		$salt = "fail";
		
		$sql = "
				SELECT
					Salt
				From
					Identifiers_People
				Where
					Username = ?
				";
				
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $user, PDO::PARAM_STR, 250);
		$stmt->execute();
		
		$count = 0;
	
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$salt = $row["Salt"];
			$count++;
		}
		
		return $salt;
	}
	
	function writeToLoginLog($PersonID, $Success)
	{
		global $conn;
		$sql = "
				INSERT INTO Log_Login (PersonID, Success, PageID, IPAddress, UserAgent, Browser, Referrer)
				VALUES(?,?,?,?,?,?,?);
				";
		$stmt = $conn->prepare($sql);
		//$stmt->bind_param("iisssss",$PersonID,$Success,basename($_SERVER['SCRIPT_FILENAME']),$_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT'],get_browser(),$_SERVER['HTTP_REFERER']);
		
		$filename = basename($_SERVER['SCRIPT_FILENAME']);
		$browser = get_browser();
		
		$stmt->bindParam(1, $PersonID, PDO::PARAM_INT, 250);
		$stmt->bindParam(2, $Success, PDO::PARAM_INT, 250);
		$stmt->bindParam(3, $filename, PDO::PARAM_STR, 250);
		$stmt->bindParam(4, $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR, 250);
		$stmt->bindParam(5, $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR, 250);
		$stmt->bindParam(6, $browser, PDO::PARAM_STR, 250);
		$stmt->bindParam(7, $_SERVER['HTTP_REFERER'], PDO::PARAM_STR, 250);
		
		$stmt->execute();
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
			
		global $conn;
		$query = "SELECT 
						people.ID as PersonID, 
						ClubID, 
						coalesce(clubs.AltName,clubs.Name) As Name, 
						Season
					FROM 
						Identifiers_People people, 
						Identifiers_Affiliations affiliations, 
						Identifiers_Institutions clubs
					WHERE 
						clubs.ID = affiliations.ClubID AND
						people.ID = affiliations.PersonID AND
						
						people.Username = ? AND
						people.PasswordHash = ? AND
						affiliations.Season = ? 
					";
					
		if($stmt = $conn->prepare($query))
		{
			//$stmt->bind_param("sss",$username,$passwordhash,$currentSeason);
			$stmt->bindParam(1, $username, PDO::PARAM_STR, 250);
			$stmt->bindParam(2, $passwordhash, PDO::PARAM_STR, 250);
			$stmt->bindParam(3, $currentSeason, PDO::PARAM_STR, 250);
			
			$stmt->execute();
			//$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;
		}
		else
		{
			printf("Errormessage: %s\n", $conn->error);
		}
		
		if($stmt->rowCount() >= 1)
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$PersonID = $row['PersonID'];
			}
			
			$_SESSION['userIsLoggedIn'] = true;
			writeToLoginLog($PersonID, 1);
			$_SESSION['userID'] = $PersonID;
		}
		else
		{
			echo "<script>alert('That username/password combination was not found.');</script>";
			writeToLoginLog($UserID, 0);
		}
	}
	
	function doesUserHavePermission($permissionName,$permissionValue){
		global $conn;
		
		$permissionFound = false;
		
		$query = "	SELECT 
						* 
					FROM
						Identifiers_Permissions
					Where 
						PermissionName = ? AND
						PermissionValue = ? AND
						Season = " . getCurrentSeason() . " AND
						PersonID = ?
				";
		$stmt = $conn->prepare($query);
		
		$userID = getUserID();
		$stmt->bindParam(1, $permissionName, PDO::PARAM_STR, 100);
		$stmt->bindParam(2, $permissionValue, PDO::PARAM_INT, 20);
		$stmt->bindParam(3, $userID, PDO::PARAM_INT, 20);
		
		$stmt->execute();
		
		//while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		if($stmt->rowCount() > 0)
		{
			$permissionFound = true;
		}
		
		return $permissionFound;
	}
	
	function userIsAdminFor($club){
		return doesUserHavePermission("InstitutionAdmin",$club);
	}
	
	function userCanRegisterFor($club){
		return doesUserHavePermission("Registration",$club);
	}
	
	function userIsCoachFor($club){
		return doesUserHavePermission("Registration",$club);
	}
	
	function userIsCaptainFor($club){
		return doesUserHavePermission("Registration",$club);
	}
	
	function userIsExecutiveAdministrator(){ //e.g. TGC administrator. This is independent of club affiliation.
		return doesUserHavePermission("TGCAdmin",1);
		//return true;
	}
	
	function userIsGymnastFor($club){
		global $conn;
		
		$affiliationFound = false;
		
		$query = "	SELECT 
						* 
					FROM
						Identifiers_Affiliations
					Where 
						PersonID = " . getUserID() . " AND
						ClubID = ? AND
						Season = " . getCurrentSeason() . "
				";
		$stmt = $conn->prepare($query);
		
		$stmt->bindParam(1, $club, PDO::PARAM_INT, 10);
		
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$affiliationFound = true;
		}
		
		return $affiliationFound;
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
		global $conn;
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
			if($stmt = $conn->prepare($query))
			{
				$userID = getUserID();
				$stmt->bindParam(1,$userID,PDO::PARAM_INT, 10);
				$stmt->execute();
				//$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;
			}
			else
			{
				printf("Errormessage: %s\n", $conn->error);
			}
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$thename = $row['Name'];
			}	
		}			
		return $thename;
	}
	
	function getListOfUserCaptainPermissions()
	{
		$clubList = array(); //array of club ID's.
		
		global $conn;
		
		$query = "	SELECT 
						PermissionValue as ClubID,
						Identifiers_Institutions.Name as ClubName
					FROM
						Identifiers_Permissions,
						Identifiers_Institutions
					Where 
						PersonID = " . getUserID() . " AND
						PermissionName = 'Registration' AND
						Season = " . getCurrentSeason() . " AND
						Identifiers_Permissions.PermissionValue = Identifiers_Institutions.ID
				";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$clubList[$row['ClubID']] = $row['ClubName'];
		}
		
		return $clubList;
	}
	
	function getListOfUserCoachPermissions()
	{
		$clubList = array(); //array of club ID's.
		
		global $conn;
		
		$query = "	SELECT 
						PermissionValue as ClubID,
						Identifiers_Institutions.Name as ClubName
					FROM
						Identifiers_Permissions,
						Identifiers_Institutions
					Where 
						PersonID = " . getUserID() . " AND
						PermissionName = 'Registration' AND
						Season = " . getCurrentSeason() . " AND
						Identifiers_Permissions.PermissionValue = Identifiers_Institutions.ID
				";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$clubList[$row['ClubID']] = $row['ClubName'];
		}
		
		return $clubList;
	}
	
	function getListOfUserClubAdministrativePermissions(){
		$clubList = array(); //array of club ID's.
		global $conn;
		
		if(userIsExecutiveAdministrator()){ // if they are executive, they are admin for all clubs
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
			if($stmt = $conn->prepare($query)){
				$stmt->execute();
			}
			else{
				printf("Errormessage: %s\n", $conn->error);
			}
			
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$clubList[$row['ID']] = $row['Name'];
			}
		
		}
		else{
			$query = "	SELECT 
						PermissionValue as ClubID,
						Identifiers_Institutions.Name as ClubName
					FROM
						Identifiers_Permissions,
						Identifiers_Institutions
					Where 
						PersonID = " . getUserID() . " AND
						PermissionName = 'InstitutionAdmin' AND
						Season = " . getCurrentSeason() . " AND
						Identifiers_Permissions.PermissionValue = Identifiers_Institutions.ID
				";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$clubList[$row['ClubID']] = $row['ClubName'];
			}
		}
		return $clubList;
	}
	
	function getUserID(){
		if(isset($_SESSION['userID']))
			return $_SESSION['userID'];
		else
			return 0;
	}
	
	//nomenclature here may be misleading. Username doesnt mean crendential, but rather then name *of* the user.
	function userIdToUserName($userID)
	{
		global $conn;
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
			if($stmt = $conn->prepare($query))
			{
				$stmt->bind_param("i",$userID);
				$stmt->execute();
				$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;
			}
			else
			{
				printf("Errormessage: %s\n", $conn->error);
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
		unset($_SESSION['userID']);
		unset($_SESSION['userIsLoggedIn']);
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

