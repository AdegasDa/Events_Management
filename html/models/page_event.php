<?php

include "../../auth/connection.php";

$id = str_replace('id=', '', $_POST['id']);

$category_event = $pdo->query("SELECT `category_name` FROM `category` "
        . "INNER JOIN `events` ON `events`.`event_category_id` = `category`.`category_id` "
        . "WHERE `category`.`category_id` = '$id'"
        . "AND `events`.`event_status` = 1;");
$category_eventArr = $category_event->fetch();

if ($category_eventArr)
{
    $events = $pdo->query("SELECT * FROM `events` "
            . "INNER JOIN `category` ON `events`.`event_category_id` = `category`.`category_id` "
            . "INNER JOIN `locations` ON `events`.`event_location_id` = `locations`.`location_id`"
            . "WHERE `category`.`category_id` = '$id'"
            . "AND `events`.`event_status` = 1;");

    $artists = $pdo->query("SELECT `category_name`, `location_country`, `artist_name` FROM `artists_events` "
            . "INNER JOIN `artists` ON `artists_events`.`artist_id` = `artists`.`artist_id` "
            . "INNER JOIN `artists_category` ON `artists_category`.`artist_id` = `artists`.`artist_id` "
            . "INNER JOIN `category` ON `category`.`category_id` = `artists_category`.`category_id` "
            . "INNER JOIN `events` ON `events`.`event_id` = `artists_events`.`event_id`"
            . "INNER JOIN `locations` ON `locations`.`location_id` = `artists`.`artist_location_id`"
            . "WHERE `events`.`event_category_id` = '$id'"
            . "AND `events`.`event_status` = 1;");

    $tag_event = $pdo->query("SELECT `category_name` FROM `category` "
            . "INNER JOIN `events` ON `events`.`event_category_id` = `category`.`category_id` "
            . "WHERE `category`.`category_id` = '$id'"
            . "AND `events`.`event_status` = 1;");

    $tags = array(
        "event" => $tag_event->fetch(),
    );

    $return = array( 
        "code" => 1,
        "category" => $category_eventArr,
        "events" => $events->fetchAll(),
        "artist" => $artists->fetchAll(),
        "tags" => $tags,
    );
}
else
{
    $return = ["code" => 0, "error" => "There are no category with this id"];
}

echo json_encode($return);