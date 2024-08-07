<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>

<script type="text/javascript" src="js/moment.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.min.js"></script>
<script type="text/javascript" src="js/chart.umd.min.js"></script>


<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
						<h2>Graphs</h2>
					<?php
					
					if(userIsExecutiveAdministrator())
					{
					?>

						

					<?php
					}	
					else
					{
						echo "You do not have permission to access this page.";
						display_login();
					}
					?>

						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
</body>	
</html>		