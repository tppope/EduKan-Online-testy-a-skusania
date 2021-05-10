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
		$vysledok_pokusu = ApiTesty_sqlContainer::zacni_pisat_test($mysqli, $kluc, $student_id);

		if (!$vysledok_pokusu) {
			return Hlasky__API_T::get_hlaska("API_T__VT_C_1");
		}


		// student uz ma tento test rozpisany, vrat mu zostavajuci cas a zoznam doteraz odoslanych odpovedi
		if ($vysledok_pokusu["udalost"] == "rozpisany-test") {
			$vystup = Hlasky__API_T::get_hlaska("API_T__VT_U_2");
			$vystup["odoslane_odpovede"] = ApiTesty_sqlContainer::get_result_zoznam_odpovedi(
				$mysqli, $kluc, $student_id, $vysledok_pokusu["datum_zaciatku_pisania"], $vysledok_pokusu["cas_zaciatku_pisania"]
			);
		}

		else if ($vysledok_pokusu["udalost"] == "student-zacal-pisat-teraz") {
			$vystup = Hlasky__API_T::get_hlaska("API_T__VT_U_1");
		}

		$vystup["zostavajuci_cas"] = $vysledok_pokusu["zostavajuci_cas"];

		$_SESSION["pisanyTestKluc"] = $kluc;
		$_SESSION["testDatumZaciatkuPisania"] = $vysledok_pokusu["datum_zaciatku_pisania"];
		$_SESSION["testCasZaciatkuPisania"] = $vysledok_pokusu["cas_zaciatku_pisania"];

		return $vystup;
	}



	// Zapise, ze tento student zacal pisat test.
	public static function odovzdaj_test(&$mysqli, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania) {
		$uspech_pokusu = ApiTesty_sqlContainer::ukonci_pisanie_testu($mysqli, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania);

		unset($_SESSION["pisanyTestKluc"], $_SESSION["testDatumZaciatkuPisania"], $_SESSION["testCasZaciatkuPisania"]);

		if ($uspech_pokusu) return Hlasky__API_T::get_hlaska("API_T__VT_U_4");
		return Hlasky__API_T::get_hlaska("API_T__VT_C_4");
	}
}