<?php
header('Content-Type: application/json; charset=utf-8');
$data = json_decode(file_get_contents("php://input"));
session_start();
require_once __DIR__ . "/../controllers/MathCanvasController.php";
$controller = new MathCanvasController();

if ($data->typ_odpovede == "matematicka" || $data->typ_odpovede == "canvas"){
    echo json_encode($controller->saveAnswer($data->otazka_id,$data->odpoved));
}
else{
    echo json_encode(array(
        "status"=>"failed",
        "error"=> true,
        "errorMessage"=>"Zle vstupne data"
    ));
}
session_commit();
