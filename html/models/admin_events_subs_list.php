<?php

include "../../auth/connection.php";
include "../class/class_datatable.php";

$requestData= $_REQUEST;

$columns = array( 
    0 => 'user_id',
    1 => 'user_name',
    2 => 'user_username',
    3 => 'user_email',
    4 => 'user_register_date',
    5 => 'user_role'
);

$sql = "SELECT `users`.* FROM ((`events`
    INNER JOIN `events_users` ON `events`.`event_id` = `events_users`.`event_id`)
    INNER JOIN `users` ON `events_users`.`user_id` = `users`.`user_id`) WHERE `events`.`event_id` LIKE " . $requestData['event_id'];

$sqlcnt = "SELECT COUNT(*) FROM ((`events`
    INNER JOIN `events_users` ON `events`.`event_id` = `events_users`.`event_id`)
    INNER JOIN `users` ON `events_users`.`user_id` = `users`.`user_id`) WHERE `events`.`event_id` LIKE " . $requestData['event_id'];

$sqlfiltred = " AND ("
    . "(`users`.`user_id`               LIKE '%{$requestData['search']['value']}%') OR"
    . "(`users`.`user_name`             LIKE '%{$requestData['search']['value']}%') OR"
    . "(`users`.`user_username`         LIKE '%{$requestData['search']['value']}%') OR"
    . "(`users`.`user_email`            LIKE '%{$requestData['search']['value']}%') OR"
    . "(`users`.`user_register_date`    LIKE '%{$requestData['search']['value']}%') OR"
    . "(`users`.`user_role`             LIKE '%{$requestData['search']['value']}%'))";

function nestedData_work_list($row)
{
    $nestedData = array();
    $nestedData[] = $row["user_id"];
    $nestedData[] = $row["user_name"];
    $nestedData[] = $row["user_username"];
    $nestedData[] = $row["user_email"];
    $nestedData[] = $row["user_register_date"];
    $nestedData[] = $row["user_role"];
    
    return $nestedData;
}

$call_back_func = "nestedData_work_list";

$class_datatable = new class_datatable;
$class_datatable->getData($pdo, $requestData, $columns, $sqlcnt, $sqlfiltred, $sql, $call_back_func);