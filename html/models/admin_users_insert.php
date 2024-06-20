<?php include "../../auth/connection.php";

include "../class/class_validations.php";
include "../class/class_users.php";

$form = convert_array($_POST['form']);

$errors = validations($pdo, $form, "");

if ( $errors )
{
    echo json_encode($errors);
}
else
{
    $class_users = new class_users;
    $insert_user = $class_users->insert_user($pdo, $form);
    
    if ( $insert_user )
    {
        $return[] = [ "code" => 2, "error" => $insert_user ];
    }
    else
    {
        $return[] = [ "code" => 0, "error" => "User created successfully!" ];
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
        $class_users->checkValidations($pdo, $errors, $key, $value, "add", $id);
    }
    
    if ( !$errors )
    {
        if ( $resp['user_add_password'] !== $resp['user_add_password1'] )
        {
            $errors[] = [ "code" => 1, "error" => "Passwords do not match", "input" => "user_add_password,#user_add_password1  " ];
        }
    }
    
    return $errors;
}