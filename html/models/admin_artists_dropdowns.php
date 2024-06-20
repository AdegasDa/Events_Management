<?php include "../../auth/connection.php";

include "../class/class_artists.php";

$class_artists = new class_artists;

$return['categories'] = $class_artists->categories($pdo);
$return['locations'] = $class_artists->locations($pdo);

echo json_encode($return);