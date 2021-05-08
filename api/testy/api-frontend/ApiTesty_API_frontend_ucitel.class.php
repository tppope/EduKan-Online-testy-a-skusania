<?php

// STATICKY KONTAJNER NA METODY, KTORYMI SA OBSLUHUJE API TVORBY A SPRAVY TESTOV UCITELOM

class ApiTesty_API_frontend_ucitel {
	
	// Aktivuje tento test, ak ho vytvoril prihlaseny ucitel.
	public static function nastav_aktivnost_testu(&$mysqli, $kluc, $ucitel_id, $aktivny) {
		$uspech_pokusu = ApiTesty_sqlContainer::nastav_aktivnost_testu($mysqli, $kluc, $ucitel_id, $aktivny);

		if ($uspech_pokusu) {
			if ($aktivny) return Hlasky__API_T::get_hlaska("API_T__PT_U_2");
			return Hlasky__API_T::get_hlaska("API_T__PT_U_3"); // test bol deaktivovany
		}
		
		return Hlasky__API_T::get_hlaska("API_T__PT_GC");
	}


	// Nacita existujuci test.
	public static function nacitaj_existujuci_test(&$mysqli, $kluc, $ucitel_id) {
		$test_bez_otazok = ApiTesty_sqlContainer::get_result_test_pre_ucitela($mysqli, $kluc, $ucitel_id);
		
		if ($test_bez_otazok == null) {
			// neexistuje kombinacia test - ucitel
			return Hlasky__API_T::get_hlaska("API_T__LT_C_1");
		}
		
		$otazky_v_teste = ApiTesty_sqlContainer::get_result_testove_otazky($mysqli, $test_bez_otazok["kluc_testu"], true); // aj s odpovedami, kluc testu je SQL bezpecny
		
		
		$vystup = Hlasky__API_T::get_hlaska("API_T__LT_U_1");
		
		$vystup["data_testu"] = array(
			"nazov" => $test_bez_otazok["nazov"],
			"casovy_limit" => $test_bez_otazok["casovy_limit"],
			"aktivny" => $test_bez_otazok["aktivny"],
			"otazky" => $otazky_v_teste
		);

		if ($test_bez_otazok["aktivny"]) {
			$vystup["data_testu"]["zoznam_pisucich_studentov"] = ApiTesty_sqlContainer::get_zoznam_studentov_na_teste($mysqli, $test_bez_otazok["kluc_testu"]);
		}
		
		return $vystup;
	}



	// Nacita zoznam vsetkych testov, ktore vytvoril aktualne prihlaseny ucitel.
	public static function nacitaj_zoznam_testov_ucitela(&$mysqli, $ucitel_id) {
		$vystup = Hlasky__API_T::get_hlaska("API_T__PT_U_1");
		$vystup["zoznam_testov"] = ApiTesty_sqlContainer::get_result_vsetky_testy_ucitela($mysqli, $ucitel_id);
		
		return $vystup;
	}


	
	// Vytvori novy test.
	public static function vytvor_novy_test(&$mysqli, $ucitel_id, $json_data) {
		$spracovane_data = ApiTesty_jsonParser::spracuj_json_novy_test($ucitel_id, $json_data);
		$uspech_pokusu = ApiTesty_sqlContainer::vytvor_novy_test($mysqli, $ucitel_id, $spracovane_data);

		if ($uspech_pokusu) {
			// test bol uspesne vlozeny
			$hlaska = Hlasky__API_T::get_hlaska("API_T__NT_U_1");
			$hlaska["kluc_testu"] = $spracovane_data["kluc"];
			
			return $hlaska;
		}

		return Hlasky__API_T::get_hlaska("API_T__NT_C_1");
	}
}
?>