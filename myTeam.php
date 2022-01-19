<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>
<?php require_once("registrationAjax.php"); ?>

<?php

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

function changePersonName() //maybe somehow say for spelling only. Also maybe a maiden name field...?
{
	
}

function getInstitutionDetails()
{
	//maybe program instead.
	//facebook pages and whatnot
	//photo? security.
}

function setPersonDegrees()
{
	//This needs to be a insert on duplicate key update
}

function removePersonDegrees()
{
	//just from the INSTID they have access for.
}

function getPersonDegrees($person, $institution)
{
	global $conn;
	$stmt= $conn->prepare("
		SELECT
			Constraints_DegreeTypes.ID,
			Constraints_DegreeTypes.Name,
			Constraints_DegreeTypes.Abbr,
			Constraints_Degrees.Group,
			Constraints_Degrees.Specialty
		FROM
			People_Degrees,
			Constraints_Degrees,
			Constraints_DegreeTypes
		WHERE
			People_Degrees.PersonID = ? AND
			People_Degrees.InstitutionID = ? AND
			People_Degrees.DegreeType = Constraints_DegreeTypes.ID AND
			People_Degrees.DegreeName = Constraints_Degrees.ID
		;");
		
	$stmt->execute();
	
	
}

function getPeepDeets()
{
	global $conn;
	$stmt= $conn->prepare("
		SELECT
			Identifiers_People.ID,
			Identifiers_People.Gender,
			Identifiers_People.FirstName,
			Identifiers_People.LastName,
			Identifiers_People.Birthday
		FROM
			Identifiers_People,
			Identifiers_Affiliations
		WHERE
			Identifiers_People.ID = Identifiers_Affiliations.PersonID AND
			Identifiers_Affiliations.ClubID = ?
		Order By 
			Identifiers_Affiliations.Season Desc
		");
		
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

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.js"></script>

<script>
	function changeYearClub()
	{
		//run only if a valid meet and club have been selected.
		//if((document.getElementById("clubBeingRegistered").value != "")&&(document.getElementById("meetSelectMenu").value != ""))
		if(true)
		{
			$.ajax({
				type: 'POST',
				url: "teamEditAjax.php",
				async: false,
				data: {
					getTeamData: 1,
					institutionID: document.getElementById("selectClub").value,
					year: document.getElementById("selectYear").value
				},
				dataType: 'json',
				success: function (data) {
					$("#teamTable").tabulator("setData", data);
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading team data");
				}
			});
		}
	}
	
	function affiliatePerson()
	{
		var iID = document.getElementById("newWId").value;
		var iSeason = document.getElementById("selectYear").value;
		var iInstitutionID = document.getElementById("selectClub").value;
		var IDLoaded = (document.getElementById("newWId").value != "");
		
		if(IDLoaded)
		{
			$.ajax({
						type: 'POST',
						url: "teamEditAjax.php",
						async: false,
						data: {
							affiliatePersontoSeason: 1,
							season: iSeason,
							personID: iID,
							institutionID: iInstitutionID
						},
						//dataType: 'json',
						success: function () {
							changeYearClub();
							//reset. Need to put in frunction.
							document.getElementById("newWFirst").value = "";
							document.getElementById("newWLast").value = "";
							document.getElementById("newWMiddle").value = "";
							document.getElementById("newWId").value = "";
						},
						error: function (textStatus, errorThrown) {
							//console.log(errorThrown);
							alert("error affiliating person");
						}
			});
		}
		else
		{
			alert("You need to click an existing person (from the popup menu as you type), or click the add new person button for new people. You will see a number populate on the left.");
		}
	}
	
	
	function addNewPerson()
	{
		var nameIsValid = false;
		var schoolIsValid = false;
		var iLastName = document.getElementById("newWLast").value;
		var iFirstName = document.getElementById("newWFirst").value;
		var iMiddleName = document.getElementById("newWMiddle").value;
		
		var iPhone = document.getElementById("newWPhone").value;
		var iGender = document.getElementById("newWGender").value;
		var iEmail = document.getElementById("newWEmail").value;
		
		var iInstitutionID;
		var InstitutionName;
		//var d = new Date();
		//var season = d.getFullYear();
		var season = document.getElementById("selectYear").value;
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
			if (confirm("Does this person attend " + schoolsLookup[document.getElementById('selectClub').value] + "?") == true)
			{
				iInstitutionID = document.getElementById("selectClub").value;
				InstitutionName = schoolsLookup[document.getElementById("selectClub").value];
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
	
	function removePersonFromSeason()
	{
		
	}
	
	function savePersonEmail(iID,iEmail)
	{
		$.ajax({
			type: 'POST',
			url: "teamEditAjax.php",
			data: {
				updateEmail: 1,
				ID: iID,
				Email: iEmail
			},
			//dataType: 'json',
			success: function () {
				;//alert("saved");
			},
			error: function (textStatus, errorThrown) {
                alert("error updating person");
				changeYearClub();
            }
		});
	}
	
	function savePersonPhone(iID,iPhone)
	{
		$.ajax({
			type: 'POST',
			url: "teamEditAjax.php",
			data: {
				updatePhone: 1,
				ID: iID,
				Phone: iPhone
			},
			//dataType: 'json',
			success: function () {
				;//alert("saved");
			},
			error: function (textStatus, errorThrown) {
                alert("error updating person");
				changeYearClub();
            }
		});
	}
	
	function savePersonGender(iID,iGender)
	{
		$.ajax({
			type: 'POST',
			url: "teamEditAjax.php",
			data: {
				updateGender: 1,
				ID: iID,
				Gender: iGender
			},
			//dataType: 'json',
			success: function () {
				;//alert("saved");
			},
			error: function (textStatus, errorThrown) {
                alert("error updating person");
				changeYearClub();
            }
		});
	}
	
	function savePersonName(iID,iName,iNameType)
	{
		$.ajax({
			type: 'POST',
			url: "teamEditAjax.php",
			data: {
				updateName: iNameType,
				ID: iID,
				Name: iName
			},
			//dataType: 'json',
			success: function () {
				;//alert("saved");
			},
			error: function (textStatus, errorThrown) {
                alert("error updating person");
				changeYearClub();
            }
		});
	}
	
	function savePersonType(iID,iType)
	{
		var iSeason = document.getElementById("selectYear").value;
		var iInstitutionID = document.getElementById("selectClub").value;
		$.ajax({
			type: 'POST',
			url: "teamEditAjax.php",
			data: {
				updatePersonType: iType,
				ID: iID,
				Season: iSeason,
				institutionID: iInstitutionID
			},
			//dataType: 'json',
			success: function () {
				;//alert("saved");
			},
			error: function (textStatus, errorThrown) {
                alert("error updating person");
				changeYearClub();
            }
		});
	}
	
	function savePersonPermission(iPerson,iPermission,iValue)
	{
		var iSeason = document.getElementById("selectYear").value;
		var iInstitutionID = document.getElementById("selectClub").value;
		$.ajax({
			type: 'POST',
			url: "teamEditAjax.php",
			data: {
				updatePermission: iPermission,
				permissionValue: iValue,
				personID: iPerson,
				Season: iSeason,
				institutionID: iInstitutionID
			},
			//dataType: 'json',
			success: function () {
				;//alert("saved");
			},
			error: function (textStatus, errorThrown) {
                alert("error updating person");
				changeYearClub();
            }
		});
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
	});
	
	var genderMutator = function(value, data, type, params, component){
		//value - original value of the cell
		//data - the data for the row
		//type - the type of mutation occurring  (data|edit)
		//params - the mutatorParams object from the column definition
		//component - when the "type" argument is "edit", this contains the cell component for the edited cell, otherwise it is the column component for the column
		if(value == 1)
			return "F";
		if(value == 2)
			return "M";
	}
</script>

<style>	
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
	
	.ui-autocomplete { position: absolute; cursor: default; background-color: #ffffff; z-index:30 !important; border: 2px solid #555555;}
	.ui-helper-hidden-accessible { display:none; }
</style>
<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
						<?php
							$displaySpecific = ((sizeof(getListOfUserCaptainPermissions()) > 0) || (sizeof(getListOfUserCoachPermissions()) > 0) || (sizeof(getListOfUserClubAdministrativePermissions()) > 0)	);
							if(userIsLoggedIn())
							{			
								if($displaySpecific)
								{
									if(userIsExecutiveAdministrator())
									{
										echo "<p>As a TGC Admin you can control all clubs' information.</p>";
										$combinedArray = getListOfUserClubAdministrativePermissions();
									}
									else
									{
										echo "<p>You are an administrator for the following clubs. Select which one you want to administrate:</p>";
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
										echo "<h2>Select the club and year you are managing:</h2>";
										echo "<select id = 'selectClub' onchange = 'changeYearClub();'>";
										//foreach (getListOfUserCaptainPermissions() AS $clubID=>$clubName)
										foreach ($combinedArray AS $clubID=>$clubName)
										{
											echo "<option value = '".$clubID."'>".$clubName."</option>";
										}
										echo "</select><button onclick = 'changeYearClub();'>&#x21bb;</button><br/>";	
										
										
									}
									else
									{
										//$_SESSION['']; set to guest or something.
										echo "You are not a team administrator.";
									}
								}
								else
								{
									//$_SESSION['']; set to guest or something.
									echo "You are not a team administrator.";
								}						
						?>
								<br/>
								<select id = "selectYear" onchange = "changeYearClub();">
								<!--button onclick = 'changeYearClub();'>&#x21bb;</button><br/-->
								<?php
									if(idate("m")>=5)
										$i = idate("Y")+1;
									else
										$i = idate("Y");
									$end = $i;
									
									while ($i >= 1950)
									{
										if($i == $end)
											echo "<option selected value = '".$i."'>". ($i-1) ."-".$i."</option>";
										else
											echo "<option value = '".$i."'>". ($i-1) ."-".$i."</option>";
										
										$i--;
									}
								?>
								</select><br/>
								<br/>
								<input size = "2" disabled id = "newWId"/>
								<input size = "15" type = "text" id = "newWLast" placeholder = "Last Name"/>
								<input size = "12" type = "text" id = "newWFirst" placeholder = "First Name"/>
								<input size = "3" type = "text" id = "newWMiddle"  placeholder = "Middle" />
								<select id = "newWGender">
									<option selected disabled>Select Gender</option>
									<option value = "1">F</option>
									<option value = "2">M</option>
								</select>
								<input size = "15" type = "text" id = "newWPhone"  placeholder = "Phone" />
								<input size = "25" type = "text" id = "newWEmail"  placeholder = "Email" /><br/>
								<button id = "addPerson" onclick ="affiliatePerson();">Affiliate person to season</button> <button id = "addNewPerson" onclick ="addNewPerson();">Add New Person to Database</button> <!--button id = "editPersonW">Name Update</button--><br/>
								<p>Registration gives registration access. Administrate gives access to this page and the Meet Hosting pages.</p>
								<div id="teamTable"></div> <br>
								
								<script type="text/javascript">
									$("#teamTable").tabulator({
										pagination:"local", 
										paginationSize:30,
										layout: "fitDataFill",
										columns:[
											{title:"ID", 			field:"ID", 			visible:false},
											{title:"PermID", 		field:"PermissionID", 	visible:false},
											{title:"First",	 		field:"FirstName",		editor: "input",	 	formatter:"plaintext",	sorter:"string"	},
											{title:"Middle", 		field:"MiddleName",		editor: "input",	 	formatter:"plaintext",	sorter:"string"	},
											{title:"Last", 			field:"LastName",		editor: "input",	 	formatter:"plaintext",	sorter:"string"	},
											{title:"Phonetic/Nick", field:"Phonetic",		editor: "input",	 	formatter:"plaintext",	sorter:"string"	},
											{title:"M/F", 			field:"Gender",			mutator:genderMutator, 	formatter:"plaintext",	sorter:"string", editor:"select", editorParams:{values:{"2":"M","1":"F"}}},
											{title:"Email",			field:"Email",	 		editor: "input",	 	formatter:"plaintext"},
											{title:"Phone", 		field:"Phone",	 		editor: "input",	 	formatter:"plaintext"},
											{title:"Registration",	field:"Registration",	formatter:"tickCross", sorter:"boolean", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
											{title:"Administrate", 	field:"Administrate",	formatter:"tickCross", sorter:"boolean", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
											{title:"Type", 			field:"Type",			formatter:"plaintext",	sorter:"string", editor:"select", editorParams:{values:["Gymnast","Coach","School Staff","Gym Staff"]}},
										],
										index:"ID",
										cellEdited:function(cell){
											//This callback is called any time a cell is edited
											var row = cell.getRow();
											var data = row.getData();
											var col = cell.getColumn();
											if(cell.getField() == "FirstName")
											{
												savePersonName(data.ID,data.FirstName,"FirstName");
											}
											else if(cell.getField() == "MiddleName")
											{
												savePersonName(data.ID,data.MiddleName,"MiddleName");
											}
											else if(cell.getField() == "LastName")
											{
												savePersonName(data.ID,data.LastName,"LastName");
											}
											else if(cell.getField() == "Phonetic")
											{
												savePersonName(data.ID,data.Phonetic,"Phonetic");
											}
											else if(cell.getField() == "Gender")
											{
												if(data.Gender == "M")
													gender = 2;
												if(data.Gender == "F")
													gender = 1;
												savePersonGender(data.ID,gender);
											}
											else if(cell.getField() == "Email")
											{
												savePersonEmail(data.ID,data.Email);
											}
											else if(cell.getField() == "Phone")
											{
												savePersonPhone(data.ID,data.Phone);
											}
											else if(cell.getField() == "Registration")
											{
												savePersonPermission(data.ID,"Registration",data.Registration);
											}
											else if(cell.getField() == "Administrate")
											{
												savePersonPermission(data.ID,"Administrate",data.Administrate);
											}
											else if(cell.getField() == "Type")
											{
												savePersonType(data.ID,data.Type);
											}
										}
									});
								</script>
								<div id="popUpDiv">
									<div id="middlePopUp">
										Officer Position(s): <br/>
										<input name = "officerPositions" type = "text"/><br/>
										Website Permissions:<br/>
											<select multiple name = "websitePositions">
												<option>Meet Scoring</option>
												<option>Meet Registration</option>
												<option>Team Management</option>
											</select><br/>
										Degree Type:<br/> 
													<select name = "degreeType">
														<option>Non-Degree Student</option>
														<option>Certificate</option>
														<option>Associates</option>
														<option>Bachelors</option>
														<option>Masters</option>
														<option>Doctorate</option>
														<option>J.D. Law Doctorate</option>
														<option>M.D. Medical Doctorate</option>
														<option>D.V.M. (Veterinary)</option>
														<option>PostDoctorate</option>
													 </select><br/>
										Major/Field:<br/>
													<input type = "text" name = "studyField" onchange = "getDegreeID();"/> DegID: <div name = "degID"></div>
										Exp Grad:<br/> 
												<select name = "graduationDate">
												<?php
													$i = idate("Y")+10;
													while ($i >= 1950)
													{
														if($i == idate("Y"))
															echo "<option selected value = '".$i."'>".$i."</option>";
														else
															echo "<option value = '".$i."'>".$i."</option>";
														$i--;
													}
												?>
												</select><br/>
										<input type = "button" value = "Add Degree"/>
									</div>
								</div>
								
						<?php
							}
							else
							{
								echo "<p>You are not a team administrator. Please log in.</p>";
								display_login();
								
							}
						?>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>
