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
			Season >= 2019 AND
			ID NOT IN (96,98)
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
					getRegistrationMetricsForMeetGender: 1,
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
	
	var resultFormatter = function (cell, formatterParams)
	{
		var color = "green";
		if(cell.getValue() == "xxx")
			color = "#FFC30F";
		else if (cell.getValue() == "+++")
			color = "red";
		return "<span style='color:"+color+"; font-weight:bold;'>" + cell.getValue() + "</span>";
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
								<p>
									Legend:
									<ul>
										<li><span style="color:green; font-weight:bold;">==</span>&nbsp; Person did what they registered for (either competed or not)</li>
										<li><span style="color:#FFC30F; font-weight:bold;">xxx</span> Person was registered but did not compete</li>
										<li><span style="color:red; font-weight:bold;">+++</span> Person was not registered and competed this event extra</li>
									</ul>
									It was not available for 2019 but I can look at the individual registration in the future, i.e. we can correlate the date a specific event was added or removed compared to when the person was first registered. 
									We could also do other things like display the date of registration or maybe highlight the person's name in red if they were a late fee addition.
								</p>
								<h2>Womens Teams and Competitors:</h2>
								<div id="womenScoreTable"></div>
								<br/>
								<h2>Mens Teams and Competitors:</h2>
								<div id="menScoreTable"></div> <br>
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
											{title:"FX", 			field:"MFX",	 	sorter:"string", formatter:resultFormatter},
											{title:"PH", 			field:"MPH",	 	sorter:"string", formatter:resultFormatter},
											{title:"SR", 			field:"MSR",	 	sorter:"string", formatter:resultFormatter},
											{title:"VT", 			field:"MVT",	 	sorter:"string", formatter:resultFormatter},
											{title:"PB", 			field:"MPB",	 	sorter:"string", formatter:resultFormatter},
											{title:"HB", 			field:"MHB",	 	sorter:"string", formatter:resultFormatter},
											{title:"AA", 			field:"MAA",	 	sorter:"string", formatter:resultFormatter},
										],
										index:"ID",
										groupHeader:function(value, count, data, group){return data[0].Team;}
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
											{title:"Team", 			field:"Institution",	 	},
											{title:"VT", 			field:"WVT",	 	sorter:"string", formatter:resultFormatter},
											{title:"UB", 			field:"WUB",	 	sorter:"string", formatter:resultFormatter},
											{title:"BB", 			field:"WBB",	 	sorter:"string", formatter:resultFormatter},
											{title:"FX", 			field:"WFX",	 	sorter:"string", formatter:resultFormatter},
											{title:"AA", 			field:"WAA",	 	sorter:"string", formatter:resultFormatter},
										],
										index:"ID",
										groupHeader:function(value, count, data, group){return data[0].Team;}
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
