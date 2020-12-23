<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>
<?php require_once("registrationAjax.php"); ?>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.min.js"></script>

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
											$('#newWLast').val(ui.item.lastName);
											$('#newWFirst').val(ui.item.firstName);
											$('#newWMiddle').val(ui.item.middleName);
											return false; //prevent widget from updating value
										},
			focus: function(event, ui) { //set for arrow keys
				$("#newWLast").val(ui.item.label);
				return false;
			},
			messages: {
				noResults: 'That name was not found, please add them to the database.',
				results: function() {}
			}
		});
	});
	
</script>

<style>
	
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
							
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>
