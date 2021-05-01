<?php

// STATICKY KONTAJNER NA METODY, KTORYMI SA OBSLUHUJE API TVORBY A SPRAVY TESTOV UCITELOM

class ApiTesty_API_frontend_ucitel {
	
	// Nacita existujuci test.
	public static function nacitaj_existujuci_test(&$mysqli, $test_id, $ucitel_id) {
		$test_bez_otazok = ApiTesty_sqlContainer::get_result_test_pre_ucitela($mysqli, $test_id, $ucitel_id);
		
		if ($test_bez_otazok == null) {
			// neexistuje kombinacia test - ucitel
			return Hlasky__API_T::get_hlaska("API_T__LT_C_1");
		}
		
		$otazky_v_teste = ApiTesty_sqlContainer::get_result_testove_otazky($mysqli, $test_bez_otazok["id"], true); // aj s odpovedami
		
		
		$vystup = Hlasky__API_T::get_hlaska("API_T__LT_U_1");
		
		$vystup["data_testu"] = array(
			"nazov" => $test_bez_otazok["nazov"],
			"casovy_limit" => $test_bez_otazok["casovy_limit"],
			"aktivny" => $test_bez_otazok["aktivny"],
			"otazky" => $otazky_v_teste
		);
		
		return $vystup;
	}


	// Nacita zoznam vsetkych testov, ktore vytvoril aktualne prihlaseny ucitel.
	public static function nacitaj_zoznam_testov_ucitela(&$mysqli, $ucitel_id) {
		$vystup = Hlasky__API_T::get_hlaska("API_T__PT_U_1");
		$vystup["zoznam_testov"] = ApiTesty_sqlContainer::get_result_vsetky_testy_ucitela($mysqli, $ucitel_id);
		
		return $vystup;
	}
	
	
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