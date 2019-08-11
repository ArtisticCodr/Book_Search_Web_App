<?php

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

function searchKnjige($kriteriumi, $operatori)
{
    $andKriteriumi = array();
    $conn          = getConn();
    $stmt          = null;
    $query         = "SELECT * FROM KnjigeData WHERE ";
    if (sizeof($operatori) == 0) {
        if ($kriteriumi[0]->value == "") {
            //kada korisnik ne unese value
            $stmt = $conn->prepare("SELECT * FROM KnjigeData WHERE prefix=? GROUP BY knjiga");
            $stmt->bind_param("s", $kriteriumi[0]->prefix);
        } else {
            // kada korisnik unese value
            $stmt = $conn->prepare("SELECT * FROM KnjigeData WHERE (prefix=? and value=?) GROUP BY knjiga");
            $stmt->bind_param("ss", $kriteriumi[0]->prefix, $kriteriumi[0]->value);
        }
    } else {
        //vrsimo konkatenaciju kriteriuma i operatora-------------------------------------------------------------------------------------------------------
        $x     = 0;
        $query = "SELECT * FROM KnjigeData WHERE ";
        if ($kriteriumi[0]->value == "") {
            $query .= "(prefix='" . $kriteriumi[0]->prefix . "')";
        } else {
            $query .= "(prefix='" . $kriteriumi[0]->prefix . "' and value='" . $kriteriumi[0]->value . "')";
        }
        for ($i = 0; $i < sizeof($operatori); $i++) {
            if ($operatori[$i]->operator == "or") {
                $query .= " or ";
                if ($kriteriumi[$i + 1]->value == "") {
                    $query .= "(prefix='" . $kriteriumi[$i + 1]->prefix . "')";
                } else {
                    $query .= "(prefix='" . $kriteriumi[$i + 1]->prefix . "' and value='" . $kriteriumi[$i + 1]->value . "')";
                }
            }

            if ($operatori[$i]->operator == "and") {
                $andKriteriumi[$x] = new stdClass();
                $andKriteriumi[$x] = $kriteriumi[$i + 1];
                $x++;
            }

        }
        $query .= " Group by knjiga";

        //echo $query."<br><br><br>";
        $stmt = $conn->prepare($query);
    }
    $stmt->execute();
    $stmt->bind_result($id, $knjiga, $prefix, $labela, $polje, $podpolje, $value);

    $knjige = array();
    $k      = 0;
    while ($stmt->fetch()) {
        $knjige[$k]         = new stdClass();
        $knjige[$k]->knjiga = $knjiga;
        $k++;
    }
    $stmt->close();

    if (sizeof($andKriteriumi) == 0) {
        $jsonStr = json_encode($knjige);
        echo $jsonStr;
    } else {
        $searchResult = array();
        $s            = 0;
        for ($i = 0; $i < sizeof($knjige); $i++) {
            $compatible = true;

            for ($j = 0; $j < sizeof($andKriteriumi); $j++) {
                $query = "SELECT * FROM KnjigeData WHERE knjiga='" . $knjige[$i]->knjiga . "' and ";
                if ($andKriteriumi[$j]->value == "") {
                    $query .= "(prefix='" . $andKriteriumi[$j]->prefix . "')";
                } else {
                    $query .= "(prefix='" . $andKriteriumi[$j]->prefix . "' and value='" . $andKriteriumi[$j]->value . "')";
                }

                $stmt = $conn->prepare($query);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows == 0) {
                    $compatible = false;
                }
                $stmt->close();
            }

            if ($compatible) {
                $searchResult[$s] = new stdClass();
                $searchResult[$s] = $knjige[$i];
                $s++;
            }
        }
        $jsonStr = json_encode($searchResult);
        echo $jsonStr;
    }

    $conn->close();
}

function startSearch($json_upit)
{
    $json_obj = json_decode($json_upit);

    $kriteriumi = array();
    $operatori  = array();

    if (is_array($json_obj)) {
        $x = 0;
        $y = 0;
        for ($i = 0; $i < sizeof($json_obj); $i++) {
            if (property_exists($json_obj[$i], "prefix") and property_exists($json_obj[$i], "value")) {
                $kriteriumi[$x]         = new stdClass();
                $kriteriumi[$x]->prefix = $json_obj[$i]->prefix;
                $kriteriumi[$x]->value  = $json_obj[$i]->value;
                $x++;
            } else {
                if (property_exists($json_obj[$i], "operator")) {
                    $operatori[$y]           = new stdClass();
                    $operatori[$y]->operator = $json_obj[$i]->operator;
                    $y++;
                }
            }

        }

        if (sizeof($kriteriumi) == (sizeof($operatori) + 1)) {
            searchKnjige($kriteriumi, $operatori);
        } else {
            echo "Podaci za pretragu nisu tacno uneseni";
        }
    }
}
