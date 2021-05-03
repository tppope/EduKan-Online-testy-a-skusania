<?php
require_once __DIR__ . "/../controllers/RegistrationController.php";
header('Content-Type: application/json; charset=utf-8');
$controller = new RegistrationController();
echo json_encode($controller->performRegistration($_POST["name"],$_POST["surname"],$_POST["email"],$_POST["password"]));
