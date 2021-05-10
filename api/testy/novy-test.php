<?php
// API ENDPOINT NA VYTVORENIE NOVEHO TESTU

$surove_prijate_data = file_get_contents('php://input');

include "api-endpoint-include.php";
include "api-frontend/ApiTesty_API_frontend_ucitel.class.php";

$generic_sanity_check =
	ApiTesty_sanityChecker::generic_check__prijate_data($surove_prijate_data) &&
	$generic_sanity_check = ApiTesty_sanityChecker::generic_check__prihlaseny_ucitel();	// na tejto API musi byt prihlaseny vylucne ucitel


if ($generic_sanity_check) {
	$prijate_data = json_decode($surove_prijate_data, true);
	$spravny_format_bool = ApiTesty_sanityChecker::novy_test($prijate_data);
	
	if ($spravny_format_bool) {
		$vystup = ApiTesty_API_frontend_ucitel::vytvor_novy_test($mysqli_api_testy, $_SESSION["userId"], $prijate_data);
		echo json_encode($vystup);
	}
	else {
		echo json_encode( Hlasky__API_T::get_hlaska("API_T__GSC_3") ); // nespravny format dat
	}
}
?>