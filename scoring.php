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
			Season >= 2020 AND
			ID NOT IN (96,98)
		ORDER BY
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
	function loadMeetData()
	{
		loadScoreData(1);
		loadScoreData(2);
		loadTeamData(1);
		loadTeamData(2);
	}
	function loadScoreData(iDiscipline)
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
					getAllScoresForMeetDiscipline: 1,
					institutionID: false,
					meetID: document.getElementById("meetSelectMenu").value,
					DisciplineID: iDiscipline
				},
				dataType: 'json',
				success: function (data) {
					if(iDiscipline == 2)
						$("#menScoreTable").tabulator("setData", data);
					if(iDiscipline == 1)
						$("#womenScoreTable").tabulator("setData", data);
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading "+iDiscipline+" person data");
				}
			});
		}
	}
	function loadTeamData(iDiscipline)
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
					DisciplineID: iDiscipline
				},
				dataType: 'json',
				success: function (data) {
					if(iDiscipline == 2)
						$("#menTeamScoreTable").tabulator("setData", data);
					if(iDiscipline == 1)
						$("#womenTeamScoreTable").tabulator("setData", data);
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading "+iDiscipline+" team data");
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
								while($row = $stmtMeets->fetch(PDO::FETCH_ASSOC))
								{
									if(isset($temp[$row['Hostclub']]))
									{
										echo "<option value = '".$row['ID']."'>".$row['MeetName']."(".$row['Date'].")</option>";
									}
								}
								echo "</select> <button onclick = 'loadMeetData();'>&#x21bb;</button><br/><br/>";
								
								?>
								<p>The team score entry panel will consist of the two tables below. The checkmarks will be replaced on the date of the meet with a field to input the score.</p>
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
										groupBy: "CompetitionID",
										columns:[
											{title:"ID", 			field:"ID", 		visible:false},
											{title:"Name",	 		field:"Name",	 	sorter:"string"},
											{title:"CompetitionID", field:"CompetitionID", 		visible:false},
											{title:"DisciplineID", 		field:"DisciplineID", 	visible:false},
											{title:"Competition",	field:"Team",	 	},
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
										groupHeader:function(value, count, data, group){return data[0].Team;},
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
											
											//all I need is person meet|competition|Discipline and event
											if(cell.getField()!="")
											{ 
												saveScore(data.ID, cell.getValue(), data.CompetitionID, changedEvent);
											}
										},
									});
									
									$("#womenScoreTable").tabulator({
										layout: "fitDataFill",
										groupBy: "CompetitionID",
										columns:[
											{title:"ID", 			field:"ID", 		visible:false},
											{title:"Name",	 		field:"Name",	 	sorter:"string"},
											{title:"CompetitionID", field:"CompetitionID", 		visible:false},
											{title:"DisciplineID", 		field:"DisciplineID", 	visible:false},
											{title:"Competition", 	field:"Team",	 	},
											{title:"Team", 			field:"Institution",	 	},
											{title:"VT", 			field:"WVT",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}, 	editor:"number"},
											{title:"UB", 			field:"WUB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}, 	editor:"number"},
											{title:"BB", 			field:"WBB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}, 	editor:"number"},
											{title:"FX", 			field:"WFX",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}, 	editor:"number"},
											{title:"AA", 			field:"WAA",	 	sorter:"number",	formatter:"money", 	formatterParams:{precision:3}},
										],
										index:"ID",
										groupHeader:function(value, count, data, group){return data[0].Team;},
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
											
											//all I need is person meet|competition|Discipline and event
											if(cell.getField()!="")
											{ 
												saveScore(data.ID, cell.getValue(), data.CompetitionID, changedEvent);
											}
										},
									});
									
									$("#womenTeamScoreTable").tabulator({
										layout: "fitDataFill",
										groupBy: "CompetitionName",
										columns:[
											{title:"Team",	 		field:"InstitutionName",	 	sorter:"string"},
											{title:"Competition", 	field:"CompetitionName"},
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
										groupBy: "CompetitionName",
										columns:[
											{title:"Team",	 		field:"InstitutionName",	 	sorter:"string"},
											{title:"Competition", 	field:"CompetitionName"},
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
