<?php
	
	//this is all ajax stuff
	function checkForDuplicateAffiliations($person1,$person2)
	{
		
	}
	
	function checkForDuplicateRoutines($person1,$person2)
	{
		
	}
	
	function deleteAffiliation($affiliationID)
	{
		$sql = "
			Delete From 
				Identifiers_Affiliations
			Where
				ID = ?
			LIMIT 1
			";
	}
	
	function reAssignAffiliation($affiliation, $person)
	{
		$sql = "
			Update 
				Identifiers_Affiliations
			Set
				PersonID = ?
			Where
				ID = ?
			";
	}
	
	function reassignRoutines($oldPersonID, $newPersonID)
	{
		//check that checkForDuplicateRoutines returns 0 rows
		$sql = "
			Update 
				Events_Routines
			Set
				PersonID = ?
			Where
				PersonID = ?
			";
	}
?>

