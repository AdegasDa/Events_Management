<?php

include "../../auth/connection.php";
include "../class/class_mail_sender.php";

$mail_sender = new class_mail_sender();

$token = generateToken(8);

$stmt = $pdo->query("SELECT `user_name`,`user_id`,`user_email` FROM `users` WHERE `users`.`user_username` LIKE '$_SESSION[user]'");
$user = $stmt->fetch();

$pdo->query("UPDATE `users` SET `user_token` = '$token' WHERE `user_id` = $user[user_id]");

$recipient = $user['user_email'];
$subject = 'Verification Token';
$message = "Hi $user[user_name],<br><br>Please use the following token to verify your account:<br><br>Token: $token<br><br>Thanks,<br>Outside Eve";

$sendMail = $mail_sender->sendMail($recipient, $subject, $message);

if ($sendMail)
{
    $return[] = [ "code" => 0, "error" => "Request successfully sent." ]; 
}
else 
{
    $return[] = [ "code" => 1, "error" => "Error. Email not sent.", "input" => "login_error" ];
}

echo json_encode($return);

function generateToken($length = 16) 
{
    return bin2hex(random_bytes($length));
}