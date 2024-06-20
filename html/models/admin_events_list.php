<?php

include "../../auth/connection.php";
include "../class/class_datatable.php";

$requestData= $_REQUEST;

$columns = array( 
    0 => 'event_id',
    1 => 'event_name',
    2 => 'category_name',
    3 => 'location_country',
    4 => 'event_date',
    5 => 'event_subscription_limit'
);

$sql = "SELECT * FROM ((`events`
    INNER JOIN `category` ON `events`.`event_category_id` = `category`.`category_id` AND `category`.`category_type` = 2)
    INNER JOIN `locations` ON `events`.`event_location_id` = `locations`.`location_id`)
    WHERE `events`.`event_status` = 1";

$sqlcnt = "SELECT COUNT(*) FROM ((`events`
    INNER JOIN `category` ON `events`.`event_category_id` = `category`.`category_id` AND `category`.`category_type` = 2)
    INNER JOIN `locations` ON `events`.`event_location_id` = `locations`.`location_id`)
    WHERE `events`.`event_status` = 1";

$sqlfiltred = " AND ("
    . "(`event_id`                          LIKE '%{$requestData['search']['value']}%') OR"
    . "(`event_name`                        LIKE '%{$requestData['search']['value']}%') OR"
    . "(`category_name`                     LIKE '%{$requestData['search']['value']}%') OR"
    . "(`location_country`                  LIKE '%{$requestData['search']['value']}%') OR"
    . "(`event_date`                        LIKE '%{$requestData['search']['value']}%') OR"
    . "(`event_subscription_limit`          LIKE '%{$requestData['search']['value']}%') OR"
    . "(FORMAT(ROUND(`event_price`, 2), 2)  LIKE '%{$requestData['search']['value']}%'))";

function nestedData_work_list($row)
{
    $class_datatable = new class_datatable();
    
    $button_edit = $class_datatable->button_edit();
    $button_delete = $class_datatable->button_delete();
    $button_subscribers = $class_datatable->button_subscribers();
      
    $nestedData = array();
    $nestedData[] = $row["event_id"];
    $nestedData[] = $row["event_name"];
    $nestedData[] = $row["category_name"];
    $nestedData[] = $row["location_country"];
    $nestedData[] = $row["event_date"];
    $nestedData[] = $row["event_subscription_limit"];
    $nestedData[] = number_format($row["event_price"], 2);
    $nestedData[] = $button_edit;
    $nestedData[] = $button_delete;
    $nestedData[] = $button_subscribers;
    $nestedData[] = $row["event_category_id"];
    $nestedData[] = $row["event_detail"];
    $nestedData[] = $row["event_address"];
    $nestedData[] = $row["event_location_id"];
    $nestedData[] = $row["event_limit"];
    
    return $nestedData;
}

$call_back_func = "nestedData_work_list";

$class_datatable = new class_datatable;
$class_datatable->getData($pdo, $requestData, $columns, $sqlcnt, $sqlfiltred, $sql, $call_back_func);