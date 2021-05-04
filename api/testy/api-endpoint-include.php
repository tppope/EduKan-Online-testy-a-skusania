<?php

// INCLUDE SUBOR PRE VSETKY API ENDPOINTY

session_start();

include "../../db-login.php";

include "api-backendova-implementacia/hlasky.php";



include "api-backendova-implementacia/db-testov-setup.php";

include "api-backendova-implementacia/ApiTesty_sanityChecker.class.php";
include "api-backendova-implementacia/ApiTesty_jsonParser.class.php";
include "api-backendova-implementacia/ApiTesty_sqlContainer.class.php";


header('Content-Type: application/json; charset=utf-8');


$generic_sanity_check = ApiTesty_sanityChecker::generic_check__prijate_data($surove_prijate_data);
?>