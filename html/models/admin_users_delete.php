<?php include "../../auth/connection.php";

include "../class/class_users.php";

$user_id = $_POST['user_id'];

$class_users = new class_users;
$delete_user = $class_users->delete_user($pdo, $user_id);

if ( $delete_user )
{
    $return[] = [ "code" => 2, "error" => $delete_user ];
}
else
{
    $return[] = [ "code" => 0, "error" => "User successfully deleted!" ];
}

echo json_encode($return);