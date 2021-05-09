<?php

// STATICKY KONTAJNER NA METODY, KTORYMI SA OBSLUHUJE API VYPRACOVANIA TESTOV A PRACE S TESTAMI STUDENTOM

class ApiTesty_API_frontend_student {
	
	// Nacita existujuci test (upravena varianta metody ApiTesty_API_frontend_ucitel::nacitaj_existujuci_test).
	public static function nacitaj_existujuci_test(&$mysqli, $kluc) {
		$test_bez_otazok = ApiTesty_sqlContainer::get_result_test_pre_studenta($mysqli, $kluc);
		
		if ($test_bez_otazok == null) {
			// neexistuje kombinacia test - student
			return Hlasky__API_T::get_hlaska("API_T__LT_C_2");
		}
		
		$otazky_v_teste = ApiTesty_sqlContainer::get_result_testove_otazky($mysqli, $test_bez_otazok["kluc_testu"]); // bez odpovedi, kluc testu je SQL bezpecny
		
		
		$vystup = Hlasky__API_T::get_hlaska("API_T__LT_U_1");
		
		$vystup["data_testu"] = array(
			"nazov" => $test_bez_otazok["nazov"],
			"casovy_limit" => $test_bez_otazok["casovy_limit"],
			"otazky" => $otazky_v_teste
		);
		
		return $vystup;
	}


	// Zapise, ze tento student zacal pisat test.
	public static function zacni_pisat(&$mysqli, $kluc, $student_id) {
		$zostavajuci_cas = ApiTesty_sqlContainer::zacni_pisat_test($mysqli, $kluc, $student_id);

		// student uz ma tento test rozpisany, vrat mu zostavajuci cas a zoznam doteraz odoslanych odpovedi
		if (isset($zostavajuci_cas["udalost"]) && $zostavajuci_cas["udalost"] == "rozpisany-test") {
			$vystup = Hlasky__API_T::get_hlaska("API_T__VT_U_2");
			$vystup["zostavajuci_cas"] = $zostavajuci_cas["zostavajuci_cas"];
			$vystup["odoslane_odpovede"] = array(); // TODO: dokoncit
			return $vystup;
		}

		// inak to nie je array
		if ($zostavajuci_cas > 0) {
			$vystup = Hlasky__API_T::get_hlaska("API_T__VT_U_1");
			$vystup["zostavajuci_cas"] = $zostavajuci_cas;
			return $vystup;
		}
		return Hlasky__API_T::get_hlaska("API_T__VT_C_1");
	}
}