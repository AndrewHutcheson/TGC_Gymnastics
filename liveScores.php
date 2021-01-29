<html>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>
<?php require_once("registrationAjax.php"); ?>
<?php require_once("meetRegistrationClass.php"); ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script>
	
	var iMeet = <?php echo $_REQUEST['meetID']; ?>;
	var numLimit = <?php echo $_REQUEST['rows']; ?>;
	
	setInterval(function(){
		loadScoreData("1,2"); 
		//loadScoreData(2); 
		}, 15000);

	function loadScoreData(iDiscipline)
	{
		var myTable = '#womenTable tbody';
		if(iDiscipline == 2)
			myTable = '#menTable tbody';
		$.ajax({
				type: 'POST',
				url: "scoreAjax.php",
				async: false,
				data: {
					getLiveScores: 1,
					meet: iMeet,
					Discipline: iDiscipline,
					numberLimit: numLimit
				},
				dataType: 'json',
				success: function (data) {
					buildHtmlTable(myTable,data);
				},
				error: function (textStatus, errorThrown) {
					//console.log(errorThrown);
					alert("error downloading score data");
				}
			});
	}
	
	function buildHtmlTable(selector, myList) {
	  var columns = addAllColumnHeaders(myList, selector);
	
	  $(selector + " tr").remove(); //clear table first

	  for (var i = 0; i < myList.length; i++) {
		var rows = $("<tr/>");
		for (var colIndex = 0; colIndex < columns.length; colIndex++) {
		  var cellValue = myList[i][columns[colIndex]];
		  if (cellValue == null) cellValue = "";
		  rows.append($('<td/>').html(cellValue));
		}
		$(selector).append(rows);
	  }
	}
	
	function addAllColumnHeaders(myList, selector) {
	  var columnSet = [];
	  //var headerTr$ = $('<tr/>');

	  for (var i = 0; i < myList.length; i++) { //for each and every row
		var rowHash = myList[i];				//store the row number
		for (var key in rowHash) {				//and look at each column in that row
		  if ($.inArray(key, columnSet) == -1) { //if it doesnt exist then add it to columnSet
			columnSet.push(key);
			//headerTr$.append($('<th/>').html(key));
		  }
		}
	  }
	  //$(selector).append(headerTr$);

	  return columnSet;
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
	thead, td 
	{
	  padding: 12px;
	  text-align: left;
	  border-bottom: 1px solid #ddd;
	  font-size: 20pt;
	}
	
	thead tr td
	{
		font-weight: bold;
	}
	
	tbody tr:nth-child(odd) 
	{
		background-color: #f2f2f2;
	}
	
	tbody tr
	{
		font-weight: bold;
	}
	
	#title
	{
		font-size: 24pt;
		text-align:center;
	}
</style>

<body>

							<?
							//display meet name and date and session maybe
							//display a table of the most recent score per event. 4 womens and 3 mens. 
							//for each score put level and division, school/team and person, plus the score. Maybe start value?
							//add a flag if the score is an update/change not a new score?
							
							$meet = new meetRegistration("byMeet",$_REQUEST['meetID']);
							
							?>
							<div contenteditable = "true" id = "title"><br/><? echo $meet->meetName . ", " . $meet->meetDate; ?></div><br/>
							<div>
							<table id = "womenTable" style = "width:100%; float:left; display:table; border-right:1px solid">
								<thead>
									<tr>
										<td>Competition</td>
										<td>Event</td>
										<td>Team</td>
										<td>Gymnast</td>
										<td>Score</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							<!--table id = "menTable" style = "width:50%; float:right; display:table;">
								<thead>
									<tr>
										<td>Competition</td>
										<td>Event</td>
										<td>Team</td>
										<td>Gymnast</td>
										<td>Score</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table-->
							</div>
	</body>
</html>
