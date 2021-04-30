<?php
session_start();
try {
    unset($_SESSION["userId"]);
    $response = array(
        "error" => false,
        "status" => "success",
    );
    echo json_encode($response);
}
catch (Exception $exception){
    $response = array(
        "error" => true,
        "status" => "failed",
        "errorMessage"=>$exception->getMessage(),
    );
    echo json_encode($response);
}
