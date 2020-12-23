<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.js"></script>

<script type="text/javascript">
	function loadScores()
	{
		var ilevel = document.getElementById("LevelSelector").value;
		var iapparatus = document.getElementById("EventSelector").value;
		var idivision = document.getElementById("DivisionSelector").value;
		
		if(iapparatus == "AA")
		{
			$.ajax({
					type: "POST",
					url: "scoreAjax.php",
					async: false,
					data: {
						getTopAllTimeAAScores: 1,
						level: ilevel,
						division: idivision
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
		else
		{
			$.ajax({
					type: "POST",
					url: "scoreAjax.php",
					async: false,
					data: {
						getTopAllTimeScores: 1,
						level: ilevel,
						division: idivision,
						apparatus: iapparatus
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
</script>

	<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
						
							<p>
								This page gets the top 10 numerical scores of all time and shows anyone who tied for those scores.
							</p>
							<select id = "LevelSelector">
								<option selected disabled>Select Level</option>
								<option value = "1">Women's Level 9</option>
								<option value = "3">Women's Level 8</option>
								<option value = "5">Women's Level 6</option>
								<option value = "2">Men's NCAA</option>
								<option value = "6">Men's Level 9</option>
							</select>
							<select id = "DivisionSelector">
								<option selected disabled>Select Division</option>
								<option value = "1">Collegiate</option>
								<option value = "3">Open</option>
							</select>
							<select id = "EventSelector">
								<option selected disabled>Select Event</option>
								<option value = "AA">All-Around</option>
								<option disabled></option>
								<option value = "1">Men's Floor</option>
								<option value = "2">Men's Pommels</option>
								<option value = "3">Men's Rings</option>
								<option value = "4">Men's Vault</option>
								<option value = "5">Men's Parallel Bars</option>
								<option value = "6">Men's High Bar</option>
								<option disabled></option>
								<option value = "8">Women's Vault</option>
								<option value = "9">Women's Bars</option>
								<option value = "10">Women's Beam</option>
								<option value = "11">Women's Floor</option>
							</select>
							<button onclick = "loadScores();">Submit</button>
							<br/>
							<br/>
							<div id="ScoreTable"></div>
							<br/>
							<script type = "text/javascript">
								$("#ScoreTable").tabulator({
											layout: "fitDataFill",
											layoutColumnsOnNewData:true,
											columns:[
												{title:"Meet", 				field:"Meet"},
												{title:"Gymnast", 			field:"Gymnast"},
												{title:"Team", 				field:"Team"},
												//{title:"Competition", 		field:"Competition"},
												//{title:"Event", 			field:"Event"},
												{title:"Score", 			field:"Score"}
												
											],
								});
							</script>	
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>
