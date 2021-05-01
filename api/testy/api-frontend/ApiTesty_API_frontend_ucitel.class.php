<?php

// STATICKY KONTAJNER NA METODY, KTORYMI SA OBSLUHUJE API TVORBY A SPRAVY TESTOV UCITELOM

class ApiTesty_API_frontend_ucitel {
	
	// Vytvori novy test.
	public static function vytvor_novy_test(&$mysqli, $ucitel_id, $prijate_data) {
		$bool_test_aktivny = 0;

		$insert_vsetky_otazky = array();
		$insert_otazky_typy = array(
			1 => array(),
			2 => array(),
			3 => array()
		);
		$insert_pary = array();


		ApiTesty_jsonParser::spracuj_json_novy_test(
			$mysqli,
			$prijate_data,
			$bool_test_aktivny,
			$insert_vsetky_otazky,
			$insert_otazky_typy,
			$insert_pary
		);
		
		
		$sql = ApiTesty_sqlContainer::get_sql_vytvor_novy_test(
			$ucitel_id,
			$mysqli->escape_string($prijate_data["nazov"]),
			$mysqli->escape_string($prijate_data["casovy_limit"]),
			$bool_test_aktivny,
			$insert_vsetky_otazky,
			$insert_otazky_typy,
			$insert_pary
		);

		
		if ( $mysqli->multi_query($sql) ) {
			$novy_test_id = 0;
			$check_tabulky_existuju_naplnene = 0;

			do {
				if ($result = $mysqli->store_result()) {
					foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
						if ( isset($row["novy_test_id"]) ) {
							$novy_test_id = intval($row["novy_test_id"]);
						}
						
						if ( isset($row["check_result"]) ) {
							$check_tabulky_existuju_naplnene = $row["check_result"];
						}
					}
				}
			} while ($mysqli->next_result());


			if ($novy_test_id != 0 && $check_tabulky_existuju_naplnene != 0) {
				// test bol uspesne vlozeny
				$hlaska = Hlasky__API_T::get_hlaska("API_T__NT_U_1");
				$hlaska["id_testu"] = $novy_test_id;
				
				return $hlaska;
			}


			// nepodarilo sa uspesne ulozit vsetky data (nezbehla transakcia, ktora vytvarala tabulky, resp. transakcia, ich naplnala, zmaz cely test so vsetkymi tabulkami
			$sql_vymaz = ApiTesty_sqlContainer::get_sql_zmaz_test($id);
			$mysqli->multi_query($sql_vymaz);
		}
		
		return Hlasky__API_T::get_hlaska("API_T__NT_C_1");
	}
}
?>