<?php include("headers.php"); ?>
<?php include("auth.php"); ?>
<?php include("globals.php"); ?>
<?php include("registrationAjax.php"); ?>

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
			scored = 1
		Order BY
			Date Desc
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

function getValidClubs()
{
	global $conn;
	$stmt= $conn->prepare("
					SELECT 
						ID, 
						coalesce(Identifiers_Institutions.AltName,Identifiers_Institutions.Name) As Name
					FROM 
						Identifiers_Institutions
					WHERE 
						(ID IN (Select InstitutionID From Identifiers_Programs Where ClubType IN (1,7,14)) OR
						ID IN (78,6203,7023)) AND
						Identifiers_Institutions.State IN ('TX','OK','LA','KS')
					ORDER BY 
						Name ASC
				;");
	$stmt->execute();
	
	if ($stmt->rowCount() > 0)
	{
		return $stmt;
	}
	else
	{
		return false;
	}
}
?>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.min.js"></script>

<script>		

	function registerPerson()
	{
		//first validate that the fields are filled out
		var firstnameEntered = (document.getElementById("newWFirst").value != "");
		var lastnameEntered = (document.getElementById("newWLast").value != "");
		var IDLoaded = (document.getElementById("newWId").value != "");
		var teamSelected = (document.getElementById("newPersonWTeam").value != "");
		var teamDesignation = (document.getElementById("newPersonWDesignation").value != "");
		
		//then add it
		var gender = "X";
		if(document.getElementById("newPersonWTeam").options[document.getElementById("newPersonWTeam").selectedIndex].text.indexOf("Women") >= 0 )
		{
			gender = "Women";
		}
		if(document.getElementById("newPersonWTeam").options[document.getElementById("newPersonWTeam").selectedIndex].text.indexOf("Men") >= 0 )
		{
			gender = "Men";
		}
		if(firstnameEntered && lastnameEntered && teamSelected && IDLoaded && teamDesignation)
		{
			var enablePerTeamCompetitionRegistration = false;
			//save to db
			var iPerson = document.getElementById("newWId").value;
			var iInstitution = document.getElementById("clubBeingRegistered").value;
			var iCompetition = document.getElementById("newPersonWTeam").value;
			var iDesignation = document.getElementById("newPersonWDesignation").value;
			var iTeam = 0;
			var iGender = 1;
			var iEvents;
			if(gender == "Men")
			{
				iGender = 2;
				iEvents = {
							/*ID from apparatus. Need to dynamically create when more disciplines added.*/
							1: document.getElementById("newMFX").checked,
							2: document.getElementById("newMPH").checked,
							3: document.getElementById("newMSR").checked,
							4: document.getElementById("newMVT").checked,
							5: document.getElementById("newMPB").checked,
							6: document.getElementById("newMHB").checked
						};
				iEventCountFlags = {
							1: !enablePerTeamCompetitionRegistration,
							2: !enablePerTeamCompetitionRegistration,
							3: !enablePerTeamCompetitionRegistration,
							4: !enablePerTeamCompetitionRegistration,
							5: !enablePerTeamCompetitionRegistration,
							6: !enablePerTeamCompetitionRegistration
						};
			}
			else if(gender == "Women")
			{
				iGender = 1;
				iEvents = {
							8: document.getElementById("newWVT").checked,
							9: document.getElementById("newWUB").checked,
							10: document.getElementById("newWBB").checked,
							11: document.getElementById("newWFX").checked
						};
				iEventCountFlags = {
							8: !enablePerTeamCompetitionRegistration,
							9: !enablePerTeamCompetitionRegistration,
							10: !enablePerTeamCompetitionRegistration,
							11: !enablePerTeamCompetitionRegistration
						};		
			}
			else
			{
				iGender = 3;
				iEvents = {
							12: document.getElementById("newMenLecture").checked,
							13: document.getElementById("newWomenLecture").checked,
							14: document.getElementById("newSocial").checked,
							15: document.getElementById("newClinicWorkout").checked,
							16: document.getElementById("newMeetLecture").checked
						};
				iEventCountFlags = {
							12: 0,
							13: 0,
							14: 0,
							15: 0,
							16: 0
						};
			}
			
			var minor = document.getElementById("under18").checked;
			
			var saved;
			saved = savePersonRegistration(iPerson,iInstitution,iCompetition,iTeam,iGender,iEvents,iEventCountFlags,true,minor,iDesignation);
			//alert(saved+"injustdrawrow");
			if(saved) //if it comes back true, draw it on the table.
			{
				if(gender == "Women")
				{
					loadScoreData(1);
				}
				else if(gender == "Men")
				{
					loadScoreData(2);
				}
				else
				{
					loadScoreData(3);
				}
				//then clear out all data.
				if(true)
				{
					document.getElementById("newWFirst").value = "";
					document.getElementById("newWLast").value = "";
					document.getElementById("newWMiddle").value = "";
					document.getElementById("newWId").value = "";
				}
			}
			else
			{
				;//nothing, an error should have already been displayed.
			}
		}
		else
		{
			var message = "empty";
			if(!IDLoaded)
				message = "You need to click an existing person (from the popup menu as you type), or click the add new person button for new people. You will see a number populate on the left.";
			else
				message = "You need to both select the team and make sure you've clicked on a name that pops up.";
			alert(message);
		}
	}

	function addNewPerson()
	{
		var nameIsValid = false;
		var schoolIsValid = false;
		var iLastName = document.getElementById("newWLast").value;
		var iFirstName = document.getElementById("newWFirst").value;
		var iMiddleName = document.getElementById("newWMiddle").value;
		
		var iPhone = "";
		var iGender = "";
		var iEmail = "";
		
		var iInstitutionID;
		var InstitutionName;
		
		//ok I need to get the season from the meet name by first looking for the open parenthesis and then taking 4 chars after
		var theString = document.getElementById('meetSelectMenu').options[document.getElementById('meetSelectMenu').selectedIndex].text;
		var pos = theString.indexOf("(");
		var season = theString.substring(pos+1,pos+5);
		//if(d.getMonth()>=7){season++;} //january starts at zero.
		
		var schoolsLookup = [];
		<?php
			$stmtClubs = getValidClubs();
			while($row = $stmtClubs->fetch(PDO::FETCH_ASSOC))
			{
				echo "schoolsLookup['".$row['ID']."'] = '". addslashes($row['Name'])."';";
			}
		?>
		
		//first let's do some data validation:
		if ((iLastName.length > 1) && (iFirstName.length >= 1)) //I have encountered someone with a single letter first name and two letter last names are common.
		{
			nameIsValid = true;
		}
		else
		{
			alert("Please make sure both the last name and first name are entered.");
			//exit function.
		}
		
		if(nameIsValid)
		{
			//then show a popup asking if its for a different team
			if (confirm("Does this person attend " + schoolsLookup[document.getElementById('clubBeingRegistered').value] + "?") == true)
			{
				iInstitutionID = document.getElementById("clubBeingRegistered").value;
				InstitutionName = schoolsLookup[document.getElementById("clubBeingRegistered").value];
				//todo: solve scope issues and remove code duplicate. 
				if(confirm("Pressing ok will create a brand new gymnast for " + iFirstName + " " + iLastName + " who will start out with affiliation for " + InstitutionName + " for the " + season + " season. If this is not correct press cancel."))
					{
						schoolIsValid = true;
						
						if(schoolIsValid)
						{
							$.ajax({
								type: 'POST',
								url: "registrationAjax.php",
								async: false,
								data: {
									addNewPersonToDatabase: 1,
									lastName: iLastName,
									firstName: iFirstName,
									middleName: iMiddleName,
									institutionID: iInstitutionID,
									gender: iGender,
									phone: iPhone,
									email: iEmail,
									season: season
								},
								//dataType: 'json',
								success: function () {
									//alert("removed");
									changeYearClub();
									document.getElementById('popupSelect').selectedIndex = 0;
									
								},
								error: function (textStatus, errorThrown) {
									//console.log(errorThrown);
									alert("error adding person");
								}
							});
						}
						//reset. Need to put in frunction.
						document.getElementById("newWFirst").value = "";
						document.getElementById("newWLast").value = "";
						document.getElementById("newWMiddle").value = "";
						document.getElementById("newWId").value = "";
						document.getElementById("newWGender").value = "";
						document.getElementById("newWPhone").value = "";
						document.getElementById("newWEmail").value = "";
					}
			}
			else //make a select popup
			{
				/*$("#popUpDiv").css("display","table");
				$("#popupSelect").change(function(e) {
					
					//right now InstID is the top menu
					iInstitutionID = $("#popupSelect").val();
					//now it is what was selected
										
					InstitutionName = schoolsLookup[$("#popupSelect").val()];
					if(confirm("Pressing ok will create a brand new gymnast for " + iFirstName + " " + iLastName + " who will start out with affiliation for " + InstitutionName + " for the " + season + " season. If this is not correct press cancel."))
					{
						schoolIsValid = true;
						
						if(schoolIsValid)
						{
							$.ajax({
								type: 'POST',
								url: "registrationAjax.php",
								async: false,
								data: {
									addNewPersonToDatabase: 1,
									lastName: iLastName,
									firstName: iFirstName,
									middleName: iMiddleName,
									institutionID: iInstitutionID,
									gender: iGender,
									phone: iPhone,
									email: iEmail
								},
								//dataType: 'json',
								success: function () {
									//alert("removed");
									document.getElementById('popupSelect').selectedIndex = 0;
								},
								error: function (textStatus, errorThrown) {
									//console.log(errorThrown);
									alert("error adding person");
								}
							});
						}

						//reset. Need to put in frunction.
						document.getElementById("newWFirst").value = "";
						document.getElementById("newWLast").value = "";
						document.getElementById("newWMiddle").value = "";
						document.getElementById("newWId").value = "";
						document.getElementById("newWGender").value = "";
						document.getElementById("newWPhone").value = "";
						document.getElementById("newWEmail").value = "";
					}
					else{
						
					}
					$("#popUpDiv").hide();
				});	*/
				alert("Please select the institution they actually attend from the top menu.");
			}
		}		
	}

	function updatePersonDesignation(iPerson,iCompetition,iDesignation,iGender,iOldDesignation)
	{
		//i should maybe add some data validation here, or on the postscript
		
		{
			$.ajax({
				type: 'POST',
				url: "registrationAjax.php",
				data: {
					changePersonsDesignation: 1,
					person: iPerson,
					competition: iCompetition,
					designation: iDesignation,
					oldDesignation: iOldDesignation,
					institution: document.getElementById("clubBeingRegistered").value
				},
				//dataType: 'json',
				success: function () {
					//alert("saved");
				},
				error: function (textStatus, errorThrown) {
					alert("error updating person");
				}
			});
		}
		loadScoreData(iGender); //sync problems
		loadTeamData(iGender);
	}

	function updatePersonCompetition(iPerson,iCompetition,iOldCompetition,iGender)
	{
		{
			$.ajax({
				type: 'POST',
				url: "registrationAjax.php",
				data: {
					changePersonsCompetition: 1,
					person: iPerson,
					newCompetition: iCompetition,
					oldCompetition: iOldCompetition,
					institution: document.getElementById("clubBeingRegistered").value
				},
				//dataType: 'json',
				success: function () {
					//alert("saved");
				},
				error: function (textStatus, errorThrown) {
					alert("error registering person");
				}
			});
		}
		loadScoreData(iGender); //sync problems
		loadTeamData(iGender);
	}

	function savePersonRegistration(iPerson,iInstitution,iCompetition,iTeam,iGender,iEvents,iEventCountFlags,iFirstAdd,iUnder18,iDesignation)
	{
		if(iUnder18 === undefined)
			iUnder18=false;
		
		returnVal = false;
		//i should maybe add some data validation here, or on the postscript
		$.ajax({
			type: 'POST',
			async: false,
			url: "registrationAjax.php",
			data: {
				registerPersonForCompetition: 1,
				person: iPerson,
				institution: iInstitution,
				competition: iCompetition,
				team: iTeam,
				gender: iGender,
				events: iEvents,
				eventCountFlags: iEventCountFlags,
				firstAdd: iFirstAdd,
				minor: iUnder18,
				designation: iDesignation
			},
			//dataType: 'json',
			success: function (data) {
				data = JSON.parse(data);
				if((data.Error == true)||(data.Error == "true"))
				{
					//kickback 8 per event errror
						//and uncheck the box.
					alert(data.Message);
					returnVal = false;
				}
				else
				{
					//alert("sucess");
					returnVal = true;
				}
			},
			error: function (textStatus, errorThrown) {
                alert("error registering person");
				returnVal = true;
            }
		});
		loadScoreData(iGender);
		return returnVal;
	}
	
	function saveScore(iperson, iscore, icompetition, ievent)
	{
		$.ajax({
			type: 'POST',
			url: "scoreAjax.php",
			async: false,
			data: {
				updatePersonScore: 1,
				person: iperson,
				score: iscore,
				competition: icompetition,
				event: ievent
			},
			dataType: 'json',
			success: function (data) {
				//update team totals
				loadTeamData(1);
				loadTeamData(2);
			},
			error: function (textStatus, errorThrown) {
				//console.log(errorThrown);
				alert("error saving score");
				//ALSO CLEAR THE SCORE
			}
		});
	}
	
	function unregisterCompetitor(iPerson, iCompetition)
	{
		//console.log(iPerson + " " + iCompetition);
		{
			$.ajax({
				type: 'POST',
				url: "registrationAjax.php",
				async: false,
				data: {
					unregisterPersonFromCompetition: 1,
					person: iPerson,
					competition: iCompetition,
					institution: document.getElementById("clubBeingRegistered").value
				},
				//dataType: 'json',
				success: function () {
					//alert("removed");
					//loadTeamData();
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error unregistering person");
				}
			});
		}
	}
	
	function loadScoreData(iGender)
	{
		//run only if a valid meet and club have been selected.
		//if((document.getElementById("clubBeingRegistered").value != "")&&(document.getElementById("meetSelectMenu").value != ""))
		if(true)
		{
			$.ajax({
				type: 'POST',
				url: "scoreAjax.php",
				async: false,
				data: {
					getAllScoresForMeetGender: 1,
					institutionID: false,
					meetID: document.getElementById("meetSelectMenu").value,
					genderID: iGender
				},
				dataType: 'json',
				success: function (data) {
					if(iGender == 2)
						$("#menScoreTable").tabulator("setData", data);
					if(iGender == 1)
						$("#womenScoreTable").tabulator("setData", data);
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading "+iGender+" person data");
				}
			});
		}
	}
	function loadTeamData(iGender)
	{
		//run only if a valid meet and club have been selected.
		//if((document.getElementById("clubBeingRegistered").value != "")&&(document.getElementById("meetSelectMenu").value != ""))
		if(true)
		{
			$.ajax({
				type: 'POST',
				url: "scoreAjax.php",
				async: false,
				data: {
					getTeamScoreResults: 1,
					meetID: document.getElementById("meetSelectMenu").value,
					genderID: iGender
				},
				dataType: 'json',
				success: function (data) {
					if(iGender == 2)
						$("#menTeamScoreTable").tabulator("setData", data);
					if(iGender == 1)
						$("#womenTeamScoreTable").tabulator("setData", data);
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading "+iGender+" team data");
				}
			});
		}
	}
	
	$(document).ready(function(){
		$("#newWLast").autocomplete({
			delay:350,
			source: "nameAutocomplete.php",
			dataType: "json",
			//source: data,
			minLength: 1,
			select: function (event, ui) {
											$('#newWId').val(ui.item.value); 
											$('#newWLast').val(ui.item.lastName);
											$('#newWFirst').val(ui.item.firstName);
											$('#newWMiddle').val(ui.item.middleName);
											return false; //prevent widget from updating value
										},
			focus: function(event, ui) { //set for arrow keys
				$("#newWLast").val(ui.item.label);
				return false;
			},
			messages: {
				noResults: 'That name was not found, please add them to the database.',
				results: function() {}
			}
		});
		
		//if(selfRegister)
			//activateSelfRegister();
		
	});
	
	function updateEventSelectMenu()
	{
		
		var gender = "men";
		var val = document.getElementById("newPersonWTeam").options[document.getElementById("newPersonWTeam").selectedIndex].text;
		if(val.indexOf("Women") >= 0)
			gender = "women";
		
		//store the old/initial value in a global var then the checkmarks won't disappear
		if(previousEventSelectMenuGender == gender)
			return false;
		else
			previousEventSelectMenuGender = gender;
		
		//ok update it		
		if(!eventOnly)
		{
			if(gender == "men")
			{
				document.getElementById("eventSelectMenu").innerHTML = ""+
										'FX:<input type = "checkbox" onchange = "addPersonEventAACheck(\'men\')" id = "newMFX"/>' +
										'PH:<input type = "checkbox" onchange = "addPersonEventAACheck(\'men\')" id = "newMPH"/>' +
										'SR:<input type = "checkbox" onchange = "addPersonEventAACheck(\'men\')" id = "newMSR"/>' +
										'VT:<input type = "checkbox" onchange = "addPersonEventAACheck(\'men\')" id = "newMVT"/>' +
										'PB:<input type = "checkbox" onchange = "addPersonEventAACheck(\'men\')" id = "newMPB"/>' +
										'HB:<input type = "checkbox" onchange = "addPersonEventAACheck(\'men\')" id = "newMHB"/>' +
										'AA:<input type = "checkbox" onchange = "addPersonAACheck(\'men\')" id = "newMAA"/>';
			}
			else if(gender == "women")
			{
				document.getElementById("eventSelectMenu").innerHTML = ""+
										'VT:<input type = "checkbox" onchange = "addPersonEventAACheck(\'women\')" id = "newWVT"/>' +
										'UB:<input type = "checkbox" onchange = "addPersonEventAACheck(\'women\')" id = "newWUB"/>' +
										'BB:<input type = "checkbox" onchange = "addPersonEventAACheck(\'women\')" id = "newWBB"/>' +
										'FX:<input type = "checkbox" onchange = "addPersonEventAACheck(\'women\')" id = "newWFX"/>' +
										'AA:<input type = "checkbox" onchange = "addPersonAACheck(\'women\')" id = "newWAA"/>';
			}
		}
		else
		{
			document.getElementById("eventSelectMenu").innerHTML = ""+
										'Mens Lecture:<input type = "checkbox" id = "newMenLecture"/>' +
										'Womens Lecure:<input type = "checkbox" id = "newWomenLecture"/>' +
										'Meet Lecure:<input type = "checkbox" id = "newMeetLecture"/>' +
										'Social:<input type = "checkbox" id = "newSocial"/>' +
										'Workout:<input type = "checkbox" id = "newClinicWorkout"/>'
		}		
	}
	
	
	function updateAllowedTeams()
	{
		if((document.getElementById("clubBeingRegistered").value != "")&&(document.getElementById("meetSelectMenu").value != "")) //in case we add a select club disabled option.
		{
			optsLookup = []; //clear and repopulate.
			//var mOpts = document.createElement('select');
			//var wOpts = document.createElement('select');
			mOpts = [];
			wOpts = [];
			xOpts = [];
			eventOnly = false;
			
			var meet = document.getElementById('meetSelectMenu').value;
			var displayName; var ID;
			var opt;
			$("#newPersonWTeam").empty();
			$.ajax({
				type: 'POST',
				url: "registrationAjax.php",
				data: {
					getCompetitionsForMeet: meet
				},
				dataType: 'json',
				success: function (data) {
					opt = document.createElement('option');
					opt.value = "";
					opt.innerHTML = "Select Team";
					opt.disabled = true;
					opt.selected = true;
					document.getElementById('newPersonWTeam').appendChild(opt);
					for (var key in data){
						displayName = data[key].Division + " " + data[key].Level + " " + data[key].Gender;
						ID = data[key].ID;
						opt = document.createElement('option');
						opt.value = ID;
						opt.innerHTML = displayName;
						document.getElementById('newPersonWTeam').appendChild(opt);
						optsLookup[ID] = displayName;
						if(data[key].Gender.indexOf("Men") >= 0) //cant use .includes b/c IE11
							mOpts[ID] = displayName;
						else if(data[key].Gender.indexOf("Women") >= 0)
							wOpts[ID] = displayName;
						else
						{	
							xOpts[ID] = displayName;
							eventOnly = true;
						}
					}
				}
			});
			
			document.getElementById("eventSelectMenu").innerHTML = "";
			previousEventSelectMenuGender = "";
			
			//load anyone already registered
			//updateFeesAndDates();
			
			loadScoreData(1);
			loadScoreData(2);
			loadTeamData(1);
			loadTeamData(2);
			
		//	toggleEventOnlyTable("on"); //need to fix, see the keep note.
		}
	}
	
	var teamEditor = function(cell, onRendered, success, cancel){
		//cell - the cell component for the editable cell
		//onRendered - function to call when the editor has been rendered
		//success - function to call to pass the successfuly updated value to Tabulator
		//cancel - function to call to abort the edit and return to a normal cell
		//editorParams - params object passed into the editorParams column definition property
		var gender = cell.getRow().getData().GenderID;
		
		//create and style editor
		var editor = document.createElement("select");
		
		//populate it
		var optionArray;
		if(gender == 2)
			optionArray = mOpts;
		else if(gender == 1)
			optionArray = wOpts;
		else
			optionArray = xOpts;
		
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
		var curVal = cell.getRow().getData().CompetitionID;
		editor.value = curVal; //need to change per keyval

		//set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
		onRendered(function(){
			editor.focus();
			editor.style.height = "100%";
		});

		//when the value has been set, trigger the cell to update
		editor.onblur = function(e){
			success(optsLookup[editor.value]);
			cell.getRow().getCell("CompetitionID").setValue(editor.value);//update hidden row
			loadScoreData(gender);
		};
		
		editor.onchange = function(e){
			success(optsLookup[editor.value]);
			cell.getRow().getCell("CompetitionID").setValue(editor.value);//update hidden row
			loadScoreData(gender);
		};

		//return the editor element
		//if(teamMenuEditable)
		if(true)
			return editor;
		else
			return false;
	}
	
	var designationEditor = function(cell, onRendered, success, cancel){
		//cell - the cell component for the editable cell
		//onRendered - function to call when the editor has been rendered
		//success - function to call to pass the successfuly updated value to Tabulator
		//cancel - function to call to abort the edit and return to a normal cell
		//editorParams - params object passed into the editorParams column definition property
		var gender = cell.getRow().getData().GenderID;
		//create and style editor
		var editor = document.createElement("select");
		
		//populate it
		optionArray = [];
		optionArray['A'] = "A";
		optionArray['B'] = "B";
		optionArray['C'] = "C";
		optionArray['D'] = "D";
		optionArray['E'] = "E";
		optionArray['F'] = "F";
		optionArray['G'] = "G";
		optionArray['H'] = "H";
		optionArray['I'] = "I";
		
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
		var curVal = cell.getRow().getData().Designation;
		editor.value = curVal; //need to change per keyval

		//set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
		onRendered(function(){
			editor.focus();
			editor.style.height = "100%";
		});

		//when the value has been set, trigger the cell to update
		editor.onblur = function(e){
			success(optionArray[editor.value]);
			loadScoreData(gender);
		};
		
		editor.onchange = function(e){
			success(optionArray[editor.value]);
			loadScoreData(gender);
		};

		//return the editor element
		//if(teamMenuEditable)
		if(true)
			return editor;
		else
			return false;
	}
	
	function loadMeetData()
	{
		updateAllowedTeams();
		
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
	
	.ui-autocomplete { position: absolute; cursor: default; background-color: #ffffff; z-index:30 !important; border: 2px solid #555555;}
	.ui-helper-hidden-accessible { display:none; }
	
	#popUpDiv{
		z-index: 100;
		position: fixed;
		padding:0;
		margin:0;
		background-color: rgba(123, 123,123, 0.8);
		display: none;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
	#middlePopUp {
		display: table-cell;
		padding-top:10%;
		width: 100%;
	}

	#popupSelect{
		display:block;
		margin:auto;	
		z-index: 1000;
		width:50%;
	}
	
	#fees{
		font-style: italic;
		font-weight: bold;
		color: #E00000;
	}
	
	.inner{
		max-width: initial !important;
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
								while($row = $stmtMeets->fetch(PDO::FETCH_ASSOC))
								{
									//if(isset($temp[$row['Hostclub']]))
									{
										echo "<option value = '".$row['ID']."'>".$row['MeetName']."(".$row['Date'].")</option>";
									}
								}
								echo "</select> <button onclick = 'loadMeetData();'>&#x21bb;</button><br/><br/>";
								
								
								$displaySpecific = (	(sizeof(getListOfUserCaptainPermissions()) > 0) || (sizeof(getListOfUserCoachPermissions()) > 0) || (sizeof(getListOfUserClubAdministrativePermissions()) > 0)	);
								$combinedArray = getListOfUserClubAdministrativePermissions();
								?>
								
								<input size = "2" disabled id = "newWId"/>
								<input size = "15" type = "text" id = "newWLast" placeholder = "Last Name"/>
								<input size = "12" type = "text" id = "newWFirst" placeholder = "First Name"/>
								<input size = "3" type = "text" id = "newWMiddle"  placeholder = "Middle" />
								<?php
									if(sizeof($combinedArray)>0) //this needs to kick us out somehow if we somehow got to the page but don't have appropriate perms.
									{
										echo "<select id = 'clubBeingRegistered'>";
										//foreach (getListOfUserCaptainPermissions() AS $clubID=>$clubName)
										foreach ($combinedArray AS $clubID=>$clubName)
										{
											echo "<option value = '".$clubID."'>".$clubName."</option>";
										}
										echo "</select>";	
									}
								?>
								<div class = "12u">
									<select style = "display:inline" id = "newPersonWTeam" onchange = "updateEventSelectMenu();">
										<option selected disabled value = "">Select a meet from the menu above</option>
									<select>
									<select style = "display:inline" id = "newPersonWDesignation">
										<option disabled>Team</option>
										<option value = "A">A</option>
										<option value = "B">B</option>
										<option value = "C">C</option>
										<option value = "D">D</option>
										<option value = "E">E</option>
										<option value = "F">F</option>
										<option value = "G">G</option>
										<option value = "H">H</option>
										<option value = "I">I</option>
									</select>
									
									<div style = "display:inline-block" id = "eventSelectMenu">
										
									</div>
									<div>
										<input type = "checkbox" name = "under18" id = "under18">Under 18 on date of event?</input>
										<br/>
									</div>
								</div>
								<button id = "addPerson" onclick = "registerPerson();">Register Selected Person for Meet</button> <button id = "addNewPerson" onclick ="addNewPerson();">Add New Person to Database</button> <!--button id = "editPersonW">Name Update</button--><br/>
								<div id="popUpDiv">
									<div id="middlePopUp">
										<select id="popupSelect">
											<option selected disabled>Please select the school they attend:</option>
											<?php
												$stmtClubs = getValidClubs();
												while($row = $stmtClubs->fetch(PDO::FETCH_ASSOC))
												{
													echo "<option value = '".$row['ID']."'>".$row['Name']."</option>";
												}
											?>
										</select>
									</div>
								</div>
								
								<h2>Womens Teams and Competitors:</h2>
								<div id="womenScoreTable"></div>
								<br/>
								<h2>Mens Teams and Competitors:</h2>
								<div id="menScoreTable"></div> <br>
								<br/>
								<h2>Team Scores:</h2>
								<div id="womenTeamScoreTable"></div> <br>
								<br/>
								<div id="menTeamScoreTable"></div> <br>
								<br/>
								<br/>
								<script type="text/javascript">
									$("#menScoreTable").tabulator({
										layout: "fitDataFill",
										groupBy: ["Team"],
										columns:[
											{title:"",		 		field:"Remove", 	formatter:"buttonCross", cellClick:function(e,cell){cell.getRow().delete();	}},
											{title:"ID", 			field:"ID", 		visible:false},
											{title:"Name",	 		field:"Name",	 	sorter:"string"},
											{title:"CompetitionID", field:"CompetitionID", 		visible:false},
											{title:"GenderID", 		field:"GenderID", 	visible:false},
											{title:"Comp", 			field:"Team",	 	editor:teamEditor},
											{title:"Team",	 		field:"Designation",editor:designationEditor},
											{title:"Team", 			field:"Institution",	 	},
											{title:"FX", 			field:"MFX",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3},	editor:"number"},
											{title:"PH", 			field:"MPH",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3},	editor:"number"},
											{title:"SR", 			field:"MSR",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3},	editor:"number"},
											{title:"VT", 			field:"MVT",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3},	editor:"number"},
											{title:"PB", 			field:"MPB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3},	editor:"number"},
											{title:"HB", 			field:"MHB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3},	editor:"number"},
											{title:"AA", 			field:"MAA",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
										],
										index:"ID",
										rowDeleted:function(row){
											var data = row.getData();
											//console.log(data.ID + " " + data.CompetitionID);
											unregisterCompetitor(data.ID,data.CompetitionID);
											loadScoreData(2); //because groupBy is broken.
										},

										//groupHeader:function(value, count, data, group){return data[0].Team;},
										//takes too long to reload everything. Just save it and update AA.
										cellEdited:function(cell){
											//This callback is called any time a cell is edited
											var row = cell.getRow();
											var data = row.getData();
											var col = cell.getColumn();
											var AAScore = 0;
											AAScore = (data.MFX) + (data.MPH) + (data.MSR) + (data.MVT) + (data.MPB) + (data.MHB);
											var changedEvent = "";
											//////////////////////////////
											/*ALL AROUND CALCULATE STUFF*/
											//////////////////////////////

											row.update({MAA:AAScore});
													
											///////////////////////////
											/*AND UPDATE STUFF in DB*/
											///////////////////////////
											data = row.getData(); //I need to refresh if any AA stuff was done?
											//var institution = document.getElementById("clubBeingRegistered").value;
											if(cell.getField()=="MFX")
											{ changedEvent = 1;	}
											if(cell.getField()=="MPH")
											{ changedEvent = 2;	}
											if(cell.getField()=="MSR")
											{ changedEvent = 3;	}
											if(cell.getField()=="MVT")
											{ changedEvent = 4;	}
											if(cell.getField()=="MPB")
											{ changedEvent = 5;	}
											if(cell.getField()=="MHB")
											{ changedEvent = 6; }
											
											if(cell.getField()=="Designation")
											{
												updatePersonDesignation(data.ID,data.CompetitionID,data.Designation,data.GenderID,cell.getOldValue()); 
											}
											else if(cell.getField()=="CompetitionID")
											{
												updatePersonCompetition(data.ID,data.CompetitionID,cell.getOldValue(),data.GenderID); //the competitionID is being updated on the onchange event. that comes first so I say if cell = competitionID. 
											}
											else if(cell.getField()!="")//all I need is person meet|competition|gender and event
											{ 
												saveScore(data.ID, cell.getValue(), data.CompetitionID, changedEvent);
											}
										},
									});
									
									$("#womenScoreTable").tabulator({
										layout: "fitDataFill",
										groupBy: ["Team"],
										columns:[
											{title:"",		 		field:"Remove", 	formatter:"buttonCross", cellClick:function(e,cell){cell.getRow().delete();	}},
											{title:"ID", 			field:"ID", 		visible:false},
											{title:"Name",	 		field:"Name",	 	sorter:"string"},
											{title:"CompetitionID", field:"CompetitionID", 		visible:false},
											{title:"GenderID", 		field:"GenderID", 	visible:false},
											{title:"Comp", 			field:"Team",	 	editor:teamEditor},
											{title:"Team",	 		field:"Designation",editor:designationEditor},
											{title:"Team", 			field:"Institution",	 	},
											{title:"VT", 			field:"WVT",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}, 	editor:"number"},
											{title:"UB", 			field:"WUB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}, 	editor:"number"},
											{title:"BB", 			field:"WBB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}, 	editor:"number"},
											{title:"FX", 			field:"WFX",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}, 	editor:"number"},
											{title:"AA", 			field:"WAA",	 	sorter:"number",	formatter:"money", 	formatterParams:{precision:3}},
										],
										index:"ID",
										rowDeleted:function(row){
											var data = row.getData();
											//console.log(data.ID + " " + data.CompetitionID);
											unregisterCompetitor(data.ID,data.CompetitionID);
											loadScoreData(1); //because groupBy is broken.
										},
										//groupHeader:function(value, count, data, group){return data[0].Team;},
										//takes too long to reload everything. Just save it and update AA.
										cellEdited:function(cell){
											//This callback is called any time a cell is edited
											var row = cell.getRow();
											var data = row.getData();
											var col = cell.getColumn();
											var AAScore = 0;
											AAScore = (data.WVT) + (data.WUB) + (data.WBB) + (data.WFX);
											var changedEvent = "";
											//////////////////////////////
											/*ALL AROUND CALCULATE STUFF*/
											//////////////////////////////

											row.update({WAA:AAScore});
													
											///////////////////////////
											/*AND UPDATE STUFF in DB*/
											///////////////////////////
											data = row.getData(); //I need to refresh if any AA stuff was done?
											//var institution = document.getElementById("clubBeingRegistered").value;
											if(cell.getField()=="WVT")
											{ changedEvent = 8;	}
											if(cell.getField()=="WUB")
											{ changedEvent = 9;	}
											if(cell.getField()=="WBB")
											{ changedEvent = 10;	}
											if(cell.getField()=="WFX")
											{ changedEvent = 11;	}
											
											
											if(cell.getField()=="Designation")
											{
												updatePersonDesignation(data.ID,data.CompetitionID,data.Designation,data.GenderID,cell.getOldValue()); 
											}
											else if(cell.getField()=="CompetitionID")
											{
												updatePersonCompetition(data.ID,data.CompetitionID,cell.getOldValue(),data.GenderID); //the competitionID is being updated on the onchange event. that comes first so I say if cell = competitionID. 
											}
											else if(cell.getField()!="")//all I need is person meet|competition|gender and event
											{ 
												saveScore(data.ID, cell.getValue(), data.CompetitionID, changedEvent);
											}
										},
									});
									
									$("#womenTeamScoreTable").tabulator({
										layout: "fitDataFill",
										groupBy: ["CompetitionName","Designation"],
										columns:[
											{title:"Team",	 		field:"InstitutionName",	 	sorter:"string"},
											{title:"Competition", 	field:"CompetitionName"},
											{title:"Designation", 	field:"Designation",	 	},
											{title:"Team Score", 	field:"WAA",	 	sorter:"number",	formatter:"money", 	formatterParams:{precision:3}},
											{title:"VT", 			field:"WVT",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"UB", 			field:"WUB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"BB", 			field:"WBB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"FX", 			field:"WFX",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"TEAM", 			field:"WAA",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											
										]
									});
									
									$("#menTeamScoreTable").tabulator({
										layout: "fitDataFill",
										groupBy: ["CompetitionName","Designation"],
										columns:[
											{title:"Team",	 		field:"InstitutionName",	 	sorter:"string"},
											{title:"Competition", 	field:"CompetitionName"},
											{title:"Designation", 	field:"Designation",	 	},
											{title:"Team Score", 	field:"MAA",	 	sorter:"number",	formatter:"money", 	formatterParams:{precision:3}},
											{title:"FX", 			field:"MFX",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"PH", 			field:"MPH",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"SR", 			field:"MSR",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"VT", 			field:"MVT",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"PB", 			field:"MPB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"HB", 			field:"MHB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"TEAM", 			field:"MAA",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
										]
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
