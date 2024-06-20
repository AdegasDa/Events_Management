<?php

include "../../auth/connection.php";

function fetchEventsByCategory($pdo, $categoryName) {
    $stmt = $pdo->prepare("SELECT * FROM `events`
                        INNER JOIN `category` ON `events`.`event_category_id` = `category`.`category_id`
                        INNER JOIN `locations` ON `events`.`event_location_id` = `locations`.`location_id`
                        WHERE `category_name` = :categoryName 
                        AND `events`.`event_status` = 1 
                        ORDER BY RAND()
                        LIMIT 4;");
    $stmt->execute([':categoryName' => $categoryName]);
    return $stmt->fetchAll();
}

function fetchCategoriesByName($pdo, $categoryName) {
    $stmt = $pdo->prepare("SELECT * FROM `category` WHERE `category`.`category_name` LIKE :categoryName");
    $stmt->execute([':categoryName' => $categoryName]);
    return $stmt->fetchAll();
}

function fetchLocations($pdo) {
    $stmt = $pdo->query("SELECT * FROM `locations` ORDER BY RAND() LIMIT 5;");
    return $stmt->fetchAll();
}

try {
    $events_lectureArr = fetchEventsByCategory($pdo, 'Lecture');
    $events_partyArr = fetchEventsByCategory($pdo, 'Party');
    $events_clubArr = fetchEventsByCategory($pdo, 'Club');
    $events_fashionArr = fetchEventsByCategory($pdo, 'Fashion');

    $category_partyArr = fetchCategoriesByName($pdo, 'Party');
    $category_lectureArr = fetchCategoriesByName($pdo, 'Lecture');
    $category_fashionArr = fetchCategoriesByName($pdo, 'Fashion');
    $category_clubArr = fetchCategoriesByName($pdo, 'Club');

    $locationArr = fetchLocations($pdo);

    $categoryArr = array(
        "Party" => $category_partyArr,
        "Lecture" => $category_lectureArr,
        "Fashion" => $category_fashionArr,
        "Club" => $category_clubArr
    );

    $eventArr = array(
        "Party" => $events_partyArr,
        "Lecture" => $events_lectureArr,
        "Club" => $events_clubArr,
        "Fashion" => $events_fashionArr
    );

    $return = array(
        "event" => $eventArr,
        "location" => $locationArr,
        "category" => $categoryArr
    );

    echo json_encode($return);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed', 'details' => $e->getMessage()]);
}
?>
