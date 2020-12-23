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
?>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.js"></script>

<script>
	
	function loadMeetData()
	{
		loadScoreData(1);
		loadScoreData(2);
		loadTeamData(1);
		loadTeamData(2);
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
						<p>You can see the score history of a person <a href = "https://tgcgymnastics.com/gymnast">here.</a></p>
						<p>To see the top scores of all time click <a href = "https://tgcgymnastics.com/topScores">here.</a></p>
						<?php
								$stmtMeets = getMeets();
								echo "<select id = 'meetSelectMenu' onchange = 'loadMeetData();'>
									<option selected disabled>Select a meet:</option>";
								while($row = $stmtMeets->fetch(PDO::FETCH_ASSOC))
								{
									echo "<option value = '".$row['ID']."'>".$row['MeetName']."(".$row['Date'].")</option>";
								}
								echo "</select> <button onclick = 'loadMeetData();'>&#x21bb;</button><br/><br/>";
								
								?>
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
											{title:"GenderID", 		field:"GenderID", 	visible:false},
											{title:"Competition",	field:"Team",	 	},
											{title:"Team", 			field:"Institution",	 	},
											{title:"Designation", 	field:"Designation",	 	},
											{title:"FX", 			field:"MFX",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"PH", 			field:"MPH",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"SR", 			field:"MSR",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"VT", 			field:"MVT",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"PB", 			field:"MPB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"HB", 			field:"MHB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"AA", 			field:"MAA",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
										],
										index:"ID",
										groupHeader:function(value, count, data, group){return data[0].Team;},
										//takes too long to reload everything. Just save it and update AA.
									});
									
									$("#womenScoreTable").tabulator({
										layout: "fitDataFill",
										groupBy: "CompetitionID",
										columns:[
											{title:"ID", 			field:"ID", 		visible:false},
											{title:"Name",	 		field:"Name",	 	sorter:"string"},
											{title:"CompetitionID", field:"CompetitionID", 		visible:false},
											{title:"GenderID", 		field:"GenderID", 	visible:false},
											{title:"Competition", 	field:"Team",	 	},
											{title:"Team", 			field:"Institution",	},
											{title:"Designation", 	field:"Designation",	},
											{title:"VT", 			field:"WVT",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"UB", 			field:"WUB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"BB", 			field:"WBB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"FX", 			field:"WFX",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"AA", 			field:"WAA",	 	sorter:"number",	formatter:"money", 	formatterParams:{precision:3}},
										],
										index:"ID",
										groupHeader:function(value, count, data, group){return data[0].Team;},
									});
									
									$("#womenTeamScoreTable").tabulator({
										layout: "fitDataFill",
										groupBy: "CompetitionName",
										columns:[
											{title:"Team",	 		field:"InstitutionName",	 	sorter:"string"},
											{title:"Designation", 	field:"Designation",	 	},
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
										groupBy: ["CompetitionName","Designation"],
										columns:[
											{title:"Team",	 		field:"InstitutionName",	 	sorter:"string"},
											{title:"Designation", 	field:"Designation",	 	},
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
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>
