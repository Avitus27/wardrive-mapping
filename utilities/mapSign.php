<?php

/**
 * Snippet taken from https://github.com/TheJokersThief/Eve/
 * @param  URL 				The request URL for googleapis.com
 * @param  signingSecret 	Your account's secret for signing requests
 * @return string 			The URL with the generated signature
 * 							appended if successful, empty otherwise
 */
function signMapsRequest( $URL, $signingSecret ){
	$needle = "googleapis.com/";
	$needleOffset = strlen($needle);
	$pos = strpos($URL, $needle);
	if( $pos === false ){
		return "";
	}
	$urlArray = array("-","_");
	$b64Array = array("+","/");
	$pathAndQuery = substr($URL, $pos+$needleOffset-1);

	$base64Secret = str_replace($urlArray, $b64Array, $signingSecret);

	$signature = hash_hmac("sha1", $pathAndQuery, $base64Secret);
	$urlSafeSignature = str_replace($b64Array, $urlArray, $signature);
	
	return $URL . "&signature=" . $urlSafeSignature;
}

?>
