<?php
include 'searchFunctions.php';
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Headers: *, X-Requested-With, Content-Type, Authentication");
    header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
}

session_start();
if (isset($_SESSION["upiti"]) and isset($_SESSION["upitiCounter"]) and isset($_GET["number"])) {
    $upiti  = $_SESSION["upiti"];
    $c      = $_SESSION["upitiCounter"];
    $number = $_GET["number"];

    if ($number < sizeof($upiti)) {
        echo $upiti[$number];
    } else {
        echo "false";
    }

} else {
    if (isset($_SESSION["upiti"]) and isset($_SESSION["upitiCounter"])) {
        echo $_SESSION["upitiCounter"];

    } else {
        echo "false";
    }
}
