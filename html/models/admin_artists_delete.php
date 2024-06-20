<?php include "../../auth/connection.php";

include "../class/class_artists.php";

$artist_id = $_POST['id'];

$class_artists = new class_artists;
$delete_artist = $class_artists->delete_artist($pdo, $artist_id);

if ( $delete_artist )
{
    $return[] = [ "code" => 2, "error" => $delete_artist ];
}
else
{
    $return[] = [ "code" => 0, "error" => "Artist successfully deleted!" ];
}

echo json_encode($return);