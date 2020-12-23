<html>

	<body>
     
    
		<div id = "mainlist" style="width:100%">
			<table>
				<tr>
					<td style = "width:20%">
						<div id = selector>
							Enter candidate city airport codes: <br/>
							<form>
								<input id = "city1" name ="city1" type = "text"/><br/>
								<input id = "city2" name ="city2" type = "text"/><br/>
								<input id = "city3" name ="city3" type = "text"/><br/>
								<input id = "city4" name ="city4" type = "text"/><br/>
								<input id = "city5" name ="city5" type = "text"/><br/>
								<input type = "submit" onclick = "loadList();" value = "Load List"/>
							</form>
						</div>   
						<?php
						if(isset($_REQUEST['city1']))
						{
						
							$schoolArrayOne = array('ACT', 'ACY', 'ATL', 'AUS', 'BDL', 'BGM', 'BHM', 'BJC', 'BNA', 'BOS', 'BTV', 'BUF', 'BUR', 'BWI', 'CAE', 'CAK', 'CHA', 'CHO', 'CID', 'CLE', 'CLL', 'CLT', 'CMH', 'CMI', 'CVG', 'CVO', 'DAL', 'DAY', 'DCA', 'DEC', 'DEN', 'DFW', 'DTW', 'ELM', 'ELP', 'EUG', 'EWR', 'FLG', 'FNL', 'FNT', 'FTW', 'FWA', 'GNV', 'GRR', 'GSP', 'GTR', 'GYY', 'HVN', 'IAD', 'IAG', 'IAH', 'ILG', 'ILM', 'IND', 'ITH', 'JFK', 'LAN', 'LAX', 'LBB', 'LBE', 'LEX', 'LFT', 'LGA', 'LSE', 'MBS', 'MCI', 'MDT', 'MDW', 'MKE', 'MKG', 'MLI', 'MQT', 'MSN', 'MSP', 'MSY', 'MWA', 'OAK', 'OCE', 'OKC', 'ONT', 'ORD', 'ORF', 'PBG', 'PDX', 'PHF', 'PHL', 'PHX', 'PIT', 'PVD', 'PWM', 'RDU', 'RFD', 'RIC', 'ROA', 'ROC', 'SAN', 'SAT', 'SBA', 'SBN', 'SBY', 'SCE', 'SDF', 'SFO', 'SHD', 'SJC', 'SLC', 'SMF', 'STL', 'SYR', 'TCL', 'TLH', 'TOL', 'TYS', 'WST', 'YQC');
							//$cityArray = array('ATL','RIC','SLC','MSP','IND');
							$cityArray = array($_REQUEST['city1'],$_REQUEST['city2'],$_REQUEST['city3'],$_REQUEST['city4'],$_REQUEST['city5']);
							for($i = 0; $i<count($cityArray); $i++)
							{
								for($j = 0; $j<count($schoolArrayOne); $j++)
								{
									echo '<a target = "sideframe" href = "https://www.kayak.com/flights/'.$schoolArrayOne[$j].'-'.$cityArray[$i].'/2021-04-14/2021-04-18">'.$schoolArrayOne[$j].'-'.$cityArray[$i].'</a><br/>';
								}
							}
						}
						?>
					</td>
					<td style = "width:80%; float:right; height:80%; vertical-align:top; position:fixed;">
						<iframe style="height:100%; width: 100%" src = "" name = "sideframe">
							...Loading...
						</iframe>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>