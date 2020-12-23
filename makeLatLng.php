<html>
<head>

<? 

	require('globals.php');
	
	$sqlInsertGPS = "UPDATE `Identifiers_Clubs` SET Lat = ?, Lng = ? WHERE ID = ?";
	if($stmtGPS = $con->prepare($sqlInsertGPS))
		;//do nothing and move on
	else
		printf("Errormessage: %s\n", $con->error);
	
	$sqlHighSchools = "SELECT `ID`, `Address2`, `City`, `State`, `Zip` FROM `Identifiers_Clubs` WHERE lat = 0 OR lng = 0";
	
	if($stmtHS = $con->prepare($sqlHighSchools))
	{
		$stmtHS->execute();
		$stmtHS->store_result(); //allow us to get properties, e.g. stmt->num_rows;

		if($stmtHS->num_rows >= 1){
			$stmtHS->bind_result($ID, $address, $city, $state, $zip);
			while($stmtHS->fetch()){
				$fullAddress = $address . " " . $city . " " . $state . " " . $zip;
				
				$coordinate = parse_address_google($fullAddress);
				$lat = $coordinate['lat'];
				$lng = $coordinate['lng'];
				//echo $fullAddress;
				//print_r($coordinate);
				//echo "<br/>Latitude is " .$lat;
				//echo "<br/>Longitude is " .$lng;
				
				
				$stmtGPS->bind_param("ddi",$lat,$lng,$ID);
				
				if($stmtGPS->execute())
					echo "club " . $ID . " updated successfully<br/>";
				else
					echo "ohes noes<br/>";
				
				
			}
		}
	}
	else
	{
		printf("Errormessage: %s\n", $con->error);
	}	
	
	function parse_address_google($address) {
	
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?sensorfalse&address='.urlencode($address);
		$results = json_decode(file_get_contents($url),1);
		//print_r($results);
		//die('<pre>'.print_r($results,true));
			
		/*$parts = array(
		  'street_number'=>array('street_number'),
		  'address'=>array('route'),
		  'city'=>array('locality'),
		  'county'=>array('administrative_area_level_2'),
		  'state'=>array('administrative_area_level_1'),
		  'zip'=>array('postal_code'),
		  'zip2'=>array('postal_code_suffix'),
		);
		
		if (!empty($results['results'][0]['address_components'])) {
			$ac = $results['results'][0]['address_components'];
			foreach($parts as $need=>&$types) {
				foreach($ac as &$a) {
					if (in_array($a['types'][0],$types)) $address_out[$need] = $a['long_name'];
					elseif (empty($address_out[$need])) $address_out[$need] = '';
				}
			}
		} 
		else echo 'empty results';*/
		
		$address_out['lat'] = $results['results'][0]['geometry']['location']['lat'];
		$address_out['lng'] = $results['results'][0]['geometry']['location']['lng'];
		
		/* debugging
		echo "<pre>";
		print_r($results);
		echo "</pre>";
		*/
		
		return $address_out;
	} 
	
?>