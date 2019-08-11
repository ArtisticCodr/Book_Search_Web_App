<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
   header("Access-Control-Allow-Headers: *, X-Requested-With, Content-Type, Authentication");
   header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
}

function getConn()
{
    $servername = "192.168.0.111";
    $username   = "admin";
    $password   = "sljiva3388";
    $dbname     = "SkriptBiblioteka";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    mysqli_set_charset($conn, "utf8");

    return $conn;
}

if (isset($_GET['knjiga'])) {
    $conn            = getConn();
    $book            = $_GET['knjiga'];
    $result          = new stdClass();
    $result->unimarc = "";

    $query = "SELECT * FROM PoljaData WHERE knjiga='" . $book . "'";
    $stmt  = $conn->prepare($query);
    $stmt->execute();
    $stmt->bind_result($id, $polje, $knjiga, $podpolja, $indikator);

    while ($stmt->fetch()) {
        $result->unimarc .= $polje . " " . $indikator . " " . $podpolja . "<br>";
    }

    $jsonStr = json_encode($result);
    echo $jsonStr;

    $stmt->close();
    $conn->close();
} else {
    echo "Error";
}
