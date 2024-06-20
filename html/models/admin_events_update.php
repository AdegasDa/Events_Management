<?php include "../../auth/connection.php";

include "../class/class_validations.php";
include "../class/class_events.php";

$form = convert_array($_POST['form']);

$errors = validations($pdo, $form, $_POST['id']);

if ( $errors )
{
    echo json_encode($errors);
}
else
{
    $artists = !empty($_POST['artists']) ? explode(",", $_POST['artists']) : [];
    
    $class_events = new class_events;
    $update_event = $class_events->update_event($pdo, $form, $_POST['id'], $artists);
    
    if ( $update_event )
    {
        $return[] = [ "code" => 2, "error" => $update_event ];
    }
    else
    {
        $return[] = [ "code" => 0, "error" => "Event data saved successfully!" ];
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
    
    $class_events = new class_events;
    
    foreach ( $resp as $key => $value )
    {
        $class_events->checkValidations($pdo, $errors, $key, $value, "edit", $id);
    }
    
    return $errors;
}