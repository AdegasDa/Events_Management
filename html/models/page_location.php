<?php

include "../../auth/connection.php";

$location = $pdo->query("SELECT * FROM `locations`;");
$locationArr = $location->fetchAll();

$return = array(
    "locations" => $locationArr,
);


echo json_encode($return); 