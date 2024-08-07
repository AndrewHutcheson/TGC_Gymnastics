<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>
<?php require_once("random_compat-2.0.17/lib/random.php"); ?>
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

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
			$mail = new PHPMailer(true);

			try {
				//Server settings
				//$mail->SMTPDebug = 2;                                 // Enable verbose debug output
				$mail->isSMTP();                                      // Set mailer to use SMTP
				$mail->Host = 'smtp.gmail.com';                  // Specify main and backup SMTP servers
				$mail->SMTPAuth = true;								// Enable SMTP authentication
				$mail->SMTPDebug = 0;
  				$mail->CharSet = 'UTF-8';                               
				$mail->Username = 'texasgymnasticsconference@gmail.com';             // SMTP username
				$mail->Password = 'qrumtmtjwtkemsws';                           // SMTP password
				$mail->SMTPSecure = 'ssl';                            // Enable SSL encryption, TLS also accepted with port 465
				$mail->Port = 465;                                    // TCP port to connect to
			
				//Recipients
				$mail->setFrom('texasgymnasticsconference@gmail.com', 'Mailer');          //This is the email your form sends From
				$mail->addAddress($email); // Add a recipient address
				//$mail->addAddress('contact@example.com');               // Name is optional
				//$mail->addReplyTo('info@example.com', 'Information');
				//$mail->addCC('cc@example.com');
				//$mail->addBCC('bcc@example.com');
			
				//Attachments
				//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
				//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
			
				//Content
				$mail->isHTML(true);                                  // Set email format to HTML
				$mail->Subject = 'TGC Password Reset Link';
				$mail->Body    = wordwrap("Hi, Please click <a href = 'https://tgcgymnastics.com/resetPwd?pwdresettoken=".$token."'>this link</a> to reset your password. The link will expire in 48 hours. If you did not request this, you can ignore this email.");
				//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
			
				$mail->send();
				$showMsg = true;

			} catch (Exception $e) {
				echo 'Message could not be sent.';
				echo 'Mailer Error: ' . $mail->ErrorInfo;
			}
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


