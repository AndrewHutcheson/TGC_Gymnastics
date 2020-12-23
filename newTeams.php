<?php include("headers.php"); ?>
<?php include("auth.php"); ?>
<?php include("globals.php"); ?>

	<? 
		//define what this page is
		$pageID = 1;
		$siteID = 0;
	?>
	
	<body class="left-sidebar">

		<!-- Wrapper -->
			<div id="wrapper">

				<!-- Content -->
					<div id="content">
						<div class="inner">

						<?
						
						////the following only displays if user is logged in as an admin
							require_once('auth.php');
							
							if(userIsLoggedIn()){
								if(userIsExecutiveAdministrator()){
									//Create New Team
									if(isset($_REQUEST['newTeamSubmit'])){
										$name = $_REQUEST["teamName"];
										$mascot = $_REQUEST["teamMascot"];
										$description = $_REQUEST["teamDescription"];
										$address1 = $_REQUEST["address1"];
										$address2 = $_REQUEST["address2"];
										$city = $_REQUEST["city"];
										$state = $_REQUEST["state"];
										$zip = $_REQUEST["zip"];
										$phone = $_REQUEST["phone"];
										$email = $_REQUEST["email"];
										$website = $_REQUEST["website"];
										$facebook = $_REQUEST["facebook"];
										$twitterHandle = $_REQUEST["twitter"];
										$instagram = $_REQUEST["instagram"];
										$lat = "null";
										$lng = "null";
										$dues = $_REQUEST["teamDues"];
										$practice = $_REQUEST["practiceTimes"];
										$photo = $_REQUEST["photo"];
										
										$query = "INSERT INTO Identifiers_Clubs(Type, Name, Mascot, Description, Address1, Address2, City, State, Zip, Phone, Email, Website, Facebook, Twitter, Instagram, Lat, Lng, Dues, Practice, Photo)
																		VALUES('1',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
										if($stmt = $con->prepare($query))
											$stmt->bind_param("sssssssssssssssssss",$name, $mascot, $description, $address1, $address2, $city, $state, $zip, $phone, $email, $website, $facebook, $twitter, $instagram, $lat, $lng, $dues, $practice, $photo);
										else
											printf("Errormessage: %s\n", $con->error);
										
										if($stmt->execute()){
											echo "<script>alert('The team has been added.');</script>";
										}
										else{
											echo "<script>alert('An error occurred. The site administrator has been notified.');</script>";
											//notify site administrator via email
											$to      = $siteAdministrator; //defined in globals.php
											$subject = 'Website Error';
											$message = 'An error occurred when a user ('.$userID.') was trying to create a new team.';
											$headers = 'From: errorbot@tgcgymnastics.org' . "\r\n" .
												'Reply-To: '.$siteAdministrator . "\r\n" .
												'X-Mailer: PHP/' . phpversion();

											mail($to, $subject, $message, $headers);
										}	
									} //end if new team submitted
									
									//mark a club inactive. I do not want them to be able to delete until foreign key constraints are all set.
									if(isset($_REQUEST['newsPostDeleteSubmit'])){
										$postID = $_REQUEST['postID'];
										
										$query = "DELETE FROM Content_Newsfeed WHERE siteID = ? AND pageID = ? AND ID = ?";
										
										$stmt = $con->prepare($query);
										$stmt->bind_param("iii",$siteID,$pageID,$postID);
										
										if($stmt->execute()){
											echo "<script>alert('The post has been deleted.'); window.location.reload();</script>";
										}
										else{
											echo "<script>alert('An error occurred. The site administrator has been notified.');</script>";
											//notify site administrator via email
											$to      = $siteAdministrator; //defined in globals.php
											$subject = 'Website Error';
											$message = 'An error occurred when a user ('.$userID.') was trying to delete a news post with ID '.$postID;
											$headers = 'From: errorbot@tgcgymnastics.org' . "\r\n" .
												'Reply-To: '.$siteAdministrator . "\r\n" .
												'X-Mailer: PHP/' . phpversion();

											mail($to, $subject, $message, $headers);
										} //end else mailto	
									} //end if delete button pressed
									
									
									//show form for making a new post.
									if(userIsExecutiveAdministrator()){
									?>
										
										<span onclick = 'toggleDiv("newTeamDiv");'>Click Here to Add a New Team</span>
										<div style = 'display:none;' id = 'newTeamDiv'>
											<form method = "post" action = '<? echo htmlspecialchars($_SERVER['PHP_SELF']); ?>'>
												Team Name: <input type = "text" name = "teamName"/><br/>
												Mascot (if school): <input type = "text" name = "teamMascot"/><br/>
												Team Description: <textarea type = "text" name = "teamDescription"/></textarea><br/>
												Address1: <input type = "text" name = "address1"/><br/>
												Address2: <input type = "text" name = "address2"/><br/>
												City: <input type = "text" name = "city"/><br/>
												State: <input type = "text" name = "state"/><br/>
												Zip: <input type = "text" name = "zip"/><br/>
												Phone: <input type = "text" name = "phone"/><br/>
												Email: <input type = "text" name = "email"/><br/>
												Website: <input type = "text" name = "website"/><br/>
												Facebook: <input type = "text" name = "facebook"/><br/>
												Twitter: <input type = "text" name = "twitter"/><br/>
												Instagram: <input type = "text" name = "instagram"/><br/>
												Dues/Cost: <textarea type = "text" name = "teamDues"/></textarea><br/>
												Practice Days and Times: <textarea type = "text" name = "practiceTimes"/></textarea><br/>
												Photo Location: <input type = "text" name = "photo"/><br/>
												
												<input type = 'submit' name = 'newTeamSubmit' value = 'submit'></input>
											</form>
										</div>
									<?
									} //end show form for new team
								} //end if user is admin
							} //end if user logged in						
						?>					
						
						
				<?
					//get all posts to display
					$query = "SELECT ID, Name, Mascot, Description, Address1, Address2, City, State, Zip, Phone, Email, Website, Facebook, Twitter, Instagram, Dues, Practice, Active, Photo
								FROM  Identifiers_Clubs
								WHERE Inactive = '0000-00-00'
								ORDER BY Name ASC"; 
										
					$stmt = $con->prepare($query);
							
					$stmt->execute();
					$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;
					
					if($stmt->num_rows >= 1){
						$stmt->bind_result($ID, $Name, $Mascot, $Description, $Address1, $Address2, $City, $State, $Zip, $Phone, $Email, $Website, $Facebook, $twitterHandle, $Instagram, $Dues, $Practice, $Active, $Photo);
						
						while($stmt->fetch()){
								
				?>
						
						<!-- Post -->
								<article class="box post post-excerpt">
									<header>

										<h2><a href="#"><? echo $Name; ?></a></h2>
										<p><? echo $Mascot; ?></p>
									</header>
									<div class="info">

										<span class="date"><? echo $City; ?></span>
										<div class="fb-like" data-href="<? echo $Facebook; ?>" data-layout="box_count" data-action="like" data-show-faces="false" data-share="false"></div>
										<a href="https://twitter.com/<? echo $twitterHandle; ?>" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @<?echo $twitterHandle; ?></a>
									</div>
										
									<a href="#" class="image featured"><img src="<? echo $Photo; ?>" alt="" /></a>
									<p>
										<ul>
											<li><strong>First Appeared: </strong><? echo date('Y',strtotime($Active)); ?></li>
											<li><strong>Website: </strong><a href = "<? echo $Website; ?>"><? echo $Website; ?></a></li>
											<li><strong>Facebook: </strong><a href = "<? echo $Facebook; ?>"><? echo $Facebook; ?></a></li>
											<li><strong>Twitter: </strong><a href = "http://twitter.com/<? echo $twitterHandle; ?>"><? echo $twitterHandle; ?></a></li>
											<li><strong>Instagram: </strong><a href = "<? echo $Instagram; ?>"><? echo $Instagram; ?></a></li>
											<li><strong>Email: </strong><a href = "mailto:<? echo $Email; ?>"><? echo $Email; ?></a></li>
											<li><strong>Cost: </strong><? echo $Dues; ?></li>
											<li><strong>Practice Times: </strong><? echo $Practice; ?></li>
										</ul>
									</p>
								</article>
				<?
						} //end stmt-fetch while loop
					} //end stmt if > 1 loop	
				?>
						</div>
					</div>

				<?php include("sidebar.php"); ?>

			</div>

	</body>
</html>