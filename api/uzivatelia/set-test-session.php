<?php
header('Content-Type: application/json; charset=utf-8');
try {
    session_start();
    switch ($_GET["akcia"]){
        case "nastav": $_SESSION["pisanyTestKluc"] = $_GET["klucTestu"]; break;
        case "vymaz": unset($_SESSION['pisanyTestKluc']); break;
    }
    echo json_encode(array(
        "error" => false,
        "status" => "success",
    ));
}
catch (Exception $exception){
    echo json_encode(array(
        "error" => true,
        "status" => "failed",
    ));
}

session_commit();
