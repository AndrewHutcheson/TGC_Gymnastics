<?php
require_once("auth.php");
require_once("globals.php");

if(userIsLoggedIn()) //quick way of parsing input to prevent sql injections since I post code to github
{
	if(isset($_REQUEST['mapMetrics']) && isset($_REQUEST['value']) && isset($_REQUEST['action']))
	{
		$action = $_REQUEST['action'];
		$value = $_REQUEST['value'];
		
		/*
		layer turned on or off - done
			action = layer on | layer off
			value = layer value
		filter turned on or off - NOT DONE
			action = filterset
			value = filterID
		highlight turned on or off - done
			action = layerselected
			value = layerval
		circle turned on - done
			action = circleON
			value = instID
		circle cleared - done
			action = circlecleared
			value = null
		circle size changed - done
			action = newcirclesize
			value = size
		nav button clicked - done
			action = navButtonClicked
			value = button
		*/
		
		log_MapAction($action,$value);
	}

	if(isset($_REQUEST['action']))
	{
		if($_REQUEST['action'] == "getOverlayData")
		{
			$type = $_REQUEST['type'];
			$state = $_REQUEST['state'];
			
			//echo stripslashes("State is " . $state . " and type is " . $type);
			//echo stripslashes("This popup will have USAG/NAIGC/Etc contact info for this region and state. This includes state or region judging directors, organizations and websites, coordinators, etc");
			echo "<u>State Level Data:</u> <br/>";
			echo stripslashes(loadStateData($state));
			echo "<br/>";
			echo "<u>Region Level Data:</u> <br/>";
			echo stripslashes(loadRegionData($state));
			log_MapAction("OverlayInfoClicked",$state);
		}
	}

	function loadStateData($state)
	{
		global $con;
		$description = "";
		
		$sql = "
						SELECT
							Type,
							Name,
							Website,
							Facebook,
							Phone,
							Email
						FROM
							Map_OverlayInfo
						WHERE
							State = ?
						;";
		if($stmt = $con->prepare($sql))
		{
			if($stmt->bind_param("s",$state))
			{
				$stmt->execute();
				$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;

				if($stmt->num_rows >= 1){	
					$stmt->bind_result($Type,$Name,$Website,$Facebook,$Phone,$Email);

					while($stmt->fetch()){		
						if(($Type != "")||($Name != ""))
							$description .= "<b>" . $Type . "</b>: " . $Name . " - ";
						if($Website != "")
							$description .= "<a href = '" . $Website . "'>Website</a> ";
						if($Facebook != "")
							$description .= "<a href = '" . $Facebook . "'>Facebook</a> ";
						if($Phone != "")
							$description .= $Phone . " ";
						if($Email != "")
							$description .= "<a href = 'mailto:" . $Email . "'>" . $Email . "</a> ";
						$description .= "<br/>";
					}
				}
			}
		}
		if($description == "")
			$description = "No State Data Found<br/>";
		return $description;
	}

	if(isset($_REQUEST['action']) && isset($_REQUEST['type']) && isset($_REQUEST['InstitutionID']))
	{
		$description = "Description Not Found (this is an error)";
		
		$action = $_REQUEST['action'];
		$id = $_REQUEST['InstitutionID'];
		$type = $_REQUEST['type'];
		$name = $_REQUEST['name'];
		
		if($type == 'MJ')
		{
			$description = $name;
			log_MapAction("PersonDescriptorClicked",$id);
		}
		elseif(($type == 'naigcboard')||($type == 'naigcboardcontact'))
		{
			$description = getNaigcBoardMemberDescription($id);
			log_MapAction("NAIGCPersonDescriptorClicked",$id);
		}
		elseif($type == 'CC')
		{
			$description = addslashes(preg_replace( "/\r|\n/", "", CCDescription($id)));
			log_MapAction("CCDescriptorClicked",$id);
		}
		elseif(($type == '6bigUniv')||($type == '6smallUniv')||($type == '1')||($type == '14'))
		{
			if(isset($_REQUEST['CGA']))
				$description = addslashes(preg_replace( "/\r|\n/", "", showDescription($id) . " <br/>" . CGADescription($id)));
			else
				$description = addslashes(preg_replace( "/\r|\n/", "", showDescription($id)));
			log_MapAction("CollegeInstitutionDescriptorClicked",$id);
		}
		else
		{
			$description = addslashes(preg_replace( "/\r|\n/", "", showDescription($id)));
			log_MapAction("InstitutionDescriptorClicked",$id);
		}
		//$description = addslashes(preg_replace( "/\r|\n/", "", showDescription(1)));
		echo stripslashes($description);
	}
}

function loadRegionData($state)
{
	global $con;
	$description = "";
	
	$sql = "
					SELECT
						Map_OverlayInfo.Type,
						Map_OverlayInfo.Name,
						Map_OverlayInfo.Website,
						Map_OverlayInfo.Facebook,
						Map_OverlayInfo.Phone,
						Map_OverlayInfo.Email
					FROM
						Map_OverlayInfo,
						Constraints_Regions
					WHERE
						Constraints_Regions.State = ? AND
						Constraints_Regions.Region = Map_OverlayInfo.Region
					;";
	if($stmt = $con->prepare($sql))
	{
		if($stmt->bind_param("s",$state))
		{
			$stmt->execute();
			$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;

			if($stmt->num_rows >= 1){	
				$stmt->bind_result($Type,$Name,$Website,$Facebook,$Phone,$Email);

				while($stmt->fetch()){
					if(($Website != "")||($Facebook != "")||($Phone != "")||($Email != ""))
					{
						if(($Type != "")||($Name != ""))
							$description .= "<b>" . $Type . "</b>: " . $Name . " - ";
						if($Website != "")
							$description .= "<a href = '" . $Website . "'>Website</a> ";
						if($Facebook != "")
							$description .= "<a href = '" . $Facebook . "'>Facebook</a> ";
						if($Phone != "")
							$description .= $Phone . " ";
						if($Email != "")
							$description .= "<a href = 'mailto:" . $Email . "'>" . $Email . "</a> ";
						$description .= "<br/>";
					}
				}
			}
		}
	}
	if($description == "")
		$description = "No State Data Found<br/>";
	return $description;
}



function getNaigcBoardMemberDescription($id)
{
	global $con;
	$PersonDescription = "";
	$sqlPerson = "
					SELECT
						Name,
						Title,
						Email1,
						Email2,
						Phone
					FROM
						TEMP_Board
					WHERE
						ID = ?
					;";
	if($stmtPerson = $con->prepare($sqlPerson))
	{
		if($stmtPerson->bind_param("i",$id))
		{
			$stmtPerson->execute();
			$stmtPerson->store_result(); //allow us to get properties, e.g. stmt->num_rows;

			if($stmtPerson->num_rows >= 1){	
				$stmtPerson->bind_result($name, $title, $Email1, $Email2, $Phone);

				while($stmtPerson->fetch()){		
					$PersonDescription .= "<b>" . $name . "</b> " . $title . "<br/>";
														if($Email1 != ""){$PersonDescription .= "<a href = 'mailto:".$Email1."'>".$Email1."</a><br/>";}
														if($Email2 != ""){$PersonDescription .= "<a href = 'mailto:".$Email2."'>".$Email2."</a><br/>";}
														if($Phone != ""){$PersonDescription .= $Phone . "<br/>";}
					
				}
			}
		}
	}
	return $PersonDescription;
}

function log_MapAction($action,$actionValue)
{
	global $con;
	$sql = "
			INSERT INTO
				Metrics_Map(Action,Value,PersonID)
			Values(?,?,".getUserID().");
			";
	$stmt = $con->prepare($sql);
	$stmt->bind_param("ss",$action,$actionValue);
	
	$stmt->execute();
}

function showerror($error)
{
	echo "<script>alert('".$error."');</script>";
}
	
function getInstitutionDescription($id)
{
	global $con;
	
	$sqlInstitutions = "SELECT 
				Identifiers_Institutions.Name, 
				Identifiers_Institutions.Address1,
				Identifiers_Institutions.Address2,
				CONCAT(Identifiers_Institutions.City, ', ',  Identifiers_Institutions.State, ' ', Identifiers_Institutions.Zip, ' ') AS MailingAddress,
				Identifiers_Institutions.Lat, 
				Identifiers_Institutions.Lng,
				Identifiers_Institutions.Enrollment, 
				Identifiers_Programs.ClubType,
				Identifiers_Institutions.Website, 
				Identifiers_Institutions.Facebook, 
				Identifiers_Institutions.Twitter,
				Identifiers_Institutions.Phone,
				Identifiers_Institutions.Email
			FROM 
				Identifiers_Institutions, 
				Identifiers_Programs
			WHERE 
				Identifiers_Institutions.ID = ? AND
				Identifiers_Institutions.ID = Identifiers_Programs.InstitutionID
			GROUP BY 
				Identifiers_Programs.InstitutionID";
	
	if($stmtInstitutions = $con->prepare($sqlInstitutions))
	{
		if($stmtInstitutions->bind_param("i",$id))
		{
			$stmtInstitutions->execute();
			$stmtInstitutions->store_result(); //allow us to get properties, e.g. stmt->num_rows;

			if($stmtInstitutions->num_rows > 1)
			{
				showerror("more than one row was found.");
			}
			elseif($stmtInstitutions->num_rows == 1){
				$stmtInstitutions->bind_result($name, $address1, $address2, $addressend, $lat, $lng, $enrollment, $type, $website, $facebook, $twitter, $phone, $email);
				
				while($stmtInstitutions->fetch()){
					
					//some stuff to format the address in a copy and paste friendly fashion if someone wants to.
					$mailingAddress = "";
					if($address1 != ""){$mailingAddress = $mailingAddress . $address1 . "<br/>";}
					if($address2 != ""){$mailingAddress = $mailingAddress . $address2 . "<br/>";}
					$mailingAddress = $mailingAddress . $addressend;
					
					$InstitutionDescription = "<h3>Institution Name: ".$name."</h3><button onclick = 'drawCircle(".$lat.", ".$lng.", ".$id.");'>Circle</button>
												<ul>";
													if($enrollment != 0){$InstitutionDescription .= "<li>Institution Enrollment: ".$enrollment."</li>";}
													if($mailingAddress != ""){$InstitutionDescription .= "<li>Institution Physical Address: <br/>".$mailingAddress."</li>";}
													
													if(($type != 1)&&($type != 5)&&($type != 7)&&($type != 14))
													{
													if($website != ""){$InstitutionDescription .= "<li>Institution Website: <a href = '".$website."'>".$website."</a></li>";}
													if($facebook != ""){$InstitutionDescription .= "<li>Institution Facebook: <a href = '".$facebook."'>".$facebook."</a></li>";}
													}
													
													/*$InstitutionDescription .= 		"<li>Institution Facebook: ".$facebook."</li>
																					<li>Institution Twitter: ".$twitter."</li-->";*/
													if($email != ""){$InstitutionDescription .= "<li>Institution Email: <a href = 'mailto:".$email."'>".$email."</a></li>";}
													if($phone != ""){$InstitutionDescription .= "<li>Institution Phone: ".$phone."</li>";}
					$InstitutionDescription .= 	"</ul>";
				}
			}
			else
			{
				showerror("there wasnt an institution record found with that id found in getInstitutionDescription()");
			}
		}
		else
		{
			showerror("getInstitutionDescription() bindparam failed");
		}
	}
	else
	{
		showerror("getInstitutionDescription() prepare failed");
	}
	log_MapAction("InstitutionDescriptorClicked",$id);
	return $InstitutionDescription;
}

function getProgramDescription($InstitutionID)
{
	global $con;
	
	$sqlPrograms = "SELECT 
				Constraints_ClubTypes.TypeOfClub as Type,
				Identifiers_Programs.Discipline, 
				Identifiers_Programs.Website, 
				Identifiers_Programs.Facebook, 
				Identifiers_Programs.Twitter,
				Identifiers_Programs.Instagram,
				Identifiers_Programs.Youtube,
				Identifiers_Programs.Tumblr,
				Identifiers_Programs.Flickr,
				Identifiers_Programs.Snapchat,
				Identifiers_Programs.Phone,
				Identifiers_Programs.Email
			FROM 
				Identifiers_Programs,
				Constraints_ClubTypes
			WHERE 
				Identifiers_Programs.InstitutionID = ? AND
				Identifiers_Programs.ClubType = Constraints_ClubTypes.ID";
		
	$ProgramDescription	= "";
	
	if($stmtPrograms = $con->prepare($sqlPrograms))
	{
		if($stmtPrograms->bind_param("i",$InstitutionID))
		{
			$stmtPrograms->execute();
			$stmtPrograms->store_result(); //allow us to get properties, e.g. stmt->num_rows;

			if($stmtPrograms->num_rows >= 1){	
				$stmtPrograms->bind_result($type, $Discipline, $website, $facebook, $twitter, $instagram, $youtube, $tumblr, $flickr, $snapchat, $phone, $email);

				while($stmtPrograms->fetch()){
					
					//some conversion because a db table doesnt exist yet -- to be removed later
					if($Discipline == 1)
						$Discipline = "Women's";
					elseif($Discipline == 2)
						$Discipline = "Men's";
					
					if($type != "Potential College"){
						$ProgramDescription .= "<b>Program Type: " . $Discipline . " " . $type . "</b>
													<ul>";
														if($website != ""){$ProgramDescription .= "<li>Program Website: <a href = '".$website."' >".$website."</a></li>";}
														if($facebook != ""){$ProgramDescription .= "<li>Program Facebook: <a href = '".$facebook."' >".$facebook."</a></li>";}
														if($twitter != ""){$ProgramDescription .= "<li>Program Twitter: <a href = '".$twitter."' >".$twitter."</a></li>";}
														if($instagram != ""){$ProgramDescription .= "<li>Program Instagram: <a href = '".$instagram."' >".$instagram."</a></li>";}
														if($youtube != ""){$ProgramDescription .= "<li>Program YouTube: <a href = '".$youtube."' >".$youtube."</a></li>";}
														if($tumblr != ""){$ProgramDescription .= "<li>Program Tumblr: <a href = '".$tumblr."' >".$tumblr."</a></li>";}
														if($flickr != ""){$ProgramDescription .= "<li>Program Flickr: <a href = '".$flickr."' >".$flickr."</a></li>";}
														if($snapchat != ""){$ProgramDescription .= "<li>Program Snapchat: <a href = '".$snapchat."' >".$snapchat."</a></li>";}
														if($phone != ""){$ProgramDescription .= "<li>Program Phone: ".$phone."</a></li>";}
														if($email != ""){$ProgramDescription .= "<li>Program Email: <a href = 'mailto:".$email."' >".$email."</a></li>";}
						$ProgramDescription .=		"</ul>";
					}
				}
			}
			else
			{
				showerror("there werent any programs found for that institution");
			}
		}
		else
		{
			showerror("getInstitutionDescription() bindparam failed");
		}
	}
	else
	{
		showerror("getprogramdescription() prepare failed");
	}
	return $ProgramDescription;
}

function showDescription($id)
{
	return getInstitutionDescription($id).getProgramDescription($id);
	//return "0";
}
function CCDescription($id)
{
	global $con;

	$ProgramDescription = "";
	$sqlCC = "
					SELECT
						Name,
						URL,
						ExSpace,
						Lat,
						Lng
					FROM
						TEMP_CC
					WHERE
						ID = ?
					;";
	if($stmtCC = $con->prepare($sqlCC))
	{
		if($stmtCC->bind_param("i",$id))
		{
			$stmtCC->execute();
			$stmtCC->store_result(); //allow us to get properties, e.g. stmt->num_rows;

			if($stmtCC->num_rows >= 1){	
				$stmtCC->bind_result($name, $website, $exspace, $lat, $lng);

				while($stmtCC->fetch()){		
					$ProgramDescription .= "<b>Convention Center: " . $name . "</b><br/><button onclick = 'drawCircle(".$lat.", ".$lng.", ".$id.");'>Circle</button>
													<ul>";
														if($website != ""){$ProgramDescription .= "<li>Cvent Link: <a href = '" . $website . "' target = '_blank'>" . $website . "</a></li>";}
														if($exspace != ""){$ProgramDescription .= "<li>Exhibit Hall Space (sqft): " . $exspace . "</li>";}
														$ProgramDescription .=	"<li>Ceiling Height: TBA (not in database yet)</li>
																				<li>Largest Meeting Room (banquet): TBA (not in database yet)</li>
																				<li>Last Quote: TBA (not in database yet)</li>
																				";
						$ProgramDescription .=		"</ul>";
					
				}
			}
			else
			{
				$ProgramDescription .= "num rows failed";
			}
		}
		else
		{
			$ProgramDescription .= "bind failed";
		}
	}
	else
	{
		$ProgramDescription .= "prepare failed";
	}
	log_MapAction("CCDescriptorClicked",$id);
	return $ProgramDescription;
}

function getIpedsID($InstitutionID)
{
	global $con;
	$IpedsID = 0;
	
	$sql = "
					SELECT
						IpedsID
					FROM
						Identifiers_Institutions
					WHERE
						ID = ?
					;";
	if($stmt = $con->prepare($sql))
	{
		if($stmt->bind_param("i",$InstitutionID))
		{
			$stmt->execute();
			$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;

			if($stmt->num_rows >= 1){	
				$stmt->bind_result($theIpedsID);

				while($stmt->fetch()){		
					$IpedsID = $theIpedsID;
				}
			}
		}
	}
	return $IpedsID;
}

function CGADescription($InstitutionID)
{
	global $con;
	$IpedsID = getIpedsID($InstitutionID);
	$CGADescription = "No NCES Data was found in the local database for this institution. It's IpedsID is: " . $IpedsID . "<br/>";
	$Year = 2017;
	
	$sql = "
		SELECT
			classification_name,
			EFMaleCount,
			EFFemaleCount,
			EFTotalCount,
			STUDENTAID_MEN,
			STUDENTAID_WOMEN,
			STUAID_MEN_RATIO,
			STUAID_WOMEN_RATIO
		FROM
			NCES_Summary
		WHERE
			IpedsID = ? AND
			Year = ".$Year."
		;";
		
	if($stmt = $con->prepare($sql))
	{
		//$CGADescription .= "prepare";
		if($stmt->bind_param("i",$IpedsID))
		{
			//$CGADescription .= "bind";
			$stmt->execute();
			$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;

			if($stmt->num_rows >= 1){	
				$CGADescription .= "rows";
				$stmt->bind_result($Classification,$MaleAthletes,$FemaleAthletes,$TotalAthletes,$MaleAid,$FemaleAid,$MaleAidRatio,$FemaleAidRatio);
				
				$CGADescription = "<b>NCES Data for Athletic Spending:</b><br/>";
				
				while($stmt->fetch()){					
					$CGADescription .= "For the year " . $Year . ":<br/>";
					$CGADescription .= "Athletes: " . $TotalAthletes . "<br/>";
					$CGADescription .= "Male Athletes: " . $MaleAthletes . " (" . number_format($MaleAthletes/$TotalAthletes*100,2) . "%)<br/>";
					$CGADescription .= "Female Athletes: " . $FemaleAthletes . " (" . number_format($FemaleAthletes/$TotalAthletes*100,2) . "%)<br/>";
					$CGADescription .= "Male Aid: " . $MaleAid . " (" . $MaleAidRatio . "%)<br/>";
					$CGADescription .= "Female Aid: " . $FemaleAid . " (" . $FemaleAidRatio . "%)<br/>";
				}
			}
		}
		$CGADescription .= "<a target = '_new' href = 'https://nces.ed.gov/collegenavigator/?id=" . $IpedsID . "'>NCES College Navigator</a><br/>";
		$CGADescription .= "<a target = '_new' href = 'https://nces.ed.gov/globallocator/col_info_popup.asp?ID=" . $IpedsID . "'>IPEDS Overview</a><br/>";
	}
	
	return $CGADescription;
}

?>