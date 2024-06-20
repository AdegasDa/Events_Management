<?php include "../../auth/connection.php";

include "../class/class_validations.php";
include "../class/class_events.php";

if (isset($_FILES['event_add_img']))
{
    $form = convert_array($_POST['form'], $_FILES['event_add_img']);
}
else
{
    $form = convert_array($_POST['form'], "");
}


$errors = validations($pdo, $form, "");

if ( $errors )
{
    echo json_encode($errors);
}
else
{
    $artists = !empty($_POST['artists']) ? explode(",", $_POST['artists']) : [];
    
    $class_events = new class_events;
    $insert_event = $class_events->insert_event($pdo, $form, $artists);
    
    if ( $insert_event )
    {
        $return[] = [ "code" => 2, "error" => $insert_event ];
    }
    else
    {
        $return[] = [ "code" => 0, "error" => "Event created successfully!" ];
    }
    
    echo json_encode($return);
}

function convert_array($resp, $img)
{
    $return = array();
    
    for ( $i = 0; $i < count($resp); $i++ )
    {
        $return[$resp[$i]['name']] = $resp[$i]['value'];
    }
    
    $return['event_add_img'] = $img;
    
    return $return;
}

function validations($pdo, $resp, $id)
{
    $errors = array();
    
    $class_events = new class_events;
    
    foreach ( $resp as $key => $value )
    {
        $class_events->checkValidations($pdo, $errors, $key, $value, "add", $id);
    }
    
    return $errors;
}