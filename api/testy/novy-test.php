<?php
// API ENDPOINT NA VYTVORENIE NOVEHO TESTU

include "api-endpoint-include.php";


if ($general_sanity_check) { // pozri include
	$prijate_data = json_decode($surove_prijate_data, true);
	$spravny_format_bool = ApiTesty_sanityChecker::novy_test($prijate_data);
	
	if ($spravny_format_bool) {
		$vystup = ApiTesty_API_frontend_ucitel::vytvor_novy_test($mysqli_api_testy, $_SESSION["userId"], $prijate_data);
		echo json_encode($vystup);
	}
	else {
		echo json_encode( Hlasky__API_T::get_hlaska("API_T__NT_SC_1") ); // nespravny format dat
	}
}
?>