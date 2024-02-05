<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>

<?

function getMeets()
{
	if(getUserID() == 2669)
		$dateString = " YEAR(Date) >= 2008 AND ";
	else
		$dateString = " Date >= CURDATE() AND ";
	
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
			" . $dateString . "
			ID NOT IN (96,98)
		ORDER BY
			Date ASC
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
						Identifiers_Institutions.State IN ('TX','OK','LA','KS','AZ')
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

function userLoggedInNameParts(){
		global $con;
		
		if(userIsLoggedIn())
		{
			$query = "
					SELECT
						ID,
						LastName,
						FirstName,
						MiddleName
					FROM
						Identifiers_People
					WHERE
						ID = ?
				;";
			if($stmt = $con->prepare($query))
			{
				$userID = getUserID();
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
				$stmt->bind_result($ID,$Last,$First,$Middle);
			
				while($stmt->fetch())
				{
					$thename['ID'] = $ID;
					$thename['Last'] = $Last;
					$thename['Middle'] = $Middle;
					$thename['First'] = $First;
				}
			}	
		}			
		return $thename;
	}
	
?>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.js"></script>

<script type="text/javascript">
	var teamMenuEditable = true;
	var removeVisible = true;
	var lateDeadlineHasPassed = false;
	var deadlineHasPassed = false;
	var enablePerTeamCompetitionRegistration = false;
	var selfRegister = false;
	
	function loadRegData(iDiscipline)
	{
		//run only if a valid meet and club have been selected.
		if((document.getElementById("clubBeingRegistered").value != "")&&(document.getElementById("meetSelectMenu").value != ""))
		{
			//updateFeesAndDates();
			
			if(lateDeadlineHasPassed) //we are late.
			{
				removeVisible = false;
				$("#menRegTable").tabulator("hideColumn","Remove");
				$("#womenRegTable").tabulator("hideColumn","Remove");
				$("#clinicEventTable").tabulator("hideColumn","Remove");
			}
			else if(deadlineHasPassed) //closed
			{
				removeVisible = false;
				teamMenuEditable = false;
				document.getElementById("addPerson").disabled = true;
				$("#menRegTable").tabulator("hideColumn","Remove");
				$("#womenRegTable").tabulator("hideColumn","Remove");
				$("#clinicEventTable").tabulator("hideColumn","Remove");
			}
			else //still early
			{
				removeVisible = true;
				teamMenuEditable = true;
				document.getElementById("addPerson").disabled = false;
				$("#menRegTable").tabulator("showColumn","Remove");
				$("#womenRegTable").tabulator("showColumn","Remove");
				$("#clinicEventTable").tabulator("showColumn","Remove");
			}
			
			$.ajax({
				type: 'POST',
				url: "registrationAjax.php",
				async: false,
				data: {
					getTeamRegistrationForCompetition: 1,
					institutionID: document.getElementById("clubBeingRegistered").value,
					meetID: document.getElementById("meetSelectMenu").value,
					DisciplineID: iDiscipline
				},
				dataType: 'json',
				success: function (data) {
					if(iDiscipline == 2)
						$("#menRegTable").tabulator("replaceData", data);
					else if(iDiscipline == 1)
						$("#womenRegTable").tabulator("replaceData", data);
					else
						$("#clinicEventTable").tabulator("replaceData", data);
					loadTeamData();
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading "+iDiscipline+" team data");
				}
			});
		}
	}
	
	function loadTeamData()
	{
		//run only if a valid meet and club have been selected.
		if((document.getElementById("clubBeingRegistered").value != "")&&(document.getElementById("meetSelectMenu").value != ""))
		{
			$.ajax({
				type: 'POST',
				url: "registrationAjax.php",
				async: false,
				data: {
					getTeamHeaderData: 1,
					institutionID: document.getElementById("clubBeingRegistered").value,
					meetID: document.getElementById('meetSelectMenu').value
				},
				dataType: 'json',
				success: function (data) {
					$("#teamTable").tabulator("replaceData", data);
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading summary team data");
				}
			});
		}
	}
	
	function addNewPerson()
	{
		var nameIsValid = false;
		var schoolIsValid = false;
		var iLastName = document.getElementById("newWLast").value;
		var iFirstName = document.getElementById("newWFirst").value;
		var iMiddleName = document.getElementById("newWMiddle").value;
		
		var iInstitutionID;
		var InstitutionName;
		var d = new Date();
		var season = d.getFullYear();
		if(d.getMonth()>=7){season++;} //january starts at zero.
		
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
									institutionID: iInstitutionID
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
					}
			}
			else //make a select popup
			{
				$("#popUpDiv").css("display","table");
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
									institutionID: iInstitutionID
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
					}
					else{
						
					}
					$("#popUpDiv").hide();
				});			
			}
		}		
	}
	
	function addRemoveButton()
	{
		removeVisible = true;
		teamMenuEditable = true;
		document.getElementById("addPerson").disabled = false;
		$("#menRegTable").tabulator("showColumn","Remove");
		$("#womenRegTable").tabulator("showColumn","Remove");
		$("#clinicEventTable").tabulator("showColumn","Remove");
	}
	
	function unregisterCompetitor(iPerson, iCompetition, iDesignation)
	{
		//console.log(iPerson + " " + iCompetition);
		var canDo = true;
		if((selfRegister) && (iPerson != <?php echo getUserID(); ?>))
			canDo = false; //no more mr meeseeks
		if(canDo)
		{
			$.ajax({
				type: 'POST',
				url: "registrationAjax.php",
				async: false,
				data: {
					unregisterPersonFromCompetition: 1,
					person: iPerson,
					competition: iCompetition,
					institution: document.getElementById("clubBeingRegistered").value,
					designation: iDesignation
				},
				//dataType: 'json',
				success: function () {
					//alert("removed");
					loadTeamData();
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error unregistering person");
				}
			});
		}
	}
	
	function savePersonRegistration(iPerson,iInstitution,iCompetition,iTeam,iDiscipline,iEvents,iEventCountFlags,iFirstAdd,iUnder18,iDesignation)
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
				Discipline: iDiscipline,
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
		loadRegData(iDiscipline);
		return returnVal;
	}
	
	function savePersonRegistrationSingleEvent(iPerson,iInstitution,iCompetition,iEvent,iEventRegistered,iDiscipline,updateData,cell)
	{
		returnVal = false;
		//i should maybe add some data validation here, or on the postscript
		var canDo = true;
		if((selfRegister) && (iPerson != <?php echo getUserID(); ?>))
			canDo = false;
		if(canDo)
		{
			$.ajax({
				type: 'POST',
				async: false,
				url: "registrationAjax.php",
				data: {
					savePersonEventRegistration: 1,
					person: iPerson,
					competition: iCompetition,
					institution: iInstitution,
					theevent: iEvent,
					registered: iEventRegistered,
					designation: cell.getRow().getData().Designation
				},
				//dataType: 'json',
				success: function (data) {
					data = JSON.parse(data);
					if((data.Error == true)||(data.Error == "true"))
					{
						//kickback 8 per event errror
							//and uncheck the box.
						alert(data.Message);
						//cell.restoreOldValue();
						cell.setValue(!cell.getValue());
					}
					else
					{
						//alert("sucess");
						returnVal = true;
					}
				},
				error: function (textStatus, errorThrown) {
					alert("error updating event");
					//cell.restoreOldValue();
					cell.setValue(!cell.getValue());
				}
			});
		}
		//replace this with timed polling
		//if(updateData)
			//loadRegData(iDiscipline);
		return returnVal;
	}
	
	function savepersonregistrationCountsForTeamSingleEvent(iPerson,iInstitution,iCompetition,iEvent,iEventCount,iDiscipline)
	{
		returnVal = false;
		//i should maybe add some data validation here, or on the postscript
		var canDo = true;
		if((selfRegister) && (iPerson != <?php echo getUserID(); ?>))
			canDo = false; //no more mr meeseeks
		if(canDo)
		{
			$.ajax({
				type: 'POST',
				async: false,
				url: "registrationAjax.php",
				data: {
					savePersonEventCountsRegistration: 1,
					person: iPerson,
					competition: iCompetition,
					institution: iInstitution,
					theevent: iEvent,
					counts: iEventCount,
					designation: cell.getRow().getData().Designation
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
					alert("error updating event");
					returnVal = true;
				}
			});
		}
		//if(true)
			//loadRegData(iDiscipline);
		return returnVal;
	}
	
	function enableTeamPerEventFlags()
	{
		//ok first just for this year you're either tgc admin, or youre a big team (UT & TAMU) otherwise don't confuse anyone, ok?
		<?php //userIsExecutiveAdministrator(); ?>
		if(enablePerTeamCompetitionRegistration && ((document.getElementById("clubBeingRegistered").value == 1)||(document.getElementById("clubBeingRegistered").value == 2)||(document.getElementById("clubBeingRegistered").value == 4)||(document.getElementById("clubBeingRegistered").value == 8)))
		{
			$("#menRegTable").tabulator("showColumn","MFXCount");
			$("#menRegTable").tabulator("showColumn","MPHCount");
			$("#menRegTable").tabulator("showColumn","MSRCount");
			$("#menRegTable").tabulator("showColumn","MVTCount");
			$("#menRegTable").tabulator("showColumn","MPBCount");
			$("#menRegTable").tabulator("showColumn","MHBCount");
			
			$("#womenRegTable").tabulator("showColumn","WVTCount");
			$("#womenRegTable").tabulator("showColumn","WUBCount");
			$("#womenRegTable").tabulator("showColumn","WBBCount");
			$("#womenRegTable").tabulator("showColumn","WFXCount");
		}
		else
		{
			$("#menRegTable").tabulator("hideColumn","MFXCount");
			$("#menRegTable").tabulator("hideColumn","MPHCount");
			$("#menRegTable").tabulator("hideColumn","MSRCount");
			$("#menRegTable").tabulator("hideColumn","MVTCount");
			$("#menRegTable").tabulator("hideColumn","MPBCount");
			$("#menRegTable").tabulator("hideColumn","MHBCount");
			
			$("#womenRegTable").tabulator("hideColumn","WVTCount");
			$("#womenRegTable").tabulator("hideColumn","WUBCount");
			$("#womenRegTable").tabulator("hideColumn","WBBCount");
			$("#womenRegTable").tabulator("hideColumn","WFXCount");
		}
	}
	
	function updateTeamOptions(iTeamScore,iCompetition,iInstitution,iDesignation)
	{
		//i should maybe add some data validation here, or on the postscript
		var canDo = true;
		if(selfRegister)
			canDo = false; //no more mr meeseeks
		if(canDo)
		{
			$.ajax({
				type: 'POST',
				url: "registrationAjax.php",
				data: {
					updateTeamScoreOption: 1,
					hasTeamScore: iTeamScore,
					competitionID: iCompetition,
					institutionID: iInstitution,
					teamDesignation: iDesignation
				},
				//dataType: 'json',
				success: function () {
					//alert("saved");
					loadTeamData();
				},
				error: function (textStatus, errorThrown) {
					alert("error registering person");
				}
			});
		}
		else
		{
			loadTeamData();
		}
	}
	
	function updatePersonDesignation(iPerson,iCompetition,iDesignation,iDiscipline,iOldDesignation)
	{
		//i should maybe add some data validation here, or on the postscript
		var canDo = true;
		if((selfRegister) && (iPerson != <?php echo getUserID(); ?>))
			canDo = false; //no more mr meeseeks
		if(canDo)
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
		loadRegData(iDiscipline); //sync problems
		loadTeamData();
	}
	
	function updatePersonCompetition(iPerson,iCompetition,iOldCompetition,iDiscipline,iDesignation)
	{
		//i should maybe add some data validation here, or on the postscript
		var canDo = true;
		if((selfRegister) && (iPerson != <?php echo getUserID(); ?>))
			canDo = false; 
		if(canDo)
		{
			$.ajax({
				type: 'POST',
				url: "registrationAjax.php",
				data: {
					changePersonsCompetition: 1,
					person: iPerson,
					newCompetition: iCompetition,
					oldCompetition: iOldCompetition,
					institution: document.getElementById("clubBeingRegistered").value,
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
				}
			});
		}
		loadRegData(iDiscipline); //sync problems
	}

	var mOpts;
	var wOpts;
	var xOpts;
	var optsLookup = [];
	var eventOnly = false;
	
	var checkMarkEditor = function(cell, onRendered, success, cancel){
		if(teamMenuEditable)
			return "<input type = 'checkbox' />";
		else
			return false;
	};
	
	var teamEditor = function(cell, onRendered, success, cancel){
		//cell - the cell component for the editable cell
		//onRendered - function to call when the editor has been rendered
		//success - function to call to pass the successfuly updated value to Tabulator
		//cancel - function to call to abort the edit and return to a normal cell
		//editorParams - params object passed into the editorParams column definition property
		var Discipline = cell.getRow().getData().DisciplineID;
		
		//create and style editor
		var editor = document.createElement("select");
		
		//populate it
		var optionArray;
		if(Discipline == 2)
			optionArray = mOpts;
		else if(Discipline == 1)
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
			loadRegData(Discipline);
		};
		
		editor.onchange = function(e){
			success(optsLookup[editor.value]);
			cell.getRow().getCell("CompetitionID").setValue(editor.value);//update hidden row
			loadRegData(Discipline);
		};

		//return the editor element
		if(teamMenuEditable)
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
		var Discipline = cell.getRow().getData().DisciplineID;
		//create and style editor
		var editor = document.createElement("select");
		
		//populate it
		optionArray = [];
		optionArray['A'] = "A";
		/*optionArray['B'] = "B";
		optionArray['C'] = "C";
		optionArray['D'] = "D";
		optionArray['E'] = "E";
		optionArray['F'] = "F";
		optionArray['G'] = "G";
		optionArray['H'] = "H";
		optionArray['I'] = "I";*/
		
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
			loadRegData(Discipline);
		};
		
		editor.onchange = function(e){
			success(optionArray[editor.value]);
			loadRegData(Discipline);
		};

		//return the editor element
		if(teamMenuEditable)
			return editor;
		else
			return false;
	}
	
	var teamEditorOld = function(cell, onRendered, success, cancel){
		//cell - the cell component for the editable cell
		//onRendered - function to call when the editor has been rendered
		//success - function to call to pass the succesfully updated value to Tabulator
		//cancel - function to call to abort the edit and return to a normal cell

		//create and style editor
		var Discipline = cell.getRow().getData().DisciplineID;
		var editor;
		if(Discipline == 2)
			editor = $(mOpts);
		else if(Discipline == 1)
			editor = $(wOpts);
		else
			editor = $(xOpts);
		
		editor.css({
			"padding":"3px",
			"width":"100%",
			"box-sizing":"border-box",
		});

		//Set value of editor to the current value of the cell when the editor opens
		var curVal = cell.getRow().getData().CompetitionID;
		editor.val(curVal); //need to change per keyval

		//set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
		onRendered(function(){
			editor.focus();
			editor.css("height","100%");
		});

		//when the value has been set, trigger the cell to update
		editor.on("change blur", function(e){
			success(optsLookup[editor.val()]);
			cell.getRow().getCell("CompetitionID").setValue(editor.val());//update hidden row
			loadRegData(Discipline);
		});

		//return the editor element
		if(teamMenuEditable)
			return editor;
		else
			return false;
	};
	
	function changeClubBeingRegistered()
	{
		if((document.getElementById("clubBeingRegistered").value != "")&&(document.getElementById("meetSelectMenu").value != ""))
		{
			//load anyone already registered
			updateFeesAndDates();
			loadRegData(1);
			loadRegData(2);
			loadRegData(3);
		}
	}
	
	function activateSelfRegister()
	{
		<?php
		$NameArray = userLoggedInNameParts();
		?>
		document.getElementById("newWFirst").disabled = true;
		document.getElementById("newWLast").disabled = true;
		document.getElementById("newWMiddle").disabled = true;
		document.getElementById("addNewPerson").disabled = true;
		
		document.getElementById("newWFirst").value = "<?php echo $NameArray['First']; ?>";
		document.getElementById("newWLast").value =  "<?php echo $NameArray['Last']; ?>";
		document.getElementById("newWMiddle").value =  "<?php echo $NameArray['Middle']; ?>";
		document.getElementById("newWId").value =  "<?php echo $NameArray['ID']; ?>";
	}
	
	function updateFeesAndDates()
	{
		var meet = document.getElementById('meetSelectMenu').value;
		var message = "We were unable to load a the dates and fees for this meet.";
		
		$.ajax({
			type: 'POST',
			url: "registrationAjax.php",
			async: false,
			data: {
				getMeetRegDateFees: 1,
				meetID: meet
			},
			dataType: 'json',
			success: function (data) {
				message = "The standard fee for this meet is $" + data['stdFee'] + " per person if registered before " + data['lateDeadline'] + ".<br/> The late fee is $" + data['lateFee'] + " per person and the final registration deadline is " + data['finalDeadline'] + ".<br/>";
				document.getElementById("fees").innerHTML = message;
				if(data['perTeamEventLimit']==0)
					enablePerTeamCompetitionRegistration = false;
				else
					enablePerTeamCompetitionRegistration = true;
				enableTeamPerEventFlags();
				
				var today = new Date();
				var deadline = new Date(data['finalDeadline']);
				
				var lateDeadline = new Date(data['lateDeadline']);
				
				//set late deadline
				if(today > lateDeadline)
					lateDeadlineHasPassed = true;
				else
					lateDeadlineHasPassed = false;
				
				//set final deadline
				if(today > deadline)
					deadlineHasPassed = true;
				else
					deadlineHasPassed = false;
			}
		});
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
						displayName = data[key].Division + " " + data[key].Level + " " + data[key].Discipline;
						ID = data[key].ID;
						opt = document.createElement('option');
						opt.value = ID;
						opt.innerHTML = displayName;
						document.getElementById('newPersonWTeam').appendChild(opt);
						optsLookup[ID] = displayName;
						if(data[key].Discipline.indexOf("Men") >= 0) //cant use .includes b/c IE11
							mOpts[ID] = displayName;
						else if(data[key].Discipline.indexOf("Women") >= 0)
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
			previousEventSelectMenuDiscipline = "";
			
			//load anyone already registered
			updateFeesAndDates();
			
			loadRegData(1);
			loadRegData(2);
			loadRegData(3);
			
		//	toggleEventOnlyTable("on"); //need to fix, see the keep note.
		}
	}
	
	function addPersonAACheck(Discipline)
	{
		if(Discipline == "women")
		{
			if(document.getElementById("newWAA").checked)
			{
				document.getElementById("newWVT").checked = true;
				document.getElementById("newWUB").checked = true;
				document.getElementById("newWBB").checked = true;
				document.getElementById("newWFX").checked = true;
			}
			else
			{
				document.getElementById("newWVT").checked = false;
				document.getElementById("newWUB").checked = false;
				document.getElementById("newWBB").checked = false;
				document.getElementById("newWFX").checked = false;
			}
		}
		if(Discipline == "men")
		{
			if(document.getElementById("newMAA").checked)
			{
				document.getElementById("newMFX").checked = true;
				document.getElementById("newMPH").checked = true;
				document.getElementById("newMSR").checked = true;
				document.getElementById("newMVT").checked = true;
				document.getElementById("newMPB").checked = true;
				document.getElementById("newMHB").checked = true;
			}
			else
			{
				document.getElementById("newMFX").checked = false;
				document.getElementById("newMPH").checked = false;
				document.getElementById("newMSR").checked = false;
				document.getElementById("newMVT").checked = false;
				document.getElementById("newMPB").checked = false;
				document.getElementById("newMHB").checked = false;
			}
		}
	}
	
	function addPersonEventAACheck(Discipline)
	{
		if(Discipline == "women")
		{
			if(document.getElementById("newWVT").checked && document.getElementById("newWUB").checked && document.getElementById("newWBB").checked && document.getElementById("newWFX").checked)
				document.getElementById("newWAA").checked = true;
			else
				document.getElementById("newWAA").checked = false;
		}
		
		if(Discipline == "men")
		{
			if(document.getElementById("newMFX").checked && 
				document.getElementById("newMPH").checked && 
				document.getElementById("newMSR").checked && 
				document.getElementById("newMVT").checked && 
				document.getElementById("newMPB").checked && 
				document.getElementById("newMHB").checked
				)
			{
				document.getElementById("newMAA").checked = true;
			}
			else
				document.getElementById("newMAA").checked = false;
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
		
		if(selfRegister)
			activateSelfRegister();
		
	});
	
	function toggleEventOnlyTable(toggle) //to fix or remove
	{
		if(toggle == "on")
		{
			$("#menRegTable").tabulator("hideColumn","MFXCount");
			$("#menRegTable").tabulator("hideColumn","MPHCount");
			$("#menRegTable").tabulator("hideColumn","MSRCount");
			$("#menRegTable").tabulator("hideColumn","MVTCount");
			$("#menRegTable").tabulator("hideColumn","MPBCount");
			$("#menRegTable").tabulator("hideColumn","MHBCount");
			$("#menRegTable").tabulator("hideColumn","MFX");
			$("#menRegTable").tabulator("hideColumn","MPH");
			$("#menRegTable").tabulator("hideColumn","MSR");
			$("#menRegTable").tabulator("hideColumn","MVT");
			$("#menRegTable").tabulator("hideColumn","MPB");
			$("#menRegTable").tabulator("hideColumn","MHB");
			$("#menRegTable").tabulator("hideColumn","MAA");
		
			$("#womenRegTable").tabulator("hideColumn","WVTCount");
			$("#womenRegTable").tabulator("hideColumn","WUBCount");
			$("#womenRegTable").tabulator("hideColumn","WBBCount");
			$("#womenRegTable").tabulator("hideColumn","WFXCount");
			$("#womenRegTable").tabulator("hideColumn","WVT");
			$("#womenRegTable").tabulator("hideColumn","WUB");
			$("#womenRegTable").tabulator("hideColumn","WBB");
			$("#womenRegTable").tabulator("hideColumn","WFX");
			$("#womenRegTable").tabulator("hideColumn","WAA");
		}
		else
		{
			//enableTeamPerEventFlags();
			$("#womenRegTable").tabulator("showColumn","WVT");
			$("#womenRegTable").tabulator("showColumn","WUB");
			$("#womenRegTable").tabulator("showColumn","WBB");
			$("#womenRegTable").tabulator("showColumn","WFX");
			$("#womenRegTable").tabulator("showColumn","WAA");
			$("#menRegTable").tabulator("showColumn","MFX");
			$("#menRegTable").tabulator("showColumn","MPH");
			$("#menRegTable").tabulator("showColumn","MSR");
			$("#menRegTable").tabulator("showColumn","MVT");
			$("#menRegTable").tabulator("showColumn","MPB");
			$("#menRegTable").tabulator("showColumn","MHB");
			$("#menRegTable").tabulator("showColumn","MAA");
		}
	}
	
	var previousEventSelectMenuDiscipline = "";
	function updateEventSelectMenu()
	{
		
		var Discipline = "men";
		var val = document.getElementById("newPersonWTeam").options[document.getElementById("newPersonWTeam").selectedIndex].text;
		if(val.indexOf("Women") >= 0)
			Discipline = "women";
		
		//store the old/initial value in a global var then the checkmarks won't disappear
		if(previousEventSelectMenuDiscipline == Discipline)
			return false;
		else
			previousEventSelectMenuDiscipline = Discipline;
		
		//ok update it		
		if(!eventOnly)
		{
			if(Discipline == "men")
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
			else if(Discipline == "women")
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
</script>
<style>
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
						//echo "<pre>"; print_r(getListOfUserCoachPermissions()); echo "</pre>";
						//echo "<pre>"; print_r($_SESSION['permissions']); echo "</pre>";
						$displaySpecific = (	(sizeof(getListOfUserCaptainPermissions()) > 0) || (sizeof(getListOfUserCoachPermissions()) > 0) || (sizeof(getListOfUserClubAdministrativePermissions()) > 0)	);
						if(userIsLoggedIn())
						{
						?>
						<p>You are currently logged in as: <?php echo userLoggedInNameIs(); ?></p>
						
						<?php 
							//if(count(array_filter(getListOfUserClubAdministrativePermissions())>0))
							if($displaySpecific)
							{
								if(userIsExecutiveAdministrator())
								{
									echo "<p>As a TGC Admin you can control all clubs' registrations.</p>";
									$combinedArray = getListOfUserClubAdministrativePermissions();
								}
								else
								{
									echo "<p>You are an administrator for the following clubs. Select which one you want to register:</p>";
									//if((sizeof(getListOfUserCaptainPermissions()) > 0) && (sizeof(getListOfUserCoachPermissions()) > 0))
										//$combinedArray = array_unique (array_merge (getListOfUserCaptainPermissions(), getListOfUserCoachPermissions())); //This looks ok but I need to order alphabetically with ksort??
									//else
									//ITS NOT WORKING FOR ASSOC ARRAY
										$combinedArray = getListOfUserCaptainPermissions();
									//echo "test";
									//print_r($combinedarray);
								}
								if(sizeof($combinedArray)>0) //this needs to kick us out somehow if we somehow got to the page but don't have appropriate perms.
								{
									echo "<h2>Step 1: Select a meet and the club you are registering:</h2>";
									echo "<select id = 'clubBeingRegistered' onchange = 'changeClubBeingRegistered();'>";
									//foreach (getListOfUserCaptainPermissions() AS $clubID=>$clubName)
									foreach ($combinedArray AS $clubID=>$clubName)
									{
										echo "<option value = '".$clubID."'>".addslashes($clubName)."</option>";
									}
									echo "</select>";	
								}
								else
								{
									echo "<h2>Step 1: Select a meet and the club you are registering:</h2>";
									//$_SESSION['']; set to guest or something.
									echo "You can only register as an open athlete.";//only open team
									echo "<select id = 'clubBeingRegistered' onchange = 'changeClubBeingRegistered();'>";
										echo "<option value = '6203'>Alumni Athlete (not in school)</option>";
										echo "<option value = '7023'>Independent Collegiate Athlete (in school)</option>";
									echo "</select>";
								}
							}
							else
							{
								//$_SESSION['']; set to guest or something.
								echo "You can only register as an open athlete.<br/>";//only open team
								echo "<select id = 'clubBeingRegistered' onchange = 'changeClubBeingRegistered();'>";
									echo "<option value = '6203'>Alumni Athlete (not in school)</option>";
									echo "<option value = '7023'>Independent Collegiate Athlete (in school)</option>";
								echo "</select>";
								echo "<script type = 'text/javascript'>selfRegister = true; activateSelfRegister();</script>";
							}
						?>
						<select id = "meetSelectMenu" onchange = "updateAllowedTeams();">
							<option selected disabled value = "">Select a meet to register for:</option>
						<?php
							if(getMeets())
							{
								$stmtMeets = getMeets();
								while($row = $stmtMeets->fetch(PDO::FETCH_ASSOC))
								{
									echo "<option value = '".$row['ID']."'>".$row['MeetName']."(".$row['Date'].")</option>";
								}
							}
							else
							{
								echo "<option selected disabled value = ''>No Meets Available</option>";
							}
						?>
						</select>
						<button onclick = 'updateAllowedTeams();'>&#x21bb;</button><br/>
						<br/>
						<p id = "fees"></p>
						<h2>Step 2: Register people for the meet:</h2>
						<p>Instructions: Start typing someone's last name and then select then from the list. If you have a new member then you will need to click the add new person button.
						Please make sure that it's actually a new person, as I have to manually go through and combine duplicate people.</p>
						<p><b>Be careful about removing someone and then adding them back. If it is past the late deadline you may trigger a late fee. If you need to change their team, just click the team in their row and select from that menu.</b></p>
							<input size = "2" disabled id = "newWId"/>
								<input size = "15" type = "text" id = "newWLast" name = "h76f875fvi" autocomplete="new-password" placeholder = ""/>
								<input size = "12" type = "text" id = "newWFirst" autocomplete="new-password" placeholder = "First Name"/>
								<input size = "3" type = "text" id = "newWMiddle" autocomplete="new-password" placeholder = "Middle" />
							<div class = "12u">
								<select style = "display:inline" id = "newPersonWTeam" onchange = "updateEventSelectMenu();">
									<option selected disabled value = "">Select a meet from the menu above</option>
								<select>
								<select style = "display:inline" id = "newPersonWDesignation">
									<option disabled>Team</option>
									<option value = "A">A</option>
									<!--option value = "B">B</option>
									<option value = "C">C</option>
									<option value = "D">D</option>
									<option value = "E">E</option>
									<option value = "F">F</option>
									<option value = "G">G</option>
									<option value = "H">H</option>
									<option value = "I">I</option-->
								</select>
								<div style = "display:inline-block" id = "eventSelectMenu">
									
								</div>
								<div>
									<input type = "checkbox" name = "under18" id = "under18">Under 18 on date of event?</input>
									<br/>
								</div>
							</div>
							<button id = "addPerson">Register Selected Person for Meet</button> <button id = "addNewPerson" onclick ="addNewPerson();">Add New Person to Database</button> <!--button id = "editPersonW">Name Update</button--><br/>
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
							<br/>
							
							
								<h2>Clinic Registration:</h2>
								<div id="clinicEventTable"></div>
							
							
							
								<h2>Womens Teams and Competitors:</h2>
								<div id="womenRegTable"></div>
							
							
							
								<br/>
								<h2>Mens Teams and Competitors:</h2>
								<div id="menRegTable"></div> <br>
							
								
							
								<h2>Step 3: Team Options</h2>
								<p>For each team, please indicate the following:
									<div id="teamTable"></div> <br>
								</p>
							
							
							<!--h2>Step 4: Print your invoice:</h2>
							<p>
								This feature is coming soon. Don't worry, we will email you your invoices in the meantime!
							</p-->
							
							<script type="text/javascript">
							
								$("#clinicEventTable").tabulator({
									layout: "fitDataFill",
									responsiveLayout:false,
									columns:[
										{title:"",		 		field:"Remove", 	formatter:"buttonCross", cellClick:function(e,cell){cell.getRow().delete();}, visible: removeVisible	},
										{title:"ID",				field:"ID",	 			visible:false},
										{title:"MeetID",			field:"MeetID",	 		visible:false},
										{title:"Name",	 			field:"Name",	 	sorter:"string", formatter:function(cell, formatterParams)
																											{
																											   var row = cell.getRow();
																											   var data = row.getData();
																											   var isMinor = data.Minor;
																												if(isMinor > 0)
																												{
																													return "<span style='color:red; font-weight:bold;'>" + data.Name + "</span>";
																												}
																												else
																												{
																													return data.Name;
																												}
																											}
										},
										{title:"DisciplineID", 			field:"DisciplineID", 		visible:false},
										{title:"CompetitionID", 	field:"CompetitionID", 	visible:false},
										{title:"InstitutionID", 	field:"InstitutionID", 	visible:false},
										{title:"Men's Lecture", 	field:"MenLecture",	 	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Women's Lecture", 	field:"WomenLecture",	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Hosting a Meet", 	field:"MeetLecture",	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Social Gathering", 	field:"Social",	 		sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Workout Clinic", 	field:"Workout",	 	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Fee", 				field:"Fee",	 	formatter:"money", 	formatterParams:{symbol:"$", precision:2}, bottomCalc:"sum", bottomCalcFormatterParams :{symbol:"$", precision:2}},
										{title:"Last Registered", 	field:"RegDate", responsive: 6},
										{title:"Registered By", field:"RegBy", responsive: 6}
									],
									index:"ID",
									rowDeleted:function(row){
										var data = row.getData();
										//console.log(data.ID + " " + data.CompetitionID);
										unregisterCompetitor(data.ID,data.CompetitionID,"");
										loadRegData(3); //because groupBy is broken.
									},	
									cellEdited:function(cell){
										
										var row = cell.getRow();
										var data = row.getData();
										
										//todo: call savepersonregistration and row.update
										var institution = document.getElementById("clubBeingRegistered").value;
										var iEvents = {
												/*ID from apparatus. Need to dynamically create when more disciplines added.*/
												12: data.MenLecture,
												13: data.WomenLecture,
												14: data.Social,
												15: data.Workout,
												16: data.MeetLecture
											};
											
										var iEventCountFlags = {
												/*ID from apparatus. Need to dynamically create when more disciplines added.*/
												12: 0,
												13: 0,
												14: 0,
												15: 0,
												16: 0
										};
										var clinicEvents = {
														"MenLecture":12,
														"WomenLecture":13,
														"Social":14,
														"Workout":15,
														"MeetLecture":16
											};
										//and save
										/*if(cell.getField()!= "Team") //...which then means the team cell updates and triggers this again.
											savePersonRegistration(data.ID,institution,data.CompetitionID,0,data.DisciplineID,iEvents,iEventCountFlags,false);
										*/
										if(Object.keys(clinicEvents).includes(cell.getField())) //...which then means the team cell updates and triggers this again.
										{
											//savePersonRegistration(data.ID,institution,data.CompetitionID,0,data.DisciplineID,iEvents,iEventCountFlags,false);
											//if cell is one of the events then
											var eventID = clinicEvents[cell.getField()];
											//alert("eventID:"+eventID+"; cell.getValue(): "+cell.getValue());
											savePersonRegistrationSingleEvent(data.ID,institution,data.CompetitionID,eventID,cell.getValue(),3,true,cell);
											//elseif cell is one of the eventcounts 
											//savepersonregistrationSingleEventCounts(iPerson,iCompetition,iEvent,iEventRegistered)
										}
									}
								});
							
								$("#menRegTable").tabulator({
									layout: "fitDataFill",
									responsiveLayout:false,
									virtualDom:false,
									groupBy: ["Team","Designation"],
									columns:[
										/*{formatter:"responsiveCollapse", width:30, minWidth:30, align:"center", resizable:false, headerSort:false},*/
										{title:"",		 		field:"Remove", 	formatter:"buttonCross", cellClick:function(e,cell){cell.getRow().delete();}, visible: removeVisible	},
										{title:"ID", 			field:"ID", 		visible:false},
										{title:"Name",	 		field:"Name",	 	sorter:"string", formatter:function(cell, formatterParams)
																											{
																											   var row = cell.getRow();
																											   var data = row.getData();
																											   var isMinor = data.Minor;
																												if(isMinor > 0)
																												{
																													return "<span style='color:red; font-weight:bold;'>" + data.Name + "</span>";
																												}
																												else
																												{
																													return data.Name;
																												}
																											}
										},
										{title:"CompetitionID", field:"CompetitionID", 		visible:false},
										{title:"DisciplineID", 		field:"DisciplineID", 	visible:false},
										{title:"Comp", 			field:"Team",	 	editor:teamEditor},
										{title:"Team",	 		field:"Designation",editor:designationEditor},
										{title:"FX", 			field:"MFX",	 	sorter:"boolean",	formatter:"tickCross", 	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Team", 			field:"MFXCount",	sorter:"boolean",	visible: false,		formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"PH", 			field:"MPH",	 	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Team", 			field:"MPHCount",	sorter:"boolean",	visible: false,		formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"SR", 			field:"MSR",	 	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Team", 			field:"MSRCount",	sorter:"boolean",	visible: false,		formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"VT", 			field:"MVT",	 	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Team", 			field:"MVTCount",	sorter:"boolean",	visible: false,		formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"PB", 			field:"MPB",	 	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Team", 			field:"MPBCount",	sorter:"boolean",	visible: false,		formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"HB", 			field:"MHB",	 	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Team", 			field:"MHBCount",	sorter:"boolean",	visible: false,		formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"AA", 			field:"MAA",	 	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Fee", 			field:"Fee",	 	formatter:"money", 	formatterParams:{symbol:"$", precision:2}, bottomCalc:"sum", bottomCalcFormatterParams :{symbol:"$", precision:2}},
										{title:"Last Registered", 		field:"RegDate", responsive: 6},
										{title:"Registered By", field:"RegBy", responsive: 6}
									],
									index:"ID",
									//groupHeader:function(value, count, data, group){return data[0].Team;},
									rowDeleted:function(row){
										var data = row.getData();
										//console.log(data.ID + " " + data.CompetitionID);
										unregisterCompetitor(data.ID,data.CompetitionID,data.Designation);
										loadRegData(2); //because groupBy is broken.
									},									
									cellEdited:function(cell){
										//This callback is called any time a cell is edited
										var row = cell.getRow();
										var data = row.getData();
										var col = cell.getColumn();
										var AAcontrol = cell.getField();
										
										/////////////////////////////
										/*ALL AROUND CHECKBOX STUFF*/
										/////////////////////////////
											//for tabulator display purposes only.
												//handle AA click
												if(AAcontrol=="MAA")
												{
													if(data.MAA == true) { //if it is what was checked, check everything else
														row.update({MFX:true, MPH:true, MSR:true, MVT:true, MPB:true, MHB:true});
													}
													else { //if its what was unchecked then uncheck everything else
														row.update({MFX:false, MPH:false, MSR:false, MVT:false, MPB:false, MHB:false});
													}
												}
												else //an event or a teamevent flag was checked.
												{
													//if all events are checked then check AA
													if(data.MFX&&data.MPH&&data.MSR&&data.MVT&&data.MPB&&data.MHB)
														row.update({MAA:true});
													else
														row.update({MAA:false});
												}
										///////////////////////////
										/*AND UPDATE EVENTS in DB*/
										///////////////////////////
												data = row.getData(); //I need to refresh if any AA stuff was done?
												var institution = document.getElementById("clubBeingRegistered").value;
												var iEvents = {
														/*ID from apparatus. Need to dynamically create when more disciplines added.*/
														1: data.MFX,
														2: data.MPH,
														3: data.MSR,
														4: data.MVT,
														5: data.MPB,
														6: data.MHB
													};
												var menEventCountFlags = {
														/*ID from apparatus. Need to dynamically create when more disciplines added.*/
														"MFXCount":1,
														"MPHCount":2,
														"MSRCount":3,
														"MVTCount":4,
														"MPBCount":5,
														"MHBCount":6
													};
												var menEvents = {
																"MFX":1,
																"MPH":2,
																"MSR":3,
																"MVT":4,
																"MPB":5,
																"MHB":6
													};
												//I can call this again because the post script will check if it's already there and just overwrite
												//however, it won't work if what I'm doing is changing their team!!!!!!!!
												if(cell.getField()=="Designation")
												{
													updatePersonDesignation(data.ID,data.CompetitionID,data.Designation,data.DisciplineID,cell.getOldValue()); 
												}
												else if(cell.getField()=="CompetitionID")
												{
													updatePersonCompetition(data.ID,data.CompetitionID,cell.getOldValue(),data.DisciplineID,data.Designation); //the competitionID is being updated on the onchange event. that comes first so I say if cell = competitionID. 
												}
												/*else if(cell.getField()!= "Team") //...which then means the team cell updates and triggers this again.
												{
													savePersonRegistration(data.ID,institution,data.CompetitionID,0,data.DisciplineID,iEvents,iEventCountFlags,false);
												}*/
												else if(Object.keys(menEvents).includes(cell.getField())) //...which then means the team cell updates and triggers this again.
												{
													//savePersonRegistration(data.ID,institution,data.CompetitionID,0,data.DisciplineID,iEvents,iEventCountFlags,false);
													//if cell is one of the events then
													
													var eventID = menEvents[cell.getField()];
													//alert("eventID:"+eventID+"; cell.getValue(): "+cell.getValue());
													savePersonRegistrationSingleEvent(data.ID,institution,data.CompetitionID,eventID,cell.getValue(),1,true,cell);
													//elseif cell is one of the eventcounts 
													//savepersonregistrationSingleEventCounts(iPerson,iCompetition,iEvent,iEventRegistered)
												}
												else if(Object.keys(menEventCountFlags).includes(cell.getField()))
												{
													var eventID = menEventCountFlags[cell.getField()];
													savepersonregistrationCountsForTeamSingleEvent(data.ID,institution,data.CompetitionID,eventID,cell.getValue(),1);
												}
												else if(AAcontrol=="MAA")
												{
													var errorLessInAA = true;
													errorLessInAA = errorLessInAA & savePersonRegistrationSingleEvent(data.ID,institution,data.CompetitionID,1,cell.getValue(),2,false,row.getCell("MFX"));
													errorLessInAA = errorLessInAA & savePersonRegistrationSingleEvent(data.ID,institution,data.CompetitionID,2,cell.getValue(),2,false,row.getCell("MPH"));
													errorLessInAA = errorLessInAA & savePersonRegistrationSingleEvent(data.ID,institution,data.CompetitionID,3,cell.getValue(),2,false,row.getCell("MSR"));
													errorLessInAA = errorLessInAA & savePersonRegistrationSingleEvent(data.ID,institution,data.CompetitionID,4,cell.getValue(),2,false,row.getCell("MVT"));
													errorLessInAA = errorLessInAA & savePersonRegistrationSingleEvent(data.ID,institution,data.CompetitionID,5,cell.getValue(),2,false,row.getCell("MPB"));
													errorLessInAA = errorLessInAA & savePersonRegistrationSingleEvent(data.ID,institution,data.CompetitionID,6,cell.getValue(),2,true,row.getCell("MHB"));
													
													if(!errorLessInAA)
													{	cell.restoreOldValue();
														alert("error");
													}
													
												}
										/////////////////////
										/*AND UPDATE TEAM*///
										/////////////////////
										
									}
								});

								$("#womenRegTable").tabulator({
									//height:"311px",
									responsiveLayout:false,
									layout: "fitDataFill",
									groupBy: ["Team","Designation"],
									virtualDom:false,
									columns:[
										/*{formatter:"responsiveCollapse", width:30, minWidth:30, align:"center", resizable:false, headerSort:false},*/
										{title:"",		 		field:"Remove", 	formatter:"buttonCross", cellClick:function(e,cell){cell.getRow().delete();	}, visible: removeVisible	},
										{title:"ID", 			field:"ID", 		visible:false},
										{title:"Name",	 		field:"Name",	 	sorter:"string", formatter:function(cell, formatterParams)
																											{
																											   var row = cell.getRow();
																											   var data = row.getData();
																											   var isMinor = data.Minor;
																												if(isMinor > 0)
																												{
																													return "<span style='color:red; font-weight:bold;'>" + data.Name + "</span>";
																												}
																												else
																												{
																													return data.Name;
																												}
																											}
										},
										{title:"CompetitionID", field:"CompetitionID", 		visible:false},
										{title:"DisciplineID", 		field:"DisciplineID", 	visible:false},
										{title:"Comp", 			field:"Team",	 	editor:teamEditor},
										{title:"Team",	 		field:"Designation",editor:designationEditor},
										{title:"VT", 			field:"WVT",	 	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Team", 			field:"WVTCount",	sorter:"boolean",	visible: false,		formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"UB", 			field:"WUB",	 	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Team", 			field:"WUBCount",	sorter:"boolean",	visible: false,		formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"BB", 			field:"WBB",	 	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Team", 			field:"WBBCount",	sorter:"boolean",	visible: false,		formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"FX", 			field:"WFX",	 	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Team", 			field:"WFXCount",	sorter:"boolean",	visible: false,		formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"AA", 			field:"WAA",	 	sorter:"boolean",	formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Fee", 			field:"Fee",	 	formatter:"money", 	formatterParams:{symbol:"$", precision:2}, bottomCalc:"sum", bottomCalcFormatterParams :{symbol:"$", precision:2}},
										{title:"Last Registered", 		field:"RegDate", responsive: 6},
										{title:"Registered By", field:"RegBy", responsive: 6}
									],
									index:"ID",
									//groupHeader:function(value, count, data, group){return data[0].Team;},
									rowDeleted:function(row){
										var data = row.getData();
										//console.log(data.ID + " " + data.CompetitionID);
										unregisterCompetitor(data.ID,data.CompetitionID,data.Designation);
										loadRegData(1); //because groupBy is broken.
									},
									cellEdited:function(cell){
										//This callback is called any time a cell is edited
										var row = cell.getRow();
										var data = row.getData();
										var col = cell.getColumn();
										var AAcontrol = cell.getField();
										
										/////////////////////////////
										/*ALL AROUND CHECKBOX STUFF*/
										/////////////////////////////
												
												//handle AA click
												if(AAcontrol=="WAA")
												{
													if(data.WAA == true) { //if it is what was checked, check everything else
														row.update({WVT:true, WUB:true, WBB:true, WFX:true});
													}
													else { //if its what was unchecked then uncheck everything else
														row.update({WVT:false, WUB:false, WBB:false, WFX:false});
													}
												}
												else //an event was checked.
												{
													//if all events are checked then check AA
													if(data.WVT&&data.WUB&&data.WBB&&data.WFX)
														row.update({WAA:true});
													else
														row.update({WAA:false});
												}
												
										///////////////////////////
										/*AND UPDATE STUFF in DB*/
										///////////////////////////
												data = row.getData(); //I need to refresh if any AA stuff was done?
												var institution = document.getElementById("clubBeingRegistered").value;
												var iEvents = {
														/*ID from apparatus. Need to dynamically create when more disciplines added.*/
														8: data.WVT,
														9: data.WUB,
														10: data.WBB,
														11: data.WFX
													};
												var womenEventCountFlags = {
														/*ID from apparatus. Need to dynamically create when more disciplines added.*/
														"WVTCount":8,
														"WUBCount":9,
														"WBBCount":10,
														"WFXCount":11
													};
												var womenEvents = {
																"WVT":8,
																"WUB":9,
																"WBB":10,
																"WFX":11
													};
												if(cell.getField()=="Designation")
												{
													updatePersonDesignation(data.ID,data.CompetitionID,data.Designation,data.DisciplineID,cell.getOldValue());
												}
												else if(cell.getField()=="CompetitionID")
												{
													updatePersonCompetition(data.ID,data.CompetitionID,cell.getOldValue(),data.DisciplineID,data.Designation); //the competitionID is being updated on the onchange event. that comes first so I say if cell = competitionID. 
												}
												else if(Object.keys(womenEvents).includes(cell.getField())) //...which then means the team cell updates and triggers this again.
												{
													//savePersonRegistration(data.ID,institution,data.CompetitionID,0,data.DisciplineID,iEvents,iEventCountFlags,false);
													//if cell is one of the events then
													
													var eventID = womenEvents[cell.getField()];
													//alert("eventID:"+eventID+"; cell.getValue(): "+cell.getValue());
													savePersonRegistrationSingleEvent(data.ID,institution,data.CompetitionID,eventID,cell.getValue(),1,true,cell);
													//elseif cell is one of the eventcounts 
													//savepersonregistrationSingleEventCounts(iPerson,iCompetition,iEvent,iEventRegistered)
												}
												else if(Object.keys(womenEventCountFlags).includes(cell.getField()))
												{
													var eventID = womenEventCountFlags[cell.getField()];
													savepersonregistrationCountsForTeamSingleEvent(data.ID,institution,data.CompetitionID,eventID,cell.getValue(),1);
												}
												else if(AAcontrol=="WAA")
												{
													
													var errorLessInAA = false;
													errorLessInAA = errorLessInAA & savePersonRegistrationSingleEvent(data.ID,institution,data.CompetitionID,8,data.WAA,1,false,row.getCell("WVT"));
													errorLessInAA = errorLessInAA & savePersonRegistrationSingleEvent(data.ID,institution,data.CompetitionID,9,data.WAA,1,false,row.getCell("WUB"));
													errorLessInAA = errorLessInAA & savePersonRegistrationSingleEvent(data.ID,institution,data.CompetitionID,10,data.WAA,1,false,row.getCell("WBB"));
													errorLessInAA = errorLessInAA & savePersonRegistrationSingleEvent(data.ID,institution,data.CompetitionID,11,data.WAA,1,true,row.getCell("WFX"));
													
													if(!errorLessInAA)
														cell.restoreOldValue();
												}
												
										/////////////////////
										/*AND UPDATE TEAM*///
										/////////////////////
									}
								});

								$("#teamTable").tabulator({
									responsiveLayout:false,
									layout: "fitDataFill",
									virtualDom:false,
									columns:[
										{title:"ID",				field:"ID",	 			visible:false},
										{title:"MeetID",			field:"MeetID",	 		visible:false},
										{title:"Name",	 			field:"TeamName", 		sorter:"string"},
										{title:"Team",	 			field:"Designation",	sorter:"string"},
										{title:"CompetitionID", 	field:"CompetitionID", 	visible:false},
										{title:"InstitutionID", 	field:"InstitutionID", 	visible:false},
										{title: "Has Team Score?", 	field:"TeamScore",		formatter:"tickCross",	bottomCalc:"count", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
										{title:"Fee", 				field:"TeamFee",	 	formatter:"money", 	formatterParams:{symbol:"$", precision:2}, bottomCalc:"sum", bottomCalcFormatterParams :{symbol:"$", precision:2}},
										{title:"Last Registered", 	field:"LastModifiedDate"},
										{title:"Last Reg By", 		field:"LastModifiedPerson"}
									],
									index:"ID",
									cellEdited: function(cell){
										
										var row = cell.getRow();
										var data = row.getData();
										
										if(cell.getField()=="TeamScore")
										{
											updateTeamOptions(data.TeamScore,data.CompetitionID,data.InstitutionID,data.Designation);
										}
									}
								});

								$("#addPerson").click(function(){
									//first validate that the fields are filled out
									var firstnameEntered = (document.getElementById("newWFirst").value != "");
									var lastnameEntered = (document.getElementById("newWLast").value != "");
									var IDLoaded = (document.getElementById("newWId").value != "");
									var teamSelected = (document.getElementById("newPersonWTeam").value != "");
									var teamDesignation = (document.getElementById("newPersonWDesignation").value != "");
									
									//then add it
									var Discipline = "X";
									if(document.getElementById("newPersonWTeam").options[document.getElementById("newPersonWTeam").selectedIndex].text.indexOf("Women") >= 0 )
									{
										Discipline = "Women";
									}
									if(document.getElementById("newPersonWTeam").options[document.getElementById("newPersonWTeam").selectedIndex].text.indexOf("Men") >= 0 )
									{
										Discipline = "Men";
									}
									if(firstnameEntered && lastnameEntered && teamSelected && IDLoaded && teamDesignation)
									{
										//save to db
										var iPerson = document.getElementById("newWId").value;
										var iInstitution = document.getElementById("clubBeingRegistered").value;
										var iCompetition = document.getElementById("newPersonWTeam").value;
										var iDesignation = document.getElementById("newPersonWDesignation").value;
										var iTeam = 0;
										var iDiscipline = 1;
										var iEvents;
										if(Discipline == "Men")
										{
											iDiscipline = 2;
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
										else if(Discipline == "Women")
										{
											iDiscipline = 1;
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
											iDiscipline = 3;
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
										saved = savePersonRegistration(iPerson,iInstitution,iCompetition,iTeam,iDiscipline,iEvents,iEventCountFlags,true,minor,iDesignation);
										//alert(saved+"injustdrawrow");
										if(saved) //if it comes back true, draw it on the table.
										{
											if(Discipline == "Women")
											{
												loadRegData(1);
											}
											else if(Discipline == "Men")
											{
												loadRegData(2);
											}
											else
											{
												loadRegData(3);
											}
										}
										else
										{
											;//nothing, an error should have already been displayed.
										}
										//then clear out all data.
										if(!selfRegister)
										{
											document.getElementById("newWFirst").value = "";
											document.getElementById("newWLast").value = "";
											document.getElementById("newWMiddle").value = "";
											document.getElementById("newWId").value = "";
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
								});
							</script>
						<?php
						}
						else
						{
							if(userIsLoggedIn())
								echo "You do not have a captain or coach permission necessary to access registration.";
							display_login();
						}
						?>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>
