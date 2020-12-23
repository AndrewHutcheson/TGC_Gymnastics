<?php
/*
File has the following:

Panel 1:
Step 1: select the code and year/version. i.e. FIG Men Artistic 2017-2020
Step 2: select discipline-event
Step 3a: add a skill
Step 3b: select existing skill and edit or remove it

Panel 2: copy button to copy all the skills on all events from one quadrennium-discipline-level to another




MAKE A TABLE
ID
Discipline
Event
Element Group Display Name
Element Group Display Number

*/


?>
<script type = "text/javascript">
	function updateEGPanel()
	{
		var event = document.getElementByID("disciplineEvent").value;
		
		optsLookup = []; //clear and repopulate.
		xOpts = "<select>";
		
		var displayName; var ID;
		var opt;
		$("#ElementGroupList").empty();
		$.ajax({
			type: 'POST',
			url: "administrateAjax.php",
			data: {
				getElementGroupsForEvent: event
			},
			dataType: 'json',
			success: function (data) {
				opt = document.createElement('option');
				opt.value = "";
				opt.innerHTML = "Select Element Group";
				opt.disabled = true;
				opt.selected = true;
				document.getElementById('ElementGroupList').appendChild(opt);
				for (var key in data){
					displayName = data[key].Division + " " + data[key].Level + " " + data[key].Gender;
					ID = data[key].ID;
					opt = document.createElement('option');
					opt.value = ID;
					opt.innerHTML = displayName;
					document.getElementById('ElementGroupList').appendChild(opt);
					optsLookup[ID] = displayName;
					xOpts = xOpts + "<option value = '" + ID + "'>" + displayName + "</option>";
				}
				xOpts = xOpts + "</select>";
			}
		});
	}
</script>


<h2>This panel is used to define a physical skill on an event.</h2>
Event-Skills<br/>
<form>
	<input type = "hidden" name = "ID"/>
	Discipline-Event:<input type = "text"/><br/>
	Skill Tecnical Name (Ver Batim) Description in FIG Box)<br/><input type = "text"/>
	Skill Alternate Names, Comma Delimited<br/><input type = "text"/>
	<input type = "submit"/>
</form>
	
<h2>This panel is used to assign a physical skill to one or more code of points. E.g. the same physical skill is added to level 7, 8 and 9.</h2>
<form>
	Select Code Version:<br/>
	<!--need function to get these.-->
	Select Discipline-Event:<br/>
	 <!--need function to get these.-->
	<select id = "disciplineEvent" onchange = "updateEGPanel"></select><br/>
	Skill ID: <input type = "text" disabled /><br/>
	Skill<input type = "text"/><br/>
	Select Element Group:<br/>
	 <!--need function to get EGs.-->
	<div id = "ElementGroupList"></div><br/>
	<br/>
	Value 	<select name = "value">
				<option selected disabled></option>
				<option value = "0">No Value</option>
				<option value = "A">A</option>
				<option value = "B">B</option>
				<option value = "C">C</option>
				<option value = "D">D</option>
				<option value = "E">E</option>
				<option value = "F">F</option>
				<option value = "G">G</option>
				<option value = "H">H</option>
			</select><br/>
	Comma-Delimited Flags <input type = "text" />
	<input type = "submit"/>
</form>