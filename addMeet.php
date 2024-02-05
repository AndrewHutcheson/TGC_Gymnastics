<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.min.js"></script>

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
					$("#competitionTable").tabulator("setData", data);
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
										
										
										var DivisionMutator = function(value, data, type, params, component){
											//value - original value of the cell
											//data - the data for the row
											//type - the type of mutation occurring  (data|edit)
											//params - the mutatorParams object from the column definition
											//component - when the "type" argument is "edit", this contains the cell component for the edited cell, otherwise it is the column component for the column
											if(value == 1)
												return "College";
											if(value == 3)
												return "Open";
											if(value == 2)
												return "Community";
										}
										var levelArray = { //to be made dynamic drom DB
											1:"TGC WAG Level 9",
											2:"TGC MAG NCAA",
											3:"TGC WAG Level 8",
											4:"TGC Registration Only Event",
											5:"TGC WAG Level 6",
											6:"TGC MAG Level 9",
											7:"NAIGC MAG Developmental",
											8:"NAIGC WAG Developmental",
											9:"NAIGC WAG Level 7",
											10:"NAIGC New Fliers",
											11:"NAIGC Intermediate Fliers",
											12:"NAIGC High Fliers",
											13:"NAIGC MAG Level 7",
											14:"TGC WAG Xcel Silver",
											15:"TGC WAG Xcel Platinum"
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
										
										var LevelMutator = function(value, data, type, params, component){
											return levelArray[value];
										}
										var LeaugeMutator = function(value, data, type, params, component){
											if(value == 2)
												return "TGC";
										}
										var DisciplineMutator = function(value, data, type, params, component){
											if(value == 1)
												return "F";
											if(value == 2)
												return "M";
										}
										
										var table = $("#competitionTable").tabulator({
											layout: "fitDataFill",
											responsiveLayout: "collapse",
											columns:[
												{title:"CompetitionID", field:"ID", 	visible:false},
												{title:"Leauge", 		field:"LeaugeID",			mutator:LeaugeMutator, 		formatter:"plaintext",	sorter:"string", editor:"select", editorParams:{values:{"2":"TGC"}}},
												{title:"Discipline", 	field:"DisciplineID",		mutator:DisciplineMutator, 	formatter:"plaintext",	sorter:"string", editor:"select", editorParams:{values:{"2":"M","1":"F"}}},
												{title:"Division", 		field:"DivisionID",			mutator:DivisionMutator,	formatter:"plaintext",	sorter:"string", editor:"select", editorParams:{values:{"1":"College","3":"Open"}}},
												{title:"Level", 		field:"LevelID",			mutator:LevelMutator, 		formatter:"plaintext",	sorter:"string", editor:"select", editorParams:{values:levelArray}},
												{title:"Max Registered Per Team on Event", 	field:"TeamMaxOnEvent", editor:"number"}
											],
											index:"ID"
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
