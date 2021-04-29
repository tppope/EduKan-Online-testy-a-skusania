<?php
// API ENDPOINT NA VYTVORENIE NOVEHO TESTU

include "../../db-login.php";

include "api-backendova-implementacia/db-testov-setup.php";

include "api-backendova-implementacia/ApiTesty_jsonParser.class.php";
include "api-backendova-implementacia/ApiTesty_sqlContainer.class.php";

include "api-frontend/ApiTesty_API_frontend_ucitel.class.php";


header('Content-Type: application/json; charset=utf-8');


$prijate_data = json_decode(file_get_contents('php://input'), true);




// id ucitela, ktory vytvoril test, je docasne 0 (neskor nahradit skutocnym id)
$ucitel_id = 0;




$vystup = ApiTesty_API_frontend_ucitel::vytvor_novy_test($mysqli_api_testy, $ucitel_id, $prijate_data);


echo json_encode($vystup);
?>