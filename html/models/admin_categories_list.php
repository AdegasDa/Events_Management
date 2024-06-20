<?php

include "../../auth/connection.php";
include "../class/class_datatable.php";

$requestData= $_REQUEST;

$columns = array( 
    0 => 'category_id',
    1 => 'category_name',
    2 => 'category_type'
);

$sql = "SELECT * FROM `category` WHERE `category_status` = 1";

$sqlcnt = "SELECT COUNT(*) FROM `category` WHERE `category_status` = 1";

$sqlfiltred = " AND (
    (`category_id`      LIKE '%{$requestData['search']['value']}%') OR
    (`category_name`    LIKE '%{$requestData['search']['value']}%') OR
    (`category_type`    LIKE '%{$requestData['search']['value']}%'))";

function nestedData_work_list($row)
{
    $class_datatable = new class_datatable();
    
    $button_edit = $class_datatable->button_edit();
    $button_delete = $class_datatable->button_delete();
    
    switch ($row["category_type"])
    {
        case 1:
            $type = "Artist";
            break;
        case 2:
            $type = "Event";
            break;
    }
    
    $nestedData = array();
    $nestedData[] = $row["category_id"];
    $nestedData[] = $row["category_name"];
    $nestedData[] = $type;
    $nestedData[] = $button_edit;
    $nestedData[] = $button_delete;
    $nestedData[] = $row["category_type"];
    return $nestedData;
}

$call_back_func = "nestedData_work_list";

$class_datatable = new class_datatable;
$class_datatable->getData($pdo, $requestData, $columns, $sqlcnt, $sqlfiltred, $sql, $call_back_func);