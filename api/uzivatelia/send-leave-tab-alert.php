<?php
require_once __DIR__ . "/../controllers/LoginController.php";
header('Content-Type: application/json; charset=utf-8');
$controller = new LoginController();
session_start();
if ($_SERVER["REQUEST_METHOD"] == 'GET')
    echo json_encode($controller->sendLeaveTabAlert());
session_commit();
