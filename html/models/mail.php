<?php

include "../../auth/connection.php";
include "../class/class_mail_sender.php";

$recipient = 'ivomiguelalmeidasantos2002@gmail.com';
$subject = 'Assunto do E-mail';
$message = 'Olá,<br><br>Este é um e-mail de exemplo.<br><br>Atenciosamente,<br>Seu Nome';

$mail_sender = new class_mail_sender();
$sendMail = $mail_sender->sendMail($recipient, $subject, $message);

echo json_encode($sendMail);