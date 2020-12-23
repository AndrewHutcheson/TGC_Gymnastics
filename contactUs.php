<?php include("headers.php"); ?>
<?php include("auth.php"); ?>
<?php include("globals.php"); ?>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

	<body class="left-sidebar">

		<!-- Wrapper -->
			<div id="wrapper">

				<!-- Content -->
					<div id="content">
						<div class="inner">
							<!-- Post -->
								<article>
									<header>
										<h2>Ask your question here</h2>
										<p>
											Just fill out the forms, hit submit and then we will get back to you shortly.
										</p>
									</header>
									<section>
										<form action="mail.php" method="POST">
											<div class="row 50%">
												<div class="6u">
													<input name="subject" placeholder="Subject" type="text" />
												</div>
												<div class="6u">
													<input name="email" placeholder="Email" type="text" />
												</div>
											</div>
											<div class="row 50%">
												<div class="12u">
													<textarea name="message" placeholder="Message"></textarea>
												</div>
											</div>
											<div class="row 50%">
												<div class="12u">
													<div class="g-recaptcha" data-sitekey="6LeyotcUAAAAALUwDE3oMIEsG9rXN4GP3bNJNdqw"></div>
													<input class="form-button-submit button icon fa-envelope" type="submit" value=" Send Message ">
												</div>
											</div>
										</form>
									<p>
										&nbsp;
									</p>
									<p>
										&nbsp;
									</p>
									<p>
										&nbsp;
									</p>
									<p>
										&nbsp;
									</p>
									<p>
										&nbsp;
									</p>
									<p>
										&nbsp;
									</p>
									<p>
										&nbsp;
									</p>
									<p>
										&nbsp;
									</p>
									<p>
										&nbsp;
									</p>
									</section>
								</article>
						</div>
					</div>

				<?php include("sidebar.php"); ?>

			</div>

	</body>
</html>