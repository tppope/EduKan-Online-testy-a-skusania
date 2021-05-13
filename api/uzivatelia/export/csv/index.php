<?php

require_once __DIR__."/../../controllers/LoginController.php";

$controller = new LoginController();

session_start();

include "../../../../db-login.php";
include "../../../testy/api-backendova-implementacia/hlasky.php";
include "../../../testy/api-backendova-implementacia/ApiTesty_sanityChecker.class.php";
include "../../../testy/api-backendova-implementacia/ApiTesty_jsonParser.class.php";
include "../../../testy/api-backendova-implementacia/ApiTesty_sqlContainer.class.php";
include "../../../testy/api-backendova-implementacia/db-testov-setup.php";
include "../../../testy/api-frontend/ApiTesty_API_frontend_ucitel.class.php";

header('Content-Encoding: UTF-8');
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=test.csv");

$kluc = $_SESSION['pisanyTestKluc'];
$ucitel_id = $_SESSION['userId'];

$cely_test = ApiTesty_API_frontend_ucitel::nacitaj_existujuci_test($mysqli_api_testy, $kluc, $ucitel_id);

$output = fopen("php://output", "w");

// KVOLI DIAKRITIKE
fputs($output, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));


fputcsv($output,array("Student_ID","Meno","Priezvisko","Body"),";");

foreach ($cely_test['data_testu']['zoznam_pisucich_studentov'] as $student) {
    $student_id = $student['student_id'];
    $info_about_student = $controller->getStudent($student_id);
    $datum_zaciatku_pisania = $student['datum_zaciatku_pisania'];
    $cas_zaciatku_pisania = $student['cas_zaciatku_pisania'];
    $odpovede_studenta = ApiTesty_API_frontend_ucitel::nacitaj_odpovede($mysqli_api_testy, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania);
    $body = $odpovede_studenta['suhrnnyPocetBodov']['ziskaneBody'];
    $array = array($student_id,$info_about_student['name'],$info_about_student['surname'],$body);
    fputcsv($output,$array,";");
}
fclose($output);

?>
