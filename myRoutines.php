<?php include("headers.php"); ?>
<?php include("auth.php"); ?>
<?php include("globals.php"); ?>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.js"></script>

<script type="text/javascript">
	
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
		height: 28px !important;
		padding: 0px 4px 0px 4px !important;
		
	}
</style>

	<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
							<select>
								<option value = '1'>Men's Floor</option>
								<option value = '2'>Men's Pommels</option>
								<option value = '3'>Men's Rings</option>
								<option value = '4'>Men's Vault</option>
								<option value = '5'>Men's Parallel Bars</option>
								<option value = '6'>Men's High Bar</option>
								<option value = '8'>Women's Floor</option>
								<option value = '9'>Women's Uneven Bars</option>
								<option value = '10'>Women's Balance Beam</option>
								<option value = '11'>Women's Floor</option>
							</select><br/>
							Select a routine on this event to load, create a new one, or create a new one via copy.<br/>
							<select>
								<option>2012-2014 Routine</option>
								<option>Nationals Routine</option>
								<option>Practice Routine 1</option>
								<option>Practice Routine 2</option>
								<option>New Routine</option>
							</select>
							<button>Copy Routine as: </button><input type = "text" placeholder = "put routine name here" /> or 
							<button>New Routine</button>
							<br/><br/>
							<br/>
							<input type = "text" placeholder = "Begin typing skill name here"/><br/>
							<button>Add skill</button>
							<div id = "RoutineTable"></div>
							<div>
								Execution Start: <br/>
								Difficulty Value: <br/>
								Element Groups: <br/>
								Potential Bonuses: <br/>
								Maximum total start value: <br/>
							</div>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
			
			
			<script>
				$("#RoutineTable").tabulator({
					responsiveLayout:false,
					layout: "fitDataFill",
					movableRows: true,
					data: [
							{ID:"1",	SkillID:"",	SkillName:"Roundoff",			Value:"None",	ElementGroup:"None",	Flags:""},
							{ID:"2",	SkillID:"",	SkillName:"Back Handspring",	Value:"A",	ElementGroup:"Back Tumbling",	Flags:""},
							{ID:"3",	SkillID:"",	SkillName:"Back full",			Value:"C",	ElementGroup:"Back Tumbling",	Flags:""},
							{ID:"4",	SkillID:"",	SkillName:"Side Scale",			Value:"A",	ElementGroup:"Non-Acrobatic",	Flags:""},
							{ID:"5",	SkillID:"",	SkillName:"Arabian",			Value:"B",	ElementGroup:"Arabian / Side",	Flags:""},
							{ID:"6",	SkillID:"",	SkillName:"Press Handstand",	Value:"A",	ElementGroup:"Non-Acrobatic",	Flags:""},
							{ID:"7",	SkillID:"",	SkillName:"Front Handspring",	Value:"A",	ElementGroup:"Front Tumbling",	Flags:""},
							{ID:"8",	SkillID:"",	SkillName:"Front Layout",		Value:"B",	ElementGroup:"Front Tumbling",	Flags:""},
							{ID:"9",	SkillID:"",	SkillName:"Roundoff",			Value:"None",	ElementGroup:"None",	Flags:""},
							{ID:"10",	SkillID:"",	SkillName:"Back Layout",		Value:"B",	ElementGroup:"Back Tumbling",	Flags:""},
							],
					columns:[
						{title:"",		 		field:"Remove", 	formatter:"buttonCross", cellClick:function(e,cell){cell.getRow().delete();}	},
						{title:"ID",				field:"ID",	 			visible:false},
						{title:"SkillID",			field:"SkillID",	 		visible:false},
						{title:"SkillName",			field:"SkillName",	 		},
						{title:"Value",				field:"Value",	 		},
						{title:"Element Group",		field:"ElementGroup",	 		},
						{title:"Flags",				field:"Flags",	 		}
						//routineID and Event are global, position is from tabulator call
					],
					index:"ID",
					cellEdited: function(cell){
						
						var row = cell.getRow();
						var data = row.getData();
						
					}
				});
			</script>
			
	</body>
</html>