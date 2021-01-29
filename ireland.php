<? session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
	html{
		height: 100% 
	}
	body{
		height: 100%; 
		margin: 0; 
		padding: 0;
	}
	
	#legend{
		width:39%; 
		float:right;
		margin: 0; 
		padding: 0;
		overflow-x:scroll;
	}
	#map_wrapper {
		height: 100%;
		width: 60%;
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
		height:42em;
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
	require_once('mapHelperFunctions.php');
	
if(userIsLoggedIn())
{	
	//A switch for singling out states.
	$state = "LIKE '%%'"; //all
	$country = "Ireland";

	//So I want to minimize the number of dots and datapoints. Well, the question of "does this institution have both mens and womens programs?" is most important for high schools (type3) and NCAA (type5)
		//anything else we are simply creating a duplicate data point and people aren't really ever going to want to know how many USAG gyms have mens equipment as often (besides, we dont have the data for them)
	//there are a few single-Discipline college clubs but these can be counted on one hand, and are usually NCAA school conflicts. OU is the only one I know and they recently are warming up to a women's club....
	//There are even fewer single-Discipline adult clubs. While you could count these on one hand... Some of them are tied to a parent institution (their usag club they work out at). So I stuck them in here so 
		//that they wouldn't ge overwritten by the group by clause with that USAG club. Although I pulled out he 
	
	//So the GROUP BY eliminates unneccesary map markers for those types. The description function of an institution contains all the program information on an institution, not program wide basis.
	//This then basically halves the number of data points.
	
	//The second group by - on the program ID - prevents an institution with multiple types from overwriting each other (e.g. a usag, usaigc, adult &... all at the same gym.)
	
	//The last two add layers and info for the Dual Program (dual Discipline) ncaa and high schools.
	$sqlClubs = "
				SELECT 
					Identifiers_Institutions.ID,
					Identifiers_Institutions.Name,
					Identifiers_Institutions.Lat,
					Identifiers_Institutions.Lng,
					ifnull(status,'uncontacted') as status,
					MAX(contactedDate)
				FROM 
					Identifiers_Programs,
					Identifiers_Institutions
				LEFT JOIN
					Recruit_Contacts
				ON
					Identifiers_Institutions.ID = Recruit_Contacts.institutionID AND
					Recruit_Contacts.ID IN (Select max(ID) FROM Recruit_Contacts WHERE Recruit_Contacts.institutionID = Identifiers_Institutions.ID)
				WHERE
					Identifiers_Programs.ClubType IN (2,3,10,12,13,18) AND
					Identifiers_Programs.InstitutionID = Identifiers_Institutions.ID AND
					Identifiers_Institutions.Country = '".$country."'
				GROUP BY
					Identifiers_Programs.InstitutionID
				;";
		
	$map[] = array();
	$index = 0;	
	
	
	if($stmtClubs = $con->prepare($sqlClubs))
	{
		$stmtClubs->execute();
		$stmtClubs->store_result(); //allow us to get properties, e.g. stmt->num_rows;

		if($stmtClubs->num_rows >= 1){
			$stmtClubs->bind_result($id, $name, $lat, $lng, $contacted, $contactedDate);
			
			while($stmtClubs->fetch()){
			
				//layer visibility defaults can be set here: 0 shown, 1 hidden
				$hide = "1";
				/*if($type == 7)
				{
					$hide = "1";
				}*/
				
				$time = strtotime("-9 month", time());
				$todaysDateMinusOneYear = date("Y-m-d", $time);

				if($contactedDate <= $todaysDateMinusOneYear)
					$contacted = "uncontacted";
				
				$map[$index] = array(
					"id"=>$id,
					"name"=>$name,
					"lat"=>$lat,
					"lng"=>$lng,
					"contacted"=>$contacted,
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
				if (is_array($val)){
					$retStr .= $key . ': ' . pp($val) . '<br/>';
				}else{
					$retStr .= $key . ': ' . $val . '<br/>';
				}
			}
		}
		$retStr .= '';
		return $retStr;
	}
	
?>

  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  
  <script>		
		var states = new Object();
		jQuery(function($) {
			// Asynchronously Load the map API 
			var script = document.createElement('script');
			script.src = "https://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize&libraries=geometry&key=<?php echo GOOGLE_MAPS_API_KEY ?>";
			document.body.appendChild(script);
			
			/*var script2 = document.createElement('script');
			script2.src = "stateBoundaries.js";
			document.body.appendChild(script2);*/
		});
   </script>
   <script src="stateBoundaries.js"></script>
   <script>

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
					$contacted = $map[$i]['contacted'];
					$description = addslashes(preg_replace( "/\r|\n/", "", showDescription($id)));
					
					echo "['" . addslashes($name) . "','" . $lat . "', '" . $lng . "', '" . $contacted . "', '" . $description . "', '" . $id . "']";
					
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
		var layers = {"NorthTX":false,"SouthTX":false,"EastTX":false,"WestTX":false,"contacted":false,"uncontacted":false,"followup":false};
		var map;
		
		function initialize() {
			var bounds = new google.maps.LatLngBounds();
			var mapOptions = {
				center: new google.maps.LatLng(53.3498, -6.2603),
				zoom:7,
				mapTypeId: 'roadmap'
			};
							
			// Display a map on the page
			map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
			makeRegionLabels(map);
			
			// Loop through our array of markers & place each one on the map  
			for( i = 0; i < markers.length; i++ ) 
			{
				//if(markers[i][4]=="0")
				{
					var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
					bounds.extend(position);
					
					var iconColor = 'MapIcons/green.png'; 
					
					if(markers[i][3] == "followup")
						iconColor = 'MapIcons/yellow.png';
					else if(markers[i][3] == "uncontacted")
						iconColor = 'MapIcons/red.png';
					
					var image = {
						 url: iconColor
					}
					
					var regionType = getLocation(position);
					
					marker = new google.maps.Marker({
						id: markers[i][5],
						position: position,
						map: map,
						title: markers[i][0],
						icon: image,
						type: regionType,
						status: markers[i][3]
					});
					
					if(firstPageLoad)
					{	marker.setVisible(false);	}
					globalMarkersArray.push(marker);
					
					var content = markers[i][4]+"<br/>";
					var institutionID = markers[i][5];
					
					//Speed Note #1 : other notes have this same label elsewhere via they are the same thing that needs to change.
						//and this javascript right here I think can be replaced with a AJAX call because the description box is so very much a lot of html to pass at once. 	
					var infowindow = new google.maps.InfoWindow();

						google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){ 
							return function() {
								infowindow.setContent(content);
								infowindow.open(map,marker);
								loadGymInfoEditor(marker.id,marker.title);
								loadGymContactHistory(marker.id);
							};
						})(marker,content,infowindow)); 
				}	
			}
			firstPageLoad = false;
			
			// Automatically center the map fitting all markers on the screen
				//map.fitBounds(bounds);
			// Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
			var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
				this.setZoom(7);
				google.maps.event.removeListener(boundsListener);
			});
			
			//stupid hack needed to fix the position so that the control bar can scroll independently.
			google.maps.event.addListener(map, 'tilesloaded', function(){
				document.getElementById('map_canvas').style.position = 'fixed';
				document.getElementById('map_canvas').style.width = '60%';
			});
		}	
		
		$.ajaxSetup({ cache: false });
		
		//ajax get the form
		function loadGymInfoEditor(id,name)
		{
			$.ajax({
				url: 'mapUpdateAjax.php',
				type: "POST",
				data: {id: id, name: name, action: 'getform', referrer: window.location.href.substring(window.location.href.lastIndexOf('/')+1)},
				success: function(response) {
					document.getElementById('selectedgyminfo').innerHTML = response;
					bindMyEventListenerAfterAjax();
				}
			});
		}
		//ajax get the history log
		function loadGymContactHistory(id)
		{
			$.ajax({
				url: 'mapUpdateAjax.php',
				type: "POST",
				data: {id: id, action: 'history'},
				success: function(response) {
					document.getElementById('selectedgymhistory').innerHTML = response;
				}
			});
		}
		//ajax submit the form
		function bindMyEventListenerAfterAjax()
		{
			$('#updateForm').submit(function(e) {
				e.preventDefault(e);
				$.ajax({
					url : $(this).attr('action') || window.location.pathname,
					type: "POST",
					data: $(this).serialize(),
					success: function (data) {
						var result = $.parseJSON(data);
						alert(result[2]);
						loadGymContactHistory(result[0]);
						updateContactedStatus(result[0],result[1]);
					},
					error: function (jXHR, textStatus, errorThrown) {
						alert(errorThrown);
					}
				});
			});
		}
		
		//make it live
		function updateContactedStatus(id,newtype)
		{
			statusVisibility = layers[newtype];
			
			for(var i = 0; i < globalMarkersArray.length; i++) 
			{				
				if(globalMarkersArray[i].id == id)
				{
					var marker_i = globalMarkersArray[i];
					marker_i.status = newtype;
					
					var typeVisibility = true;
					if(statusVisibility && typeVisibility)
					{
					  marker_i.setVisible(true);
					}
					
					if (newtype == 'contacted')
						marker_i.setIcon('MapIcons/green.png'); 
					else if(newtype == "followup")
						marker_i.setIcon('MapIcons/yellow.png');
					else if(newtype == "uncontacted")
						marker_i.setIcon ('MapIcons/red.png');
				}
			}
		}
		
		//get which region within the state based on what polygon the point is in//
		///////////////////////////////////////////////////////////////////////////
		function getLocation(position)
		{
			var region;
			for(var i = 0; i < globalPolygonsArray.length; i++) 
			{
				var polygon_i = globalPolygonsArray[i];
				if(google.maps.geometry.poly.containsLocation(position,polygon_i))
				{
					return polygon_i['name']; //globalPolygonsArray[i].name
				}
			}
			
			return polygon_i;
		}
		
		//This function makes the region overlay legend in the right pane//
		///////////////////////////////////////////////////////////////////
		function makeRegionLabels(map)
		{
			var regionColor;

			//TX TGC		
				//region 1
				regionColor = "#00BFFF";
					colorState('WestTX',regionColor,map,'Show');
					
				//region 2
				regionColor = "#FF0000";
					colorState('EastTX',regionColor,map,'Show');
				
				//region 3
				regionColor = "#FFA500";
					colorState('SouthTX',regionColor,map,'Show');
					
				//region 4
				regionColor = "#00FF00";
					colorState('NorthTX',regionColor,map,'Show');
		}
		
		//These functions help highlights the states for each region-group//
		//toggleOverlayLayer, colorstates, toggleRegions, addRegionOverlay//
		////////////////////////////////////////////////////////////////////
		/*function toggleOverlayLayer(type)
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
		}*/
		
		
		//This function colors states for the region highlighter//
		//////////////////////////////////////////////////////////
		function colorState(state,color,map,org)
		{
			 points = getStateBorder(state);
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
						name: state,
						type: org
					  });
			}
			catch(e)
			{
				alert("error for polygon. state is "+state+" error is "+e.message);
			}
			
			StateFill.setMap(map);
			StateFill.setVisible(false);
			globalPolygonsArray.push(StateFill);
		}
		
		//These functions help highlights the states for each region-group//
		//toggleOverlayLayer, colorstates, toggleRegions, addRegionOverlay//
		////////////////////////////////////////////////////////////////////
		/*function toggleRegions()
		{
			if(firstPageLoad)
				activeRegion = 'none';
			else
				activeRegion = document.getElementById('regionOverlaySelector').value;
			
			addRegionOverlay(activeRegion,map)
		}*/
		
		//this listens for the select menu changing without clicking or pressing enter. Allows arrow keys to be used in navigation.
		//setInterval(toggleRegions, 100);
		
		//These functions help highlights the states for each region-group//
		//toggleOverlayLayer, colorstates, toggleRegions, addRegionOverlay//
		////////////////////////////////////////////////////////////////////
		/*function addRegionOverlay(name,map)
		{
			//document.getElementById('colorKeyTxRegions').style.display = 'none';
			if(name == "None")
			{
				toggleOverlayLayer(name);
			}
			else if(name == "Show")
			{
				//document.getElementById('colorKeyTxRegions').style.display = 'inline';
				toggleOverlayLayer(name);
			}
		}*/
		////////////////////////////////////////////////////////////////////////////////////////////
		//This function toggles each gym status - in this case its contacted / not contacted / etc//
		////////////////////////////////////////////////////////////////////////////////////////////
		function showHideStatus(status) 
		{
			if(layers[status] == true)
				layers[status] = false;
			else //if(layers[type] == false)
				layers[status] = true;
				
			for(var i = 0; i < globalMarkersArray.length; i++) 
			{
				var type = globalMarkersArray[i].type;
				//typeVisibility = layers[type];
				typeVisibility = true;
				
				if(globalMarkersArray[i].status == status)
				{
					var marker_i = globalMarkersArray[i];
					var statusVisibility = layers[marker_i.status];
					if(statusVisibility && typeVisibility)
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
				countVisible();
		}
		
		//This function toggles each gym layer type (high school, usag, college, etc)// in this case its the region within tx
		///////////////////////////////////////////////////////////////////////////////
		/*function showHideLayer(type) 
		{
			if(layers[type] == true)
				layers[type] = false;
			else //if(layers[type] == false)
				layers[type] = true;
				
			for(var i = 0; i < globalMarkersArray.length; i++) 
			{
				var status = globalMarkersArray[i].status;
				statusVisibility = layers[status];
				
				if(globalMarkersArray[i].type == type)
				{
					var marker_i = globalMarkersArray[i];
					var typeVisibility = layers[marker_i.type];
					if(typeVisibility && statusVisibility)
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
				countVisible();
		}*/
		
		//This function updates the statistics pane for just the visible layer so people can count only what is shown//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////
		var visibleStats = {};
		function countVisible()
		{
			visibleStats = {}; //erase
			for(var i = 0; i < globalMarkersArray.length; i++) 
			{
				if(globalMarkersArray[i].getVisible())
				{
					type = globalMarkersArray[i].type;
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
		}
	</script>
</head>

<body>

	<div id="map_wrapper">
		<div id="map_canvas" class="mapping"></div>
	</div>

	<div id = "legend">
		<!--table id = "gymControls">
			<tr>
				<td>
					Highlight responsibility zones?<br/>
					<select id = "regionOverlaySelector" onchange = "toggleRegions();">
						<option selected disabled>Select overlay:</option>
						<option value = "None">None</option>
						<option value = "Show">Show</option>
					</select>
				</td>
			</tr>
		</table-->
		<!--table>
			<tr>
				<td width="25%">
					<font color = "#00BFFF">West:</font>
					<div class="onoffswitch">
						<input onchange = "showHideLayer('WestTX');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_West">
						<label class="onoffswitch-label" for="myonoffswitch_West">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
				<td width="25%">
					<font color = "#00FF00">North: </font>
					<div class="onoffswitch">
						<input onchange = "showHideLayer('NorthTX');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_North">
						<label class="onoffswitch-label" for="myonoffswitch_North">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
				<td width="25%">
					<font color = "#FFA500">South: </font>
					<div class="onoffswitch">
						<input onchange = "showHideLayer('SouthTX');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_South">
						<label class="onoffswitch-label" for="myonoffswitch_South">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
				<td width="25%">
					<font color = "#FF0000">East:</font>
				    <div class="onoffswitch">
						<input onchange = "showHideLayer('EastTX');" type="checkbox" class="onoffswitch-checkbox" id="myonoffswitch_East">
						<label class="onoffswitch-label" for="myonoffswitch_East">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
		</table-->
		<table>
			<tr>
				<td>
				<br/>Hide completed contacts?<br/>
					Gyms Contacted: <img src = "MapIcons/green.png"/><br/>
					<div class="onoffswitch">
						<input onchange = "showHideStatus('contacted');" type="checkbox" class="onoffswitch-checkbox" id="statusvisibilitycontacted">
						<label class="onoffswitch-label" for="statusvisibilitycontacted">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					Gyms Not Contacted: <img src = "MapIcons/red.png"/><br/>
					<div class="onoffswitch">
						<input onchange = "showHideStatus('uncontacted');" type="checkbox" class="onoffswitch-checkbox" id="statusvisibilityuncontacted">
						<label class="onoffswitch-label" for="statusvisibilityuncontacted">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					Needs Follow Up: <img src = "MapIcons/yellow.png"/><br/>
					<div class="onoffswitch">
						<input onchange = "showHideStatus('followup');" type="checkbox" class="onoffswitch-checkbox" id="statusvisibilityfollowup">
						<label class="onoffswitch-label" for="statusvisibilityfollowup">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<br/><u>Currently Selected Gym:</u>
					<div id = 'selectedgyminfo'>
						<br/>Click on a gym to update it.
						<form id="updateForm"></form>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<br/><u>Contact History:</u><br/>
					<div id = 'selectedgymhistory'>
						<br/>Click on a gym to see this.
					</div>
				</td>
			</tr>
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
