<?php

include "../../auth/connection.php";

// Correctly extract the ID from POST data
$id = str_replace('id=', '', $_POST['id']);

// Correct SQL query for selecting the location
$location = $pdo->query("SELECT * FROM `locations` WHERE `locations`.`location_id` = '$id';");
$locationArr = $location->fetchAll();

if ($locationArr)
{
    // Correct SQL query for selecting random events
    $event = $pdo->query("SELECT * FROM `events` 
        INNER JOIN `category` ON `events`.`event_category_id` = `category`.`category_id` 
        INNER JOIN `locations` ON `events`.`event_location_id` = `locations`.`location_id` 
        WHERE `event_location_id` = '$id' 
        AND `events`.`event_status` = 1 
        ORDER BY RAND();");

    // Correct SQL query for selecting events of the week
    $event_week = $pdo->query("SELECT * FROM `events` 
        INNER JOIN `category` ON `events`.`event_category_id` = `category`.`category_id` 
        INNER JOIN `locations` ON `events`.`event_location_id` = `locations`.`location_id` 
        WHERE DATE(event_date) BETWEEN CURDATE() - INTERVAL WEEKDAY(CURDATE()) DAY AND CURDATE() + INTERVAL 6 - WEEKDAY(CURDATE()) DAY 
        AND `events`.`event_status` = 1 
        AND DATE(event_date) >= CURDATE() 
        AND `event_location_id` = '$id' 
        LIMIT 4;");

    // Fetch data into arrays
    $eventArr = $event->fetchAll();
    $event_weekArr = $event_week->fetchAll();

    // Retrieve country from location array
    $country = $locationArr[0]["location_country"];

    // Correct SQL query for selecting random artists from the same country
    $artist = $pdo->query("SELECT * FROM `artists` 
        INNER JOIN `artists_category` ON `artists_category`.`artist_id` = `artists`.`artist_id` 
        INNER JOIN `category` ON `category`.`category_id` = `artists_category`.`category_id` 
        INNER JOIN `locations` ON `locations`.`location_id` = `artists`.`artist_location_id` 
        WHERE `artists`.`artist_country` = '$country' 
        AND `artists`.`artist_status` = 1 
        ORDER BY RAND();");

    $artistsArr = $artist->fetchAll();

    // Prepare return array
    $return = array(
        "code" => 1,
        "event" => $eventArr,
        "event_week" => $event_weekArr,
        "artist" => $artistsArr,
        "location" => $locationArr
    );    
}
else 
{
    $return = ["code" => 0, "error" => "There are no such location with this id"];
}

echo json_encode($return);