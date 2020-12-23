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
			Season >= 2018 AND
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
	
	$(document).ready(function(){
		$("#newWLast").autocomplete({
			delay:350,
			source: "nameAutocomplete.php",
			dataType: "json",
			//source: data,
			minLength: 1,
			select: function (event, ui) {
											$('#newWId').val(ui.item.value); 
											$('#newWLast').val(ui.item.lastName + ", " + ui.item.firstName + " " + ui.item.middleName);
											loadScoreData(1);
											loadScoreData(2);
											return false; //prevent widget from updating value
										},
			focus: function(event, ui) { //set for arrow keys
				$('#newWId').val(ui.item.value); 
				$("#newWLast").val(ui.item.label);
				loadScoreData(1);
				loadScoreData(2);
				return false;
			},
			messages: {
				noResults: 'That name was not found.',
				results: function() {}
			}
		});
		
	});
	
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
					getScoreHistoryForGymnast: 1,
					personID: document.getElementById("newWId").value,
					gender: iGender
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
</script>

<style>
	.ui-autocomplete { position: absolute; cursor: default; background-color: #ffffff; z-index:30 !important; border: 2px solid #555555;}
	.ui-helper-hidden-accessible { display:none; }

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
						<p>Hello, we are currently processing all the results of the past several years into our system. In the meantime you can find them at the schedule <a href = "schedule">page</a>.</p>
						
								<input size = "2" disabled id = "newWId"/>
								<input size = "25" type = "text" id = "newWLast" placeholder = "Last Name"/><br/>
								<br/>
								<h2>Womens Competitions:</h2>
								<div id="womenScoreTable"></div>
								<br/>
								<h2>Mens Competitons:</h2>
								<div id="menScoreTable"></div> <br>
								<br/>
								
								<br/>
								<script type="text/javascript">
									$("#menScoreTable").tabulator({
										layout: "fitDataFill",
										groupBy: "Team",
										columns:[
											{title:"ID", 			field:"ID", 		visible:false},
											{title:"Meet",	 		field:"Meet",	 	sorter:"string"},
											{title:"CompetitionID", field:"CompetitionID", 		visible:false},
											{title:"GenderID", 		field:"GenderID", 	visible:false},
											{title:"Competition",	field:"Team",	 	},
											{title:"Team", 			field:"Institution",	 	},
											{title:"FX", 			field:"MFX",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"FXSV", 			field:"MFXSV",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"PH", 			field:"MPH",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"PHSV", 			field:"MPHSV",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"SR", 			field:"MSR",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"SRSV", 			field:"MSRSV",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"VT", 			field:"MVT",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"VTSV", 			field:"MVTSV",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"PB", 			field:"MPB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"PBSV", 			field:"MPBSV",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"HB", 			field:"MHB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"HBSV", 			field:"MHBSV",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"AA", 			field:"MAA",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
										],
										index:"ID",
										groupHeader:function(value, count, data, group){return data[0].Team;},
										//takes too long to reload everything. Just save it and update AA.
									});
									
									$("#womenScoreTable").tabulator({
										layout: "fitDataFill",
										groupBy: "Team",
										columns:[
											{title:"ID", 			field:"ID", 		visible:false},
											{title:"Meet",	 		field:"Meet",	 	sorter:"string"},
											{title:"CompetitionID", field:"CompetitionID", 		visible:false},
											{title:"GenderID", 		field:"GenderID", 	visible:false},
											{title:"Competition", 	field:"Team",	 	},
											{title:"Team", 			field:"Institution",	 	},
											{title:"VT", 			field:"WVT",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"VTSV", 			field:"WVTSV",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"UB", 			field:"WUB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"UBSV", 			field:"WUBSV",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"BB", 			field:"WBB",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"BBSV", 			field:"WBBSV",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"FX", 			field:"WFX",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"FXSV", 			field:"WFXSV",	 	sorter:"number",	formatter:"money",	formatterParams:{precision:3}},
											{title:"AA", 			field:"WAA",	 	sorter:"number",	formatter:"money", 	formatterParams:{precision:3}},
										],
										index:"ID",
										groupHeader:function(value, count, data, group){return data[0].Team;},
									});
								</script>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>
