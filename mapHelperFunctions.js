		//This function makes the region overlay legend in the right pane//
		///////////////////////////////////////////////////////////////////
		function makeRegionLabels(map)
		{
			var regionColor;
			//NAIGC NEW	
				//West Region
				regionColor = "#FFFF00";
					colorState('CA',regionColor,map,'NAIGCnew');
					colorState('NV',regionColor,map,'NAIGCnew');
					colorState('UT',regionColor,map,'NAIGCnew');
					colorState('AZ',regionColor,map,'NAIGCnew');
					colorState('OR',regionColor,map,'NAIGCnew');
					colorState('ID',regionColor,map,'NAIGCnew');
					colorState('MT',regionColor,map,'NAIGCnew');
					colorState('WA',regionColor,map,'NAIGCnew');
					colorState('CO',regionColor,map,'NAIGCnew');
					colorState('WY',regionColor,map,'NAIGCnew');
					colorState('NM',regionColor,map,'NAIGCnew');
					colorState('AK',regionColor,map,'NAIGCnew');
					colorState('HI',regionColor,map,'NAIGCnew');colorState('HI2',regionColor,map,'NAIGCnew');colorState('HI3',regionColor,map,'NAIGCnew');colorState('HI4',regionColor,map,'NAIGCnew');
						colorState('HI5',regionColor,map,'NAIGCnew');colorState('HI6',regionColor,map,'NAIGCnew');colorState('HI7',regionColor,map,'NAIGCnew');
				//South Central Region
				regionColor = "#FF0000";
					colorState('TX',regionColor,map,'NAIGCnew');
					colorState('OK',regionColor,map,'NAIGCnew');
					colorState('KS',regionColor,map,'NAIGCnew');
					colorState('AR',regionColor,map,'NAIGCnew');
				//Midwest Region
				regionColor = "#00BFFF";
					colorState('WI',regionColor,map,'NAIGCnew');
					colorState('IL',regionColor,map,'NAIGCnew');
					colorState('MN',regionColor,map,'NAIGCnew');
					colorState('IA',regionColor,map,'NAIGCnew');
					colorState('MO',regionColor,map,'NAIGCnew');
					colorState('ND',regionColor,map,'NAIGCnew');
					colorState('SD',regionColor,map,'NAIGCnew');
					colorState('NE',regionColor,map,'NAIGCnew');
				//MidEast Region
				regionColor = "#000000";
					colorState('OH',regionColor,map,'NAIGCnew');
					colorState('IN',regionColor,map,'NAIGCnew');
					colorState('MI',regionColor,map,'NAIGCnew');
					colorState('KY',regionColor,map,'NAIGCnew');
				//NorthEast Region
				regionColor = "#FFA500";
					colorState('ME',regionColor,map,'NAIGCnew');	
					colorState('VT',regionColor,map,'NAIGCnew');
					colorState('NH',regionColor,map,'NAIGCnew');
					colorState('MA',regionColor,map,'NAIGCnew');
					colorState('RI',regionColor,map,'NAIGCnew');
					colorState('CT',regionColor,map,'NAIGCnew');
					colorState('NY',regionColor,map,'NAIGCnew');
				//Mid-Atlantic
				regionColor = "#FF00FF";
					colorState('PA',regionColor,map,'NAIGCnew');
					colorState('NJ',regionColor,map,'NAIGCnew');
					colorState('VA',regionColor,map,'NAIGCnew');
					colorState('WV',regionColor,map,'NAIGCnew');
					colorState('MD',regionColor,map,'NAIGCnew');
					colorState('DE',regionColor,map,'NAIGCnew');
				//Southeast
				regionColor = "#00FF00";
					colorState('TN',regionColor,map,'NAIGCnew');
					colorState('NC',regionColor,map,'NAIGCnew');
					colorState('SC',regionColor,map,'NAIGCnew');
					colorState('AL',regionColor,map,'NAIGCnew');
					colorState('GA',regionColor,map,'NAIGCnew');
					colorState('MS',regionColor,map,'NAIGCnew');
					colorState('LA',regionColor,map,'NAIGCnew');
					colorState('FL',regionColor,map,'NAIGCnew');
					
			//NAIGC OLD	
				//West Region
				regionColor = "#FFFF00";
					colorState('CA',regionColor,map,'NAIGCold');
					colorState('NV',regionColor,map,'NAIGCold');
					colorState('UT',regionColor,map,'NAIGCold');
					colorState('AZ',regionColor,map,'NAIGCold');
					colorState('OR',regionColor,map,'NAIGCold');
					colorState('ID',regionColor,map,'NAIGCold');
					colorState('MT',regionColor,map,'NAIGCold');
					colorState('WA',regionColor,map,'NAIGCold');
					colorState('CO',regionColor,map,'NAIGCold');
					colorState('WY',regionColor,map,'NAIGCold');
					colorState('AK',regionColor,map,'NAIGCold');
					colorState('HI',regionColor,map,'NAIGCold');colorState('HI2',regionColor,map,'NAIGCold');colorState('HI3',regionColor,map,'NAIGCold');colorState('HI4',regionColor,map,'NAIGCold');
						colorState('HI5',regionColor,map,'NAIGCold');colorState('HI6',regionColor,map,'NAIGCold');colorState('HI7',regionColor,map,'NAIGCold');
				//Southwest Region
				regionColor = "#FF0000";
					colorState('TX',regionColor,map,'NAIGCold');
					colorState('OK',regionColor,map,'NAIGCold');
					colorState('LA',regionColor,map,'NAIGCold');
					colorState('AR',regionColor,map,'NAIGCold');
					colorState('NM',regionColor,map,'NAIGCold');
				//Midwest Region
				regionColor = "#00BFFF";
					colorState('OH',regionColor,map,'NAIGCold');
					colorState('KY',regionColor,map,'NAIGCold');
					colorState('IN',regionColor,map,'NAIGCold');
					colorState('IL',regionColor,map,'NAIGCold');
					colorState('IA',regionColor,map,'NAIGCold');
					colorState('WI',regionColor,map,'NAIGCold');
					colorState('MN',regionColor,map,'NAIGCold');
					colorState('MI',regionColor,map,'NAIGCold');
					colorState('KS',regionColor,map,'NAIGCold');
					colorState('MO',regionColor,map,'NAIGCold');
					colorState('NE',regionColor,map,'NAIGCold');
					colorState('ND',regionColor,map,'NAIGCold');
					colorState('SD',regionColor,map,'NAIGCold');
				//NorthEast Region
				regionColor = "#FFA500";
					colorState('ME',regionColor,map,'NAIGCold');	
					colorState('VT',regionColor,map,'NAIGCold');
					colorState('NH',regionColor,map,'NAIGCold');
					colorState('MA',regionColor,map,'NAIGCold');
					colorState('RI',regionColor,map,'NAIGCold');
					colorState('CT',regionColor,map,'NAIGCold');
					colorState('NY',regionColor,map,'NAIGCold');
					colorState('PA',regionColor,map,'NAIGCold');
					colorState('NJ',regionColor,map,'NAIGCold');					
					colorState('DE',regionColor,map,'NAIGCold');
				//East
				regionColor = "#00FF00";
					colorState('MD',regionColor,map,'NAIGCold');
					colorState('VA',regionColor,map,'NAIGCold');
					colorState('NC',regionColor,map,'NAIGCold');
					colorState('SC',regionColor,map,'NAIGCold');
					colorState('FL',regionColor,map,'NAIGCold');
					colorState('GA',regionColor,map,'NAIGCold');
					colorState('TN',regionColor,map,'NAIGCold');
					colorState('AL',regionColor,map,'NAIGCold');
					colorState('MS',regionColor,map,'NAIGCold');
					colorState('WV',regionColor,map,'NAIGCold');
					
			// AAU	
				//midwest
				regionColor = "#00BFFF";
					colorState('WI',regionColor,map,'AAU');
					colorState('MN',regionColor,map,'AAU');
					colorState('IA',regionColor,map,'AAU');
					colorState('MO',regionColor,map,'AAU');
					colorState('ND',regionColor,map,'AAU');
					colorState('SD',regionColor,map,'AAU');
					colorState('NE',regionColor,map,'AAU');
					colorState('KS',regionColor,map,'AAU');
					colorState('AR',regionColor,map,'AAU');
					colorState('IN',regionColor,map,'AAU');
					colorState('MI',regionColor,map,'AAU');
					colorState('IL',regionColor,map,'AAU');
				//northeast
				regionColor = "#FFA500";
					colorState('ME',regionColor,map,'AAU');	
					colorState('VT',regionColor,map,'AAU');
					colorState('NH',regionColor,map,'AAU');
					colorState('MA',regionColor,map,'AAU');
					colorState('RI',regionColor,map,'AAU');
					colorState('CT',regionColor,map,'AAU');
					colorState('NY',regionColor,map,'AAU');
					colorState('PA',regionColor,map,'AAU');
					colorState('NJ',regionColor,map,'AAU');
					colorState('MD',regionColor,map,'AAU');
					colorState('DE',regionColor,map,'AAU');
					colorState('OH',regionColor,map,'AAU');
				//southern
				regionColor = "#00FF00";
					colorState('TN',regionColor,map,'AAU');
					colorState('NC',regionColor,map,'AAU');
					colorState('SC',regionColor,map,'AAU');
					colorState('AL',regionColor,map,'AAU');
					colorState('GA',regionColor,map,'AAU');
					colorState('MS',regionColor,map,'AAU');
					colorState('LA',regionColor,map,'AAU');
					colorState('FL',regionColor,map,'AAU');	
					colorState('KY',regionColor,map,'AAU');
					colorState('VA',regionColor,map,'AAU');
					colorState('WV',regionColor,map,'AAU');
				//western
				regionColor = "#FFFF00";
					colorState('CA',regionColor,map,'AAU');
					colorState('NV',regionColor,map,'AAU');
					colorState('UT',regionColor,map,'AAU');
					colorState('AZ',regionColor,map,'AAU');
					colorState('OR',regionColor,map,'AAU');
					colorState('ID',regionColor,map,'AAU');
					colorState('MT',regionColor,map,'AAU');
					colorState('WA',regionColor,map,'AAU');
					colorState('CO',regionColor,map,'AAU');
					colorState('WY',regionColor,map,'AAU');
					colorState('TX',regionColor,map,'AAU');
					colorState('OK',regionColor,map,'AAU');
					colorState('NM',regionColor,map,'AAU');
					
			//USAG WOMEN		
				//region 1
				regionColor = "#FFFF00";
					colorState('CA',regionColor,map,'USAGwomen');
					colorState('NV',regionColor,map,'USAGwomen');
					colorState('UT',regionColor,map,'USAGwomen');
					colorState('AZ',regionColor,map,'USAGwomen');
					colorState('HI',regionColor,map,'USAGwomen');colorState('HI2',regionColor,map,'USAGwomen');colorState('HI3',regionColor,map,'USAGwomen');
						colorState('HI4',regionColor,map,'USAGwomen');colorState('HI5',regionColor,map,'USAGwomen');colorState('HI6',regionColor,map,'USAGwomen');
						colorState('HI7',regionColor,map,'USAGwomen');
					
				//region 2
				regionColor = "#800080";
					colorState('OR',regionColor,map,'USAGwomen');
					colorState('ID',regionColor,map,'USAGwomen');
					colorState('MT',regionColor,map,'USAGwomen');
					colorState('WA',regionColor,map,'USAGwomen');
					colorState('AK',regionColor,map,'USAGwomen');
				
				//region 3
				regionColor = "#ff0000";
					colorState('CO',regionColor,map,'USAGwomen');
					colorState('WY',regionColor,map,'USAGwomen');
					colorState('TX',regionColor,map,'USAGwomen');
					colorState('OK',regionColor,map,'USAGwomen');
					colorState('AR',regionColor,map,'USAGwomen');
					colorState('NM',regionColor,map,'USAGwomen');
					colorState('KS',regionColor,map,'USAGwomen');
					
				//region 4
				regionColor = "#00BFFF";
					colorState('WI',regionColor,map,'USAGwomen');
					colorState('MN',regionColor,map,'USAGwomen');
					colorState('IA',regionColor,map,'USAGwomen');
					colorState('MO',regionColor,map,'USAGwomen');
					colorState('ND',regionColor,map,'USAGwomen');
					colorState('SD',regionColor,map,'USAGwomen');
					colorState('NE',regionColor,map,'USAGwomen');
				
				//region 5
				regionColor = "#000000";
					colorState('IL',regionColor,map,'USAGwomen');
					colorState('OH',regionColor,map,'USAGwomen');
					colorState('IN',regionColor,map,'USAGwomen');
					colorState('MI',regionColor,map,'USAGwomen');
					colorState('KY',regionColor,map,'USAGwomen');
				
				//region 6
				regionColor = "#FFA500";
					colorState('ME',regionColor,map,'USAGwomen');	
					colorState('VT',regionColor,map,'USAGwomen');
					colorState('NH',regionColor,map,'USAGwomen');
					colorState('MA',regionColor,map,'USAGwomen');
					colorState('RI',regionColor,map,'USAGwomen');
					colorState('CT',regionColor,map,'USAGwomen');
					colorState('NY',regionColor,map,'USAGwomen');
				
				//region 7
				regionColor = "#FF00FF";
					colorState('PA',regionColor,map,'USAGwomen');
					colorState('NJ',regionColor,map,'USAGwomen');
					colorState('VA',regionColor,map,'USAGwomen');
					colorState('WV',regionColor,map,'USAGwomen');
					colorState('MD',regionColor,map,'USAGwomen');
					colorState('DE',regionColor,map,'USAGwomen');
				
				//region 8
				regionColor = "#00FF00";
					colorState('TN',regionColor,map,'USAGwomen');
					colorState('NC',regionColor,map,'USAGwomen');
					colorState('SC',regionColor,map,'USAGwomen');
					colorState('AL',regionColor,map,'USAGwomen');
					colorState('GA',regionColor,map,'USAGwomen');
					colorState('MS',regionColor,map,'USAGwomen');
					colorState('LA',regionColor,map,'USAGwomen');
					colorState('FL',regionColor,map,'USAGwomen');
					
			//USAG MEN		
				//region 1
				regionColor = "#FFFF00";
					colorState('CA',regionColor,map,'USAGmen');
					colorState('NV',regionColor,map,'USAGmen');
					colorState('AZ',regionColor,map,'USAGmen');
					colorState('HI',regionColor,map,'USAGmen');colorState('HI2',regionColor,map,'USAGmen');colorState('HI3',regionColor,map,'USAGmen');
						colorState('HI4',regionColor,map,'USAGmen');colorState('HI5',regionColor,map,'USAGmen');colorState('HI6',regionColor,map,'USAGmen');
						colorState('HI7',regionColor,map,'USAGmen');
					
				//region 2
				regionColor = "#800080";
					colorState('OR',regionColor,map,'USAGmen');
					colorState('ID',regionColor,map,'USAGmen');
					colorState('MT',regionColor,map,'USAGmen');
					colorState('WA',regionColor,map,'USAGmen');
					colorState('AK',regionColor,map,'USAGmen');
				
				//region 3
				regionColor = "#ff0000";
					colorState('TX',regionColor,map,'USAGmen');
					colorState('OK',regionColor,map,'USAGmen');
					colorState('KS',regionColor,map,'USAGmen');
					colorState('LA',regionColor,map,'USAGmen');
					colorState('AR',regionColor,map,'USAGmen');
					colorState('MO',regionColor,map,'USAGmen');
					
				//region 4
				regionColor = "#00BFFF";
					colorState('WI',regionColor,map,'USAGmen');
					colorState('MN',regionColor,map,'USAGmen');
					colorState('IA',regionColor,map,'USAGmen');
					colorState('ND',regionColor,map,'USAGmen');
					colorState('SD',regionColor,map,'USAGmen');
					colorState('NE',regionColor,map,'USAGmen');
				
				//region 5
				regionColor = "#000000";
					colorState('IL',regionColor,map,'USAGmen');
					colorState('OH',regionColor,map,'USAGmen');
					colorState('IN',regionColor,map,'USAGmen');
					colorState('MI',regionColor,map,'USAGmen');
					colorState('KY',regionColor,map,'USAGmen');
				
				//region 6
				regionColor = "#FFA500";
					colorState('ME',regionColor,map,'USAGmen');	
					colorState('VT',regionColor,map,'USAGmen');
					colorState('NH',regionColor,map,'USAGmen');
					colorState('MA',regionColor,map,'USAGmen');
					colorState('RI',regionColor,map,'USAGmen');
					colorState('CT',regionColor,map,'USAGmen');
				
				//region 7
				regionColor = "#FF00FF";
					colorState('NY',regionColor,map,'USAGmen');
					colorState('PA',regionColor,map,'USAGmen');
					colorState('NJ',regionColor,map,'USAGmen');
					colorState('VA',regionColor,map,'USAGmen');
					colorState('WV',regionColor,map,'USAGmen');
					colorState('MD',regionColor,map,'USAGmen');
					colorState('DE',regionColor,map,'USAGmen');
				
				//region 8
				regionColor = "#00FF00";
					colorState('TN',regionColor,map,'USAGmen');
					colorState('NC',regionColor,map,'USAGmen');
					colorState('SC',regionColor,map,'USAGmen');
					colorState('AL',regionColor,map,'USAGmen');
					colorState('GA',regionColor,map,'USAGmen');
					colorState('MS',regionColor,map,'USAGmen');
					colorState('FL',regionColor,map,'USAGmen');	
					
				//region 9 
				regionColor = "#eeeeee";
					colorState('NM',regionColor,map,'USAGmen');
					colorState('CO',regionColor,map,'USAGmen');
					colorState('WY',regionColor,map,'USAGmen');
					colorState('UT',regionColor,map,'USAGmen');
		}