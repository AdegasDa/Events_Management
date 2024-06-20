<?php include "../../auth/connection.php";

include "../class/class_validations.php";
include "../class/class_users.php";

$form = convert_array($_POST['form']);

$errors = validations($pdo, $form, $_POST['id']);

if ( $errors )
{
    echo json_encode($errors);
}
else
{
    $class_users = new class_users;
    $update_user = $class_users->update_user($pdo, $form, $_POST['id']);
    
    if ( $update_user )
    {
        $return[] = [ "code" => 2, "error" => $update_user ];
    }
    else
    {
        $return[] = [ "code" => 0, "error" => "User data saved successfully!" ];
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
    
    $class_users = new class_users;
    
    foreach ( $resp as $key => $value )
    {
        $class_users->checkValidations($pdo, $errors, $key, $value, "edit", $id);
    }
    
    return $errors;
}