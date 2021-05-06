<?php
// API ENDPOINT NA NACITANIE TESTU

$surove_prijate_data = $_GET;

include "api-endpoint-include.php";
include "api-frontend/ApiTesty_API_frontend_ucitel.class.php";
include "api-frontend/ApiTesty_API_frontend_student.class.php";


if ($generic_sanity_check) { // na tejto API musi byt prihlaseny bud student alebo ucitel
	$generic_sanity_check = ApiTesty_sanityChecker::generic_check__prihlaseny_ucitel_alebo_student();
}



if ($generic_sanity_check) {
	if ( ApiTesty_sanityChecker::nacitaj_test_ucitel($surove_prijate_data) ) {
		$vystup = ApiTesty_API_frontend_ucitel::nacitaj_existujuci_test($mysqli_api_testy, $surove_prijate_data["kluc"], $_SESSION["userId"]);
		echo json_encode($vystup);
	}

	elseif ( ApiTesty_sanityChecker::nacitaj_test_student($surove_prijate_data) ) {
		$vystup = ApiTesty_API_frontend_student::nacitaj_existujuci_test($mysqli_api_testy, $surove_prijate_data["kluc"]);
		echo json_encode($vystup);
	}

	else {
		echo json_encode( Hlasky__API_T::get_hlaska("API_T__GSC_3") ); // nespravny format dat
	}
}
?>