<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/var/www/html/vendor/phpmailer/phpmailer/src/Exception.php';
require '/var/www/html/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '/var/www/html/vendor/phpmailer/phpmailer/src/SMTP.php';

class class_mail_sender
{
    public function sendMail($recipient, $subject, $message)
    {
        $mail = new PHPMailer(true);
        
        try 
        {
            // Configure PHPMailer
            $this->configure_PHPMailer($mail);
            // Recipient
            $mail->addAddress($recipient);
            // Email content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            
            $mail->send();
            
            return [ "code" => 0, "error" => "Email successfully sent!" ];
        } 
        catch ( Exception $e ) 
        {
            return [ "code" => 1, "error" => "Message could not be sent: <br>" . $mail->ErrorInfo ];
        }
    }
    
    private function configure_PHPMailer(&$mail)
    {
        try
        {
            $mail->CharSet = 'UTF-8';
//            $mail->SMTPDebug = 2;
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = '3d28a0363bb00e';
            $mail->Password = '6bdecc29ca14fb';
            $mail->SMTPSecure = '';
            $mail->Port = 2525;
            $mail->setFrom('atw2024.ddns@gmail.com', 'ATW 2024');
        }
        catch ( Exception $ex )
        {
            
        }
    }
}