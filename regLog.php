<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>

<script type="text/javascript" src="js/moment.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.min.js"></script>

<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
						<h2>Registration Activity Log</h2>
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
								Season >= 2008 AND
								scored = 1
							ORDER BY
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

					function getInstitutionNameFromID($ID)
					{
						global $conn;
						$thename = "Unknown";
				
						$query = "
								SELECT
									Name
								FROM
									Identifiers_Institutions
								WHERE
									ID = ?
							;";
						if($stmt = $conn->prepare($query))
						{
							$stmt->bindParam(1,$ID,PDO::PARAM_INT);
							$stmt->execute();
							//$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;
						}
						else
						{
							printf("Errormessage: %s\n", $conn->error);
						}
						while($row = $stmt->fetch(PDO::FETCH_ASSOC))
						{
							$thename = $row['Name'];
						}		
						return $thename;
					}

					function getCompName($id)
					{
						global $conn;
						$sql = "SELECT
									concat(
											Constraints_MeetDivisions.Name, ' ',
											Constraints_MeetLevels.DisplayName, ' ',
											Constraints_Disciplines.DisciplineShortName
										) AS CompetitionName
								FROM
									Constraints_MeetDivisions,
									Constraints_MeetLevels,
									Constraints_Disciplines,
									Events_Competitions
								WHERE
									Events_Competitions.ID = ? AND
									Events_Competitions.Division = Constraints_MeetDivisions.ID AND
									Events_Competitions.Level = Constraints_MeetLevels.ID AND
									Events_Competitions.Discipline = Constraints_Disciplines.ID
								";
						$stmt = $conn->prepare($sql);
						$stmt->bindParam(1, $id, PDO::PARAM_INT);	
						
						$stmt->execute();
						while($row = $stmt->fetch(PDO::FETCH_ASSOC))
						{
							return $row['CompetitionName'];
						}
					}

					if(userIsExecutiveAdministrator())
					{

					$sql = "
							Select 
								Log_Registration.Action,
								Log_Registration.Timestamp,
								Log_Registration.PersonID as SubjectID,
								concat(a.FirstName, ' ', a.LastName) as PersonID,
								concat(b.FirstName, ' ', b.LastName) as UserID,
								concat(c.FirstName, ' ', c.LastName) as EmulatorID,
								ItemKey,
								Value,
								concat(
									Events_Meets.Season, ' ',
									Events_Meets.MeetName, ' ',
									Constraints_MeetDivisions.Name, ' ',
									Constraints_MeetLevels.DisplayName, ' ',
									Constraints_Disciplines.DisciplineShortName
								 ) AS CompetitionName
							From
								Events_Competitions,
								Constraints_MeetDivisions,
								Constraints_MeetLevels,
								Constraints_Disciplines,
								Events_Meets,
								Log_Registration
							Left Join
								Identifiers_People a
							ON
								Log_Registration.PersonID = a.ID
							Left Join
								Identifiers_People b
							ON
								Log_Registration.UserID = b.ID
							Left Join
								Identifiers_People c
							ON
								Log_Registration.EmulatorID = c.ID
							Where
								Log_Registration.CompetitionID = Events_Competitions.ID AND
								Events_Competitions.Division = Constraints_MeetDivisions.ID AND
								Events_Competitions.Level = Constraints_MeetLevels.ID AND
								Events_Competitions.Discipline = Constraints_Disciplines.ID AND	
								Events_Competitions.MeetID = Events_Meets.ID AND
								Events_Meets.ID = ?
							Order By 
								Timestamp Desc
							;";

					$stmt = $conn->prepare($sql);

					if(isset($_REQUEST['meetID']))
					{
						$meetid = $_REQUEST['meetID'];
						$stmt->bindParam(1, $meetid, PDO::PARAM_INT);

						$stmt->execute();
						
						$theArray = array();
						$count = 0;

						while($row = $stmt->fetch(PDO::FETCH_ASSOC))
						{
							$EventLookup = array(
								'1'=>'MFX',
								'2'=>'MPH',
								'3'=>'MSR',
								'4'=>'MVT',
								'5'=>'MPB',
								'6'=>'MHB',
								'8'=>'WVT',
								'9'=>'WUB',
								'10'=>'WBB',
								'11'=>'WFX'
							);
							
							$key = $row['ItemKey'];
							$value = $row['Value'];
							$subject = $row['PersonID'];

							if(($row['Action'] == 'Person Added to Event') || ($row['Action'] == 'Register Person for Event') || ($row['Action'] == 'Person Removed from Event'))
								$key = $EventLookup[$row['ItemKey']];
							if($row['Action'] == "Person's Competition (level/division) Changed")
							{
								$key = getCompName($key);
								$value = getCompName($value);
							}
							if($row['Action'] == "Team Score Changed")
							{
								$key = $row['ItemKey'];
								$subject = getInstitutionNameFromID($row['SubjectID']);
							}

							//echo "in the loop";
							$theArray[$count] = array(
													'Competition'=>$row['CompetitionName'],
													'Action'=>$row['Action'],
													'Subject'=>$subject,
													'Timestamp'=>$row['Timestamp'],
													'Key'=>$key,
													'Value'=>$value,
													'UserID'=>$row['UserID'],
													'Emulator'=>$row['EmulatorID']
													);
							
							$count++;
						}
					}

					echo '<p>Note:"Register Person for Event" is triggered when a person is added to the competition. "Person Added to Event" is triggered when the table checkbox is clicked after registration. </p>';

					$stmtMeets = getMeets();
					echo "<select id = 'meetSelectMenu' onchange = 'loadMeetData();'>
						<option selected disabled>Select a meet:</option>";
					while($row = $stmtMeets->fetch(PDO::FETCH_ASSOC))
					{
						$selected = 0;
						if(isset($_REQUEST['meetID']))
						{
							$selected = $_REQUEST['meetID'];
						}
						if($row['ID'] == $selected)
							echo "<option selected value = '".$row['ID']."'>".$row['MeetName']."(".$row['Date'].")</option>";
						else
							echo "<option value = '".$row['ID']."'>".$row['MeetName']."(".$row['Date'].")</option>";
					}
					echo "</select> <button onclick = 'loadMeetData();'>&#x21bb;</button><br/><br/>";

					?>
					<h2>Download Registration: <span onclick = 'table.download("csv", "meetRegLog.csv");'>(csv)</span></h2>
					<div id = "thetable"></div>
					<!--style>
						#content > .inner{
							max-width: 1400px !important;
						}
						.tabulator-header{
							height: 80px;
						}
					</style-->
					<script type="text/javascript">
						function loadMeetData()
						{
							window.location.href = "regLog.php?meetID=" + document.getElementById("meetSelectMenu").value;
						}

						var theData = [];

						var table = new Tabulator("#thetable", 
						{
							layout: "fitColumns",
							layoutColumnsOnNewData:true,
							columns:[
								{title:"Comp", field:"Competition", headerFilter:"input"},
								{title:"Action", field:"Action", headerFilter:"input"},
								{title:"Subject", field:"Subject", headerFilter:"input"},
								{title:"Timestamp", field:"Timestamp", headerFilter:true },
								{title:"Key", field:"Key", headerFilter:"input"},
								{title:"Value", field:"Value", headerFilter:"input"},
								{title:"UserID", field:"UserID", headerFilter:"input"},
								{title:"Emulator", field:"EmulatorID", headerFilter:"input"},
							],
						});

						<?php
						if(isset($_REQUEST['meetID']))
						{
						?>
							theData = <?php echo json_encode($theArray); ?>;
							table.on("tableBuilt", function(){
								table.setData(theData);
							});
						<?php
						}
						?>

					</script>
					
					<?php
					}	
					else
					{
						echo "You do not have permission to access this page.";
						display_login();
					}
					?>

						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
</body>	
</html>		