<?php
require_once __DIR__ . "/../controllers/LoginController.php";
header('Content-Type: application/json; charset=utf-8');
$controller = new LoginController();
if (isset($_GET["studentId"]))
    echo json_encode($controller->getStudent($_GET["studentId"]));
else
    echo json_encode(array(
        "error"=>true,
        "status"=>"failed",
        "message"=>"Id not set"
        ));
