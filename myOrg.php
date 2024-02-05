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
						ID IN (7126)
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
					getOrgData: 1,
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
	
	function validateEmail(email) {
		const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(String(email).toLowerCase());
	}
	
	function addNewPerson()
	{
		var nameIsValid = false;
		var emailIsValid = false;
		var genderIsValid = false;
		var statusIsValid = false;
		var iLastName = document.getElementById("newWLast").value;
		var iFirstName = document.getElementById("newWFirst").value;
		var iMiddleName = document.getElementById("newWMiddle").value;
		
		var iPhone = document.getElementById("newWPhone").value;
		var iGender = document.getElementById("newWGender").value;
		var iStatus = document.getElementById("newWStatus").value;
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
		}
		if(validateEmail(iEmail))
		{
			emailIsValid = true;
		}
		else
		{
			alert("Please make sure an email is entered for this user, it will be their log in.");
		}

		if(nameIsValid && emailIsValid)
		{
			//then show a popup asking if its for a different team
			//if (confirm("Does this person attend " + schoolsLookup[document.getElementById('selectClub').value] + "?") == true)
			var emailIsUnused = true;
			$.ajax({
				type: 'POST',
				url: "teamEditAjax.php",
				async: false,
				data: {
					checkIfEmailIsUsed: 1,
					emailToCheck: iEmail
				},
				dataType: 'json',
				success: function (data) {
					if(data.emailExists >= 1)
					{
						emailIsUnused = false;
					}
				},
				error: function (textStatus, errorThrown) {
					alert("error validating email");
					emailIsUnused = false;
				}
			});

			var phoneIsUnused = true;
			if(iPhone != "")
			{
				$.ajax({
					type: 'POST',
					url: "teamEditAjax.php",
					async: false,
					data: {
						checkIfPhoneIsUsed: 1,
						phoneToCheck: iPhone
					},
					dataType: 'json',
					success: function (data) {
						if(data.phoneExists >= 1)
						{
							phoneIsUnused = false;
						}
					},
					error: function (textStatus, errorThrown) {
						alert("error validating phone");
						phoneIsUnused = false;
					}
				});
			}
			if(emailIsUnused && phoneIsUnused)
			{
				iInstitutionID = document.getElementById("selectClub").value;
				InstitutionName = schoolsLookup[document.getElementById("selectClub").value];
				//todo: solve scope issues and remove code duplicate. 
				if(confirm("Pressing ok will create a brand new gymnast for " + iFirstName + " " + iLastName + " who will start out with affiliation for " + InstitutionName + " for the " + season + " season. If this is not correct press cancel."))
				{
					if(iGender >= 1)
						genderIsValid = true;

					if(iStatus != "")
						statusIsValid = true;	
					
					if(genderIsValid)
					{
						if(statusIsValid)
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
									status: iStatus,
									phone: iPhone,
									email: iEmail,
									season: season
								},
								//dataType: 'json',
								success: function () {
									//alert("removed");
									changeYearClub();

									//reset. Need to put in frunction.
									document.getElementById("newWFirst").value = "";
									document.getElementById("newWLast").value = "";
									document.getElementById("newWMiddle").value = "";
									document.getElementById("newWId").value = "";
									document.getElementById("newWGender").value = "";
									document.getElementById("newWStatus").value = "";
									document.getElementById("newWPhone").value = "";
									document.getElementById("newWEmail").value = "";
									
								},
								error: function (textStatus, errorThrown) {
									//console.log(errorThrown);
									alert("error adding person");
								}
							});
						}
						else
						{
							alert("Please input student status.");	
						}
					}
					else
					{
						alert("Please input gender.");
					}
				}
				else
				{
					;//do nothing, they hit cancel
				}
			}
			else 
			{
				if(emailIsUnused == false)
					alert("A user with that email already exists.");
				if(phoneIsUnused == false)
					alert("A user with that phone number already exists.");
			}
		}		
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
			dataType: 'json',
			success: function (data) {
				if(data.hasOwnProperty('errorMsg'))
				{
					alert(data.errorMsg);
					changeYearClub();
				}
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
				updateTGCPermission: iPermission,
				permissionValue: iValue,
				personID: iPerson,
				Season: iSeason
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

	function removeClubAffiliation(iSeason,iID)
    {
		var iSeason = document.getElementById("selectYear").value;
		var iInstitutionID = document.getElementById("selectClub").value;
		if(iID != "")
		{
			savePersonPermission(iID,"Registration","false");
			savePersonPermission(iID,"Administrate","false");
			$.ajax({
				type: 'POST',
				url: "teamEditAjax.php",
				async: false,
				data: {
					removePersonFromSeason: 1,
					season: iSeason,
					personID: iID,
					institutionID: iInstitutionID
				},
				//dataType: 'json',
				success: function () {
					getListOfUserClubAffiliations();
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error affiliating");
				}
			});
		}
    }

	function addMembershipToCart(ipersonID,item)
    {
		//todo: validate type
		
		//alright, now do an ajax call to get the logged in userID and add the stuff to the user's cart.
		$.ajax({
			type: 'POST',
			url: "paymentAjax.php",
			async: false,
			data: {
				membershipTier: item,
				personID : ipersonID, 
				addMembershipFeeToCart: true,
			},
			//dataType: 'json',
			success: function () {
				checkShoppingCart();
			},
			error: function (textStatus, errorThrown) {
				//console.log(errorThrown);
				alert("error adding to cart");
			}
		});

    }

	var membershipButton = function(cell)
	{
		var iSeason = document.getElementById("selectYear").value;
		var currentSeason = <?php echo getCurrentSeason(); ?>;

		if(!((iSeason == currentSeason) || (iSeason == currentSeason+1)))
			return "";
		
		/*var memberid = cell.getRow().getData().ID;
		var isPaid = false;
		var coachPaid = false;
		$.ajax({
			type: 'POST',
			url: "paymentAjax.php",
			async: false,
			data: {
				getMembershipOptions : 1,
				personID : memberid
			},
			dataType: 'json',
			success: function (data) {
				console.log(data);
				if(data.hasOwnProperty("upgrade"))
					coachPaid = true;
				if(data.hasOwnProperty("paid"))
					isPaid = true;
			},
			error: function (textStatus, errorThrown) {
				//console.log(errorThrown);
				alert("error getting available membership tiers");
			}
		});*/
		var coachPaid = false;
		var isPaid = false;
		var row = cell.getRow();
		var data = row.getData();
		var memberid = data.ID;
		if(data.MembershipOptionsArray.hasOwnProperty("upgrade"))
			coachPaid = true;
		if(data.MembershipOptionsArray.hasOwnProperty("paid"))
			isPaid = true;

		if(isPaid)
			return "Purchased";
		else if(coachPaid)
		{
			return "<button onclick = 'addMembershipToCart("+memberid+",\"Upgrade Membership\");'>Upgrade Membership</button>";
		}
		else
			return "<button onclick = 'addMembershipToCart("+memberid+",\"Coach Membership\");'>Coach Membership</button><button onclick = 'addMembershipToCart("+memberid+",\"Gymnast Membership\");'>Gymnast Membership</button>";
	};
	
	$(document).ready(function(){
		$("#newWLast").autocomplete({
			delay:350,
			source: function(request, response) {
				$.getJSON(
					"nameAutocomplete.php",
					{ 
						term:request.term
					}, 
					response
				);
			},
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
							if(userIsSuperAdministrator())
							{			
										echo "<h2>Select the club and year you are managing:</h2>";
										echo "<select id = 'selectClub' onchange = 'changeYearClub();'>";
											echo "<option selected disabled value = '0'>Select a club:</option>";
											echo "<option value = '7126'>TGC</option>";
										echo "</select><button onclick = 'changeYearClub();'>&#x21bb;</button><br/>";	
													
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
								<input size = "15" type = "text" id = "newWLast" autocomplete = "off" placeholder = "Last Name"/>
								<input size = "12" type = "text" id = "newWFirst" placeholder = "First Name"/>
								<input size = "3" type = "text" id = "newWMiddle"  placeholder = "Middle" />
								<select id = "newWGender">
									<option selected disabled>Select Gender</option>
									<option value = "1">F</option>
									<option value = "2">M</option>
								</select>
								<select id = "newWStatus">
									<option selected disabled value ="">Student Status</option>
									<option value = "Student">Student</option>
									<option value = "Part-Time Student">Part-Time Student</option>
									<option value = "Non-Student">Non-Student</option>
								</select>
								<input size = "15" type = "text" id = "newWPhone"  placeholder = "Phone" />
								<input size = "25" type = "text" id = "newWEmail"  placeholder = "Email" /><br/>
								<button id = "addPerson" onclick ="affiliatePerson();">Affiliate person to season</button> <button id = "addNewPerson" onclick ="addNewPerson();">Add New Person to Database</button> <!--button id = "editPersonW">Name Update</button--><br/>
								<p>Super Admin gives access to *this* page. TGC Admin gives typical all-club-admin access.</p>
								<div id="teamTable"></div> <br>
								
								<script type="text/javascript">
									$("#teamTable").tabulator({
										pagination:"local", 
										paginationSize:30,
										layout: "fitDataFill",
										columns:[
											{title:"",		 		field:"Remove", 	formatter:"buttonCross", cellClick:function(e,cell){cell.getRow().delete();}	},
											{title:"ID", 			field:"ID", 			visible:false},
											{title:"PermID", 		field:"PermissionID", 	visible:false},
											{title:"First",	 		field:"FirstName",		editor: "input",	 	formatter:"plaintext",	sorter:"string"	},
											{title:"Middle", 		field:"MiddleName",		editor: "input",	 	formatter:"plaintext",	sorter:"string"	},
											{title:"Last", 			field:"LastName",		editor: "input",	 	formatter:"plaintext",	sorter:"string"	},
											{title:"Phonetic/Nick", field:"Phonetic",		editor: "input",	 	formatter:"plaintext",	sorter:"string"	},
											//{title:"M/F", 			field:"Gender",			mutator:genderMutator, 	formatter:"plaintext",	sorter:"string", editor:"select", editorParams:{values:{"2":"M","1":"F"}}},
											{title:"Email",			field:"Email",	 		editor: "input",	 	formatter:"plaintext"},
											{title:"Phone", 		field:"Phone",	 		editor: "input",	 	formatter:"plaintext"},
											{title:"Super Admin",	field:"TGCSuperAdmin",		formatter:"tickCross", sorter:"boolean", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
											{title:"TGC Admin",	field:"TGCAdmin",		formatter:"tickCross", sorter:"boolean", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
											{title:"Emulate", 		field:"Emulate",		formatter:"tickCross", sorter:"boolean", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
											//{title:"Create<br>Waiver", 		field:"Waiver",		formatter:"tickCross", sorter:"boolean", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
											//{title:"Issue<br>Refund", 		field:"Refund",		formatter:"tickCross", sorter:"boolean", cellClick:function(e, cell){cell.setValue(!cell.getValue());}	},
											//{title:"MembershipPaid", field:"MembershipPaid", 	visible:false},
											//{title:"Membership Paid?", field:"Membership",			formatter:membershipButton, 	sorter:"string"},
										],
										index:"ID",
										rowDeleted:function(row){
											var data = row.getData();
											removeClubAffiliation(data.Season,data.ID);
										},
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
											else if(cell.getField() == "TGCAdmin")
											{
												savePersonPermission(data.ID,"TGCAdmin",data.TGCAdmin);
											}
											else if(cell.getField() == "Emulate")
											{
												savePersonPermission(data.ID,"TGCEmulation",data.Emulate);
											}
											else if(cell.getField() == "TGCSuperAdmin")
											{
												savePersonPermission(data.ID,"TGCSuperAdmin",data.TGCSuperAdmin);
											}
											else if(cell.getField() == "Refund")
											{
												savePersonPermission(data.ID,"IssueRefund",data.Refund);
											}
											else if(cell.getField() == "Waiver")
											{
												savePersonPermission(data.ID,"CreateWaiver",data.Waiver);
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
								echo "<p>You are not an TGC super administrator. Please log in.</p>";
								display_login();
								
							}
						?>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>