<?php

// INCLUDE SUBOR PRE VSETKY API ENDPOINTY

session_start();

include "../../db-login.php";

include "api-backendova-implementacia/hlasky.php";



include "api-backendova-implementacia/db-testov-setup.php";

include "api-backendova-implementacia/ApiTesty_sanityChecker.class.php";
include "api-backendova-implementacia/ApiTesty_jsonParser.class.php";
include "api-backendova-implementacia/ApiTesty_sqlContainer.class.php";

include "api-frontend/ApiTesty_API_frontend_ucitel.class.php";


header('Content-Type: application/json; charset=utf-8');


$surove_prijate_data = file_get_contents('php://input');


$general_sanity_check = true;



if ($surove_prijate_data == "") {
	echo json_encode( Hlasky__API_T::get_hlaska("API_T__GSC_1") ); // neexistuju data testu
	$general_sanity_check = false;
}


else if ( !isset($_SESSION["userId"]) ) {
	echo json_encode( Hlasky__API_T::get_hlaska("API_T__GSC_2") ); // nie je prihlaseny ucitel
	$general_sanity_check = false;
}
?>