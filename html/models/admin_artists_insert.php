<?php include "../../auth/connection.php";

include "../class/class_validations.php";
include "../class/class_artists.php";

$form = convert_array($_POST['form']);

$errors = validations($pdo, $form, "");

if ( $errors )
{
    echo json_encode($errors);
}
else
{
    $class_artists = new class_artists;
    $insert_artist = $class_artists->insert_artist($pdo, $form);
    
    if ( $insert_artist )
    {
        $return[] = [ "code" => 2, "error" => $insert_artist ];
    }
    else
    {
        $return[] = [ "code" => 0, "error" => "Artist created successfully!" ];
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
    
    $class_artists = new class_artists;
    
    foreach ( $resp as $key => $value )
    {
        $class_artists->checkValidations($pdo, $errors, $key, $value, "add", $id);
    }
    
    return $errors;
}