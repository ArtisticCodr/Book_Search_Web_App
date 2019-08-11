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
    $conn           = getConn();
    $book           = $_GET['knjiga'];
    $result         = new stdClass();
    $result->autori = array();
    $a              = 0;

    $query = "SELECT * FROM KnjigeData WHERE knjiga='" . $book . "' and (prefix='TI' or prefix='AU' or prefix='PU' or prefix='PY' or prefix='PP')";
    $stmt  = $conn->prepare($query);
    $stmt->execute();
    $stmt->bind_result($id, $knjiga, $prefix, $labela, $polje, $podpolje, $value);

    while ($stmt->fetch()) {
        if ($prefix == "AU") {
            $result->autori[$a]        = new stdClass();
            $result->autori[$a]->autor = $value;
            $a++;
        }
        if ($prefix == "TI") {
            $result->naslov = $value;
        }
        if ($prefix == "PU") {
            $result->izdavac = $value;
        }
        if ($prefix == "PY") {
            $result->godinaIzdavanja = $value;
        }
        if ($prefix == "PP") {
            $result->mestoIzdavanja = $value;
        }
    }

    $jsonStr = json_encode($result);
    echo $jsonStr;

    $stmt->close();
    $conn->close();
} else {
    echo "Error";
}
