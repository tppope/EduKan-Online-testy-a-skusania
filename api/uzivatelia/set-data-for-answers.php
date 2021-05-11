<?php
header('Content-Type: application/json; charset=utf-8');
try {
    session_start();
    if (isset($_GET["akcia"])){
        if ($_GET["akcia"] =='nastav'){
            $_SESSION["studentId"] = $_GET["studentId"];
            $_SESSION["datumZaciatkuPisania"] = $_GET["datumZaciatkuPisania"];
            $_SESSION["casZaciatkuPisania"] = $_GET["casZaciatkuPisania"];
        }
        elseif ($_GET["akcia"] =='vymaz'){
            unset($_SESSION["studentId"]);
            unset($_SESSION["datumZaciatkuPisania"]);
            unset($_SESSION["casZaciatkuPisania"]);
        }
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
