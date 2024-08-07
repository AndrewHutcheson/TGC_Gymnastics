<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.min.js"></script>

<script type="text/javascript">

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

function getCompetitionsInMeet()
{
	$.ajax({
				type: 'POST',
				url: "meetAjax.php",
				async: false,
				data: {
					getCompetitionsForMeet: 1,
					meetID: document.getElementById("meetSelectMenu").value
				},
				dataType: 'json',
				success: function (data) {
					table.setData(data);
					getDivisions();
					getDisciplines();
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading "+iDiscipline+" team data");
				}
			});
}

function getDivisions()
{
	$.ajax({
				type: 'POST',
				url: "meetAjax.php",
				async: false,
				data: {
					getDivisions: 1
				},
				dataType: 'json',
				success: function (data) {
					$('#addDivision').empty();
					$('#addDivision').append("<option selected disabled value = ''>Select a division</option>"); 
					data.forEach(function(datum) {
						$('#addDivision').append("<option value=" + datum['ID'] + ">" + datum['Name'] + "</option>"); 
					});
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading division data");
				}
			});
}

function getDisciplines()
{
	$.ajax({
				type: 'POST',
				url: "meetAjax.php",
				async: false,
				data: {
					getDisciplines: 1
				},
				dataType: 'json',
				success: function (data) {
					$('#addDiscipline').empty();
					$('#addDiscipline').append("<option selected disabled value = ''>Select a discipline</option>"); 
					data.forEach(function(datum) {
						$('#addDiscipline').append("<option value=" + datum['ID'] + ">" + datum['Name'] + "</option>"); 
					});
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading discipline data");
				}
			});
}

function getLevels()
{
	if(($('#addDiscipline').value != "") && ($('#addDivision').value!=""))
	{
		$.ajax({
				type: 'POST',
				url: "meetAjax.php",
				async: false,
				data: {
					getLevels: 1,
					discipline: document.getElementById('addDiscipline').value
				},
				dataType: 'json',
				success: function (data) {
					$('#addLevel').empty();
					$('#addLevel').append("<option selected disabled value = ''>Select a level</option>"); 
					if(data != false)
					{
						data.forEach(function(datum) {
							$('#addLevel').append("<option value=" + datum['ID'] + ">" + datum['Name'] + "</option>"); 
						});
					}
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading level data");
				}
			});
	}
}

function addCompetition()
{
	if(($('#meetSelectMenu').value != "") && ($('#addDivision').value!="") && ($('#addLevel').value!="") && ($('#addDiscipline').value!="") && ($('#eventMax').value!=""))
	{
		$.ajax({
				type: 'POST',
				url: "meetAjax.php",
				async: false,
				data: {
					addNewCompetition:document.getElementById('meetSelectMenu').value,
					addDivision:document.getElementById('addDivision').value,
					addLevel:document.getElementById('addLevel').value,
					addDiscipline:document.getElementById('addDiscipline').value,
					eventMax:document.getElementById('eventMax').value
				},
				dataType: 'json',
				success: function (data) {
					getCompetitionsInMeet();
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error adding competition");
				}
			});
	}
}

function addMeet()
{
	if(($('#selectHost').value != "") && ($('#meetDate').value!="") && ($('#meetName').value!=""))
	{
		$.ajax({
				type: 'POST',
				url: "meetAjax.php",
				async: false,
				data: {
					addNewMeet: 1,
					hostClub:document.getElementById('selectHost').value,
					meetDate:document.getElementById('meetDate').value,
					meetName:document.getElementById('meetName').value
				},
				dataType: 'json',
				success: function (data) {
					getMeets();
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error adding meet");
				}
			});
	}
}

function updateNumPerTeamScore(iCompetitionID,iNum)
	{
		$.ajax({
			type: 'POST',
			url: "meetAjax.php",
			async: false,
			data: {
				updateNumPerTeamScore:1,
				competitionID:iCompetitionID,
				numPerTeamScore:iNum
			},
			dataType: 'json',
			success: function (data) {
				getCompetitionsInMeet();
			},
			error: function (textStatus, errorThrown) {
				//console.log(errorThrown);
				alert("error editing # per team score");
			}
		});	
	}

	function updateTeamMaxOnEvent(iCompetitionID,iMax)
	{
		$.ajax({
			type: 'POST',
			url: "meetAjax.php",
			async: false,
			data: {
				updateTeamMaxOnEvent:1,
				competitionID:iCompetitionID,
				max:iMax
			},
			dataType: 'json',
			success: function (data) {
				getCompetitionsInMeet();
			},
			error: function (textStatus, errorThrown) {
				//console.log(errorThrown);
				alert("error editing Team # of places");
			}
		});	
	}

	function updateTeamPlacesToAward(iCompetitionID,iPlaces)
	{
		$.ajax({
			type: 'POST',
			url: "meetAjax.php",
			async: false,
			data: {
				updateTeamPlacesToAward:1,
				competitionID:iCompetitionID,
				places:iPlaces
			},
			dataType: 'json',
			success: function (data) {
				getCompetitionsInMeet();
			},
			error: function (textStatus, errorThrown) {
				//console.log(errorThrown);
				alert("error editing Team # of places");
			}
		});	
	}

	function updatePlacesToAward(iCompetitionID,iPlaces)
	{
		$.ajax({
			type: 'POST',
			url: "meetAjax.php",
			async: false,
			data: {
				updatePlacesToAward:1,
				competitionID:iCompetitionID,
				places:iPlaces
			},
			dataType: 'json',
			success: function (data) {
				getCompetitionsInMeet();
			},
			error: function (textStatus, errorThrown) {
				//console.log(errorThrown);
				alert("error editing number of places");
			}
		});	
	}

	function updateLevel(iCompetitionID,iLevel)
	{
		$.ajax({
			type: 'POST',
			url: "meetAjax.php",
			async: false,
			data: {
				updateLevel:1,
				competitionID:iCompetitionID,
				level:iLevel
			},
			dataType: 'json',
			success: function (data) {
				getCompetitionsInMeet();
			},
			error: function (textStatus, errorThrown) {
				//console.log(errorThrown);
				alert("error editing level");
			}
		});	
	}

	function updateDivision(iCompetitionID,iDivision)
	{
		$.ajax({
			type: 'POST',
			url: "meetAjax.php",
			async: false,
			data: {
				updateDivision:1,
				competitionID:iCompetitionID,
				division:iDivision
			},
			dataType: 'json',
			success: function (data) {
				getCompetitionsInMeet();
			},
			error: function (textStatus, errorThrown) {
				//console.log(errorThrown);
				alert("error editing division");
			}
		});	
	}

	function deleteCompetition(iCompetitionID)
	{
		if(true)
		{
			$.ajax({
				type: 'POST',
				url: "meetAjax.php",
				async: false,
				data: {
					deleteCompetition:1,
					competitionID:iCompetitionID
				},
				dataType: 'json',
				success: function (data) {
					getCompetitionsInMeet();
					if(data['status']=="error"){
						alert(data['description']);
					}
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error removing competition");
				}
			});
		}
		else
		{
			alert("An error occurred");
		}
	}

$(document).ready(function() {
    getMeets();
});

</script>

	<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
								<h2>Add a new meet:</h2>
								<p>THIS PAGE IS STILL UNDER CONSTRUCTION. All you can do at the moment is add a competition to a meet or create a new meet.</p>
							<?php
								if(userIsLoggedIn() && (sizeOf(getListOfUserClubAdministrativePermissions()) > 0))
								{
									if(userIsExecutiveAdministrator())
									{
										echo "<p>As a TGC Admin you can control all clubs' meets.</p>";
										$combinedArray = getListOfUserClubAdministrativePermissions();
									}
									else
									{
										echo "<p>You are an administrator for the following clubs. Select which one you want to create a meet for:</p>";
											$combinedArray = getListOfUserCaptainPermissions();
									}
									if(sizeof($combinedArray)>0) //this needs to kick us out somehow if we somehow got to the page but don't have appropriate perms.
									{
										echo "<select id = 'selectHost' onchange = ''>";
										
											echo "<option selected disabled value = ''>Select Host</option>";
										foreach ($combinedArray AS $clubID=>$clubName)
										{
											echo "<option value = '".$clubID."'>".$clubName."</option>";
										}
										echo "</select>";
									}
							?>
									<br/>
									Date: <input type = "date" id = "meetDate"></input><br/>
									Meet Name: <input id = "meetName"></input><br/>
									<button onclick = "addMeet();">Submit</button>
									<br><hr>
									
							<?php
									echo "<h2>Edit Competitions for a meet:</h2>";
									echo "<select id = 'meetSelectMenu' onchange = 'getCompetitionsInMeet();'>
										<option selected disabled>Select a meet:</option>";
									echo "</select><button onclick = 'getCompetitionsInMeet();'>&#x21bb;</button><br/>";
									
							?>
							
									<br/>
									<select name = "addDivision" id = "addDivision" onchange = "getLevels();">
										<option selected disabled value = "">Select a division:</option>
									</select><br/>
									<select name = "addDiscipline" id = "addDiscipline" onchange = "getLevels();">
										<option selected disabled value = "">Select a discipline:</option>
									</select><br/>
									<select name = "addLevel" id = "addLevel">
										<option selected disabled value = "">Select a level:</option>
									</select><br/>
									Max per event: <input name = "eventMax" id = "eventMax" type = "number" placeholder = "8" /><br/>
									<button onclick = "addCompetition();">Submit</button>
									<br/><br/>
									The following competitions are a part of this meet:<br/>
									
									<div id = "competitionTable"></div>
									
									<script type="text/javascript">
										
										
										//redefine these mutators into the respective php loops above. when moving to object will have to do something different.
										var levelArray = { //to be made dynamic drom DB
											"1":"TGC WAG Level 9",
											"2":"TGC MAG NCAA",
											"3":"TGC WAG Level 8",
											"4":"TGC Registration Only Event",
											"5":"TGC WAG Level 6",
											"6":"TGC MAG Level 9",
											"7":"NAIGC MAG Developmental",
											"8":"NAIGC WAG Developmental",
											"9":"NAIGC WAG Level 7",
											"10":"NAIGC New Fliers",
											"11":"NAIGC Intermediate Fliers",
											"12":"NAIGC High Fliers",
											"13":"NAIGC MAG Level 7",
											"14":"TGC WAG Xcel Silver",
											"15":"TGC WAG Xcel Platinum"
										};

										var levelLookupArray = { //to be made dynamic drom DB
											"TGC WAG Level 9":1,
											"TGC MAG NCAA":2,
											"TGC WAG Level 8":3,
											"TGC Registration Only Event":4,
											"TGC WAG Level 6":5,
											"TGC MAG Level 9":6,
											"NAIGC MAG Developmental":7,
											"NAIGC WAG Developmental":8,
											"NAIGC WAG Level 7":9,
											"NAIGC New Fliers":10,
											"NAIGC Intermediate Fliers":11,
											"NAIGC High Fliers":12,
											"NAIGC MAG Level 7":13,
											"TGC WAG Xcel Silver":14,
											"TGC WAG Xcel Platinum":15
										};
										
										//oof I need a select
										/*var levelEditor = function(cell, onRendered, success, cancel){
											var theLevel = cell.getRow().getData().LevelID;
											
											//create and style editor
											var editor = document.createElement("select");
											
											//populate it
											var optionArray;
											optionArray = levelArray;
											
											var opt;
											for (var ID in optionArray){
												opt = document.createElement('option');
												opt.value = ID;
												opt.innerHTML = optionArray[ID];
												editor.appendChild(opt);
											}
											
											//create and style input
											editor.style.padding = "3px";
											editor.style.width = "100%";
											editor.style.boxSizing = "border-box";

											//Set value of editor to the current value of the cell when the editor opens
											var curVal = cell.getRow().getData().LevelID;
											editor.value = curVal; //need to change per keyval

											//set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
											onRendered(function(){
												editor.focus();
												editor.style.height = "100%";
											});

											//when the value has been set, trigger the cell to update
											editor.onblur = function(e){
												success(optionArray[editor.value]);
											};
											
											editor.onchange = function(e){
												success(optionArray[editor.value]);
											};
											return editor;
										}*/
										
										function LevelFormatter(cell){
											return levelArray;
										}

										function levelLookup(cell) {

											var menlevelArray = {
												2: "TGC MAG NCAA",
												6: "TGC MAG Level 9",
												13: "NAIGC MAG Level 7"
											};

											var womenlevelArray = {
												1: "TGC WAG Level 9",
												3: "TGC WAG Level 8",
												14: "TGC WAG Xcel Silver",
												15: "TGC WAG Xcel Platinum"
											};

											let row = cell.getRow();
											if (row.getData().DisciplineID == 1) {
												return {values: womenlevelArray};
											} else if (row.getData().DisciplineID == 2) {
												return {values: menlevelArray};
											}
											return {values: {}}; // Return an empty object as default
										}

										var table = new Tabulator("#competitionTable", {					
											layout: "fitDataFill",
											responsiveLayout: "collapse",
											columns: [
												{title: "CompetitionID", field: "ID", visible: false},
												{title: "Leauge", field: "LeaugeID", formatter: "lookup", formatterParams: {"2":"TGC"}, sorter: "string", editor: "list", editorParams: {values: {"2": "TGC"}}},
												{title: "Discipline", field: "DisciplineID", formatter: "lookup", formatterParams: {"1":"F","2":"M"},  sorter: "string"},
												{title: "Division", field: "DivisionID", formatter: "lookup", formatterParams: {"1": "College", "3": "Open"}, sorter: "string", editor: "list", editorParams: {values: {"1": "College", "3": "Open"}}},
												{title: "Level", field: "LevelID", formatter: "lookup", formatterParams:LevelFormatter, sorter: "string", editor: "list", editorParams: levelLookup},
												{title: "Max Registered Per Team on Event", field: "TeamMaxOnEvent", editor: "number"}
											],
											index: "ID"
										});

										table.on("cellEdited", function(cell){
											//This callback is called any time a cell is edited
											var row = cell.getRow();
											var data = row.getData();
											var col = cell.getColumn();
											if(cell.getField() == "LevelID")
											{
												updateLevel(data.ID,levelLookupArray[data.LevelID]);
											}
											else if(cell.getField() == "DivisionID")
											{
												updateDivision(data.ID,data.DivisionID);
											}
											else if(cell.getField() == "PlacesToAward")
											{
												updatePlacesToAward(data.ID,data.PlacesToAward);
											}
											else if(cell.getField() == "TeamPlacesToAward")
											{
												updateTeamPlacesToAward(data.ID,data.TeamPlacesToAward);
											}
											else if(cell.getField() == "TeamMaxOnEvent")
											{
												updateTeamMaxOnEvent(data.ID,data.TeamMaxOnEvent);
											}
											else if(cell.getField() == "numPerTeamScore")
											{
												updateNumPerTeamScore(data.ID,data.numPerTeamScore);
											}
										});
							<?php
								}
								else
								{
									echo "<p>You are not a meet administrator. Please log in.</p>";
									display_login();
								}
							?>
							</script>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>
