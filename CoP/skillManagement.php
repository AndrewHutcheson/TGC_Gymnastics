<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.min.js"></script>

<script type="text/javascript">

var selectedEvent = "";

function addSkill()
{	
	selectedEvent = document.getElementById("eventSelector").value;
	
	var newName = document.getElementById("newNameInput").value;
	var newAltNames = document.getElementById("newAltNameInput").value;
	var newDescription = document.getElementById("newDescriptionInput").value;
	var newPhoto = document.getElementById("newPhotoInput").value;
	var newVideo = document.getElementById("newVideoInput").value;
	
		$.ajax({
				type: 'POST',
				url: "skillAjax.php",
				async: false,
				data: {
					addSkill: true,
					eventID: selectedEvent,
					name: newName,
					altNames: newAltNames,
					description: newDescription,
					photo: newPhoto,
					video: newVideo
				},
				dataType: 'json',
				success: function (data) {
					//clear the fields
					loadSkills();
				},
				error: function (textStatus, errorThrown) {
					
				}
			});
}

function editSkill(skillId, newName, newDescription, newAltNames, newPhoto, newVideo)
{
	selectedEvent = document.getElementById("eventSelector").value;

		$.ajax({
				type: 'POST',
				url: "skillAjax.php",
				async: false,
				data: {
					editSkill: skillId,
					eventID: selectedEvent,
					name: newName,
					description: newDescription,
					altNames: newAltNames,
					photo: newPhoto,
					video: newVideo
				},
				dataType: 'json',
				success: function (data) {
					//show an alert.
				},
				error: function (textStatus, errorThrown) {
					alert("an error occurred");
				}
			});
}

function deleteSkill(skillId)
{
	selectedEvent = document.getElementById("eventSelector").value;
	
	$.ajax({
				type: 'POST',
				url: "skillAjax.php",
				async: false,
				data: {
					deleteSkill: skillId
				},
				dataType: 'json',
				success: function (data) {
					loadSkills(document.getElementById("eventSelector").value);
				},
				error: function (textStatus, errorThrown) {
					alert("an error occurred");
				}
			});
}

function loadSkills()
{
	document.getElementById("addSkillButton").disabled = false;
	selectedEvent = document.getElementById("eventSelector").value;
	
	$.ajax({
				type: 'POST',
				url: "skillAjax.php",
				async: false,
				data: {
					loadSkills: true,
					eventID: selectedEvent
				},
				dataType: 'json',
				success: function (data) {
					table.setData(data);
				},
				error: function (textStatus, errorThrown) {
					alert("an error occurred");
				}
			});
}

</script>

<style>
	.inner{
		max-width: initial !important;
	}
</style>

<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
							<div>
								Select Event: 
								<select onchange = "loadSkills();" id = "eventSelector">
									<option selected disabled value = "">Click to select</option>
									<option value = 1>Men-FX</option>
									<option value = 2>Men-PH</option>
									<option value = 3>Men-SR</option>
									<option value = 4>Men-VT</option>
									<option value = 5>Men-PB</option>
									<option value = 6>Men-HB</option>
									<option value = 8>Women-VT</option>
									<option value = 9>Women-UB</option>
									<option value = 10>Women-BB</option>
									<option value = 11>Women-FX</option>
								</select>
							</div>
							<div>
								Add new skill: <br/>
								<input id = "newNameInput" name = "" placeholder = "Name" /><br/>
								<input id = "newDescriptionInput" name = "" placeholder = "Description" /><br/>
								<input id = "newAltNameInput" name = "" placeholder = "Alt Names" /><br/>
								<input type = "hidden" id = "newPhotoInput" name = "" placeholder = "Photo" /><br/>
								<input type = "hidden" id = "newVideoInput" name = "" placeholder = "Video" /><br/>
								<button id = "addSkillButton" disabled onclick = "addSkill();" >Add</button><br/>
							</div>
							<div id = "skillTable"></div>
						
							<script type="text/javascript">
								var table = new Tabulator("#skillTable", 
								{
									layout: "fitDataFill",
									columns:[
										{title:"",	field:"Remove", 	formatter:"buttonCross", cellClick:function(e,cell){cell.getRow().delete();	} },
										{title:"SkillID", field:"ID", editor:"input", visible: false},
										{title:"Skill Name", field:"SkillName", editor:"input"},
										{title:"Alt. Names", field:"AltNames", editor:"input"},
										{title:"Description", field:"Description", editor:"input"},
										{title:"Photo", field:"Photo", editor:"input", visible: false},
										{title:"Video", field:"Video", editor:"input", visible: false},
									],
									rowDeleted:function(row){
										var data = row.getData();
										;//deleteSkill(data.ID);
									},
									cellEdited:function(cell){
										//This callback is called any time a cell is edited
										var row = cell.getRow();
										var data = row.getData();
										
										if(cell.getField != "remove")
											;//editSkill(data.ID, data.SkillName, data.Description, data.AltNames, data.Photo, data.Video);
									}
								});
							</script>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
</body>	
</html>		