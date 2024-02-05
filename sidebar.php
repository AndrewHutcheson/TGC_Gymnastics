<?php require_once("auth.php"); ?>
				<div id = "alerts">
					<?php
						if(isUserCurrentlyEmulating())
						{
							echo "YOU ARE ACTUALLY USER " . getemulatorUserID() . "-" . userIdToUserName(getemulatorUserID()) . " BUT YOU ARE CURRENTLY EMULATING USER " . getUserID() . "-" . userIdToUserName(getUserID()) ;
						}
					?>
				</div>
				<!-- Sidebar -->	
				<div id="sidebar">
					
						<!-- Logo -->
							<h1 id="logo"><a href="https://tgcgymnastics.com">TGC</a></h1>
					
						<!-- Nav -->
							<nav id="nav">
								<ul>
									<?
									
									/*
										TODO: if the current page is under a dropdown menu, start the menu expanded.
									*/
									
									if(userIsLoggedIn())
									{ ?>
										<li><div onclick = '$("#peopleNav").toggle();'>My Team</div>
											<ul id = "peopleNav" style = "display:none">
												<li <? if(basename($_SERVER['PHP_SELF'])=="#"){echo 'class="current"'; } ?> ><!--a href="#">My Profile</a></li-->
												<?
												//if((sizeof(getListOfUserCaptainPermissions()) > 0) || (sizeof(getListOfUserCoachPermissions()) > 0) || (userIsExecutiveAdministrator()))
												if(true)
												{
												?>
													<li <? if(basename($_SERVER['PHP_SELF'])=="register.php"){echo 'class="current"'; } ?> ><a href="register.php">Meet Registration</a></li>
												<?
												}
												if((sizeof(getListOfUserClubAdministrativePermissions()) > 0) || (userIsExecutiveAdministrator()))
												{
												?>
													<li <? if(basename($_SERVER['PHP_SELF'])=="myTeam.php"){echo 'class="current"'; } ?> ><a href="myTeam.php">Team Management</a></li>
													<li <? if(basename($_SERVER['PHP_SELF'])=="meetManagement.php"){echo 'class="current"'; } ?> ><a href="meetManagement.php">Meet Management</a></li>
												<?php
												}
												if(sizeOf(getClubsThatUserCanScore()) > 0)
												{
												?>
													<li <?php if(basename($_SERVER['PHP_SELF'])=="score.php"){echo 'class="current"'; } ?> ><a href="score">Meet Scoring</a></li>
													<li <?php if(basename($_SERVER['PHP_SELF'])=="verifyScore.php"){echo 'class="current"'; } ?> ><a href="verifyScore">Score Verification</a></li>
												<?
												}
												?>
											</ul>
										</li>
										<?
										if(userIsExecutiveAdministrator())
										{ ?>
											<li><div onclick = '$("#tgcBoard").toggle();'>TGC Board</div>
												<ul id = "tgcBoard" style = "display:none">
													<li <? if(basename($_SERVER['PHP_SELF'])=="retroScoring.php"){echo 'class="current"'; } ?> ><a href="retroScoring.php">ALL Meet Scoring</a></li>
													<li <? if(basename($_SERVER['PHP_SELF'])=="addMeet.php"){echo 'class="current"'; } ?> ><a href="addMeet.php">Create Meet</a></li>
													<li <? if(basename($_SERVER['PHP_SELF'])=="institutionEdit.php"){echo 'class="current"'; } ?> ><a href="institutionEdit.php">Institution Edit</a></li>
													<li <? if(basename($_SERVER['PHP_SELF'])=="registrationMetrics.php"){echo 'class="current"'; } ?> ><a href="registrationMetrics.php">Registration Metrics</a></li>
													<li <? if(basename($_SERVER['PHP_SELF'])=="tgcOutreach.php"){echo 'class="current"'; } ?> ><a href="tgcOutreach.php">Outreach Map</a></li>
													<li <? if(basename($_SERVER['PHP_SELF'])=="map.php"){echo 'class="current"'; } ?> ><a href="map.php">USGym Map</a></li>
													<!--li <? if(basename($_SERVER['PHP_SELF'])=="CollegeClubFeeds.php"){echo 'class="current"'; } ?> ><a href="CollegeClubFeeds.php">Social Stream (national)</a></li-->
													<li><a href="https://tgcgymnastics.com/mailman/admin/clubreps_tgcgymnastics.com">TGC Email List</a></li>
													<li><a href="https://tgcgymnastics.com/mailman/admin/alumni_tgcgymnastics.com">Alumni Email List</a></li>
													<?php 
													if(userCanEmulate())
													{
													?>
													<li <?php if(basename($_SERVER['PHP_SELF'])=="emulate.php"){echo 'class="current"'; } ?> ><a href="emulate">Emulate User</a></li>
													<?php
													}
													if(userIsSuperAdministrator())
													{
													?>
													<li <?php if(basename($_SERVER['PHP_SELF'])=="myOrg.php"){echo 'class="current"'; } ?> ><a href="myOrg">TGC Users</a></li>
													<?php
													}
													?>
												</ul>
											</li>
										<?
										}
									}
									?>
									<li <? if(basename($_SERVER['PHP_SELF'])=="index.php"){echo 'class="current"'; } ?> ><a href="index.php">News</a></li>
									<li <? if(basename($_SERVER['PHP_SELF'])=="schedule.php"){echo 'class="current"'; } ?> ><a href="schedule.php">Schedule</a></li>
									<li <? if(basename($_SERVER['PHP_SELF'])=="results.php"){echo 'class="current"'; } ?> ><a href="results.php">Results</a></li>
									<li <? if(basename($_SERVER['PHP_SELF'])=="gymnasts.php"){echo 'class="current"'; } ?> ><a href="gymnasts.php">FAQ</a></li>
									<li <? if(basename($_SERVER['PHP_SELF'])=="nope.php"){echo 'class="current"'; } ?> ><a href="https://docs.google.com/forms/d/1aqBcYhMx7LegZcQGFZOmxegohzZ3DEUJyRVIRfVmzoQ/viewform">Join/make a team</a></li>
									<li <? if(basename($_SERVER['PHP_SELF'])=="teams.php"){echo 'class="current"'; } ?> ><a href="teams.php">Our Teams</a></li>
									<li <? if(basename($_SERVER['PHP_SELF'])=="about.php"){echo 'class="current"'; } ?> ><a href="about.php">About Us</a></li>
									<li <? if(basename($_SERVER['PHP_SELF'])=="scholarships.php"){echo 'class="current"'; } ?> ><a href="scholarships.php">Scholarships</a></li>
									<li <? if(basename($_SERVER['PHP_SELF'])=="files.php"){echo 'class="current"'; } ?> ><a href="files.php">Files</a></li>
									<li <? if(basename($_SERVER['PHP_SELF'])=="contactUs.php"){echo 'class="current"'; } ?> ><a href="contactUs.php">Contact Us</a></li>
									<?
									if(!userIsLoggedIn())
									{ 
									?>
										<li <? if(basename($_SERVER['PHP_SELF'])=="login.php"){echo 'class="current"'; } ?> ><a href="login.php">Login</a></li>
									<?
									}
									else
									{
									?>
										<li><a href="<? echo basename($_SERVER['PHP_SELF']); ?>?logout">Logout</a></li>
									<?
									}
									?>
								</ul>
							</nav>

						<!-- Search -->
							<!--section class="box search">
								<form method="post" action="#">
									<input type="text" class="text" name="search" placeholder="Search" />
								</form>
							</section-->
					
						<!-- Recent Posts -->
						<?
							$query = "SELECT news.ID, Title
								FROM  Content_Newsfeed news
								WHERE news.siteID = ? AND news.pageID = ?
								ORDER BY Posted DESC
								LIMIT 6";
										
							$stmt = $con->prepare($query);
							$stmt->bind_param("ii",$siteID, $pageID);
									
							$stmt->execute();
							$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;
							
							if($stmt->num_rows >= 1){
								$stmt->bind_result($postID, $Title);
						?>
							<!--section class="box recent-posts">
								<header>
									<h2>Recent News</h2>
								</header>
								<ul>
								<?
								while($stmt->fetch()){
									echo '<li><a href="index.php#'.$postID.'">'.substr($Title,0,39).'...</a></li>';
								}	
								?>
								</ul>
							</section-->
						<?
							};
						?>
							
						<!-- Text -->
							<section class="box text-style1">
									<!--div class="fb-like" data-href="https://www.facebook.com/TexasGymnasticsConference" data-layout="box_count" data-action="like" data-show-faces="false" data-share="false"></div-->
									<!--a href="https://twitter.com/TgcGymnastics" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @TgcGymnastics</a>
									<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script-->
							</section>
					
						<!-- Recent Comments -->
							<!--section class="box recent-comments">
								<header>
									<h2>Recent Comments</h2>
								</header>
								<ul>
									<li>case on <a href="#">Lorem ipsum dolor</a></li>
									<li>molly on <a href="#">Sed dolore magna</a></li>
									<li>case on <a href="#">Sed dolore magna</a></li>
								</ul>
							</section-->
					
						<!-- Calendar -->
							<!--section class="box calendar">
								<div class="inner">
									<table>
										<caption>Dec 2014</caption>
										<thead>
											<tr>
												<th scope="col" title="Monday">M</th>
												<th scope="col" title="Tuesday">T</th>
												<th scope="col" title="Wednesday">W</th>
												<th scope="col" title="Thursday">T</th>
												<th scope="col" title="Friday">F</th>
												<th scope="col" title="Saturday">S</th>
												<th scope="col" title="Sunday">S</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td colspan="4" class="pad"><span>&nbsp;</span></td>
												<td><span>1</span></td>
												<td><span>2</span></td>
												<td><span>3</span></td>
											</tr>
											<tr>
												<td><span>4</span></td>
												<td><span>5</span></td>
												<td><a href="#">6</a></td>
												<td><span>7</span></td>
												<td><span>8</span></td>
												<td><span>9</span></td>
												<td><a href="#">10</a></td>
											</tr>
											<tr>
												<td><span>11</span></td>
												<td><span>12</span></td>
												<td><span>13</span></td>
												<td class="today"><a href="#">14</a></td>
												<td><span>15</span></td>
												<td><span>16</span></td>
												<td><span>17</span></td>
											</tr>
											<tr>
												<td><span>18</span></td>
												<td><span>19</span></td>
												<td><span>20</span></td>
												<td><span>21</span></td>
												<td><span>22</span></td>
												<td><a href="#">23</a></td>
												<td><span>24</span></td>
											</tr>
											<tr>
												<td><a href="#">25</a></td>
												<td><span>26</span></td>
												<td><span>27</span></td>
												<td><span>28</span></td>
												<td class="pad" colspan="3"><span>&nbsp;</span></td>
											</tr>
										</tbody>
									</table>
								</div>
							</section-->
						
						<!-- Copyright -->
							<ul id="copyright">
								<li>&copy; <? echo date("Y"); ?> Texas Gymnastics Conference. All Rights Reserved.</li>
							</ul>

					</div>