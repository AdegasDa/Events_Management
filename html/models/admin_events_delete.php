<?php include "../../auth/connection.php";

include "../class/class_events.php";

$event_id = $_POST['event_id'];

# APAGAR SE NÃO TIVER A SER UTILIZADO

$class_events = new class_events;
$delete_event = $class_events->delete_event($pdo, $event_id);

if ( $delete_event )
{
    $return[] = [ "code" => 2, "error" => $delete_event ];
}
else
{
    $return[] = [ "code" => 0, "error" => "Event successfully deleted!" ];
}

echo json_encode($return);