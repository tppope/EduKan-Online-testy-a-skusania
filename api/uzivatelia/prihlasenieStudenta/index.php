<?php
require_once __DIR__ . "/../controllers/LoginController.php";
header('Content-Type: application/json; charset=utf-8');
$controller = new LoginController();
if ($_SERVER["REQUEST_METHOD"] == 'POST')
    echo json_encode($controller->performStudentLogin($_POST["key"],$_POST["id-number"],$_POST["name"],$_POST["surname"]));
