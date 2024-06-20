<?php

include "../../auth/connection.php";
include "../class/class_mail_sender.php";

$mail_sender = new class_mail_sender();

$resp = $_POST['login_form'];

$login_form = convert_array($resp);

$errors = validations($login_form);

$user_query = $pdo->query("SELECT `user_id`,`user_name`,`user_email` FROM `users` WHERE `user_username` = '$login_form[login_user]' OR `user_email` = '$login_form[login_user]'");
$user = $user_query->fetch();
        
if ( $errors )
{
    echo json_encode($errors);
}
else
{
    if ( checkPassword($pdo, $login_form) )
    {
        if (checkStatus($pdo, $login_form) )
        {
            $token = generateToken(8);

            $update_query = $pdo->prepare("UPDATE `users` SET `user_token` = :token WHERE `user_id` = :user_id");
            $update_query->execute([':token' => $token, ':user_id' => $user['user_id']]);

            $recipient = $user['user_email'];
            $subject = 'Verification Token';
            $message = "Hi $user[user_name],<br><br>Please use the following token to verify your account:<br><br>Token: $token<br><br>Thanks,<br>Outside Eve";

            $sendMail = $mail_sender->sendMail($recipient, $subject, $message);

            if ($sendMail)
            {
                $return[] = [ "code" => 0, "error" => "Credentials correct, email sent." ]; 
            }
            else 
            {
                $return[] = [ "code" => 1, "error" => "Error. Email not sent.", "input" => "login_error" ];
            }
        }
        else
        {
            $return[] = [ "code" => 1, "error" => "Account either deactivated or not accepted yet.", "input" => "login_error" ];
        }
    }
    else
    {
        $return[] = [ "code" => 1, "error" => "Username or password incorrect", "input" => "login_pass" ];
    }
    
    echo json_encode($return);
}

function checkStatus($pdo, $login_form)
{
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE (user_email = :username OR user_username = :username) AND `users`.`user_status` = 1 AND `users`.`user_role` NOT LIKE 'request'");
    $stmt->execute(array(':username' => $login_form['login_user']));
    
    $user = $stmt->fetch();
    
    if ( $user )
    {
        $_SESSION['admin'] = false;
        $_SESSION['user'] = $user['user_username'];
        
        if ( $user['user_role'] == "administrator" )
        {
            $_SESSION['admin'] = true;
        }
        
        return true;
    }
}

function checkPassword($pdo, $login_form)
{
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE (user_email = :username OR user_username = :username)");
    $stmt->execute(array(':username' => $login_form['login_user']));
    
    $user = $stmt->fetch();
    
    if ( $user && (password_verify($login_form['login_pass'], $user['user_password'])) )
    {   
        return true;
    }
}

function generateToken($length = 16) {
    return bin2hex(random_bytes($length));
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

function validations($resp)
{
    $errors = array();
    
    if ( empty($resp['login_user']) )
    {
        $errors[] = [ "code" => 1, "error" => "Preencha este campo", "input" => "login_user" ];
    }

    if ( empty($resp['login_pass']) )
    {
        $errors[] = [ "code" => 1, "error" => "Preencha este campo", "input" => "login_pass" ];
    }
    
    return $errors;
}