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
}