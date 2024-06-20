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

$sql = "SELECT * FROM `users` WHERE `users`.`user_status` = 0 AND `users`.`user_role` NOT LIKE 'request'";

$sqlcnt = "SELECT COUNT(*) FROM `users` WHERE `users`.`user_status` = 0 AND `users`.`user_role` NOT LIKE 'request'";

$sqlfiltred = " AND (
    (`user_id`              LIKE '%{$requestData['search']['value']}%') OR
    (`user_name`            LIKE '%{$requestData['search']['value']}%') OR
    (`user_username`        LIKE '%{$requestData['search']['value']}%') OR
    (`user_email`           LIKE '%{$requestData['search']['value']}%') OR
    (`user_register_date`   LIKE '%{$requestData['search']['value']}%') OR
    (`user_role`            LIKE '%{$requestData['search']['value']}%'))";

function nestedData_work_list($row)
{
    $class_datatable = new class_datatable();
    
    $button_active = $class_datatable->button_active_user();
      
    $nestedData = array();
    $nestedData[] = $row["user_id"];
    $nestedData[] = $row["user_name"];
    $nestedData[] = $row["user_username"];
    $nestedData[] = $row["user_email"];
    $nestedData[] = $row["user_register_date"];
    $nestedData[] = $row["user_role"];
    $nestedData[] = $button_active;
    $nestedData[] = $row["user_address"];
    $nestedData[] = $row["user_phone"];
    $nestedData[] = $row["user_cc"];
    $nestedData[] = $row["user_birth_date"];
    return $nestedData;
}

$call_back_func = "nestedData_work_list";

$class_datatable = new class_datatable;
$class_datatable->getData($pdo, $requestData, $columns, $sqlcnt, $sqlfiltred, $sql, $call_back_func);