<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
   header("Access-Control-Allow-Credentials: true");
   header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
   header("Access-Control-Allow-Headers: *, X-Requested-With, Content-Type, Authentication");
   header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
}
session_start();

function getPrefixes()
{
    $prefixi    = array();
    $servername = "192.168.0.111";
    $username   = "admin";
    $password   = "sljiva3388";
    $dbname     = "SkriptBiblioteka";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    mysqli_set_charset($conn, "utf8");

    $sql    = "SELECT * FROM prefixi GROUP BY label ORDER BY label";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $prefixi[] = array(
                "id"     => $row['id'],
                "prefix" => $row['prefix'],
                "label"  => $row['label'],
            );
        }
    } else {
        echo "0 results";
    }
    $conn->close();

    return $prefixi;
}

$prefixi = null;
if (isset($_SESSION["prefixes"])) {
    $prefixi = $_SESSION["prefixes"];

} else {
    $prefixi              = getPrefixes();
    $_SESSION["prefixes"] = $prefixi;
}

$jsonStr = json_encode($prefixi);
echo $jsonStr;
