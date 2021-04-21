<?php

$mysqli_api_testy = new mysqli($db_host, $db_user, $db_password, "wt_skuskove_zadanie_databaza_testov");
$mysqli_api_testy->set_charset("UTF-8");
$mysqli_api_testy->autocommit(false);

?>