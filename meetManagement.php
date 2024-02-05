<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>
<?php require_once("registrationAjax.php"); ?>

<?php
function getMeets()
{
	global $conn;
	$stmtMeets= $conn->prepare("
		SELECT
			ID, 
			Date,
			MeetName,
			Hostclub
		FROM
			Events_Meets
		WHERE
			Season >= 201 AND
			Scored = 1
		Order By 
			Date
		");
	$stmtMeets->execute();
	
	if ($stmtMeets->rowCount() > 0)
	{
		return $stmtMeets;
	}
	else
	{
		return false;
	}
}
?>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.js"></script>

<script>

	$(document).ready(function() {
		getMeets();
	});

	function getMeets()
	{
		$.ajax({
					type: 'POST',
					url: "meetAjax.php",
					async: false,
					data: {
						getMeetsForWhichUserIsAdmin:1
					},
					dataType: 'json',
					success: function (data) {
						$('#meetSelectMenu').empty();
						$('#meetSelectMenu').append("<option selected disabled value = ''>Select a Meet</option>"); 
						data.forEach(function(datum) {
							$('#meetSelectMenu').append("<option value=" + datum['ID'] + ">" + datum['Name'] + "</option>");
						});
					},
					error: function (textStatus, errorThrown) {
						console.log(textStatus);
						console.log(errorThrown);
						alert("Error downloading meet list. You probably don't have any meets created yet.");
					}
				});
	}

	function updateTeamOptions(iRotation, iCompetition, iInstitution, iDesignation, rowID)
	{
		var returnValue = false;
		$.ajax({
			type: 'POST',
			url: "registrationAjax.php",
			async: false,
			data: {
				updateTeamOptions: 1,
				institutionID: iInstitution,
				competitionID: iCompetition,
				teamDesignation: iDesignation,
				rotationID: iRotation
			},
			dataType: 'json',
			success: function (data) {
				//loadTeamData();
				//$("#teamTable").tabulator("updateData",([{ID:rowID, rotationID:iRotation}]));
				returnValue = true;
				loadTeamData();
			},
			error: function (textStatus, errorThrown) {
				//console.log(errorThrown);
				alert("error changing rotation");
			}
		});
		return returnValue;
	}

	function loadMeetData()
	{
		loadRegData(1);
		loadRegData(2);
		loadRegData(3);
		loadTeamData();
		$("#teamTable").tabulator("setHeight", "800px");
	}
	function loadRegData(iDiscipline)
	{
		//run only if a valid meet and club have been selected.
		//if((document.getElementById("clubBeingRegistered").value != "")&&(document.getElementById("meetSelectMenu").value != ""))
		if(true)
		{
			$.ajax({
				type: 'POST',
				url: "registrationAjax.php",
				async: false,
				data: {
					getTeamRegistrationForCompetition: 1,
					institutionID: false,
					meetID: document.getElementById("meetSelectMenu").value,
					DisciplineID: iDiscipline
				},
				dataType: 'json',
				success: function (data) {
					if(iDiscipline == 2)
						$("#menRegTable").tabulator("setData", data);
					else if(iDiscipline == 1)
						$("#womenRegTable").tabulator("setData", data);
					else
						$("#clinicEventTable").tabulator("setData", data);
					
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading "+iDiscipline+" team data");
				}
			});
		}
	}
	
	var meetID; var url; var liveUrl;
	
	function loadTeamData()
	{
		//run only if a valid meet and club have been selected.
		//if((document.getElementById("clubBeingRegistered").value != "")&&(document.getElementById("meetSelectMenu").value != ""))
		if(true)
		{
			meetID = document.getElementById('meetSelectMenu').value;
			url = "<a target = '_blank' href = 'https://tgcgymnastics.com/PrintMeetMaterials?meetID=" + meetID + "'>Click here</a> to print meet materials.";
			url2 = "Aternatively <a target = '_blank' href = 'https://tgcgymnastics.com/PrintMeetMaterials2?meetID=" + meetID + "'>click here</a> to print meet materials separated for scorecards on cardstock.";
			liveUrl = "<a target = '_blank' href = 'https://tgcgymnastics.com/liveScores.php?meetID=" + meetID + "&rows=20'>Click here</a> for live scoring.";
			scriptUrl = "<a target = '_blank' href = 'https://tgcgymnastics.com/awardAnnouncement.php?meetID=" + meetID + "&maxPlaces=8'>Click here</a> for a readable script for speaking during awards.";
			$.ajax({
				type: 'POST',
				url: "registrationAjax.php",
				async: false,
				data: {
					getTeamHeaderData: 1,
					institutionID: false,
					meetID: document.getElementById('meetSelectMenu').value
				},
				dataType: 'json',
				success: function (data) {
					$("#teamTable").tabulator("setData", data);
					document.getElementById('MeetMaterialLink').innerHTML = url + " " + liveUrl;
					document.getElementById('MeetMaterialLink2').innerHTML = url2;
					document.getElementById('scriptLink').innerHTML = scriptUrl;
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading summary team data");
				}
			});
		}
	}
</script>

<style>
	@media screen and (min-width: 480px) 
	{
		.inner
		{
			max-width: initial !important;
		}
	}
	
	.tabulator-cell{
		max-height: 28px;
		padding: 0px 4px 0px 4px !important;
		
	}
</style>

<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
						<?php
							$isAnyClubAdmin = false;
							
							if(userIsLoggedIn() && (sizeOf(getListOfUserClubAdministrativePermissions()) > 0))
							{
								//print_r(getListOfUserClubAdministrativePermissions());
								$temp = getListOfUserClubAdministrativePermissions();
								echo "<p>You are an administrator for the following meets:</p>";

								$stmtMeets = getMeets();
								echo "<select id = 'meetSelectMenu' onchange = 'loadMeetData();'>
									<option selected disabled>Select a meet:</option>";
								/*while($row = $stmtMeets->fetch(PDO::FETCH_ASSOC))
								{
									//if(isset($temp[$row['Hostclub']]))
									{
										echo "<option value = '".$row['ID']."'>".$row['MeetName']."(".$row['Date'].")</option>";
									}
								}*/
								echo "</select><button onclick = 'loadMeetData();'>&#x21bb;</button><br/><br/>";
								
								?>
								<div id = "MeetMaterialLink"></div>
								<div id = "MeetMaterialLink2"></div>
								<div id = "scriptLink"></div>
								<h2>Clinic Registration: <span onclick = '$("#clinicEventTable").tabulator("download", "csv", "clinic.csv");'>(csv)</span></h2>
								<div id="clinicEventTable"></div>
								<br/>
								<h2>Womens Teams and Competitors: <span onclick = '$("#womenRegTable").tabulator("download", "csv", "women.csv");'>(csv)</span></h2>
								<div id="womenRegTable"></div>
								<br/>
								<h2>Mens Teams and Competitors: <span onclick = '$("#menRegTable").tabulator("download", "csv", "men.csv");'>(csv)</span></h2>
								<div id="menRegTable"></div> <br>
								<br/>
								<h2>Team Summary - see fees and create rotations: <span onclick = '$("#teamTable").tabulator("download", "csv", "teams.csv");'>(csv)</span><button onclick = 'loadTeamData();'>&#x21bb;</button></h2>
								<p>This tool will help you calculate and balance rotations. The rotations are counted on a per-name basis. 
								So name them: Men 1, Men 2, Women 1, Women 2, ByeRoation etc. If you have multiple sets of equipment, 
								like Texas Tech does for women, just name them with 8 or 9 in the name or something to create a new counter.</p>
								<div id="teamTable"></div> <br>
								<br/>
								
								<br/>
								<script type="text/javascript">
									$("#clinicEventTable").tabulator({
										layout: "fitDataFill",
										//responsiveLayout: "collapse",
										columns:[
											{title:"ID",				field:"ID",	 			visible:false},
											{title:"MeetID",			field:"MeetID",	 		visible:false},
											{formatter: "rownum", width: 20},
											{title:"Name",	 			field:"Name",	 	sorter:"string"},
											{title:"Team",	 			field:"Institution",	 	sorter:"string"},
											{title:"DisciplineID", 			field:"DisciplineID", 		visible:false},
											{title:"CompetitionID", 	field:"CompetitionID", 	visible:false},
											{title:"InstitutionID", 	field:"InstitutionID", 	visible:false},
											{title:"Men's Lecture", 	field:"MenLecture",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{crossElement:false,}, 	bottomCalc:"count", responsive: 5},
											{title:"Women's Lecture", 	field:"WomenLecture",	sorter:"boolean",	formatter:"tickCross", formatterParams:{crossElement:false,}, 	bottomCalc:"count", responsive: 5},
											{title:"Meet Lecture", 		field:"MeetLecture",	sorter:"boolean",	formatter:"tickCross", formatterParams:{crossElement:false,}, 	bottomCalc:"count", responsive: 5},
											{title:"Social Gathering", 	field:"Social",	 		sorter:"boolean",	formatter:"tickCross", formatterParams:{crossElement:false,}, 	bottomCalc:"count", responsive: 5},
											{title:"Workout Clinic", 	field:"Workout",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{crossElement:false,}, 	bottomCalc:"count", responsive: 5},
											{title:"Fee", 				field:"Fee",	 	formatter:"money", 	formatterParams:{symbol:"$", precision:2}, bottomCalc:"sum", bottomCalcFormatterParams :{symbol:"$", precision:2}, responsive: 6},
											{title:"Last Registered", 	field:"RegDate", responsive: 6},
											{title:"Registered By", field:"RegBy", responsive: 6}
										],
										index:"ID"
									});
								
									$("#menRegTable").tabulator({
										layout: "fitDataFill",
										groupBy: "CompetitionID",
										columns:[
											{title:"ID", 			field:"ID", 		visible:false},
											{formatter: "rownum", width: 20},
											{title:"Name",	 		field:"Name",	 	sorter:"string"},
											{title:"CompetitionID", field:"CompetitionID", 		visible:false},
											{title:"DisciplineID", 		field:"DisciplineID", 	visible:false},
											{title:"Competition",	field:"Team",	 	},
											{title:"Club", 			field:"Institution",	sorter:"string" 	},
											{title:"Team",			field:"Designation",	 	},
											{title:"FX", 			field:"MFX",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{allowEmpty:true, crossElement:false,}, 		bottomCalc:"count"},
											{title:"PH", 			field:"MPH",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{allowEmpty:true, crossElement:false,},		bottomCalc:"count"},
											{title:"SR", 			field:"MSR",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{allowEmpty:true, crossElement:false,},		bottomCalc:"count"},
											{title:"VT", 			field:"MVT",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{allowEmpty:true, crossElement:false,},		bottomCalc:"count"},
											{title:"PB", 			field:"MPB",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{allowEmpty:true, crossElement:false,},		bottomCalc:"count"},
											{title:"HB", 			field:"MHB",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{allowEmpty:true, crossElement:false,},		bottomCalc:"count"},
											{title:"AA", 			field:"MAA",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{allowEmpty:true, crossElement:false,},		bottomCalc:"count"},
											{title:"Fee", 			field:"Fee",	 	formatter:"money", 	formatterParams:{symbol:"$", precision:2}, bottomCalc:"sum", bottomCalcFormatterParams :{symbol:"$", precision:2}}
										],
										index:"ID",
										groupHeader:function(value, count, data, group){return data[0].Team;},
									});
									
									$("#womenRegTable").tabulator({
										layout: "fitDataFill",
										groupBy: "CompetitionID",
										columns:[
											{title:"ID", 			field:"ID", 		visible:false},
											{formatter: "rownum", width: 20},
											{title:"Name",	 		field:"Name",	 	sorter:"string"},
											{title:"CompetitionID", field:"CompetitionID", 		visible:false},
											{title:"DisciplineID", 		field:"DisciplineID", 	visible:false},
											{title:"Competition", 	field:"Team",	 	},
											{title:"Club", 			field:"Institution",	sorter:"string" 	},
											{title:"Team",			field:"Designation",	 	},
											{title:"VT", 			field:"WVT",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{crossElement:false,},	bottomCalc:"count"},
											{title:"UB", 			field:"WUB",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{crossElement:false,},	bottomCalc:"count"},
											{title:"BB", 			field:"WBB",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{crossElement:false,},	bottomCalc:"count"},
											{title:"FX", 			field:"WFX",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{crossElement:false,},	bottomCalc:"count"},
											{title:"AA", 			field:"WAA",	 	sorter:"boolean",	formatter:"tickCross", formatterParams:{crossElement:false,},	bottomCalc:"count"},
											{title:"Fee", 			field:"Fee",	 	formatter:"money", 	formatterParams:{symbol:"$", precision:2}, bottomCalc:"sum", bottomCalcFormatterParams :{symbol:"$", precision:2}}
										],
										index:"ID",
										groupHeader:function(value, count, data, group){return data[0].Team;},
									});
									
									$("#teamTable").tabulator({
									layout: "fitDataFill",
									groupBy: "Rotation",
									height: "100px",
									movableColumns: true,
									columns:[
										{title:"ID",				field:"ID",	 			visible:false},
										{title:"MeetID",			field:"MeetID",	 		visible:false},
										{title:"Competition",	 	field:"TeamName", 		sorter:"string"},
										{title:"Club",	 			field:"InstitutionName", 		sorter:"string"},
										{title:"Team",	 			field:"Designation"	},
										{title:"Rotation", 			field:"Rotation", 		sorter:"string",	editor: "input"},
										{title:"MFX",	 			field:"MFX", 			sorter:"number",	bottomCalc:"sum"},
										//{title:"FX",	 			field:"FX", 			sorter:"number",	bottomCalc:"sum"},
										{title:"PH",	 			field:"PH", 			sorter:"number",	bottomCalc:"sum"},
										{title:"SR",	 			field:"SR", 			sorter:"number",	bottomCalc:"sum"},
										//{title:"VT",	 			field:"VT", 			sorter:"number",	bottomCalc:"sum"},
										{title:"MVT",	 			field:"MVT", 			sorter:"number",	bottomCalc:"sum"},
										{title:"PB",	 			field:"PB", 			sorter:"number",	bottomCalc:"sum"},
										{title:"HB",	 			field:"HB", 			sorter:"number",	bottomCalc:"sum"},
										{title:"WVT",	 			field:"WVT", 			sorter:"number",	bottomCalc:"sum"},
										{title:"UB",	 			field:"UB", 			sorter:"number",	bottomCalc:"sum"},
										{title:"BB",	 			field:"BB", 			sorter:"number",	bottomCalc:"sum"},
										{title:"WFX",	 			field:"WFX", 			sorter:"number",	bottomCalc:"sum"},
										{title:"CompetitionID", 	field:"CompetitionID", 	visible:false},
										{title:"InstitutionID", 	field:"InstitutionID", 	visible:false},
										{title:"Score?", 			field:"TeamScore",		formatter:"tickCross", formatterParams:{crossElement:false,}, bottomCalc:"sum" },
										{title:"Fee", 				field:"TeamFee",	 	formatter:"money", 	formatterParams:{symbol:"$", precision:2}, bottomCalc:"sum", bottomCalcFormatterParams :{symbol:"$", precision:2}}
									],
									//groupBy: "TeamName",
									index:"ID",
									cellEdited: function(cell){
										
										var row = cell.getRow();
										var data = row.getData();
										
										if(cell.getField()=="Rotation")
										{
											if(updateTeamOptions(data.Rotation,data.CompetitionID,data.InstitutionID,data.Designation,data.ID))
												; //yay it saved
											else
												cell.restoreOldValue();
										}
									}
								});
								</script>
								
						<?php
							}
							else
							{
								echo "<p>You are not a meet administrator. Please log in.</p>";
								display_login();
								
							}
						?>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>
