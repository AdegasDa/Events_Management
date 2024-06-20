<?php

include "../../auth/connection.php";
include "../class/class_datatable.php";

$requestData= $_REQUEST;

$columns = array( 
    0 => 'artist_id',
    1 => 'artist_name',
    2 => 'artist_country',
    3 => 'category_name'
);

$sql = "SELECT `artists`.*, `category`.*, `locations`.*  FROM (((`artists`
    INNER JOIN `artists_category` ON `artists`.`artist_id` = `artists_category`.`artist_id`)
    INNER JOIN `category` ON `artists_category`.`category_id` = `category`.`category_id` AND `category`.`category_type` = 1)
    INNER JOIN `locations` ON `artists`.`artist_location_id` = `locations`.`location_id`)
    WHERE `artists`.`artist_status` = 1";

$sqlcnt = "SELECT COUNT(*) FROM (((`artists`
    INNER JOIN `artists_category` ON `artists`.`artist_id` = `artists_category`.`artist_id`)
    INNER JOIN `category` ON `artists_category`.`category_id` = `category`.`category_id` AND `category`.`category_type` = 1)
    INNER JOIN `locations` ON `artists`.`artist_location_id` = `locations`.`location_id`)
    WHERE `artists`.`artist_status` = 1";

$sqlfiltred = " AND (
    (`artists`.`artist_id`          LIKE '%{$requestData['search']['value']}%') OR
    (`artists`.`artist_name`        LIKE '%{$requestData['search']['value']}%') OR
    (`locations`.`location_country` LIKE '%{$requestData['search']['value']}%') OR
    (`locations`.`location_city`    LIKE '%{$requestData['search']['value']}%') OR
    (`category`.`category_name`     LIKE '%{$requestData['search']['value']}%'))";

function nestedData_work_list($row)
{
    $class_datatable = new class_datatable();
    
    $button_edit = $class_datatable->button_edit();
    $button_delete = $class_datatable->button_delete();
      
    $nestedData = array();
    $nestedData[] = $row["artist_id"];
    $nestedData[] = $row["artist_name"];
    $nestedData[] = $row["location_country"];
    $nestedData[] = $row["location_city"];
    $nestedData[] = $row["category_name"];
    $nestedData[] = $button_edit;
    $nestedData[] = $button_delete;
    $nestedData[] = $row["location_id"];
    $nestedData[] = $row["category_id"];
    return $nestedData;
}

$call_back_func = "nestedData_work_list";

$class_datatable = new class_datatable;
$class_datatable->getData($pdo, $requestData, $columns, $sqlcnt, $sqlfiltred, $sql, $call_back_func);