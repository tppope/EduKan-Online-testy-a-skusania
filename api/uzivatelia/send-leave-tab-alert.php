<?php
require_once __DIR__ . "/../controllers/LoginController.php";
header('Content-Type: application/json; charset=utf-8');
$controller = new LoginController();
if ($_SERVER["REQUEST_METHOD"] == 'GET')
    echo json_encode($controller->sendLeaveTabAlert());
