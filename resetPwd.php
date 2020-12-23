<?php include("headers.php"); ?>
<?php include("auth.php"); ?>
<?php include("globals.php"); ?>
<?php require_once("random_compat-2.0.17/lib/random.php"); //REQUIRED FOR PHP 5 but was added to PHP 7 ?> 
<?php

function random_str($length)
{
	$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pieces = array();
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) 
	{
        $pieces[]= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

$displayPwdField = false;
$changed = false;

if(isset($_REQUEST['pwdresettoken']))
{
	if(checkForValidToken())
		$displayPwdField = true;
}

if(isset($_REQUEST['password']) && isset($_REQUEST['password2']) && isset($_REQUEST['pwdresettoken']))
{
	global $conn;
	$error = false;
	
	if($_REQUEST['password'] == $_REQUEST['password2'])
	{
		
		$newSalt = random_str(127); //eh I'm not worried about collision. If you have computation time for one then you've got it for two.
		$saltCopy = $newSalt; //in case of store by reference to the function?
		//$newSalt = "SAHXgMLruTXqXq6Gd4elfD0nYCdMZUBMQ3SwnoMmT2ju80APb2RXhajysQnDgeYMF31AupmhHRYDflmk7qPXzJ4YtvKUnyy4lkos4qdHSC7Ylsurqy0NxRmtkIVh6aB0";
		$newpwd = hash('sha512', $_REQUEST['password'].$saltCopy);
		try
		{
			$conn->beginTransaction();
			$sql = "
					UPDATE
						Identifiers_People
					Set
						PasswordHash = ?,
						Salt = ?
					Where
						ID IN (
								Select
									PersonID
								From
									Identifiers_PwdResetTokens
								WHERE
									Token = ? AND
									ExpirationDate >= CURDATE() AND
									Invalid = 0
								)
					";
			
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(1, $newpwd, PDO::PARAM_STR, 512);
			$stmt->bindParam(2, $saltCopy, PDO::PARAM_STR, 512);
			$stmt->bindParam(3, $_REQUEST['pwdresettoken'], PDO::PARAM_STR, 512);
			$stmt->execute();
			
			$sql2 = "
					Update 
						Identifiers_PwdResetTokens
					Set
						Invalid = 1
					Where
						Token = ?
					";
			$stmt2 = $conn->prepare($sql2);
			$stmt2->bindParam(1, $_REQUEST['pwdresettoken'], PDO::PARAM_STR, 512);
			$stmt2->execute();
		}
		catch(PDOException $e)
		{
			$error = true;
			$conn->rollBack();
			echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
			echo "<script>alert('An error occurred.');</script>";
		}
		
		if(!$error)
		{
			$changed = true;
			$conn->commit();
			echo "<script>alert('Password successfully updated.');</script>";
		}
	}
	else //passwords dont match.
	{
		echo "<script>alert('Passwords do not match.');</script>";
	}
}

function checkForValidToken()
{
	global $conn;
	$sql = "
			Select
				count(*) AS theCount
			From
				Identifiers_PwdResetTokens
			Where
				Token = ? AND
				ExpirationDate >= CURDATE() AND
				Invalid = 0
			";
	
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(1, $_REQUEST['pwdresettoken'], PDO::PARAM_STR, 512);
	$stmt->execute();
	
	$count = 0;
	
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$count = $row['theCount'];
	}
	
	if($count>0) //PDO num_rows not supported in mysql
	{
		return true;
	}
	return false;
}

/*function setNewPassword($email,$pwd)
{
	global $conn;
	$salt = random_bytes(128);
	$newpwd = hash('sha512', $pwd.$salt);
	$sql = "
			UPDATE 
				Identifiers_People
			SET
				Salt = ?,
				PasswordHash = ?
			WHERE
				Email = ?
			;";
			
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(1, $salt, PDO::PARAM_STR, 250);
	$stmt->bindParam(2, $newpwd, PDO::PARAM_STR, 250);
	$stmt->bindParam(3, $email, PDO::PARAM_STR, 250);
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
}*/

?>
	<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
						<?php
						if($changed)
						{
							echo "<p>Your password has been changed! <a href = 'register'>click here</a> to login to register for meets.</p>";
						}
						elseif($displayPwdField)
						{							
						?>
							<form method = "post">
								Enter password<input type = "password" name = "password"/><br/>
								Verify password<input type = "password" name = "password2"/><br/>
								<input type = "hidden" name = "pwdresettoken" value = "<?php echo $_REQUEST['pwdresettoken']; ?>"/>
								<input type = "submit">
							</form>
						<?php
						}
						elseif(userIsLoggedIn())
						{
							echo "<p>You are already logged in. A feature to change your password from your profile page is coming soon, hang tight. In the meantime just email me at andrew.hutcheson@utexas.edu.</p>";
						}
						else
						{
							echo "<p>That link is invalid or has expired. A feature to change your password from your profile page is coming soon, hang tight. In the meantime just email me at andrew.hutcheson@utexas.edu.</p>";
						}
						?>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>


