<?php include "../../auth/connection.php";

include "../class/class_users.php";

$user_id = $_POST['user_id'];

$class_users = new class_users;
$active_user = $class_users->active_user($pdo, $user_id);

if ( $active_user )
{
    $return[] = [ "code" => 2, "error" => $active_user ];
}
else
{
    $return[] = [ "code" => 0, "error" => "User successfully activated!" ];
}

echo json_encode($return);