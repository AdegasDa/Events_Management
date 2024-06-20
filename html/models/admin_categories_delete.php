<?php include "../../auth/connection.php";

include "../class/class_categories.php";

$category_id = $_POST['category_id'];

$class_users = new class_categories;
$category = $class_users->delete_category($pdo, $category_id);

if ( $category )
{
    $return[] = [ "code" => 2, "error" => $category ];
}
else
{
    $return[] = [ "code" => 0, "error" => "Category successfully deleted!" ];
}

echo json_encode($return);