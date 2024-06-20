<?php

include "../../auth/connection.php";
include "../class/class_events.php";

$artists = !empty($_POST['subscribers']) ? explode(",", $_POST['subscribers']) : [];

$class_events = new class_events;
$update_event_subs = $class_events->update_event_subs($pdo, $_POST['id'], $artists);

if ( $update_event_subs )
{
    $return[] = [ "code" => 2, "error" => $update_event_subs ];
}
else
{
    $return[] = [ "code" => 0, "error" => "Subscribers list saved successfully!" ];
}

echo json_encode($return);