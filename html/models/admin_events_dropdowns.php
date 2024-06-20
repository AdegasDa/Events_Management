<?php include "../../auth/connection.php";

include "../class/class_events.php";

$class_events = new class_events;

$return['categories'] = $class_events->categories($pdo);
$return['locations'] = $class_events->locations($pdo);
$return['artists'] = $class_events->artists($pdo, $_POST['id']);
$return['subscribers'] = $class_events->subscribers($pdo, $_POST['id']);

echo json_encode($return);