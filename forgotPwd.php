<?php include("headers.php"); ?>
<?php include("auth.php"); ?>
<?php include("globals.php"); ?>
<?php require_once("random_compat-2.0.17/lib/random.php"); ?>
<?php
/**
 * Generate a random string, using a cryptographically secure 
 * pseudorandom number generator (random_int)
 * 
 * For PHP 7, random_int is a PHP core function
 * For PHP 5.x, depends on https://github.com/paragonie/random_compat
 * 
 * @param int $length      How many characters do we want?
 * @param string $keyspace A string of all possible characters
 *                         to select from
 * @return string
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

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

$emailNotFound = false;
$showMsg = false;
//write function to generate a token and to send an email with that token in a link.
//and inavtivate any other token for that user

if(isset($_REQUEST['sendpwdrecovery']))
{
	$email = $_REQUEST['email'];
	
	//see if email is there if so: 
		//get personID
		//get person email
		//in same transaction:
			//set all tokens for that personid to zero
			//set a token and a 2hr expiry
	$sql = "
			Select
				ID
			From
				Identifiers_People
			Where
				Username = ?
			";
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(1, $email, PDO::PARAM_STR, 512); //USERNAME HAS TO BE EMAIL
	$stmt->execute();
	
	$count = 0;
	
	
	
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$UserID = $row['ID'];
		$count++;
	}
	
	//if the email exists.
	if($count>0) //PDO num_rows not supported in mysql
	{
		$error = false;
		try
		{
			$conn->beginTransaction();
			
			$sql0 = "
					Update 
						Identifiers_PwdResetTokens
					Set
						Invalid = 1
					Where
						PersonID = ?
					";
			$stmt0 = $conn->prepare($sql0);
			$stmt0->bindParam(1, $UserID, PDO::PARAM_INT, 20);
			$stmt0->execute();
			
			//if a random token exists I can just append timestamp. The probability of collision is remote, the probability of collision in the same second is astronomical. This is just easiest.
			$date = date_create();
			$token = random_str(150) . date_timestamp_get($date);
			
			$sql2 = "
					Insert 
						Identifiers_PwdResetTokens(Token,ExpirationDate,Invalid,PersonID)
					Values(
							?,
							DATE_ADD(NOW(), INTERVAL 2 HOUR),
							0,
							?
						)
					";
			$stmt2 = $conn->prepare($sql2);
			$stmt2->bindParam(1, $token, PDO::PARAM_STR, 512);
			$stmt2->bindParam(2, $UserID, PDO::PARAM_STR, 512);
			$stmt2->execute();
		}
		catch (PDOException $e)
		{
			$error = true;
			$conn->rollBack();
			echo 'ERROR: ' . $e->getMessage()."<br/>".var_dump($conn->errorInfo());
		}
		//I tried a finally block but php blew up.
		if(!$error)
		{
			$conn->commit();
			$msg = wordwrap("Hi, Please click <a href = 'https://tgcgymnastics.com/resetPwd?pwdresettoken=".$token."'>this link</a> to reset your password. The link will expire in two hours. If you did not request this, please email info@tgcgymnastics.com");
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			mail($email,"TGC Password Reset Link",$msg,$headers);
			$showMsg = true;
		}		
	}
	else
	{
		$emailNotFound = true; //display error, email not found in this case. set an alert on $showmsg
	}
	
}
?>

<?php
if($showMsg)
{
	echo "<script>alert('An email with a link to reset your password has been sent.');</script>";
}
if($emailNotFound)
{
	echo "<script>alert('That email was not found. Please contact your club officers or the TGC board.');</script>";
}
?>
	<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">
							<form method = "post">
								Enter Email Address<input type = "text" name = "email"/>
								<input type = "hidden" name = "sendpwdrecovery" />
								<input type = "submit">
							</form>
						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
	</body>
</html>


