<?php

include "../../auth/connection.php";

$form = convert_array($_POST['token_form']);
$token_input = $form['login_token'];
$user_name = $_SESSION['user'];

$query = $pdo->query("SELECT `user_token`,`user_email`,`user_name` FROM `users` WHERE `user_username` = '$user_name' OR `user_address` = '$user_name'");
$queryArr = $query->fetch();
$token_user = $queryArr['user_token'];


if ($token_input === $token_user)
{
    $_SESSION['loggedin'] = true;
    $return[] = [ "code" => 0, "error" => "Login efetuado com sucesso" ];
}
else
{
    $return[] = [ "code" => 1, "error" => "Token incorrect!", "input" => "login_token" ];
}

echo json_encode($return);

function convert_array($resp)
{
    $return = array();
    
    for ( $i = 0; $i < count($resp); $i++ )
    {
        $return[$resp[$i]['name']] = $resp[$i]['value'];
    }
    
    return $return;
}