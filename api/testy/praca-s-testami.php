<?php
// API ENDPOINT NA PRACU S TESTAMI Z POHLADU UCITELA

$surove_prijate_data = $_GET;

include "api-endpoint-include.php";
include "api-frontend/ApiTesty_API_frontend_ucitel.class.php";

$generic_sanity_check =
	ApiTesty_sanityChecker::generic_check__prijate_data($surove_prijate_data) &&
	$generic_sanity_check = ApiTesty_sanityChecker::generic_check__prihlaseny_ucitel();	// na tejto API musi byt prihlaseny vylucne ucitel


if ($generic_sanity_check) {
	// sanity checker v tychto vetvach rovno kontroluje, ktora poziadavka sa ma vykonat, preto je to rozvetvene cez if elseif

	if (ApiTesty_sanityChecker::praca_s_testami_ucitel__nacitaj_vsetky_testy($surove_prijate_data)) {
		$vystup = ApiTesty_API_frontend_ucitel::nacitaj_zoznam_testov_ucitela($mysqli_api_testy, $_SESSION["userId"]);
		echo json_encode($vystup);
	}

	elseif (ApiTesty_sanityChecker::praca_s_testami_ucitel__aktivuj_test($surove_prijate_data)) {
		$vystup = ApiTesty_API_frontend_ucitel::nastav_aktivnost_testu($mysqli_api_testy, $surove_prijate_data["kluc"], $_SESSION["userId"], 1);
		echo json_encode($vystup);
	}

	elseif (ApiTesty_sanityChecker::praca_s_testami_ucitel__deaktivuj_test($surove_prijate_data)) {
		$vystup = ApiTesty_API_frontend_ucitel::nastav_aktivnost_testu($mysqli_api_testy, $surove_prijate_data["kluc"], $_SESSION["userId"], 0);
		echo json_encode($vystup);
	}

	elseif (ApiTesty_sanityChecker::praca_s_testami_ucitel__nacitaj_vysledky_testu($surove_prijate_data)) {
		$vystup = ApiTesty_API_frontend_ucitel::nacitaj_odpovede(
			$mysqli_api_testy, $_SESSION["pisanyTestKluc"],
            $_SESSION["studentId"], $_SESSION["datumZaciatkuPisania"], $_SESSION["casZaciatkuPisania"]
		);
		
		echo json_encode($vystup);
	}

	else {
		echo json_encode( Hlasky__API_T::get_hlaska("API_T__GSC_3") ); // nespravny format dat
	}
}
?>
