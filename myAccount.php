<?php include("headers.php"); ?>
<?php include("auth.php"); ?>
<?php include("globals.php"); ?>

<?

/*YOU CANT ACCESS THIS PAGE UNLESS YOU ARE LOGGED IN
make a different page solely for handling the token and email reset pair.
also rate limit that page. and record IP. and ua string. 
*/

?>
<script type = "text/javascript">
	function meetSelection()
	{
		window.location.href = window.location.pathname + "?meet=" + document.getElementById("meet").value;
	}
</script>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.min.js"></script>
	<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
							<h2>Change your password</h2>
							<p>stuff will go here</p>
							<h2>Change your profile information:</h2>
							<ul>
							<li>Email</li>
							<li>Phone</li>
							<li>Birthday</li>
							<li>Add a school-year</li>
							<li>Add its major /degree</li>
							<li>Email Lists</li>
							</ul>
							<h2>Manage your public info</h2>
							<p>Your scores are always public. You can however make some things unlisted: (display videos and photos and bio info like major.)</p>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>

<?php
function setNewPassword($email,$pwd)
{
	global $conn;
	$salt = random_bytes(128);
	$newpwd = hash('sha512', $pwd.$salt);
	$sql = "
			UPDATE 
				Identifiers_People
			SET
				Password = ?, 
				Hash = ?
			WHERE
				Email = ?
			;";
			
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(1, $email, PDO::PARAM_STR, 250);
	$stmt->bindParam(2, $hash, PDO::PARAM_STR, 250);
	$stmt->bindParam(3, $newpwd, PDO::PARAM_STR, 250);
	$stmt->execute();
	
	$sql2 = 
		"UPDATE
			Constraints_PwdResetTokens
		Set 
			Invalid = 1
		WHERE
			Email = ? AND
			ExpirationDate >= CURDATE()
		;";
		
	$stmt2 = $conn->prepare($sql);
	$stmt2->bindParam(1, $email, PDO::PARAM_STR, 250);
	$stmt2->execute();
	
	$changed = true;
}

?>
<script type = "text/javascript">
	function meetSelection()
	{
		window.location.href = window.location.pathname + "?meet=" + document.getElementById("meet").value;
	}
</script>

<script type="text/javascript" src="tabulator-master/dist/js/tabulator.min.js"></script>
	<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
						<?php
						if($displayPwdField)	
						{						
						?>
							<form method = "post">
								Enter password<input type = "password" /><br/>
								Verify password<input type = "password" /><br/>
							</form>
						<?php
						}
						elseif($changed)
						{
							echo "<p>Your password has been changed! <a href = 'register'>click here</a> to login to register for meets.</p>";
						}
						elseif(userIsLoggedIn())
						{
							echo "<p>You are already logged in. A feature to automatically change and reset your password is coming soon, hang tight. In the meantime just email me at andrew.hutcheson@utexas.edu.</p>";
						}
						else
						{
							echo "<p>That link is invalid or has expired. A feature to automatically change and reset your password is coming soon, hang tight. In the meantime just email me at andrew.hutcheson@utexas.edu.</p>";
						}
						?>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>
