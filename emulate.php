<?php require_once("headers.php"); ?>
<?php require_once("auth.php"); ?>
<?php require_once("globals.php"); ?>

<style>
    .ui-autocomplete { position: absolute; cursor: default; background-color: #ffffff; z-index:30 !important; border: 2px solid #555555;}
	.ui-helper-hidden-accessible { display:none; }
</style>
 
	<body class="left-sidebar">

		<!-- Wrapper -->
			<div id="wrapper">

				<!-- Content -->
					<div id="content">
						<div class="inner">
							<!-- Post -->
								<article>
									<header>
										<h2>Login</h2>
									</header>
									<section>
										<?php 
										if(isUserCurrentlyEmulating()){
											echo "You are logged in. Click <a href = '".htmlspecialchars($_SERVER['PHP_SELF'])."?logout'>here</a> to log out.";
                                            echo "Emulator User ID is:" . getemulatorUserID()."<br/>";
                                            echo "User ID is: " . getUserID()."<br/>";
										}
										elseif(userCanEmulate()){
                                        ?>
                                            <script>
                                                //ugh this is duplicate code....
                                                $(document).ready(function(){
                                                    $("#newWLast").autocomplete({
                                                        delay:350,
                                                        source: "nameAutocomplete.php",
                                                        dataType: "json",
                                                        //source: data,
                                                        minLength: 1,
                                                        select: function (event, ui) {
                                                                                        $("#newWId").val(ui.item.value); 
                                                                                        $("#newWLast").val(ui.item.lastName);
                                                                                        $("#newWFirst").val(ui.item.firstName);
                                                                                        $("#newWMiddle").val(ui.item.middleName);
                                                                                        return false; //prevent widget from updating value
                                                                                    },
                                                        focus: function(event, ui) { //set for arrow keys
                                                            $("#newWLast").val(ui.item.label);
                                                            return false;
                                                        },
                                                        messages: {
                                                            noResults: "That name was not found, please add them to the database.",
                                                            results: function() {}
                                                        }
                                                    });
                                                });
                                            </script>
                                            <?php 
                                                echo "<form method='post' action = '".str_replace("logout","",basename($_SERVER['REQUEST_URI']))."'>" 
                                            ?>
                                                <input size = "2" id = "newWId" name = "newWId" />
                                                <input size = "15" type = "text" id = "newWLast" autocomplete="off" name = "g234798tyh" placeholder = ""/>
                                                <input size = "12" type = "text" id = "newWFirst" placeholder = "First Name"/>
                                                <input size = "3" type = "text" id = "newWMiddle"  placeholder = "Middle" />
                                                <input type = 'submit' name = 'userWantsToEmulate' value='Submit' />
                                            </form>
                                        <?php
										}
                                        else{
											display_login(); 
										}											
										?>
									</section>
								</article>
						</div>
					</div>

				<?php include("sidebar.php"); ?>

			</div>

	</body>
</html>