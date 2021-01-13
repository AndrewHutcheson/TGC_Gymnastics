<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>

 
	<body class="left-sidebar">

		<!-- Wrapper -->
			<div id="wrapper">

				<!-- Content -->
					<div id="content">
						<div class="inner">
							<!-- Post -->
								<article>
									<header>
										<h2>Login</h2>
									</header>
									<section>
										<?php 
										if(userIsLoggedIn()){
											echo "You are logged in. Click <a href = '".htmlspecialchars($_SERVER['PHP_SELF'])."?logout'>here</a> to log out.";
										}
										else{
											display_login(); 
										}										
										?>
									</section>
								</article>
						</div>
					</div>

				<?php include("sidebar.php"); ?>

			</div>

	</body>
</html>