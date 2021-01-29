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
			ID NOT IN (96,98) AND
			DATE >= Date(NOW())
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
	function saveScore(iperson, iscore, icompetition, ievent, istartValue)
	{
		var doit = true;
		var returnVal = false;
		//first check that for womens events its < 10
		if((ievent <= 11)&&(ievent >=7))
			if(iscore > 10)
			{
				doit = false;
				alert ("Please try again. Women's scores are capped at 10.0");
			}
		//add SV and check score < SV
		if(istartValue < iscore)
		{
			doit = false;
			alert ("Please try again. The start value cannot be less than the score. Did you switch them?");
		}
		
		if(doit)
		{
			$.ajax({
				type: 'POST',
				url: "scoreAjax.php",
				async: false,
				data: {
					updatePersonScore: 1,
					person: iperson,
					score: iscore,
					startValue: istartValue,
					competition: icompetition,
					event: ievent
				},
				dataType: 'json',
				success: function (data) {
					returnVal = true;
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error saving score");
				}
			});
		}
		return returnVal;
	}
	function loadScoreData(iDiscipline)
	{
		//run only if a valid meet and event have been selected.		
		if((document.getElementById("eventSelection").value != "")&&(document.getElementById("meetSelectMenu").value != ""))
		{
			var event = document.getElementById("eventSelection");
			var iEvent = event.selectedIndex;
			var iDiscipline = 1
			if(iEvent < 7)
				iDiscipline = 2
			
			$.ajax({
				type: 'POST',
				url: "scoreAjax.php",
				async: false,
				data: {
					getAllScoresForMeetEvent: 1,
					eventID: iEvent,
					DisciplineID: iDiscipline,
					institutionID: false,
					meetID: document.getElementById("meetSelectMenu").value
				},
				dataType: 'json',
				success: function (data) {
					$("#ScoreTable").tabulator("setData", data);
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading score data");
				}
			});
		}
	}
	
	function buttonClicked()
	{
		//make sure a meet and an event have both been selected.
		
		var event = document.getElementById("eventSelection");
		var eventID = event.selectedIndex;
		var eventLabel = event.options[event.selectedIndex].text + " Score";
		
		//and populate it with that event's data.
		if((eventID <= 11)&&(eventID >7))
			loadScoreData(1);
		else
			loadScoreData(2);
	}
	
</script>

<style>
	
	.tabulator-cell{
		height: 40px;
		padding: 0px 4px 0px 4px !important;
		font-size: 17px;
		line-height: 38px;
	}
	
	.tabulator .tabulator-header .tabulator-col{
		height: 64px !important;
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
								echo "<select id = 'meetSelectMenu'>
									<option selected disabled value = ''>Select a meet:</option>";
								if($stmtMeets->num_rows >= 1){
									while($row = $stmtMeets->fetch(PDO::FETCH_ASSOC))
									{
										if(isset($temp[$row['Hostclub']]))
										{
											echo "<option value = '".$row['ID']."'>".$row['MeetName']."(".$row['Date'].")</option>";
										}
									}
								}
								echo "</select><br/><br/>";
								
							?>
								<p>Which event are you scoring?</p>
								<select id = "eventSelection">
									<option selected disabled value = "">Select an event to score</option>
									<option value = "1">Men's Floor</option>
									<option value = "2">Men's Pommels</option>
									<option value = "3">Men's Rings</option>
									<option value = "4">Men's Vault</option>
									<option value = "5">Men's Parallel Bars</option>
									<option value = "6">Men's Horizontal Bar</option>
									<option disabled value = "7"></option>
									<option value = "8">Women's Vault</option>
									<option value = "9">Women's Bars</option>
									<option value = "10">Women's Beam</option>
									<option value = "11">Women's Floor</option>
								</select>
								
								<button name = "selectEvent" id = "selectEvent" onclick = "buttonClicked();">Submit</button><br/><br/>
								
								<h2>Competitors:</h2>
								<div id="ScoreTable"></div>
								<br/>
								<script type = "text/javascript">
									$("#ScoreTable").tabulator({
										layout: "fitDataFill",
										groupBy: "CompetitionID",
										resizableColumns:false,
										autoResize:false,
										columns:[
											{title:"ID", 			field:"ID", 		visible:false},
											{title:"Name",	 		field:"Name",	 	sorter:"string", headerVertical:true},
											{title:"CompetitionID", field:"CompetitionID", 		visible:false},
											{title:"DisciplineID", 		field:"DisciplineID", 	visible:false},
											{title:"Competition",	field:"Team",	 	visible:false, headerVertical:true},
											{title:"Team", 			field:"Institution", width:50, headerVertical:true	 	},
											{title:"SV", 	field:"SV",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:1},	editor:"number", width:50, headerVertical:true},
											{title:"Score", 		field:"Score",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3},	editor:"number", width:53, headerVertical:true},
										],
										index:"ID",
										groupHeader:function(value, count, data, group){return data[0].Team;},
										//takes too long to reload everything. Just save it and update AA.
										cellEdited:function(cell){
												//This callback is called any time a cell is edited
												var row = cell.getRow();
												var data = row.getData();
												var col = cell.getColumn();
														
												///////////////////////////
												/*AND UPDATE STUFF in DB*/
												///////////////////////////
												
												//all I need is person meet|competition|Discipline and event
												if(cell.getField()!="")
												{ 
													var editingEvent = document.getElementById("eventSelection");
													var editedEventID = editingEvent.selectedIndex;
													if(saveScore(data.ID, data.Score, data.CompetitionID, editedEventID, data.SV))
														; //yay
													else
													{
														//this can cause problems unless I just recall buttonclicked or loadscoredata.
														//like on a score update it'll zero out but not save.
														//better yet do cell.previousvalue.
														row.update({Score:0});
														row.update({SV:0});
													}
												}
										},
									});
								</script>
								<br/>
								
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
