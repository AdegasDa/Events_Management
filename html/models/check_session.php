<?php

include "../../auth/connection.php";

$return = array();

if ( isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true )
{
    $return['code'] = 1;
    $return['msg'] = "Logged in user!";
    $return['adm'] = $_SESSION['admin'];
    $return['user'] = $_SESSION['user'];
}
else
{
    $return['code'] = 0;
    $return['msg'] = "User not logged in!";
}

echo json_encode($return);