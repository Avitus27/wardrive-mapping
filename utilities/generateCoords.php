<?php

function generateMarkers( $table, $returnAsJSON = true ){
	$returnTable = array();
	$i = 0;
	foreach ($table as $nestedTable) {
		if ($nestedTable['Type'] == 'WIFI'){
			$returnTable[$i]["SSID"] = $nestedTable['SSID'];

			if (strpos($nestedTable['AuthMode'], 'WEP') !== false) {
				$returnTable[$i]["security"] = "WEP";
			} elseif (strpos($nestedTable['AuthMode'], 'WPS') !== false) {
				$returnTable[$i]["security"] = "WPS";
			} elseif (strpos($nestedTable['AuthMode'], 'WPA') !== false) {
				$returnTable[$i]["security"] = "WPA";
			}

			$returnTable[$i]["long"] = $nestedTable['CurrentLongitude'];
			$returnTable[$i]["lat"] = $nestedTable['CurrentLatitude'];
			$returnTable[$i]["accuracy"] = $nestedTable['Accuracy_meters'];
			$i++;
		}
	}
	if($returnAsJSON){
		return json_encode($returnTable);
	} else {
		return $returnTable;
	}
}

?>
