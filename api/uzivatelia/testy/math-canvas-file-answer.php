<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . "/../controllers/MathCanvasController.php";
$controller = new MathCanvasController();


$response = array();
try {

    $key = array_keys($_FILES)[0];
    $file = $_FILES[$key];

    $otazkaId = (explode("-",$key))[1];


if($file["error"] == 4){
    throw new Exception("Nebol nahraný žiaden obrázok");
}
if($file["error"] == 1){
    throw new Exception("Súbor musí byť menší ako 2MB");
}
if($file["error"] == 3){
    throw new Exception("Súbor bol iba čiastočne odoslaný. Skúste to prosím znova");
}
if($file["error"] == 5){
    throw new Exception("Odosielanie bolo neúspešné ERROR 5");
}


    $fileType = $file["type"];
    $type = substr($fileType,strpos($fileType,"/") +1);
    if (move_uploaded_file($file["tmp_name"],"uploadedImages/".$otazkaId."_".$_SESSION["pisanyTestKluc"]."_".$_SESSION["studentId"]."_".$_SESSION["testDatumZaciatkuPisania"]."_".$_SESSION["testCasZaciatkuPisania"].".".$type)){
        $controller->saveAnswer($otazkaId, "inFiles-".$type);
        $response = array(
            "status" => "success",
            "error" => false,
            "message" => $otazkaId
        );
    }
    else{
        throw new Exception("Odosielanie bolo neúspešné");
    }



    echo json_encode($response);



}catch (Exception $e)
{
    $response = array(
        "status" => "failed",
        "error" => true,
        "message" => $e->getMessage()
    );
    echo json_encode($response);
}

session_commit();



