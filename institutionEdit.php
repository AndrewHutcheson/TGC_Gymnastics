<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.js"></script>

<script type="text/javascript">
	
	$(document).ready(function(){
		$("#InstName").autocomplete({
			delay:350,
			source: "institutionAjax.php",
			dataType: "json",
			//source: data,
			minLength: 3,
			select: function (event, ui) {
											$('#InstID').val(ui.item.ID); 
											$('#InstName').val(ui.item.Name);
											$('#State').val(ui.item.State);
											$('#City').val(ui.item.City);
											$('#Lat').val(ui.item.Lat);
											$('#Lng').val(ui.item.Lng);
											return false; //prevent widget from updating value
										},
			focus: function(event, ui) { //set for arrow keys
				$("#InstName").val(ui.item.label);
				return false;
			},
			messages: {
				noResults: 'That name was not found.',
				results: function() {}
			}
		});
	});

	function changeType(ID,Type)
	{
		$.ajax({
				type: 'POST',
				url: "institutionAjax.php",
				async: false,
				data: {
					updateProgramID:1,
					programID:ID,
					newType:Type
				},
				dataType: 'json',
				success: function (data) {
					;
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error");
				}
			});
	}
	
	function changePhone(ID,Value)
	{
		$.ajax({
				type: 'POST',
				url: "institutionAjax.php",
				async: false,
				data: {
					updateProgramPhone:1,
					programID:ID,
					phone:Value
				},
				dataType: 'json',
				success: function (data) {
					;
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error");
				}
			});
	}

	function changeEmail(ID,Value)
	{
		$.ajax({
				type: 'POST',
				url: "institutionAjax.php",
				async: false,
				data: {
					updateProgramEmail:1,
					programID:ID,
					email:Value
				},
				dataType: 'json',
				success: function (data) {
					;
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error");
				}
			});
	}
</script>

<style>
	.ui-autocomplete { position: absolute; cursor: default; background-color: #ffffff; z-index:50 !important; border: 2px solid #555555;}
	.ui-helper-hidden-accessible { display:none; }
</style>

	<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
						
						<?php
						//echo "<pre>"; print_r(getListOfUserCoachPermissions()); echo "</pre>";
						//echo "<pre>"; print_r($_SESSION['permissions']); echo "</pre>";
						if(userIsExecutiveAdministrator())
						{
						?>
						<p>You are currently logged in as: <?php echo userLoggedInNameIs(); ?></p>
								<p>THIS PAGE IS STILL UNDER CONSTRUCTION. All you can do at the moment is change an existing program's type. I.e. potential college->collegiate club, or you can edit the phone & email.</p>
								<input size = "2" disabled id = "InstID"/>
								<input size = "35" type = "text" id = "InstName" placeholder = "Institution Name"/>
								<input size = "15" type = "text" id = "State" placeholder = "State"/>
								<input size = "15" type = "text" id = "City" placeholder = "City"/><br/>
								<input size = "15" type = "text" id = "Lat" placeholder = "Lat"/>
								<input size = "15" type = "text" id = "Lng" placeholder = "Lng"/>
								<br>
								<button onclick = "loadProgramData();">Load</button><br/>
								<br/>
								<h2>Programs at this institution:</h2>
								
								<div id="programTable"></div>
								
								
								
								<script type="text/javascript">
							
									function loadProgramData()
									{
										$.ajax({
											type: 'POST',
											url: "institutionAjax.php",
											async: false,
											data: {
												getProgramDetails: 1,
												institutionID: document.getElementById("InstID").value,
											},
											dataType: 'json',
											success: function (data) {
												$("#programTable").tabulator("replaceData", data);
											},
											error: function (textStatus, errorThrown) {
												//console.log(errorThrown);
												alert("error downloading data");
											}
										});
									}
								
									var genderMutator = function(value, data, type, params, component){
										//value - original value of the cell
										//data - the data for the row
										//type - the type of mutation occurring  (data|edit)
										//params - the mutatorParams object from the column definition
										//component - when the "type" argument is "edit", this contains the cell component for the edited cell, otherwise it is the column component for the column
										if(value == 1)
											return "W";
										if(value == 2)
											return "M";
									}
									
									var typeLookup = [];
										typeLookup[0]="Olympic Training Center";
										typeLookup[1]="Collegiate Club";
										typeLookup[2]="USAG Club";
										typeLookup[3]="High School";
										typeLookup[4]="Middle School";
										typeLookup[5]="NCAA Artistic";
										typeLookup[6]="Potential College";
										typeLookup[7]="Adult Team";
										typeLookup[8]="Elementary After School Program";
										typeLookup[9]="American Athletic Union";
										typeLookup[10]="USAIGC";
										typeLookup[11]="Unaffiliated/Other";
										typeLookup[12]="American Gymnastics Association";
										typeLookup[13]="Texas Amateur Athletic Federation";
										typeLookup[14]="New or Proposed College";
										typeLookup[15]="Collegiate Tumbling and Acrobatics";
										typeLookup[16]="Potential High School";
										typeLookup[17]="Potential Middle School";
										typeLookup[18]="Gymnastics Ireland";
										typeLookup[19]="British Gymnastics";
									
									var typeEditor = function(cell, onRendered, success, cancel){
										//cell - the cell component for the editable cell
										//onRendered - function to call when the editor has been rendered
										//success - function to call to pass the successfuly updated value to Tabulator
										//cancel - function to call to abort the edit and return to a normal cell
										//editorParams - params object passed into the editorParams column definition property
										
										//create and style editor
										var editor = document.createElement("select");

										var opt;
										for (var ID in typeLookup){
											opt = document.createElement('option');
											opt.value = ID;
											opt.innerHTML = typeLookup[ID];
											editor.appendChild(opt);
										}
										
										//create and style input
										editor.style.padding = "3px";
										editor.style.width = "100%";
										editor.style.boxSizing = "border-box";

										//Set value of editor to the current value of the cell when the editor opens
										var curVal = cell.getRow().getData().ClubTypeID;
										editor.value = curVal; //need to change per keyval

										//set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
										onRendered(function(){
											editor.focus();
											editor.style.height = "100%";
										});

										//when the value has been set, trigger the cell to update
										editor.onblur = function(e){
											success(typeLookup[editor.value]);
											cell.getRow().getCell("ClubTypeID").setValue(editor.value);//update hidden row
											loadProgramData();
										};
										
										editor.onchange = function(e){
											success(typeLookup[editor.value]);
											cell.getRow().getCell("ClubTypeID").setValue(editor.value);//update hidden row
											loadProgramData();
										};

										//return the editor element
										return editor;
									}
									
									$("#programTable").tabulator({
										layout: "fitDataFill",
										responsiveLayout:false,
										columns:[
											{title:"ID",					field:"ProgramID", 		visible:false	},
											{title:"Alt Name",				field:"Name" 				},
											{title:"Type",					field:"ClubTypeID" ,		visible:false	},
											{title:"Type",					field:"ClubTypeName" ,		editor:typeEditor	},
											{title:"M/W", 					field:"Gender",			mutator:genderMutator, 	formatter:"plaintext",	sorter:"string", editor:"select", editorParams:{values:{"2":"M","1":"W"}}},
											{title:"Division",				field:"Division" 			},
											{title:"Phone",					field:"Phone",			editor:"input"		},
											{title:"Email",					field:"Email",			editor:"input",	formatter:"plaintext",	sorter:"string",  				},
											{title:"Website",				field:"Website",		editor:"input",	formatter:"plaintext",	sorter:"string",  				},
											{title:"Twitter",				field:"Twitter",		editor:"input",	formatter:"plaintext",	sorter:"string",  				},
											{title:"Instagram",				field:"Instagram",		editor:"input",	formatter:"plaintext",	sorter:"string",  				},
											{title:"Facebook",				field:"Facebook",		editor:"input",	formatter:"plaintext",	sorter:"string",  				},
											{title:"YouTube",				field:"YouTube",		editor:"input",	formatter:"plaintext",	sorter:"string",  				},
											{title:"Snapchat",				field:"Snapchat",		editor:"input",	formatter:"plaintext",	sorter:"string",  				},
											{title:"Inactive",				field:"Inactive",		editor:"input",	formatter:"plaintext",	sorter:"string",  				}
										],
										index:"ID",
										cellEdited: function(cell){
										
										var row = cell.getRow();
										var data = row.getData();
										
										if(cell.getField()=="ClubTypeID")
										{
											changeType(data.ProgramID,data.ClubTypeID);
										}
										else if(cell.getField()=="Phone")
										{
											changePhone(data.ProgramID,data.Phone);
										}
										else if(cell.getField()=="Email")
										{
											changeEmail(data.ProgramID,data.Email);
										}
									}
									});
								</script>
						<?php
						}
						else
						{
							if(userIsLoggedIn())
								echo "You do not have permission necessary to access this page.";
							display_login();
						}
						?>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>
