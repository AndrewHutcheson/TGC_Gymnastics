<? session_start(); ?>
<?php
/*	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/
?>
<!DOCTYPE html>
<html>
<!-- Copyright 2016-2019 Andrew Hutcheson. All rights reserved. 
This software may not be distributed or modified without expressed written consent.-->
<head>
<meta charset="UTF-8">
<style>
	html{
		height: 100%;
	}
	body{
		height: 100%; 
		margin: 0; 
		padding: 0;
	}
	
	#legend{
		width:19%; 
		float:right;
		margin: 0; 
		padding: 0;
		overflow-x:scroll;
	}
	#map_wrapper {
		height: 100%;
		width: 80%;
		float:left;
	}

	#map_canvas {
		width: 100%;
		height: 100%;
	}

    .onoffswitch {
        position: relative; width: 75px;
        -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
    }
    .onoffswitch-checkbox {
        display: none;
    }
    .onoffswitch-label {
        display: block; overflow: hidden; cursor: pointer;
        border: 2px solid #999999; border-radius: 16px;
    }
    .onoffswitch-inner {
        display: block; width: 200%; margin-left: -100%;
        transition: margin 0.3s ease-in 0s;
    }
    .onoffswitch-inner:before, .onoffswitch-inner:after {
        display: block; float: left; width: 50%; height: 14px; padding: 0; line-height: 14px;
        font-size: 12px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
        box-sizing: border-box;
    }
    .onoffswitch-inner:before {
        content: "ON";
        padding-left: 10px;
        background-color: #34C240; color: #FFFFFF;
    }
    .onoffswitch-inner:after {
        content: "OFF";
        padding-right: 10px;
        background-color: #D61717; color: #EBE6E6;
        text-align: right;
    }
    .onoffswitch-switch {
        display: block; width: 18px; margin: -0.5px;
        background: #FFFFFF;
        position: absolute; top: 0; bottom: 0;
        right: 54px;
        border: 2px solid #999999; border-radius: 16px;
        transition: all 0.3s ease-in 0s; 
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
        margin-left: 0;
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
        right: 0px; 
    }
	
	.multiselect {
		width:20em;
		border:solid 1px #c0c0c0;
		overflow:auto;
	}
	
	#stateSelector {
		height:42em;
	}
	
	.multiselect2 {
		width:20em;
		height:20em;
		border:solid 1px #c0c0c0;
		overflow:auto;
	}
	 
	.multiselect label {
		display:block;
	}
	 
	.multiselect-on {
		color:#ffffff;
		background-color:#000099;
	}
	.multiselect-off {
		color:#000000;
		background-color:#ffffff;
	}
	
	#gymControls img
	{
		vertical-align:middle;
	}
</style>

<? 
	require_once("auth.php");
	require_once('globals.php');
	require_once('mapsApiKey.php');
	
if(userIsLoggedIn())
{
	if(isset($_REQUEST['ucutoff']))
		$smallU_Cutoff = $_REQUEST['ucutoff'];
	else
		$smallU_Cutoff = 15000;
	if(isset($_REQUEST['cccutoff']))
		$smallCC_Cutoff = $_REQUEST['cccutoff'];
	else
		$smallCC_Cutoff = 25000;
	
	$smallU_Cutoff_Abbr = (string)($smallU_Cutoff/1000) . "K";
	$smallCC_Cutoff_Abbr = (string)($smallCC_Cutoff/1000) . "K";
	
	//A switch for singling out states.
	$state = "LIKE '%%'"; //all
	if(isset($_REQUEST['state']))
		$state = "IN ('".str_replace(",","','",$_REQUEST['state'])."')";
	
	$year = DATE("Y");
	if(DATE("m") >= 7)
		$year = DATE("Y")+1;
	if(isset($_REQUEST['year']))
		$year = $_REQUEST['year'];
	
	//$showCgaControls = false;
	$showCgaControls = true;
	if(isset($_REQUEST['adrian']))
		$showCgaControls = true;
	
	$showThsgcaControls = false;
	//$showThsgcaControls = true;
	if(isset($_REQUEST['thsgca']))
		$showThsgcaControls = true;
	
	//$showNaigcControls = false;
	$showNaigcControls = true;
	if(isset($_REQUEST['naigc']))
		$showNaigcControls = true;
	
	//So I want to minimize the number of dots and datapoints. Well, the question of "does this institution have both mens and womens programs?" is most important for high schools (type3) and NCAA (type5)
		//anything else we are simply creating a duplicate data point and people aren't really ever going to want to know how many USAG gyms have mens equipment as often (besides, we dont have the data for them)
	//there are a few single-Discipline college clubs but these can be counted on one hand, and are usually NCAA school conflicts. OU is the only one I know and they recently are warming up to a women's club....
	//There are even fewer single-Discipline adult clubs. While you could count these on one hand... Some of them are tied to a parent institution (their usag club they work out at). So I stuck them in here so 
		//that they wouldn't ge overwritten by the group by clause with that USAG club. Although I pulled out he 
	
	//So the GROUP BY eliminates unneccesary map markers for those types. The description function of an institution contains all the program information on an institution, not program wide basis.
	//This then basically halves the number of data points.
	
	//The second group by - on the program ID - prevents an institution with multiple types from overwriting each other (e.g. a usag, usaigc, adult &... all at the same gym.)
	
	//The last two add layers and info for the Dual Program (dual Discipline) ncaa and high schools.
	
	//gets high school, ncaa and adult, Discipline together
	//union
	//everything but high school, ncaa and adult, combine all Discipline.
	//union
	//combine HS
	//union
	//combine NCAA
	
	//TODO: check count = 2, tis possible I don't want this is a particular school has multiple programs for that clubtype-Discipline???
	$sqlClubs = "
				SELECT 
					Identifiers_Institutions.ID,
					Identifiers_Institutions.Name,
					Identifiers_Institutions.Lat,
					Identifiers_Institutions.Lng,
					Identifiers_Programs.ClubType, 
					Constraints_ClubTypes.TypeOfClub, 
					Identifiers_Institutions.Enrollment, 
					Identifiers_Institutions.Exclusive, 
					Identifiers_Programs.Discipline,
					Identifiers_Institutions.Division,
					NCES_Summary.ClassificationCode AS Division2,
					Identifiers_Institutions.State,
					Identifiers_Institutions.onCampusGym
				FROM 
					Identifiers_Programs, 
					Constraints_ClubTypes,
					Identifiers_Institutions
				LEFT JOIN
					NCES_Summary
				ON
					Identifiers_Institutions.IpedsID = NCES_Summary.IpedsID AND
					NCES_Summary.Year = 2017	
				WHERE 
					Identifiers_Institutions.ID = Identifiers_Programs.InstitutionID AND
					Constraints_ClubTypes.ID = Identifiers_Programs.ClubType AND
					Identifiers_Programs.ClubType IN (3,5,7) AND 
					Identifiers_Institutions.State " . $state . " AND
					(Identifiers_Institutions.Inactive >= CURDATE() OR Identifiers_Institutions.Inactive = '0000-00-00') AND
					(Identifiers_Programs.Inactive >= CURDATE() OR Identifiers_Programs.Inactive = '0000-00-00')
		UNION
				SELECT 
					Identifiers_Institutions.ID, 
					Identifiers_Institutions.Name, 
					Identifiers_Institutions.Lat, 
					Identifiers_Institutions.Lng, 
					Identifiers_Programs.ClubType, 
					Constraints_ClubTypes.TypeOfClub, 
					Identifiers_Institutions.Enrollment, 
					Identifiers_Institutions.Exclusive, 
					Identifiers_Programs.Discipline,
					Identifiers_Institutions.Division,
					NCES_Summary.ClassificationCode AS Division2,
					Identifiers_Institutions.State,
					Identifiers_Institutions.onCampusGym
				FROM 
					Identifiers_Programs, 
					Constraints_ClubTypes,
					Identifiers_Institutions
				LEFT JOIN
					NCES_Summary
				ON
					Identifiers_Institutions.IpedsID = NCES_Summary.IpedsID AND
					NCES_Summary.Year = 2017
				WHERE 
					Identifiers_Institutions.ID = Identifiers_Programs.InstitutionID AND
					Constraints_ClubTypes.ID = Identifiers_Programs.ClubType AND
					Identifiers_Programs.ClubType NOT IN (3,5,7) AND 
					Identifiers_Institutions.State ".$state." AND
					(Identifiers_Institutions.Inactive >= CURDATE() OR Identifiers_Institutions.Inactive = '0000-00-00') AND
					(Identifiers_Programs.Inactive >= CURDATE() OR Identifiers_Programs.Inactive = '0000-00-00')
				GROUP BY
					Identifiers_Programs.InstitutionID,
					Identifiers_Programs.ID
		UNION
				SELECT
					Identifiers_Institutions.ID, 
					Identifiers_Institutions.Name, 
					Identifiers_Institutions.Lat, 
					Identifiers_Institutions.Lng, 
					'3b' AS ClubType, 
					'Both Mens and Womens High School' AS TypeOfClub, 
					Identifiers_Institutions.Enrollment, 
					Identifiers_Institutions.Exclusive, 
					Identifiers_Programs.Discipline,
					Identifiers_Institutions.Division,
					0 as Division2,
					Identifiers_Institutions.State,
					0 as onCampusGym
				FROM 
					Identifiers_Programs, 
					Identifiers_Institutions,
					Constraints_ClubTypes
				WHERE 
					Identifiers_Institutions.ID = Identifiers_Programs.InstitutionID AND
					Constraints_ClubTypes.ID = Identifiers_Programs.ClubType AND
					Identifiers_Programs.ClubType IN (3) AND 
					Identifiers_Institutions.State ".$state." AND
					(Identifiers_Institutions.Inactive >= CURDATE() OR Identifiers_Institutions.Inactive = '0000-00-00') AND
					(Identifiers_Programs.Inactive >= CURDATE() OR Identifiers_Programs.Inactive = '0000-00-00')
				GROUP BY
					Identifiers_Programs.InstitutionID
				HAVING
					COUNT(*) = 2
		UNION
				SELECT
					Identifiers_Institutions.ID, 
					Identifiers_Institutions.Name, 
					Identifiers_Institutions.Lat, 
					Identifiers_Institutions.Lng, 
					'5b' AS ClubType, 
					'Both Mens and Womens NCAA' AS TypeOfClub, 
					Identifiers_Institutions.Enrollment, 
					Identifiers_Institutions.Exclusive, 
					Identifiers_Programs.Discipline,
					Identifiers_Institutions.Division,
					0 as Division2,
					Identifiers_Institutions.State,
					Identifiers_Institutions.onCampusGym
				FROM 
					Identifiers_Programs, 
					Identifiers_Institutions,
					Constraints_ClubTypes
				WHERE 
					Identifiers_Institutions.ID = Identifiers_Programs.InstitutionID AND
					Constraints_ClubTypes.ID = Identifiers_Programs.ClubType AND
					Identifiers_Programs.ClubType IN (5) AND 
					Identifiers_Institutions.State ".$state." AND
					(Identifiers_Institutions.Inactive >= CURDATE() OR Identifiers_Institutions.Inactive = '0000-00-00') AND
					(Identifiers_Programs.Inactive >= CURDATE() OR Identifiers_Programs.Inactive = '0000-00-00')
				GROUP BY
					Identifiers_Programs.InstitutionID
				HAVING
					COUNT(*) = 2
				";
		//just load if set for hacky improvement to speed/optimization
		if(isset($_REQUEST['andrew']))
		{
			$sqlClubs .= "
			UNION
					SELECT
						CONCAT('TJ',TEMP_TXJUDGE.ID) AS ID, 
						TEMP_TXJUDGE.Name, 
						TEMP_TXJUDGE.aLat AS Lat, 
						TEMP_TXJUDGE.aLng AS Lng, 
						'MJ' AS ClubType, 
						'TX Mens Judge' AS TypeOfClub, 
						TEMP_TXJUDGE.Quantity AS Enrollment,
						0 AS Exclusive, 
						0 AS Discipline,
						0 AS Division,
						0 as Division2,
						'TX' AS State,
						0 as onCampusGym
					FROM
						TEMP_TXJUDGE					
					";			
		}
		if($showNaigcControls)
		{
			$sqlClubs .= "
			UNION
					SELECT
						TEMP_Board.ID AS ID,
						TEMP_Board.Name, 
						TEMP_Board.Lat, 
						TEMP_Board.Lng, 
						'naigcboard' AS ClubType, 
						'NAIGC BoD' AS TypeOfClub, 
						1 AS Enrollment, 
						0 AS Exclusive, 
						0 AS Discipline,
						0 AS Division,
						0 as Division2,
						State AS State,
						0 as onCampusGym
					FROM
						TEMP_Board
					WHERE
						(Title = 'Director' OR Title = 'Advisor' OR Title = 'Board Member') AND
						Season = ".$year."
			UNION
					SELECT
						TEMP_Board.ID AS ID,
						TEMP_Board.Name, 
						TEMP_Board.Lat, 
						TEMP_Board.Lng, 
						'naigcboardcontact' AS ClubType, 
						'NAIGC BoD' AS TypeOfClub, 
						1 AS Enrollment, 
						0 AS Exclusive, 
						0 AS Discipline,
						0 AS Division,
						0 as Division2,
						State AS State,
						0 as onCampusGym
					FROM
						TEMP_Board
					WHERE
						Title = 'Board Contact' AND
						Season = ".$year."
			UNION
					SELECT
						ID,
						TEMP_CC.Name, 
						TEMP_CC.Lat, 
						TEMP_CC.Lng, 
						'CC' AS ClubType, 
						'CC' AS TypeOfClub, 
						1 AS Enrollment, 
						0 AS Exclusive, 
						0 AS Discipline,
						0 AS Division,
						0 as Division2,
						State,
						0 as onCampusGym
					FROM
						TEMP_CC	
					WHERE
						State ".$state."
					";
		}
		
		$sqlClubs .= ";";
		
	$map[] = array();
	$index = 0;	
	
	
	if($stmtClubs = $con->prepare($sqlClubs))
	{
		$stmtClubs->execute();
		$stmtClubs->store_result(); //allow us to get properties, e.g. stmt->num_rows;

		if($stmtClubs->num_rows >= 1){
			$stmtClubs->bind_result($id, $name, $lat, $lng, $type, $namedType, $enrollment, $exclusive, $Discipline, $division, $classification, $State, $OnCampusGym);
			
			while($stmtClubs->fetch()){
				$newClassification = $classification;
				if($enrollment != 0)
				{
					//hacky way of fxing colleges that don't have an entry in the NCES data
					if(empty($classification) || is_null($classification) || ($classification == '') || ($classification == "") || ($classification == null))
						$newClassification = "unknown";
				}
				if(($enrollment != 0) && ($type == 6))
				{
					if(($enrollment >= $smallU_Cutoff)&&($division != 4))
					{
						$type = '6bigUniv';
						$namedType = $namedType." (>".$smallU_Cutoff_Abbr.")";
					}
					else if(($enrollment < $smallU_Cutoff)&&($division != 4))
					{
						$type = '6smallUniv';
						$namedType = $namedType." (<".$smallU_Cutoff_Abbr.")";
					}
					else if(($enrollment >= $smallCC_Cutoff)&&($division == 4))
					{
						$type = '6bigCC';
						$namedType = $namedType." (>".$smallCC_Cutoff_Abbr.")";
					}
					else if(($enrollment < $smallCC_Cutoff)&&($division == 4))
					{
						$type = '6smallCC';
						$namedType = $namedType." (<".$smallCC_Cutoff_Abbr.")";
					}
				}
			
				//layer visibility defaults can be set here: 0 shown, 1 hidden
				$hide = "1";
				/*if($type == 7)
				{
					$hide = "1";
				}*/
				
				$map[$index] = array(
					"id"=>$id,
					"name"=>$name,
					"lat"=>$lat,
					"lng"=>$lng,
					"type"=>$type,
					"namedType"=>$namedType,
					"Discipline"=>$Discipline,
					"hide"=>$hide,
					"enrollment"=>$enrollment,
					"exclusive"=>$exclusive,
					"division"=>$division,
					"classification"=>$newClassification,
					"state"=>$State,
					"onCampusGym"=>$OnCampusGym
				);
				$index++;
			}
		}
	}
	else
	{
		printf("Errormessage: %s\n", $con->error);
	}
	
	$programCounts = array();
	
	function prettyPrint($arr)
	{
		$retStr = '';
		if (is_array($arr)){
			foreach ($arr as $key=>$val){
				$startPos = 0;
				$startPos = strpos($key,"-")+1;
				if (is_array($val)){
					$retStr .= substr($key,$startPos) . ': ' . pp($val) . '<br/>';
				}else{
					$retStr .= substr($key,$startPos) . ': ' . $val . '<br/>';
				}
			}
		}
		$retStr .= '';
		return $retStr;
	}
	
	$sqlStates = "
				SELECT 
					Name,
					Abbr
				FROM 
					Constraints_States
				";
	
	if($stmtStates = $con->prepare($sqlStates))
	{
		$stmtStates->execute();
		$stmtStates->store_result(); //allow us to get properties, e.g. stmt->num_rows;

		if($stmtStates->num_rows >= 1)
		{
			$stmtStates->bind_result($name, $abbr);
		}
	}
	
	$sql_CGAData = "
					Select 
						ID
					";
	
?>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  
  <script>
		var states = new Object();
		jQuery(function($) {
			// Asynchronously Load the map API 
			var script = document.createElement('script');
			script.src = "https://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize&key=<?php echo GOOGLE_MAPS_API_KEY ?>";
			document.body.appendChild(script);
			
			/*var script2 = document.createElement('script');
			script2.src = "stateBoundaries.js";
			document.body.appendChild(script2);*/
		});
		
		/*function getInstitutionDescription(marker)
		{
			var returnData = "ajax not loaded";
			var infowindow = new google.maps.InfoWindow();
			$.ajax({
				url: 'mapHelperFunctions.php',
				type: "POST",
				data: {action: "getDescription", InstitutionID: marker.id, type: marker.type},
				success: function(response) {
					returnData = response;
					infowindow.close();
					infowindow.setContent(returnData);
					infowindow.open(map,marker);
					//markers.push(marker);
				}
			});
		}*/
		
   </script>
   <script src = "stateBoundaries.js"></script> <!--replace with google polygons-->
   <script src = "mapHelperFunctions.js"></script>
   <script>
		function loadMapShapes() 
		{
			// load US state outline polygons from a GeoJSON file
			map.data.loadGeoJson('https://storage.googleapis.com/mapsdevsite/json/states.js', { idPropertyName: 'STATE' });
		}

		//set global scope
		var marker = [];
		var markers;
		
		markers = [
			  <?
				for($i = 0; $i < sizeof($map); $i++)
				{
					$name = $map[$i]['name'];
					$id = $map[$i]['id'];
					$lat = $map[$i]['lat'];
					$lng = $map[$i]['lng'];
					$type = $map[$i]['type'];
					$namedType = $map[$i]['namedType'];
					$Discipline = $map[$i]['Discipline'];
					$hide = $map[$i]['hide'];
					$enrollment = $map[$i]['enrollment'];
					$exclusive = $map[$i]['exclusive'];
					$division = $map[$i]['division'];
					$classification = $map[$i]['classification'];
					$state = $map[$i]['state'];
					$onCampusGym = $map[$i]['onCampusGym'];
					//Speed Note #1 : other notes have this same label elsewhere via they are the same thing that needs to change.
						//in this case the showDescription function is passing a lot of data back for each of these things. It should instead be an AJAX call.
					/*if(($type == 'MJ')||($type == 'naigcboard')||($type == 'naigcboardcontact'))
						$description = $name;
					elseif($type == 'CC')
						$description = addslashes(preg_replace( "/\r|\n/", "", CCDescription($id)));
					else
						$description = addslashes(preg_replace( "/\r|\n/", "", showDescription($id)));*/
					$description = "";
					
					//temporary until I pull from db
					if($Discipline == '2')
					{
						$Discipline = 'm';
						$DisciplineName = "Men's";
					}
					else
					{
						$Discipline = 'f';
						$DisciplineName = "Women's";
					}
					
					//if it's a type where I want separate Discipline controls then add that to the layer name.
					if(($type == '3')||($type == '5'))
					{
						echo "['" . addslashes($name) . "','" . $lat . "', '" . $lng . "', '" . $type.$Discipline . "', '" . $hide . "', '" . $enrollment . "', '" . $description . "', '" . $state . "', '" . $division . "', '" . $exclusive. "', '" . $onCampusGym . "', '" . $id . "', '" . $classification . "']";
						$programCounts[$type. "-" .$DisciplineName. " " .$namedType]++;
					}
					elseif(($type == 'MJ')||($type == 'naigcboard')||($type == 'naigcboardcontact'))
					{
						echo "['" . addslashes($name) . "','" . $lat . "', '" . $lng . "', '" . $type . "', '" . $hide . "', '" . $enrollment . "', '" . addslashes($description) . "', '" . $state . "', '" . $division . "', '" . $exclusive. "', '" . $onCampusGym . "', '" . $id . "', '" . $classification  . "']";
						$programCounts[$type. "-" .$namedType] += $enrollment;
					}
					elseif($type == 'CC')
					{
						echo "['" . addslashes($name) . "','" . $lat . "', '" . $lng . "', '" . $type . "', '" . $hide . "', '" . $enrollment . "', '" . $description . "', '" . $state . "', '" . $division . "', '" . $exclusive. "', '" . $onCampusGym . "', '" . $id . "', '" . $classification  . "']";
						$programCounts[$type. "-" .$namedType] += $enrollment;
					}						
					else
					{
						echo "['" . addslashes($name) . "','" . $lat . "', '" . $lng . "', '" . $type . "', '" . $hide . "', '" . $enrollment . "', '" . $description . "', '" . $state . "', '" . $division . "', '" . $exclusive. "', '" . $onCampusGym . "', '" . $id . "', '" . $classification  . "']";
						$programCounts[$type. "-" .$DisciplineName. " " .$namedType]++;
					}
					
					if($i != sizeof($map)-1)
					{
						echo ",\n";
					}
					else
						echo "\n";
				}
			  ?>
			];
		
		var firstPageLoad = true;		
		var activeRegion = "None";
		var globalMarkersArray = [];
		var globalPolygonsArray = [];
		var layers = {"1":false,"2":false,"3f":false,"3m":false,"3b":false,"4":false,"5f":false,"5m":false,"5b":false,"6bigUniv":false,"6smallUniv":false,"6bigCC":false,"6smallCC":false,"7":false,"8":false,"10":false,"MJ":false,"12":false,"13":false,"14":false,"15":false,"16":false,"17":false};
		var filters = {"exclusive":2, "onCampus":"clear"};
		var map;
		var myCircle;
		var circleOn = false;
		var theCircleRadius = 500;
		
		function initialize() {
			var bounds = new google.maps.LatLngBounds();
			var mapOptions = {
				center: new google.maps.LatLng(40.0220, -94.0667),
				zoom:5,
				mapTypeId: 'roadmap'
			};
			
			myCircle = new google.maps.Circle({
				strokeColor: '#FF0000',
				strokeOpacity: 0.8,
				strokeWeight: 2,
				fillColor: '#FF0000',
				fillOpacity: 0.35,
				map: null,
				center: {lat: 0, lng: 0},
				radius: 0
			  });
							
			// Display a map on the page
			map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
			//map.setTilt(45);
		
			//something invisible and blank to make the map center when nothing is on it.
			//this is so that the fitBounds function centers in the U.S. and not the middle of the pacific.
			/*	var centerposition = new google.maps.LatLng(40.0220, -94.0667);
				var centerimage = { url:"MapIcons/Transparent.png" }
				marker = new google.maps.Marker({
						position: centerposition,
						map: map,
						title: "You can't see me!",
						icon: centerimage
					});
				bounds.extend(centerposition);*/
			
			// Loop through our array of markers & place each one on the map  
			for( i = 0; i < markers.length; i++ ) 
			{
				//if(markers[i][4]=="0")
				{
					var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
					bounds.extend(position);
					
					var iconColor = 'MapIcons/redX.png'; 
					
					if(markers[i][3] == "0")
						iconColor = 'MapIcons/Olympic.png';
					else if(markers[i][3] == "1")
						iconColor = 'MapIcons/blue-dot.png';
					else if(markers[i][3] == "2")
						iconColor = 'MapIcons/green.png';
					else if(markers[i][3] == "3f")
						iconColor = 'MapIcons/pink.png';
					else if(markers[i][3] == "3m")
						iconColor = 'MapIcons/blue.png';
					else if(markers[i][3] == "3b")
						iconColor = 'MapIcons/hsboth.png';
					else if(markers[i][3] == "4")
						iconColor = 'MapIcons/yellow.png';
					else if(markers[i][3] == "5f")
						iconColor = 'MapIcons/red.png';
					else if(markers[i][3] == "5m")
						iconColor = 'MapIcons/darkblue.png';
					else if(markers[i][3] == "5b")
						iconColor = 'MapIcons/ncaaboth.png';
					else if(markers[i][3] == "6bigUniv")
						iconColor = 'MapIcons/ltblue-dot.png';
					else if(markers[i][3] == "6smallUniv")
						iconColor = 'MapIcons/mm_20_ltblue.png';
					else if(markers[i][3] == "6bigCC")
						iconColor = 'MapIcons/purple-dot.png';
					else if(markers[i][3] == "6smallCC")
						iconColor = 'MapIcons/mm_20_purple.png';
					else if(markers[i][3] == "7")
						iconColor = 'MapIcons/marker_white.png';						
					else if(markers[i][3] == "8")
						iconColor = 'MapIcons/black.png';	
					else if(markers[i][3] == "10")
						iconColor = 'MapIcons/darkgreen.png';
					else if(markers[i][3] == "12")
						iconColor = 'MapIcons/GreenTriangle.png';
					else if(markers[i][3] == "13")
						iconColor = 'MapIcons/GreenTriangle.png';
					else if(markers[i][3] == "14")
						iconColor = 'MapIcons/blue-whitedot.png';
					else if(markers[i][3] == "15")
						iconColor = 'MapIcons/red.png';
					else if(markers[i][3] == "16")
						iconColor = 'MapIcons/newHS.png';
					else if(markers[i][3] == "17")
						iconColor = 'MapIcons/newMS.png';
					else if(markers[i][3] == "MJ")
						iconColor = 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='+markers[i][5]+'|FF0000|000000';
					else if(markers[i][3] == "naigcboard")
						iconColor = 'MapIcons/red-dot.png';
					else if(markers[i][3] == "naigcboardcontact")
						iconColor = 'MapIcons/ltred-dot.png';
					else if(markers[i][3] == "CC")
						iconColor = 'MapIcons/cc.png';
					else if(markers[i][3] == "18")
						iconColor = 'MapIcons/green.png';
					
					var image = {
						 url: iconColor
						// This marker is 20 pixels wide by 32 pixels high.
						//size: new google.maps.Size(20, 32),
						// The origin for this image is (0, 0).
						//origin: new google.maps.Point(0, 0),
						// The anchor for this image is the base of the flagpole at (0, 32).
						//anchor: new google.maps.Point(0, 32)
					}
						
					marker = new google.maps.Marker({
						position: position,
						map: map,
						id: markers[i][11],
						title: markers[i][0],
						icon: image,
						type: markers[i][3],
						state: markers[i][7],
						exclusive: markers[i][9],
						division: markers[i][8],
						classification: markers[i][12],
						enrollment: markers[i][5],
						onCampusGym: markers[i][10]
					});
					
					if(firstPageLoad)
					{	marker.setVisible(false);	}
					globalMarkersArray.push(marker);
					
					//var content = markers[i][6]+"<br/>";
					
					//Speed Note #1 : other notes have this same label elsewhere via they are the same thing that needs to change.
						//and this javascript right here I think can be replaced with a AJAX call because the description box is so very much a lot of html to pass at once. 	
						var infowindow = new google.maps.InfoWindow();

						/*google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){ 
							return function() {
								//infowindow.setContent(content);
								infowindow.setContent(getInstitutionDescription(marker.id,marker.type));
								infowindow.open(map,marker);
							};
						})(marker,content,infowindow)); */
						
						google.maps.event.addListener(marker,'click', (function(marker,infowindow){ 
							return function() {
								$.ajax({
									url: 'mapHelperFunctions.php',
									type: "POST",
									data: {
											action: "getDescription", 
											InstitutionID: marker.id,
											name: marker.title,
											type: marker.type 
											<?php if($showCgaControls) {echo ", CGA: true";} ?>
										  },
									success: function(response) {
										infowindow.setContent(response);
										infowindow.open(map,marker);
										//markers.push(marker);
									}
								});
							};
						})(marker,infowindow));
				}	
			}
			firstPageLoad = false;
			
			//addRegionOverlay(activeRegion,map);
			makeRegionLabels(map);
			
			// Automatically center the map fitting all markers on the screen
				//map.fitBounds(bounds);
			// Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
			var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
				this.setZoom(5);
				google.maps.event.removeListener(boundsListener);
			});
			
			//stupid hack needed to fix the position so that the control bar can scroll independently.
			google.maps.event.addListener(map, 'tilesloaded', function(){
				document.getElementById('map_canvas').style.position = 'fixed';
				document.getElementById('map_canvas').style.width = '80%';
			});
		}	
		
		function drawCircle(myLat, myLng, instID)
		{
			myCircle.setMap(map);
			myCircle.setCenter({lat: myLat, lng: myLng});
			myCircle.setRadius(theCircleRadius * 1609.34);
			circleOn = true;
			mapLog("addCircle",instID);
			applyFilters();
		}
		
		function updateCircle()
		{
			if(document.getElementById("setCircleRadius"))
				theCircleRadius = document.getElementById("setCircleRadius").value;//this function only can get called if the panel is visible.
			myCircle.setRadius(theCircleRadius * 1609.34);
			mapLog("updateCircle",theCircleRadius);
			applyFilters();
		}
		
		function deleteCircle()
		{
			myCircle.setMap(null);
			circleOn = false;
			mapLog("clearCircle","");
			applyFilters();
		}
		
		//These functions help highlights the states for each region-group//
		//toggleOverlayLayer, colorstates, toggleRegions, addRegionOverlay//
		////////////////////////////////////////////////////////////////////
		function addRegionOverlay(name,map)
		{
			document.getElementById('colorKeyAAU').style.display = 'none';
			document.getElementById('colorKeyNaigcNew').style.display = 'none';
			document.getElementById('colorKeyNaigcOld').style.display = 'none';
			document.getElementById('colorKeyUSAGwomen').style.display = 'none';
			document.getElementById('colorKeyUSAGmen').style.display = 'none';
			
			if(name == "None")
			{
				toggleOverlayLayer(name);
			}
			else if(name == "NAIGCnew")
			{
				document.getElementById('colorKeyNaigcNew').style.display = 'inline';
				toggleOverlayLayer(name);
			}
			else if(name == "NAIGCold")
			{
				document.getElementById('colorKeyNaigcOld').style.display = 'inline';
				toggleOverlayLayer(name);
			}
			else if(name == "AAU")
			{
				document.getElementById('colorKeyAAU').style.display = 'inline';
				toggleOverlayLayer(name);
			}
			else if(name == "USAGmen")
			{
				document.getElementById('colorKeyUSAGmen').style.display = 'inline';
				toggleOverlayLayer(name);
			}
			else if(name == "USAGwomen")
			{
				document.getElementById('colorKeyUSAGwomen').style.display = 'inline';
				toggleOverlayLayer(name);
			}
		}
		
		//These functions help highlights the states for each region-group//
		//toggleOverlayLayer, colorstates, toggleRegions, addRegionOverlay//
		////////////////////////////////////////////////////////////////////
		function toggleOverlayLayer(type)
		{
			toggleOverlayLayerWrapped(type);
			mapLog("layerSelected",type);
		}
		
		function toggleOverlayLayerWrapped(type)
		{
			for(var i = 0; i < globalPolygonsArray.length; i++) 
			{
				var polygon_i = globalPolygonsArray[i];
				if(globalPolygonsArray[i].type == type)
				{
					polygon_i.setVisible(true);
				}
				else
				{
					polygon_i.setVisible(false);
				}
			}
		}
		
		//this listens for the select menu changing without clicking or pressing enter. Allows arrow keys to be used in navigation.
		//setInterval(toggleOverlayLayerWrapped, 100);
		
		//This function toggles each marker icons for the colleges and universities when you redefine the size///
		/////////////////////////////////////////////////////////////////////////////
		function toggleCollegiateSizeMarkers()
		{
			var Usize = parseInt(document.getElementById("ucutoff").value);
			var CCsize = parseInt(document.getElementById("cccutoff").value);
			
			for(var i = 0; i < globalMarkersArray.length; i++)
			{
				var type = globalMarkersArray[i].type;
				var enrollment = parseInt(globalMarkersArray[i].enrollment);
				
				var state = globalMarkersArray[i].state;
				stateVisibility = true;
				if(document.getElementById("stateVisibility"+state) != null) //this shouldn't be necessary. It means there is data whose state is corrupt or not spelled right or something.
					var stateVisibility = document.getElementById("stateVisibility"+state).checked;
				
				if((type=="6bigCC")||(type=="6smallCC")||(type=="6bigUniv")||(type=="6smallUniv"))
				{
					if((type=="6bigCC")||(type=="6smallCC"))
					{
						if(enrollment >= CCsize)
						{
							globalMarkersArray[i].type = "6bigCC";
							globalMarkersArray[i].setIcon("MapIcons/purple-dot.png");
						}
						else
						{
							globalMarkersArray[i].type = "6smallCC";
							globalMarkersArray[i].setIcon("MapIcons/mm_20_purple.png");
						}
					}
					else if((type=="6bigUniv")||(type=="6smallUniv"))
					{
						if(enrollment >= Usize)
						{
							globalMarkersArray[i].type = "6bigUniv";
							globalMarkersArray[i].setIcon("MapIcons/ltblue-dot.png");
						}
						else
						{
							globalMarkersArray[i].type = "6smallUniv";
							globalMarkersArray[i].setIcon("MapIcons/mm_20_ltblue.png");
						}
					}
				}
				if((layers[globalMarkersArray[i].type]) && (stateVisibility))
					globalMarkersArray[i].setVisible(true);
				else
					globalMarkersArray[i].setVisible(false);
			}
			if(!firstPageLoad)
			{
				countVisible();
				document.getElementById("uwocl").innerHTML = "University w/o Club, "+Usize/1000+"K+ Enrollment";
				document.getElementById("uwocs").innerHTML = "University w/o Club, <"+Usize/1000+"K Enrollment";
				document.getElementById("ccwocl").innerHTML = "Comm College w/o Club, "+CCsize/1000+"K+ Enrollment";
				document.getElementById("ccwocs").innerHTML = "Comm College w/o Club, <"+CCsize/1000+"K Enrollment";
			}
		}
		
		//This function toggles each gym marker when you select or unselect states///
		/////////////////////////////////////////////////////////////////////////////
		function toggleStateMarkers(state)
		{	
			for(var i = 0; i < globalMarkersArray.length; i++)
			{
				var type = globalMarkersArray[i].type;
				var typeVisible = layers[type];
				
				var stateVisible = document.getElementById("stateVisibility"+state).checked;
				
				if(globalMarkersArray[i].state == state)
				{
					if(!typeVisible || !stateVisible)
					{
						globalMarkersArray[i].setVisible(false);
					}
					else if(stateVisible && typeVisible)
					{
						globalMarkersArray[i].setVisible(true);
					}
				}
			}
			if(!firstPageLoad)
				countVisible();
		}
		
		
		function polygonCenter(poly) {
			var lowx,
				highx,
				lowy,
				highy,
				lats = [],
				lngs = [],
				vertices = poly.getPath();

			for(var i=0; i<vertices.length; i++) {
			  lngs.push(vertices.getAt(i).lng());
			  lats.push(vertices.getAt(i).lat());
			}

			lats.sort();
			lngs.sort();
			lowx = lats[0];
			highx = lats[vertices.length - 1];
			lowy = lngs[0];
			highy = lngs[vertices.length - 1];
			center_x = lowx + ((highx-lowx) / 2);
			center_y = lowy + ((highy - lowy) / 2);
			return (new google.maps.LatLng(center_x, center_y));
		}
		
		//This function colors states for the region highlighter//
		//////////////////////////////////////////////////////////
		function colorState(istate,color,map,org)
		{
			 points = getStateBorder(istate)
			 // Construct the polygon
			try
			{
				var StateFill = new google.maps.Polygon({
						paths: points,
						strokeColor: "#0ABA02",
						strokeOpacity: 0.0,
						strokeWeight: 2,
						fillColor: color,
						fillOpacity: 0.3,
						type: org,
						state:istate
					  });
				
				var infowindow = new google.maps.InfoWindow();
				
				google.maps.event.addListener(StateFill,'click', (function(StateFill){ 
					infowindow.setPosition(polygonCenter(StateFill));
					return function() {
						$.ajax({
							url: 'mapHelperFunctions.php',
							type: "POST",
							data: {
									action: "getOverlayData", 
									type: org,
									state: istate
								  },
							success: function(response) {
								infowindow.setContent(response);
								infowindow.open(map);
							}
						});
					};
				})(StateFill));
			}
			catch(e)
			{
				alert("error for polygon. state is "+istate+" layer is " + org + " error is " + e.message);
			}
			
			StateFill.setMap(map);
			StateFill.setVisible(false);
			globalPolygonsArray.push(StateFill);
		}
		
		//These functions help highlights the states for each region-group//
		//toggleOverlayLayer, colorstates, toggleRegions, addRegionOverlay//
		////////////////////////////////////////////////////////////////////
		function toggleRegions()
		{
			if(firstPageLoad)
				activeRegion = 'none';
			else
				activeRegion = document.getElementById('regionOverlaySelector').value;
			
			addRegionOverlay(activeRegion,map);
		}
		
		//This function toggles each gym layer type (high school, usag, college, etc)//
		///////////////////////////////////////////////////////////////////////////////
		function showHideLayer(type) 
		{
			if(layers[type] == true)
			{
				layers[type] = false;
				mapLog("hideLayer",type);
			}
			else //if(layers[type] == false)
			{
				layers[type] = true;
				mapLog("showLayer",type);
			}
				
			for(var i = 0; i < globalMarkersArray.length; i++) 
			{
				var state = globalMarkersArray[i].state;
				stateVisibility = true;
				if(document.getElementById("stateVisibility"+state) != null) //this shouldn't be necessary. It means there is data whose state is corrupt or not spelled right or something.
					stateVisibility = document.getElementById("stateVisibility"+state).checked;
				
				if(globalMarkersArray[i].type == type)
				{
					var marker_i = globalMarkersArray[i];
					var typeVisibility = layers[marker_i.type];
					if(typeVisibility && stateVisibility)
					{
					  marker_i.setVisible(true);
					}
					else 
					{
					  marker_i.setVisible(false);
					}
				}
			}
			
			if(!firstPageLoad)
			{
				applyFilters();
				countVisible();
			}
		}
		
		function circleContains(latLng) {
			return myCircle.getBounds().contains(latLng) && google.maps.geometry.spherical.computeDistanceBetween(myCircle.getCenter(), latLng) <= myCircle.getRadius();
		}
		
		function collegeDivSelectAll(input)
		{
			var checkboxes = document.getElementsByName('Classifications');
			var n=checkboxes.length;
			
			for(var i=0;i<n;i++)
			{
				if(input == 'All')
				{
					checkboxes[i].checked = true;
					//toggleStateMarkers(checkboxes[i].value); //call my onchange for checkbox event
					checkboxes[i].parentElement.className = 'multiselect-on';
				}
				else if(input == 'None')
				{
					checkboxes[i].checked = false;
					//toggleStateMarkers(checkboxes[i].value); //call my onchange for checkbox event
					checkboxes[i].parentElement.className = 'multiselect-off';
				}
			}
			applyFilters();
			if(!firstPageLoad)
			{
				countVisible();
			}
		}
		
		function applyFilters() 
		{
			//option: 0 public, 1 private, 2 all
			var option = 2;
			if(document.getElementById("exclusivitySelector"))
				option = document.getElementById("exclusivitySelector").value;
			//option: 3: men and women, 2: men only, 1: women only, 0: explicitly no gym, clear: all, 4: *either*
			var option2 = "clear";
			if(document.getElementById("onCampusSelector"))	
				option2 = document.getElementById("onCampusSelector").value;
			
			var allFiltersPassed;
			var position;
			var bounds;
			
			for(var i = 0; i < globalMarkersArray.length; i++) 
			{
				var marker_i = globalMarkersArray[i];
				allFiltersPassed = true;
				var state = globalMarkersArray[i].state;
				stateVisibility = true;
				if(document.getElementById("stateVisibility"+state) != null) //this shouldn't be necessary. It means there is data whose state is corrupt or not spelled right or something.
					stateVisibility = document.getElementById("stateVisibility"+state).checked;
				var typeVisibility = layers[marker_i.type];
				
				var classification = globalMarkersArray[i].classification;
				if((classification != null)&&(classification != ""))
				{
					//var tempElementName = "getDescription" + classification;
					var tempElementName = "classificationVisibility_" + classification;
					var classificationVisible = document.getElementById(tempElementName).checked;
				}
				else
				{
					classification = 0;
					var classificationVisible = true;
				}
				
				if(!typeVisibility || !stateVisibility)
					allFiltersPassed = false;
				
				//filter for the 500mile radius circle
				if(circleOn)
				{
					//bounds = myCircle.getBounds();
					position = marker_i.getPosition();
					//if(bounds.contains(position))
					if(circleContains(position))
						; //allFiltersPassed is still true
					else
						allFiltersPassed = false;
				}
							
				//filter for college stuff
				var schoolInstitutionArray = ['1','5f','5m','5b','6bigUniv','6smallUniv','6bigCC','6smallCC','14','15'];
				if(schoolInstitutionArray.includes(globalMarkersArray[i].type)) //if its a school type then we care.
				{
					if(typeVisibility && stateVisibility) //just for optimization
					{
						
					//check for exclusive filter
						if((option == 2) || (marker_i.exclusive == option))
							;//allFiltersPassed is still true
						else //then we have selected something in the menu that doesn't match this gym's properties so we want to hide it.
							allFiltersPassed = false;
						
					//check for campus filter
						//show *either/any*
						if((option2 == 4) && (marker_i.onCampusGym >=1))
							;
						//show all or show only the type that matches.
						else if((option2 == "clear") || (marker_i.onCampusGym == option2))
							;
						else
							allFiltersPassed = false;
					//check for the naia/ncaa classification name filter
						if(classificationVisible)
							; //we are good
						else
							allFiltersPassed = false;
					}
					else
					{
						allFiltersPassed = false; //marker_i.setVisible(false);
					}
				}
				marker_i.setVisible(allFiltersPassed);
			}
			
			if(!firstPageLoad)
				countVisible();
			
			recalculateStatsPanel();
		}
		
		//////////////////////////////////////////////////////		
		//This function toggles the right side control pane///
		//////////////////////////////////////////////////////
		
		function recalculateStatsPanel()
		{
			var conversionStats = {
				"0" : "Olympic Training Center",
				"2" : "USAG Club",
				"10" : "USAIGC Club",
				"12" : "AGA Club",
				"13" : "TAFF Club",
				"8" : "Elementary School",
				"17" : "Potential MS",
				"4" : "Middle School",
				"16" : "Potential HS",
				"3m" : "High School Men",
				"3f" : "High School Women",
				"3b" : "High School Both",
				"5m" : "NCAA Men",
				"5f" : "NCAA Women",
				"5b" : "NCAA Both",
				"6bigUniv" : "Potential 'Big' Univ",
				"6smallUniv" : "Potential 'Small' Univ",
				"6bigCC" : "Potential 'Big' Comm College",
				"6smallCC" : "Potential 'Small' Comm College",
				"1" : "College Club",
				"14" : "New College Club",
				"15" : "College T&A",
				"7" : "Adult Club",
				"CC" : "Convention Center",
				"naigcboard" : "NAIGC Board",
				"naigcboardcontact" : "NAIGC Board Friends",
				"18" : "Gymnastics Ireland"
			};
			
			countVisible();
				var output = '';
				var dontDivide = ["0","3m","3f","3b","4","5m","5f","5b","7","12","13","15","16","17","CC","naigcboard","naigcboardcontact","Gymnastics Ireland"];
				for (var property in visibleStats)
				{
					if(dontDivide.includes(property))
						output += conversionStats[property] + ': ' + visibleStats[property]+' <br/>';
					else
						output += conversionStats[property] + ': ' + visibleStats[property]/2+' <br/>';
				}
				
				document.getElementById('statsVisible').innerHTML = output;
				
				var output2 = '';
				for (var property in allStats)
				{
					if(dontDivide.includes(property))
						output2 += conversionStats[property] + ': ' + allStats[property]+' <br/>';
					else
						output2 += conversionStats[property] + ': ' + allStats[property]/2+' <br/>';
				}
				
				document.getElementById('statsAll').innerHTML = output2;
		}
		
		function switchRightSide(pane)
		{
			document.getElementById('about').style.display = 'none';
			document.getElementById('regionControls').style.display = 'none';
			document.getElementById('gymControls').style.display = 'none';
			document.getElementById('stats').style.display = 'none';
			document.getElementById('subMaps').style.display = 'none';
		<?php
			if($showCgaControls)
			{
		?>
			document.getElementById('CGA').style.display = 'none';
		<?php
			}
			if($showThsgcaControls)
			{
		?>
			document.getElementById('THSGCA').style.display = 'none';
		<?php
			}
		?>	
			document.getElementById(pane).style.display = 'inline';
			
			if(pane == 'stats')
			{
				recalculateStatsPanel();
			}
			mapLog("navButtonClicked",pane);
		}
		
		//multiple select dropdown used for styling the states into a scrollable div. Probably don't need anymore
		//because it used to be part of a $_POST submit.
		jQuery.fn.multiselect = function() {
			$(this).each(function() {
				var checkboxes = $(this).find("input:checkbox");
				checkboxes.each(function() {
					var checkbox = $(this);
					// Highlight pre-selected checkboxes
					if (checkbox.prop("checked"))
						checkbox.parent().addClass("multiselect-on");
		 
					// Highlight checkboxes that the user selects
					checkbox.click(function() {
						if (checkbox.prop("checked"))
							checkbox.parent().addClass("multiselect-on");
						else
							checkbox.parent().removeClass("multiselect-on");
					});
				});
			});
		};

		$(function() {
			 $(".multiselect").multiselect();
		});
		
		//more of the old styling left over from the $_POST
		function setStates()
		{
			var nodeList = document.querySelectorAll('input[name=states]:checked');
			var states = [];
			for (var i = 0; i < nodeList.length; ++i) {
				states[i] = nodeList[i].value;
			}
			window.location.href = location.protocol + '//' + location.host + location.pathname + "?state=" + states;
		}
		
		//This function toggles each state layer group (all, none, tba in future would be a region)//
		/////////////////////////////////////////////////////////////////////////////////////////////
		function selectStates(group) 
		{
			var checkboxes = document.getElementsByName('states');
			var n=checkboxes.length;
			
			for(var i=0;i<n;i++)
			{
				if(group == 'All')
				{
					checkboxes[i].checked = true;
					toggleStateMarkers(checkboxes[i].value); //call my onchange for checkbox event
					checkboxes[i].parentElement.className = 'multiselect-on';
				}
				else if(group == 'None')
				{
					checkboxes[i].checked = false;
					toggleStateMarkers(checkboxes[i].value); //call my onchange for checkbox event
					checkboxes[i].parentElement.className = 'multiselect-off';
				}
			}
			if(!firstPageLoad)
				countVisible();
		}
		
		function getSortedKeys(obj) {
			var keys = []; for(var key in obj) keys.push(key);
			return keys.sort(function(a,b){return obj[b]-obj[a]});
		}
		
		//This function updates the statistics pane for just the visible layer so people can count only what is shown//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////
		var visibleStats = {};
		var allStats = {};
		var temp = {};
		function countVisible()
		{
			var conversionStats = {
				"0" : "Olympic Training Center",
				"2" : "USAG Club",
				"10" : "USAIGC Club",
				"12" : "AGA Club",
				"13" : "TAFF Club",
				"8" : "Elementary School",
				"17" : "Potential MS",
				"4" : "Middle School",
				"16" : "Potential HS",
				"3m" : "High School Men",
				"3f" : "High School Women",
				"3b" : "High School Both",
				"5m" : "NCAA Men",
				"5f" : "NCAA Women",
				"5b" : "NCAA Both",
				"6bigUniv" : "Potential 'Big' Univ",
				"6smallUniv" : "Potential 'Small' Univ",
				"6bigCC" : "Potential 'Big' Comm College",
				"6smallCC" : "Potential 'Small' Comm College",
				"1" : "College Club",
				"14" : "New College Club",
				"15" : "College T&A",
				"7" : "Adult Club",
				"CC" : "Convention Center",
				"naigcboard" : "NAIGC Board",
				"naigcboardcontact" : "NAIGC Board Friends",
				"18" : "Gymnastics Ireland"
			};
			var dontDivide = ["0","3m","3f","3b","4","5m","5f","5b","7","12","13","14","15","16","17","CC","naigcboard","naigcboardcontact","Gymnastics Ireland"];
			
			visibleStats = {}; //erase
			allStats = {};
			temp = conversionStats; //which I defined in order.
			
			for(var i = 0; i < globalMarkersArray.length; i++) 
			{
				type = globalMarkersArray[i].type;
				
				if (allStats[type]) 
				{
					allStats[type]++;
				} 
				else 
				{
					allStats[type] = 1;
				}
				
				temp[type] = allStats[type]; //is associative so order doesnt matter. ///actually thats wrong. see here: https://stackoverflow.com/questions/5199901/how-to-sort-an-associative-array-by-its-values-in-javascript
				
				if(globalMarkersArray[i].getVisible())
				{
					if (visibleStats[type]) 
					{
						visibleStats[type]++;
					} 
					else 
					{
						visibleStats[type] = 1;
					}
				}
			}
			allStats = temp; //now is in order.
			//and maybe just a sanity check
			for(var key in allStats) 
			{
				if(isNaN(allStats[key]))
					allStats[key] = 0;
			}
		}
		
		function mapLog(theAction,theValue)
		{
			$.ajax({
				url: 'mapHelperFunctions.php',
				type: "POST",
				data: {
						mapMetrics: true,
						value: theValue,
						action: theAction
					  },
				success: function(response) {
					;
				}
			});
		}
	</script>
</head>

<?
	function displayAbout()
	{
		/*echo "<p style = 'font-size:10pt;'>
				The markers will be overlapping and you may not see them. For example: a high school with men AND womens programs will have 
				one color overlapping the other so you may not see it until you look at an individual layer. But by clicking a simgle dot it will list everything in the info box. 
			</p><br/><hr/><br/>";
		*/echo "<p style = 'font-size:10pt;'>For the USAG layer, I got the list of clubs from USAG's club search option on their website. Some clubs are USAG members but aren't on the USAG website, so they'd be missing here. Other clubs aren't usag members. E.G. USAIGC. <br><br>
		Basically every college in the U.S. with over 15,000 students enrolled is included, unless it was ITT Tech, or the Art Institutes, DeVry, University of Phoenix, etc -- the for 
		profit or online only schools were left off, or the specialised trade schools, or schools without a single sport like soccer. Those sould be reatively, not proactively pursued. <br/><br/>
		15k was chosen because in my judgement that's where you can have high enough confidence to guarantee a club will last from year to year. It seems there's about 1 gymnasts in every 1200 to 1500 students at a universtiy, and
		about that gives you 10-12 gymnasts in your club, which is probably the minimum guaranteed size for club sustainabilty.<br><br>
		Everything else was just imported from a U.S. DOE database of colleges, with for profit and trade schools filtered out.<br/></p>
		<!--As for the small college layer (small fuschia markers):<br/>
		For most states, any college with more than 5000 or so students was added. Anything less than 5000 I made a 10 second judgement based on whether they had
		a sports department or other clubs or sports, and what kind of degrees offered, etc. For example, a 3000 person community college whose biggest program gives you your big rig drivers license I would have left off (there was one of those in MT...).
		The rational behind that being that gymnastics is a rich man's sport. No denying it. So the probability of finding gymnasts at that type of school is low. <br/><br/>
		To provide a little rationale for that, on a macroscopic level, this is a population density map. On a microscopic, city-level, you'll notice that it is an income/wealth map. More USAG clubs exist on the richer side of the city.<br/><br/>
		Anyway, that's the logic behind the schools that were left off. I could see programs at those colleges between 5k-15k students in the far future, and we do have some now, but I do not see ready and easy sustainability at a school with 1k students.
		Not that it's impossible, but priority for new schools and with limited man hours we should target the larger ones first. I did kind of ballpark this minimum: it wasn't 5000 for every state. In the west, like Nevada, 
		Montana, etc, I went a little lower and maybe did 3000. In the middle of chicago or new york city I maybe went a little higher and did 7,000 or 8,000 students. The rationale for not being consistent was to give more leeway to the less
		populated areas like the middle of the desert where clubs have a hard time driving 12 hours to the nearest meet.<br></p-->
		";
	}
	function displayStats()
	{
		//print_r($programCounts);
		global $programCounts;
		ksort($programCounts);
		echo prettyPrint($programCounts);
	}
?>

<body>

	<div id="map_wrapper">
		<div id="map_canvas" class="mapping"></div>
	</div>

	<div id = "legend">
		<table>
			<tr>
				<td>
					<button onclick = "switchRightSide('about');">About</button>

					<button onclick = "switchRightSide('gymControls');">Gyms</button>

					<button onclick = "switchRightSide('regionControls');">Overlays</button>

					<button onclick = "switchRightSide('stats');">Stats</button>

					<button onclick = "switchRightSide('subMaps');">States</button>
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td>
					<button onclick = "deleteCircle();">Delete Circle</button>
				</td>
		<?php 
				if($showCgaControls)
				{
		?>
				<td>
					<button onclick = "switchRightSide('CGA');">CGA</button>
				</td>
		<?php
				}
				if($showThsgcaControls)
				{
		?>
				<td>
					<button onclick = "switchRightSide('THSGCA');">THSGCA</button>
				</td>
		<?php
				}
		?>
			</tr>
		</table>
		<table id = "gymControls">
			<tr>
				<td>
					<b><u>Pre-College:</b></u><br/>
					Elementary Schools: (TBA!!!) <img src = "MapIcons/black.png"></img><br/>
					<div class="onoffswitch">
						<input onchange = "showHideLayer('8');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_8">
						<label class="onoffswitch-label" for="myonoffswitch_8">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					Middle Schools: (TBA/WIP) <img src = "MapIcons/yellow.png"></img><br/>
					<div class="onoffswitch">
						<input onchange = "showHideLayer('4');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_4">
						<label class="onoffswitch-label" for="myonoffswitch_4">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<?
			if($showThsgcaControls)
			{
			?>
			<tr>
				<td>
					Potential Middle School: <img src = "MapIcons/newMS.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('17');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_17">
						<label class="onoffswitch-label" for="myonoffswitch_17">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<?
			}
			?>
			<tr>
				<td>
					High School Men: <img src = "MapIcons/blue.png"></img><br/>
					<div class="onoffswitch">
						<input onchange = "showHideLayer('3m');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_3m">
						<label class="onoffswitch-label" for="myonoffswitch_3m">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					High School Women: <img src = "MapIcons/pink.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('3f');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_3f">
						<label class="onoffswitch-label" for="myonoffswitch_3f">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					High School Both: <img src = "MapIcons/hsboth.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('3b');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_3b">
						<label class="onoffswitch-label" for="myonoffswitch_3b">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<?
			if($showThsgcaControls)
			{
			?>
			<tr>
				<td>
					Potential High School: <img src = "MapIcons/newHS.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('16');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_16">
						<label class="onoffswitch-label" for="myonoffswitch_16">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<?
			}
			?>
			<tr>
				<td>
					USAG Club: <img src = "MapIcons/green.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('2');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_2">
						<label class="onoffswitch-label" for="myonoffswitch_2">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					USAIGC Club: <img src = "MapIcons/darkgreen.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('10');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_10">
						<label class="onoffswitch-label" for="myonoffswitch_10">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					AGA Club: <img src = "MapIcons/GreenTriangle.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('12');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_12">
						<label class="onoffswitch-label" for="myonoffswitch_12">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					TAFF Club: <img src = "MapIcons/GreenTriangle.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('13');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_13">
						<label class="onoffswitch-label" for="myonoffswitch_13">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<b><u><br/>College:</b></u><br/>
					NCAA Artistic Men: <img src = "MapIcons/darkblue.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('5m');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_5m">
						<label class="onoffswitch-label" for="myonoffswitch_5m">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					NCAA Artistic Women: <img src = "MapIcons/red.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('5f');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_5f">
						<label class="onoffswitch-label" for="myonoffswitch_5f">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					NCAA Artistic Both Disciplines: <img src = "MapIcons/ncaaboth.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('5b');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_5b">
						<label class="onoffswitch-label" for="myonoffswitch_5b">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					College Tumbling & Acro: <img src = "MapIcons/red.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('15');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_15">
						<label class="onoffswitch-label" for="myonoffswitch_15">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					Has College Club: <img src = "MapIcons/blue-dot.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('1');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_1">
						<label class="onoffswitch-label" for="myonoffswitch_1">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					NEW College Club: <img src = "MapIcons/blue-whitedot.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('14');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_14">
						<label class="onoffswitch-label" for="myonoffswitch_14">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style = "display:inline" id = 'uwocl'>University w/o Club, <? echo $smallU_Cutoff_Abbr; ?>+ Enrollment: </div><img src = "MapIcons/ltblue-dot.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('6bigUniv');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_6bigUniv">
						<label class="onoffswitch-label" for="myonoffswitch_6bigUniv">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style = "display:inline" id = 'uwocs'>University w/o Club < <? echo $smallU_Cutoff_Abbr; ?> Enrollment: </div><img src = "MapIcons/mm_20_ltblue.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('6smallUniv');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_6smallUniv">
						<label class="onoffswitch-label" for="myonoffswitch_6smallUniv">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style = "display:inline" id = 'ccwocl'>Comm College w/o Club, <? echo $smallCC_Cutoff_Abbr; ?>+ Enrollment: </div><img src = "MapIcons/purple-dot.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('6bigCC');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_6bigCC">
						<label class="onoffswitch-label" for="myonoffswitch_6bigCC">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style = "display:inline" id = 'ccwocs'>Comm College w/o Club < <? echo $smallCC_Cutoff_Abbr; ?> Enrollment: </div><img src = "MapIcons/mm_20_purple.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('6smallCC');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_6smallCC">
						<label class="onoffswitch-label" for="myonoffswitch_6smallCC">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<b><u><br/>Adult & Other:</b></u><br/>
					Adult Clubs: <img src = "MapIcons/marker_white.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('7');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_7">
						<label class="onoffswitch-label" for="myonoffswitch_7">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					Gymnastics Ireland: <img src = "MapIcons/green.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('18');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_18">
						<label class="onoffswitch-label" for="myonoffswitch_18">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					Olympic/National Training Centers: <img src = "MapIcons/Olympic.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('0');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_0">
						<label class="onoffswitch-label" for="myonoffswitch_0">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<?
			if(isset($_REQUEST['andrew']))
			{
			?>
			<tr>
				<td>
					Men's Judges: <img src = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%23|FF0000|000000"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('MJ');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_MJ">
						<label class="onoffswitch-label" for="myonoffswitch_MJ">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<?
			}
			if($showNaigcControls)
			{
			?>
			<tr>
				<td>
					NAIGC Board: <img src = "MapIcons/red-dot.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('naigcboard');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_board">
						<label class="onoffswitch-label" for="myonoffswitch_board">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					NAIGC Board Friends: <img src = "MapIcons/ltred-dot.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('naigcboardcontact');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_boardcontact">
						<label class="onoffswitch-label" for="myonoffswitch_boardcontact">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					Convention Centers: <img src = "MapIcons/cc.png"></img><br/>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('CC');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_CC">
						<label class="onoffswitch-label" for="myonoffswitch_CC">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<?
			}
			?>
			<tr>
				<td>
					<br/>
						Big - Small University Cutoff Number:<br/>
						<input type="number" min = "1000" step = "1000" id = "ucutoff" name = "ucutoff" onchange = "toggleCollegiateSizeMarkers();" value = <? echo $smallU_Cutoff;?> ></input></br>
						Big - Small Comm College Cutoff Number:<br/>
						<input type="number" min = "1000" step = "1000" id = "cccutoff" name = "cccutoff" onchange = "toggleCollegiateSizeMarkers();" value = <? echo $smallCC_Cutoff;?> ></input></br>
						<!--button onclick = "toggleCollegiateSizeMarkers();">Update</button-->
				</td>
			</tr>
		</table>
		<?php
		if($showCgaControls)
		{
		?>
		<table id = "CGA" style = "display:none">
			<tr>
				<td>
					<br/>
					<b>Collegiate School Filter</b><br/>
					<select id = "exclusivitySelector" onchange = "applyFilters();">
						<option value = "2" selected>All Schools</option>
						<option value = "0">Public Schools Only</option>
						<option value = "1">Private Schools Only</option>
					</select><br/>
					<br/>
				</td>
			</tr>
			<tr>
				<td>
					<b>On Campus Gym Filter (college only)</b><br/>
					<select id = "onCampusSelector" onchange = "applyFilters();" >
						<option selected value = "clear" selected>All</option>
						<option value = "3">Gym on campus (both)</option>
						<option value = "2">On campus men only</option>
						<option value = "1">On campus women only</option>
						<option value = "4">On campus either/any</option>
						<option value = "0">Not on campus only</option>
					</select><br/>
					<br/>
				</td>
			</tr>
			<tr>
				<td>
					<b>TBA: Filter out only those colleges which have:</b><br/>
					Past men's NCAA Program:<br/>
					Past women's NCAA Program:<br/>
					Past men's college club:<br/>
					Past women's college club:<br/>
					NO current men's NCAA:<br/>
					NO current women's NCAA:<br/>
					<br/>
				</td>
			</tr>
			<tr>
				<td>
					<b>Collegiate Division Filter:</b><br/>
					<button onclick = "collegeDivSelectAll('All');">All</button> <button onclick = "collegeDivSelectAll('None');">None</button> 
					<div class = "multiselect">
						<?
						$sqlClassifications = "
							SELECT
								Distinct classification_name,
								ClassificationCode
							FROM
								NCES_Summary
							Group By
								classification_name,
								ClassificationCode
							;";
							
						if($stmtClassifications = $con->prepare($sqlClassifications))
						{
							$stmtClassifications->execute();
							$stmtClassifications->store_result(); //allow us to get properties, e.g. stmt->num_rows;

							if($stmtClassifications->num_rows >= 1)
							{
								$stmtClassifications->bind_result($classification, $classificationCode);
								while($stmtClassifications->fetch())
								{
									echo '<label><input id = "classificationVisibility_' . $classificationCode . '" onchange = "applyFilters();" type="checkbox" checked name="Classifications" value="' . $classification . '" />' . $classification . '</label>';
								}
								echo '<label><input id = "classificationVisibility_0" onchange = "applyFilters();" type="checkbox" checked name="Classifications" value="0" />(all non-colleges)</label>';
								echo '<label><input id = "classificationVisibility_unknown" onchange = "applyFilters();" type="checkbox" checked name="Classifications" value="unknown" />Unknown</label>';
							}
						}
						?>
					</div>
					<br/>
				</td>
			</tr>
			<tr>
				<td>
					<b>NCES Title IX Filters (TBA):</b><br/>
					Change Year:
						<select>
							<option selected value = "2017" >2017</option>
							<option value = "2016" >2016</option>
							<option value = "2015" >2015</option>
							<option value = "2014" >2014</option>
							<option value = "2013" >2013</option>
							<option value = "2012" >2012</option>
							<option value = "2011" >2011</option>
							<option value = "2010" >2010</option>
							<option value = "2009" >2009</option>
							<option value = "2008" >2008</option>
							<option value = "2007" >2007</option>
							<option value = "2006" >2006</option>
							<option value = "2005" >2005</option>
							<option value = "2004" >2004</option>
							<option value = "2003" >2003</option>
						</select>
					<br/>
					(+ leans women %, - leans men %)<br/>
					Scholarship Spending: <input type = "number" min = "-100" max = "100" step = "0.1"></input>
					<br/>
					All Spending: <input type = "number" min = "-100" max = "100" step = "0.1"></input>
					<br/>
					# of Athletes: <input type = "number" min = "-100" max = "100" step = "0.1"></input>
					<br/>
				</td>
			</tr>
		</table>
		<?php
		}
		?>
		<?php
		if($showThsgcaControls)
		{
		?>
		<table id = "THSGCA" style = "display:none">
			<tr>
				<td>
					
				</td>
			</tr>
		</table>
		<?php
		}
		?>
		<!--tr>
				<td>
					<img src = "MapIcons/redX.png"></img> Unknown<br/> unknown is a catch-all for clubs/team that don't have a type. Please let andrew know if you see any red X marks.
				</td>
			</tr-->
		<table id = "regionControls" style = "display:none">
			<tr>
				<td>
					Circle Radius: <input id = "setCircleRadius" onchange = "updateCircle();" type = "number" step = "50" min = "50" max = "1000" value = 500></input>mi<br/>
				</td>
			</tr>
			<tr>
				<td>
					The states will highlight different colors corresponding to different regions.<br/><br/>
					Note that USAG Men splits California into NORCAL and SOCAL but they are in the same region.<br/><br/>
					<select id = "regionOverlaySelector" onchange = "toggleRegions();">
						<option selected disabled>Select overlay:</option>
						<option value = "None">None</option>
						<option value = "USAGmen">USAG Men</option>
						<option value = "USAGwomen">USAG Women</option>
						<option value = "NAIGCnew">NAIGC New</option>
						<option value = "NAIGCold">NAIGC Old</option>
						<option value = "AAU">AAU</option>
					</select>
					<p>
						Legend:
					</p>
					<p id = "colorKeyNaigcOld" style = "display:none">
						<span style="background-color: #333333"><font color = "#FFFF00">West</font></span><br/>
						<font color = "#FF0000">Southwest</font><br/>
						<font color = "#00BFFF">Midwest</font><br/>
						<font color = "#FFA500">NorthEast</font><br/>
						<font color = "#00FF00">East</font><br/>
					</p>
					<p id = "colorKeyNaigcNew" style = "display:none">
						<span style="background-color: #333333"><font color = "#FFFF00">West</font></span><br/>
						<font color = "#FF0000">South Central</font><br/>
						<font color = "#00BFFF">Midwest</font><br/>
						<font color = "#000000">Mideast</font><br/>
						<font color = "#FFA500">NorthEast</font><br/>
						<font color = "#FF00FF">Mid-Atlantic</font><br/>
						<font color = "#00FF00">Southeast</font><br/>
					</p>
					<p id = "colorKeyUSAGwomen" style = "display:none">
						<span style="background-color: #333333"><font color = "#FFFF00">Region 1</font></span><br/>
						<font color = "#800080">Region 2</font><br/>
						<font color = "#FF0000">Region 3</font><br/>
						<font color = "#00BFFF">Region 4</font><br/>
						<font color = "#000000">Region 5</font><br/>
						<font color = "#FFA500">Region 6</font><br/>
						<font color = "#FF00FF">Region 7</font><br/>
						<font color = "#00FF00">Region 8</font><br/>
					</p>
					<p id = "colorKeyUSAGmen" style = "display:none">
						<span style="background-color: #333333"><font color = "#FFFF00">Region 1</font></span><br/>
						<font color = "#800080">Region 2</font><br/>
						<font color = "#FF0000">Region 3</font><br/>
						<font color = "#00BFFF">Region 4</font><br/>
						<font color = "#000000">Region 5</font><br/>
						<font color = "#FFA500">Region 6</font><br/>
						<font color = "#FF00FF">Region 7</font><br/>
						<font color = "#00FF00">Region 8</font><br/>
						<span style="background-color: #333333"><font color = "#eeeeee">Region 9</font></span><br/>
					</p>
					<p id = "colorKeyAAU" style = "display:none">
						<font color = "#00BFFF">Midwest</font><br/>
						<font color = "#FFA500">Northeast</font><br/>
						<font color = "#00FF00">Southern</font><br/>
						<span style="background-color: #333333"><font color = "#FFFF00">Western</font></span><br/>
					</p>
				</td>
			</tr>
		</table>
		<table id = "about" style = "display:none">
			<tr>
				<td>
					<? displayAbout(); ?>
				</td>
			</tr>
		</table>
		<table id = "stats" style = "display:none">
			<tr>
				<td>
					<p>This map has a total of:</p>
					<? //displayStats(); ?>
					<p id = "statsAll"></p>
				</td>
			</tr>
			<tr>
				<td>
					<p>This map currently has the following shown/visible:</p>
					<p id = "statsVisible"></p>
				</td>
			</tr>
		</table>
		<table id = "subMaps" style = "display:none">
			<tr>
				<td>
					<p>If the map is too busy you can filter out things so the entire nation isn't displayed. This is also how you'd update the stats page to count specific states.</p>
					<p>I'll add a control in the future to get all of the states in a region selected at one click.</p>
					<button onclick = "selectStates('All');">All</button><button onclick = "selectStates('None');">None</button>
						<div id = "stateSelector" class = "multiselect">
							<?
							while($stmtStates->fetch())
							{
								//so just write an onchange here with the function of toggleStateMarkers(state)
								echo '<label><input id = "stateVisibility'.$abbr.'" onchange = "toggleStateMarkers(\''.$abbr.'\');" type="checkbox" checked name="states" value="'.$abbr.'" />'.$name.'</label>';
							}
							echo '<label><input id = "stateVisibilityOntario" onchange = "toggleStateMarkers(\'Ontario\');" type="checkbox" checked name="states" value="Ontario" />Ontario</label>';
							echo '<label><input id = "stateVisibilityBC" onchange = "toggleStateMarkers(\'BC\');" type="checkbox" checked name="states" value="BC" />British Columbia</label>';
							?>
						</div>
				</td>
			</tr>
			<!--tr>
				<td>
					<button onclick = "setStates();">Submit</button>
				</td>
			</tr-->
		</table>
	</div>

<?
}
else
{
	display_login();
}
?>
</body>
</html>
