<?php
header('Content-Type: application/json; charset=utf-8');
try {
    session_start();
    $fileName = "";
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
        elseif ($_GET["akcia"] =='dostan'){
            $fileName = $_SESSION["pisanyTestKluc"]."_".$_SESSION["studentId"]."_".$_SESSION["datumZaciatkuPisania"]."_".$_SESSION["casZaciatkuPisania"];
        }
    }

    echo json_encode(array(
        "error" => false,
        "status" => "success",
        "fileName"=>$fileName
    ));

}
catch (Exception $exception){
    echo json_encode(array(
        "error" => true,
        "status" => "failed",
    ));
}
session_commit();
