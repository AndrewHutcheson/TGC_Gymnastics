<?php include("headers.php"); ?>
<?php include("auth.php"); ?>
<?php include("globals.php"); ?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<script type="text/javascript" src="js/moment.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="tabulator-master/dist/js/jquery_wrapper.min.js"></script>

<body class="left-sidebar">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Content -->
					<div id="content">
						<div class="inner">

					<?php
					
					function geoArray($ip)
					{
						$theArray = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));
						$returnValue = $theArray['geoplugin_city'] . ", " . $theArray['geoplugin_regionCode'];
						return $returnValue;
					}
					
					if(userIsExecutiveAdministrator())
					{
					$sql = "
							Select *
							From
							(
								Select 
									Action,
									concat(FirstName, ' ', LastName) as Person,
									Timestamp,
									concat(Identifiers_Institutions.Name, ', ' , Identifiers_Institutions.State) as Value
								From
									Metrics_Map,
									Identifiers_Institutions,
									Identifiers_People
								Where
									Action IN ('InstitutionDescriptorClicked', 'CollegeInstitutionDescriptorClicked') AND
									Metrics_Map.Value = Identifiers_Institutions.ID AND
									Metrics_Map.PersonID = Identifiers_People.ID
							UNION
								Select 
									Action,
									concat(FirstName, ' ', LastName) as Person,
									Timestamp,
									Value
								From
									Metrics_Map,
									Identifiers_People
								Where
									Action NOT IN ('InstitutionDescriptorClicked', 'CollegeInstitutionDescriptorClicked', 'PersonDescriptorClicked', 'CCDescriptorClicked') AND
									Metrics_Map.PersonID = Identifiers_People.ID
							UNION
								Select
									Action,
									concat(FirstName, ' ', LastName) as Person,
									Timestamp,
									concat(Name , ', ' , TEMP_Board.State) AS Value
								From
									Metrics_Map,
									TEMP_Board,
									Identifiers_People
								Where
									Action = 'PersonDescriptorClicked' AND
									Metrics_Map.PersonID = Identifiers_People.ID AND
									substring(Metrics_Map.Value,6) = TEMP_Board.ID
							UNION
								Select 
									'Login' As Action,
									concat(FirstName, ' ', LastName) as Person,
									Time as Timestamp,
									concat(IPAddress, ', ', Referrer) AS Value
								From
									Log_Login,
									Identifiers_People
								Where
									(PageID like '%map%' OR PageID like '%louisiana%' OR PageID like '%tgcOutreach%') AND
									Log_Login.PersonID = Identifiers_People.ID AND
									Success = 1
							UNION 
								Select
									Action,
									concat(FirstName, ' ', LastName) as Person,
									Timestamp,
									concat(TEMP_CC.Name, ', ', TEMP_CC.State) AS Value
								From
									Metrics_Map,
									TEMP_CC,
									Identifiers_People
								Where
									Action = 'CCDescriptorClicked' AND
									Metrics_Map.PersonID = Identifiers_People.ID AND
									Metrics_Map.Value = TEMP_CC.ID
							) x
							Where
								Person <> 'Andrew Hutcheson' AND
								Timestamp >= '2019-04-10 00:00:00'
							Order By 
								Timestamp Desc
							;";

					$stmt = $conn->prepare($sql);
					$stmt->execute();
					$result = $stmt->fetchAll();
					
					//alright so we have our fetchall statement but what if we can geolocate it
					
					//and lets store results so that we dont max the api.
					$ipResults = array();
					
					for($i = 0; $i < sizeof($result); $i++)
					{
						$ip = substr($result[$i]["Value"],0,strpos($result[$i]["Value"],","));
						if($result[$i]["Action"] == "Login")
						{
							if(isset($ipResults[$ip]))
							{
								$result[$i]["Value"] = $ipResults[$ip];
							}
							else
							{
								$location = geoArray($ip);
								//if(strlen($location) > 4)
								if(false)
								{
									$temp = $result[$i]["Value"];
									$result[$i]["Value"] = $location . ", " . $temp;
								}
								$ipResults[$ip] = $result[$i]["Value"];
							}
						}
					}
					
					?>
					<div id = "thetable"></div>
					<style>
						#content > .inner{
							max-width: 1400px !important;
						}
						.tabulator-header{
							height: 80px;
						}
					</style>
					<script type="text/javascript">
						var theData = <?php echo json_encode($result); ?>;

						var table = new Tabulator("#thetable", 
						{
							layout: "fitColumns",
							layoutColumnsOnNewData:true,
							columns:[
								{title:"Action", field:"Action", headerFilter:"input"},
								{title:"Person", field:"Person", headerFilter:"input"},
								{title:"Timestamp", field:"Timestamp", headerFilter:true },
								{title:"Value", field:"Value", headerFilter:"input"},
							]
						});
						table.setData(theData);
						table.redraw(true);
					</script>
					
					<?php
					}	
					else
					{
						echo "You do not have permission to access this page.";
						display_login();
					}
					?>

						</div>
					</div>
				<?php include("sidebar.php"); ?>
			</div>
</body>	
</html>		