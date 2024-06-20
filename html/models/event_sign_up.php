<?php

include "../../auth/connection.php";

$name = $_SESSION['user'];
$event_id = $_POST['event_id'];

$stmt = $pdo->prepare("SELECT `user_id`, `user_username` FROM `users` WHERE `user_username` LIKE :name");
$stmt->execute([':name' => $name]);
$id_userArr = $stmt->fetch();

if ($id_userArr) {
    $id = $id_userArr['user_id'];

    $check_stmt = $pdo->prepare("SELECT * FROM `events_users` WHERE `event_id` = :event_id AND `user_id` = :user_id");
    $check_stmt->execute([':event_id' => $event_id, ':user_id' => $id]);

    if ($check_stmt->rowCount() > 0) {
        $return[] = ["code" => 0, "error" => "Already registered."];
    } else {
        $insert_stmt = $pdo->prepare("INSERT INTO `events_users` (`user_id`, `event_id`) VALUES (:user_id, :event_id)");
        $insert_stmt->execute([':user_id' => $id, ':event_id' => $event_id]);

        $return[] = ["code" => 1, "error" => "Successfully registered."];
    }
} else {
    $return[] = ["code" => 2, "error" => "User not found."];
}

echo json_encode($return);

