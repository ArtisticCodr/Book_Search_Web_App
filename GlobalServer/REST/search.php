<?php
include 'searchFunctions.php';
if (isset($_SERVER['HTTP_ORIGIN'])) {
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
   header("Access-Control-Allow-Headers: *, X-Requested-With, Content-Type, Authentication");
   header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
}
session_start();

$json_upit = file_get_contents('php://input');

if (isset($_SESSION["upiti"]) and isset($_SESSION["upitiCounter"])) {
    $upiti = $_SESSION["upiti"];
    $c     = $_SESSION["upitiCounter"];

    $upiti[$c] = new stdClass();
    $upiti[$c] = $json_upit;
    $c++;

    $_SESSION["upiti"]        = $upiti;
    $_SESSION["upitiCounter"] = $c;
} else {
    $upiti = array();
    $c     = 0;

    $upiti[$c] = new stdClass();
    $upiti[$c] = $json_upit;
    $c++;

    $_SESSION["upiti"]        = $upiti;
    $_SESSION["upitiCounter"] = $c;
}
startSearch($json_upit);
