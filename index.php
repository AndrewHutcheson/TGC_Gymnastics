<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>

	<? 
		//define what this page is
		$pageID = 0;
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
							
							if(userIsLoggedIn())
							{
								if(userIsExecutiveAdministrator()){
								//show form for making a new post.
								?>
								
									<script type = "text/javascript">
										function saveNewPost()
										{
											var title = document.getElementById("postTitle").value;
											var subTitle = document.getElementById("postSubtitle").value;
											//var content = editor.getData()
											CKEDITOR.instances.editor.updateElement();
											var content = document.getElementById("editor").value;
											
											$.ajax({
												type: 'POST',
												url: "newsPostAjax.php",
												async: false,
												data: {
													newNewsPostSubmit: 1,
													postTitle: title,
													postSubtitle: subTitle,
													postContent: content
												},
												dataType: 'json',
												success: function (data) {
													alert(data['Message']);
												},
												error: function (textStatus, errorThrown) {
													console.log(errorThrown);
													alert("error saving post");
												}
											});
										}
										
										function deleteNewsPost(id)
										{
											$.ajax({
												type: 'POST',
												url: "newsPostAjax.php",
												async: false,
												data: {
													deleteNewsPostSubmit: 1,
													postID: id
												},
												dataType: 'json',
												success: function (data) {
													alert(data['Message']);
												},
												error: function (textStatus, errorThrown) {
													console.log(errorThrown);
													alert("error deleting post");
												}
											});
										}
									</script>
								
									<span onclick = 'toggleDiv("newNewsPostDiv");'>Click Here to Add a New News Post</span><br/>
									<div style = 'display:none;' id = 'newNewsPostDiv'>
										Post Title: <input type = "text" id = "postTitle" name = "postTitle"/><br/>
										Post Subtitle: <input type = "text" id = "postSubtitle" name = "postSubtitle"/><br/>
										Post Content: <!--div id = "editor" id = "postContent" name = "postContent"/></div><br/-->
														<textarea id = "editor" id = "postContent" name = "postContent"/></textarea><br/>
										<button name = 'newNewsPostSubmit' onclick = "saveNewPost();">Submit</button>
									</div>
									
									<script src="ckeditor4/ckeditor.js"></script>
									<script>
										CKEDITOR.replace( 'editor' );
									</script>
									<!--script src="ckEditor/ckeditor.js"></script-->
									<!--script>										
										let editor;
										
										ClassicEditor
										.create( document.querySelector( '#editor' ), {
											
											toolbar: {
												items: [
													'bold',
													'italic',
													'underline',
													'strikethrough',
													'subscript',
													'superscript',
													'|',
													'fontSize',
													'fontColor',
													'fontBackgroundColor',
													'highlight',
													'link',
													'|',
													'bulletedList',
													'numberedList',
													'indent',
													'outdent',
													'|',
													'blockQuote',
													'insertTable',
													'mediaEmbed',
													'|',
													'undo',
													'redo'
												]
											},
											language: 'en',
											image: {
												toolbar: [
													'imageTextAlternative',
													'imageStyle:full',
													'imageStyle:side'
												]
											},
											table: {
												contentToolbar: [
													'tableColumn',
													'tableRow',
													'mergeTableCells'
												]
											},
											licenseKey: '',
											
										} )
										.then( newEditor => {
											editor = newEditor;
										} )
										.catch( error => {
											console.error( error );
										} );
									</script-->
								<?
								} //end show form for new post / is admin
							} //end if user logged in						
						?>					
						
						
				<?
					//get all posts to display
					$query = "SELECT news.ID, Title, Subtitle, Posted, Updated, LastUpdatedAuthor, Content, people.Firstname, people.Lastname
								FROM  Content_Newsfeed news, Identifiers_People people
								WHERE news.siteID = ? AND news.pageID = ? AND news.InitialAuthor = people.ID AND news.Deleted = 0
								ORDER BY Posted DESC"; // LIMIT --todo: add pagination
										
					$stmt = $con->prepare($query);
					$stmt->bind_param("ii",$siteID, $pageID);
							
					$stmt->execute();
					$stmt->store_result(); //allow us to get properties, e.g. stmt->num_rows;
					
					if($stmt->num_rows >= 1){
						$stmt->bind_result($postID, $Title, $Subtitle, $Posted, $Updated, $LastUpdatedAuthor, $Content, $Firstname, $Lastname);
						
						while($stmt->fetch()){
							//first we need a little manipulation with this template's css/js layout
								//get 3 char month
								$mmm = date('M',strtotime($Posted));
								//get stupid rest of month
								$srof = substr(date('F',strtotime($Posted)),3);
								//get 2 digit day
								$dd = date('j',strtotime($Posted));
								//get 4 digit year
								$yyyy = date('Y',strtotime($Posted));
								//echo "<script>alert('".strtotime($Posted)."');</script>";
								
				?>
						
						<!-- Post -->
								<article id = "<?php echo $postID ?>" class="box post post-excerpt">
									<header>
										<h2><a href="#"><?php echo $Title ?></a>
										<?php
										if(userIsExecutiveAdministrator())
										{
											echo "<button onclick = 'deleteNewsPost(". $postID .");'>X</button>";
										}
										?></h2>
										<p><?php echo $Subtitle ?></p>
									</header>
									<p><?php //echo "Posted at: " . $Posted . " by " . $Firstname . " " . $Lastname ?></p>
									<div class="info">
										<span class="date"><span class="month"><?php echo $mmm ?><span><?php echo $srof ?></span></span> <span class="day"><?php echo $dd ?></span><span class="year">, <? echo $yyyy ?></span></span>
										<!--ul class="stats">
											<li><a href="#" class="icon fa-comment">16</a></li>
											<li><a href="#" class="icon fa-heart">32</a></li>
											<li><a href="#" class="icon fa-twitter">64</a></li>
											<li><a href="#" class="icon fa-facebook">128</a></li>
										</ul-->
									</div>
									<?php echo $Content ?>
								</article>
								
								
				<?
						} //end stmt-fetch while loop
					} //end stmt if > 1 loop	
				?>
							<!-- Pagination -->
								<div class="pagination">
									<!--<a href="#" class="button previous">Previous Page</a>-->
									<div class="pages">
										<a href="#" class="active">1</a>
										<!--a href="#">2</a>
										<a href="#">3</a>
										<a href="#">4</a>
										<span>&hellip;</span>
										<a href="#">20</a-->
									</div>
									<!--a href="#" class="button next">Next Page</a-->
								</div>

						</div>
					</div>

				<?php include("sidebar.php"); ?>

			</div>

	</body>
</html>