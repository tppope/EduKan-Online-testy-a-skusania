<?php
require_once __DIR__ . "/../controllers/LoginController.php";
header('Content-Type: application/json; charset=utf-8');
$controller = new LoginController();
if (isset($_GET["studentId"]))
    echo json_encode($controller->getStudent($_GET["studentId"]));
else if (isset($_GET["akcia"])){
    session_start();
    echo json_encode($controller->getStudent($_SESSION["studentId"]));
    session_commit();
}
else
    echo json_encode(array(
        "error"=>true,
        "status"=>"failed",
        "message"=>"Id not set"
        ));
