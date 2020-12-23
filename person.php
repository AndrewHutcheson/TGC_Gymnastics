<?php include("headers.php"); ?>
<?php include("auth.php"); ?>
<?php include("globals.php"); ?>
<?php include("registrationAjax.php"); ?>

<?php

?>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.min.js"></script>

<script>

</script>

<style>	

</style>
<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
							<?php
								if(userIsLoggedIn()) //changing this later...? certainly for email, phone etc.
								{
							?>
									Demographics:<br/>
									<br/>
									Registration and Score History:<br/>
									<br/>
									Position and Permission History<br/>
									<br/>
							<?php
								}
								else
								{
									if(userIsLoggedIn())
										echo "You do not have a captain or coach permission necessary to access registration.";
									display_login();
								}
							?>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>
