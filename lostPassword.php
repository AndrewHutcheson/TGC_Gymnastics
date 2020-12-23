<?php include("headers.php"); ?>
<?php include("auth.php"); ?>
<?php include("globals.php"); ?>

<?
	if(isset($_REQUEST['sendResetEmailTo']))
	{
		//if email exists in identifiers_people
		//then create a timedout token send an email
		
		//if not email webmaster but dont tell user.
	}
	
	if(isset($_REQUEST['email']))
	{
		$token = $_REQUEST['token'];
		if ($token = getCurrentToken($_REQUEST['email']))
		{
			$displayPwdField = true;
		}
	}

	function checkEmailValidity($email)
	{
		$sql = "
					SELECT
						count(*) As Num
					FROM
						Identifiers_People
					WHERE
						Email = ?
					;";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(1, $email, PDO::PARAM_STR, 250);
			$stmt->execute();
			
		$count = 0;
	
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$count = $row['Num'];
		}
		
		if($count == 0)
			return false;
		else
			return true;
	}
	
	function makeNewToken($email)
	{
		global $conn;
		$error = false;
		
		try
		{
			$conn->beginTransaction();
			
			//first, is this a real account? I mean a valid email?
			$isEmailValid = false;
			$isEmailValid = checkEmailValidity($email);
			
			if($isemailValid)
			{
				//invalidate all other tokens.
				$sql = "
						Update
							Constraints_PwdResetTokens
						Set 
							Invalid = 1
						WHERE
							Email = ?
						;";
				$stmt = $conn->prepare($sql);
				$stmt->bindParam(1, $email, PDO::PARAM_STR, 250);
				$stmt->execute();
				
				//make new token.
				$newToken = random_bytes(128);
				
				$sql = "
						INSERT INTO
							Constraints_PwdResetTokens(Email,Token,ExpirationDate,Invalid)
						VALUES
							(?,?,?,0)
						;";
				$stmt = $conn->prepare($sql);
				$stmt->bindParam(1, $email, PDO::PARAM_STR, 250);
				$stmt->bindParam(1, $newToken, PDO::PARAM_STR, 250);
				$stmt->bindParam(1, date()+86400, PDO::PARAM_STR, 250); //one day ???
				$stmt->execute();
				
				//send out the email here. should get a mail function in globals? $siteAdministrator
			}
			else
			{
				//send the webadmin an email.
			}
		}
		catch (PDOException $e)
		{
			$error = true;
			$conn->rollBack();
			echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		}
		if(!error)
		{
			$conn->commit();
		}
	}
	
	function getCurrentToken($email) //FUNCTION DUPLICATED
	{
		global $conn;
		$token = false;
		$stmt = $conn->prepare("
			SELECT
				token
			FROM
				Constraints_PwdResetTokens
			WHERE
				Email = ? AND
				ExpirationDate >= CURDATE() AND
				Invalid = 0
			;");
		
		$stmt->bindParam(1, $email, PDO::PARAM_STR, 250);
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$token = $row['token'];
		}
		
		return $token;
	}

	if(isset($_REQUEST['setPwd']))
	{
		$email = $_REQUEST['email'];
		$token = $_REQUEST['token'];
		$pwd = $_REQUEST['pwd'];
		
		if($token = getCurrentToken($request['email']))
		{
			setNewPassword($email,$token,$pwd);
		}
	}
?>

	<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
							<p>If you have lost your password, please input the email address associated with your account and we will email you a link to reset your password.</p>
							<form method = "post">
								<input type = "text" name = "sendResetEmailTo"></input>
								<input type = "submit" />
							</form>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>
