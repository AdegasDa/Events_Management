<?php

include "../../auth/connection.php";

$event_id = $_POST['event_id'];
$user_username = $_SESSION['user'];

$stmt = $pdo->prepare("SELECT `event_id`, `event_subscription_limit` FROM `events` WHERE `event_id` = :event_id");
$stmt->bindValue(':event_id', $event_id, PDO::PARAM_INT);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

try {
    if ($event) {
        $current_date = new DateTime();
        $event_subscription_limit = new DateTime($event['event_subscription_limit']);
        
        $subscription_passed = $current_date > $event_subscription_limit;
        
        if (!$subscription_passed) {
            $stmt2 = $pdo->prepare("DELETE `events_users` FROM `events_users`
                INNER JOIN `events` ON `events`.`event_id` = `events_users`.`event_id`
                INNER JOIN `users` ON `users`.`user_id` = `events_users`.`user_id`
                WHERE `events_users`.`event_id` = :event_id
                AND `users`.`user_username` = :user_username");
            $stmt2->bindValue(':event_id', $event_id, PDO::PARAM_INT);
            $stmt2->bindValue(':user_username', $user_username, PDO::PARAM_STR);
            $stmt2->execute();

            if ($stmt2->rowCount() > 0) {
                $return = ["code" => 1, "error" => "Event association deleted successfully."];
            } else {
                $return = ["code" => 0, "error" => "Event association could not be deleted."];
            }
        } else {
            $return = ["code" => 0, "error" => "You cannot unsubscribe from the event because the event registration date has passed"];
        }
    } else {
        $return = ["code" => 0, "error" => "Event not found."];
    }    
    
    echo json_encode($return);
} catch (Exception $ex) {
    $return = ["code" => 0, "error" => "Error."];
    
    echo json_encode($return);
}




