<?php
session_start();
require_once("globals.php");
require_once("auth.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Create New News Post
if(userIsExecutiveAdministrator())
{
	if(isset($_REQUEST['newNewsPostSubmit'])){
		$title = $_REQUEST['postTitle'];
		$subtitle = $_REQUEST['postSubtitle'];
		$userID = getUserID();
		$content = $_REQUEST['postContent'];
		$pageID = 0;
		$siteID = 0;
		
		$query = "INSERT INTO Content_Newsfeed (SiteID,PageID,title,subtitle,InitialAuthor,content)
										VALUES (?,?,?,?,?,?);";
		$stmt = $con->prepare($query);
		$stmt->bind_param("iissis",$siteID,$pageID,$title,$subtitle,$userID,$content);
		
		$noError = true;
		$moreMessage = "";
		
		try
		{
			$stmt->execute();
		}
		catch (Exception $e) 
		{
			$moreMessage = $e->getMessage();
			$noError = false;
		}
		
		if($noError){
			echo json_encode(array(
									'Error' => false,
									'Message'=>"The post has been submitted."
									)
							);
		}
		else
		{
			echo json_encode(array(
									'Error' => true,
									'Message'=>"An error occurred. The site administrator has been notified.",
									'moreMessage'=>$moreMessage
									)
							);
			//notify site administrator via email
			$to      = $siteAdministrator; //defined in globals.php
			$subject = 'Website Error';
			$message = 'An error occurred when a user ('.$userID.') was trying to post a new news post.';
			$headers = 'From: errorbot@tgcgymnastics.org' . "\r\n" .
				'Reply-To: '.$siteAdministrator . "\r\n" .
				'X-Mailer: PHP/' . phpversion();

			mail($to, $subject, $message, $headers);
		}	
	} //end if new post submitted
	
	//delete a news post
	if(isset($_REQUEST['deleteNewsPostSubmit'])){
		$postID = $_REQUEST['postID'];
		$userID = getUserID();
		$pageID = 0;
		$siteID = 0;
		$query = "Update Content_Newsfeed SET Deleted = 1, DeletedDate = NOW(), DeletedBy = ? WHERE siteID = ? AND pageID = ? AND ID = ?";
		
		$stmt = $con->prepare($query);
		$stmt->bind_param("iiii",$userID,$siteID,$pageID,$postID);
		
		$noError = true;
		$moreMessage = "";
		
		try
		{
			$stmt->execute();
		}
		catch (Exception $e) 
		{
			$moreMessage = $e->getMessage();
			$noError = false;
		}
		
		if($noError){
			echo json_encode(array(
									'Error' => false,
									'Message'=>"The post has been deleted."
									)
							);
		}
		else
		{
			echo json_encode(array(
									'Error' => true,
									'Message'=>"An error occurred. The site administrator has been notified.",
									'moreMessage'=>$moreMessage
									)
							);
			//notify site administrator via email
			$to      = $siteAdministrator; //defined in globals.php
			$subject = 'Website Error';
			$message = 'An error occurred when a user ('.$userID.') was trying to delete a news post with ID '.$postID;
			$headers = 'From: errorbot@tgcgymnastics.org' . "\r\n" .
				'Reply-To: '.$siteAdministrator . "\r\n" .
				'X-Mailer: PHP/' . phpversion();

			mail($to, $subject, $message, $headers);
		} //end else mailto	
	} //end if delete button presses
}
?>