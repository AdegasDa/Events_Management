<?php include "../../auth/connection.php";

include "../class/class_validations.php";
include "../class/class_categories.php";

$form = convert_array($_POST['form']);

$errors = validations($pdo, $form, $_POST['id']);

if ( $errors )
{
    echo json_encode($errors);
}
else
{
    $class_categories = new class_categories;
    $update_category = $class_categories->update_category($pdo, $form, $_POST['id']);
    
    if ( $update_category )
    {
        $return[] = [ "code" => 2, "error" => $update_category ];
    }
    else
    {
        $return[] = [ "code" => 0, "error" => "Category data saved successfully!" ];
    }
    
    echo json_encode($return);
}

function convert_array($resp)
{
    $return = array();
    
    for ( $i = 0; $i < count($resp); $i++ )
    {
        $return[$resp[$i]['name']] = $resp[$i]['value'];
    }
    
    return $return;
}

function validations($pdo, $resp, $id)
{
    $errors = array();
    
    $class_categories = new class_categories;
    
    foreach ( $resp as $key => $value )
    {
        $class_categories->checkValidations($pdo, $errors, $key, $value, "edit", $id);
    }
    
    return $errors;
}