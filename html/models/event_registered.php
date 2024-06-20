<?php

include "../../auth/connection.php";
include "../class/class_datatable.php";

$requestData= $_REQUEST;
$username = $_SESSION['user'];

$columns = array( 
    0 => 'event_id',
    1 => 'event_name',
    2 => 'event_address',
    3 => 'event_price',
    4 => 'event_date',
);

$sql = "SELECT `events`.* FROM `events`"
        . "INNER JOIN `events_users` ON `events_users`.`event_id` = `events`.`event_id`"
        . "INNER JOIN `users` ON `users`.`user_id` = `events_users`.`user_id`"
        . "WHERE `users`.`user_username` LIKE '$username'";

$sqlcnt = "SELECT COUNT(*) FROM `events_users`"
        . "INNER JOIN `events` ON `events_users`.`event_id` = `events`.`event_id`"
        . "INNER JOIN `users` ON `users`.`user_id` = `events_users`.`user_id`"
        . "WHERE `users`.`user_username` LIKE '$username'";

$sqlFiltred = " AND ("
    ."(`events`.`event_name`              LIKE '%{$requestData['search']['value']}%') OR"
    ."(`event_address`           LIKE '%{$requestData['search']['value']}%') OR"
    ."(`event_price`             LIKE '%{$requestData['search']['value']}%') OR"
    ."(`event_date`              LIKE '%{$requestData['search']['value']}%'))";

function nestedData_work_list($row)
{
    $class_datatable = new class_datatable();
    
    $button_display = $class_datatable->button_event_display();
    $button_delete = $class_datatable->button_delete_association();
    
    $eventDate = date_create($row["event_date"]);
    $formattedDate = date_format($eventDate, 'd/m/Y');
      
    $nestedData = array();
    $nestedData[] = $row["event_id"];
    $nestedData[] = $row["event_name"];
    $nestedData[] = $row["event_address"];
    $nestedData[] = $row["event_price"];
    $nestedData[] = $row["event_date"];
    $nestedData[] = $button_display;
    $nestedData[] = $button_delete;
    return $nestedData;
}

$call_back_func = "nestedData_work_list";

$class_datatable = new class_datatable;
$class_datatable->getData($pdo, $requestData, $columns, $sqlcnt, $sqlFiltred, $sql, $call_back_func);