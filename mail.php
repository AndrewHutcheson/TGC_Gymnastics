<html>
	<head><title>Contact Us</title></head>
	<body>
		<?php

		/* All form fields are automatically passed to the PHP script through the array $HTTP_POST_VARS. */
		$email = $_REQUEST['email'];
		$subject = $_REQUEST['subject'];
		$message = $_REQUEST['message'];

		
		$captchaFailed = false;
		
		//Now we check the response.
		/*URL: https://www.google.com/recaptcha/api/siteverify METHOD: POST
		POST Parameter 	Description
		secret 	Required. The shared key between your site and reCAPTCHA.
		response 	Required. The user response token provided by the reCAPTCHA client-side integration on your site.*/
		
		$post_data = http_build_query(
			array(
				'secret' => "6LeyotcUAAAAAABr04Kju7aWTBkXMTwqkYuxEY6O",
				'response' => $_POST['g-recaptcha-response'],
				'remoteip' => $_SERVER['REMOTE_ADDR']
			)
		);
		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $post_data
			)
		);
		$context  = stream_context_create($opts);
		$response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
		$result = json_decode($response);
		if (!$result->success) {
			//throw new Exception('Gah! CAPTCHA verification failed. Please email me directly at: jstark at jonathanstark dot com', 1);
			$captchaFailed = true;
		}
			
		if(!$captchaFailed)
		{	
			/* PHP form validation: the script checks that the Email field contains a valid email address and the Subject field isn't empty. preg_match performs a regular expression match. It's a very powerful PHP function to validate form fields and other strings - see PHP manual for details. */
			$email = filter_var($email, FILTER_SANITIZE_EMAIL);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			  echo "<h4>Invalid email address</h4>";
			  echo "<a href='javascript:history.back(1);'>Back</a>";
			} elseif ($subject == "") {
			  echo "<h4>TGC: No subject</h4>";
			  echo "<a href='javascript:history.back(1);'>Back</a>";
			}

			/* Sends the mail and outputs the "Thank you" string if the mail is successfully sent, or the error string otherwise. */
			elseif (mail('andrew.hutcheson@utexas.edu',$subject,$message,'Reply-To: ' . $email)) 
			{
				echo "<h4>Thank you, we will contact you shortly.</h4><br/>";
				echo "<a href='javascript:history.back(1);'>Back</a>";
			} 
			else 
			{
				echo "<h4>An error occurred. Please contact the <a href = 'mailto:andrew.hutcheson@gmail.com'>webmaster</a>.</h4>";
			}
		}
		else
		{
			echo "<h4>Please verify you are not a robot.</h4>";
		}
		?>
	</body>
</html> 