<?php
/**
	 * Get the WEP keys for a given eircom ssid
	 *
	 * @param the SSID of the access point "eircom1234 5678"
	 * @return    array the four WEP keys
	 */
	private function _get_key( $ssid ) {
		
		// remove text, spaces, convert to integer...
		$ssid = intval(str_replace(array('eircom',' '), '', $ssid));
		
		// convert to DEC
		$dec_ssid = base_convert( $ssid , 8 , 10);
		
		// xor with 0x000fcc / 4044 to get the mac address 
		$mac = ($dec_ssid ^ 4044);
		
		// Add this number to 0x010000000 or 16777216 to get serial
		$serial = $mac + 16777216;
		
		// convert numeric $serial to words. i.e '123' => 'OneTwoThree
		$numbers = array('Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine');
		$characters = str_split($serial);
		
		$plaintext = '';
		foreach ($characters as $char) {
			$plaintext .= $numbers["$char"];
		}
		
		// Append some text - "Third Stone From The Sun" by Jimi Hendrix
		// (Reproduced without Jimi Hendrix permission!)
		$lyrics = array("Although your world wonders me, ", 
						"with your superior cackling hen,",
						"Your people I do not understand,",
						"So to you I shall put an end and",
						"You'll never hear surf music aga",
						"Strange beautiful grassy green, ",
						"With your majestic silver seas, ",
						"Your mysterious mountains I wish");
		$appended = '';
		for ( $x=0 ; $x<=7 ; $x++ ) {
			$appended[$x] = $plaintext . $lyrics[$x];
		}
		
		// Perform SHA-1 on each line and concatenate
		$ciphertext= "";
		for ( $x=0 ; $x<=7 ; $x++ ) {
			$ciphertext .= sha1($appended[$x]);  
		}
		
		// Break it up into 26-bit portions to give you the four WEP keys
		$cipherportion = array();
		$cipherportion[] = substr ($ciphertext, 0, 26);
		$cipherportion[] = substr ($ciphertext, 26, 26);
		$cipherportion[] = substr ($ciphertext, 52, 26);
		$cipherportion[] = substr ($ciphertext, 78, 26);
	
		// all done
		return $cipherportion;
		
	}
?>