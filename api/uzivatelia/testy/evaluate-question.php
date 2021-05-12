<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . "/../controllers/MathCanvasController.php";
$controller = new MathCanvasController();

if ($_GET["vyhodnotenie"] == "spravne"){
    echo json_encode($controller->markAsRight(true, $_GET["otazkaId"]));
}
elseif($_GET["vyhodnotenie"] == "nespravne"){
    echo json_encode($controller->markAsRight(false, $_GET["otazkaId"]));
}
else{
    echo json_encode(array(
        "status" => "failed",
        "error" => true,
        "message" => "Zle zadane parametre"
    ));
}

session_commit();
