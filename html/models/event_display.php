<?php

include "../../auth/connection.php";

$id = str_replace('id=', '', $_POST['id']);

$eventStmt = $pdo->prepare("SELECT * FROM `events` 
    INNER JOIN `category` ON `events`.`event_category_id` = `category`.`category_id`
    WHERE `event_id` = :id
    AND `events`.`event_status` = 1;");
$eventStmt->execute([':id' => $id]);
$eventArr = $eventStmt->fetchAll();

if ($eventArr)
{
    // Use prepared statements to prevent SQL injection
    $registeredStmt = $pdo->prepare("SELECT COUNT(*) AS registered
        FROM `events`
        INNER JOIN `events_users` ON `events_users`.`event_id` = `events`.`event_id`
        WHERE `events`.`event_id` = :id
        AND `events`.`event_status` = 1;");
    $registeredStmt->execute([':id' => $id]);
    $registeredArr = $registeredStmt->fetchAll();


    $artistStmt = $pdo->prepare("SELECT * FROM `artists_events`
        INNER JOIN `artists` ON `artists_events`.`artist_id` = `artists`.`artist_id`
        INNER JOIN `artists_category` ON `artists_category`.`artist_id` = `artists`.`artist_id`
        INNER JOIN `category` ON `category`.`category_id` = `artists_category`.`category_id`
        INNER JOIN `locations` ON `locations`.`location_id` = `artists`.`artist_location_id`
        WHERE `artists_events`.`event_id` = :id
        AND `artists`.`artist_status` = 1;");
    $artistStmt->execute([':id' => $id]);
    $artistsArr = $artistStmt->fetchAll();

    $location_id = $eventArr[0]["event_location_id"];
    $locationStmt = $pdo->prepare("SELECT * FROM `locations` WHERE `locations`.`location_id` = :location_id;");
    $locationStmt->execute([':location_id' => $location_id]);
    $locationArr = $locationStmt->fetchAll();

    $category_name = $eventArr[0]["category_name"];
    $similarStmt = $pdo->prepare("SELECT * FROM `events`
        INNER JOIN `category` ON `events`.`event_category_id` = `category`.`category_id`
        INNER JOIN `locations` ON `events`.`event_location_id` = `locations`.`location_id`
        WHERE `category`.`category_name` = :category_name
        AND `event_id` != :id
        AND `events`.`event_status` = 1 
        ORDER  BY RAND()
        LIMIT 4;");
    $similarStmt->execute([':category_name' => $category_name, ':id' => $id]);
    $similarArr = $similarStmt->fetchAll();
    
    $check_subscription_date = $pdo->prepare("SELECT `event_subscription_limit` FROM `events` WHERE `event_id` = :id");
    $check_subscription_date->execute([':id' => $id]);
    $subscription_date = $check_subscription_date->fetchColumn();

    $currentDateTime = new DateTime();
    $event_subscription_limit = new DateTime($subscription_date);
    $subscription_passed = $currentDateTime > $event_subscription_limit;

    // Prepare the return array
    $return = [
        "subscription_passed" => $subscription_passed,
        "code" => 1,
        "event" => $eventArr,
        "artist" => $artistsArr,
        "location" => $locationArr,
        "similar_event" => $similarArr,
        "registered" => $registeredArr,
    ];
}
else
{
    $return = ["code" => 0, "error" => "There are no such event with this id"];
}


echo json_encode($return);
