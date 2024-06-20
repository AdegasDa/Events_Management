<?php

include "../../auth/connection.php";

$resp = $_POST['register_form'];

$register_form = convert_array($resp);

$error = array();

$firstName = trim($register_form['inputFirstName']);
$lastName = trim($register_form['inputLastName']);
$email = trim($register_form['inputEmail']);
$password = trim($register_form['inputPassword']);
$passwordEncrypt = encryptPassword($password);
$confirmPassword = trim($register_form['inputPasswordConfirm']);
$username = trim($register_form['inputUsername']);

$sql = "SELECT `user_username` FROM `users` WHERE `user_username` = :username";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':username', $username);
$stmt->execute();
$results = $stmt->fetchAll();

$sql_email = "SELECT `user_email` FROM `users` WHERE `user_email` = :email";
$stmt_email = $pdo->prepare($sql_email);
$stmt_email->bindParam(':email', $email);
$stmt_email->execute();
$results_email = $stmt_email->fetchAll();

if ( empty($username) )
{
    $error[] = [ "code" => 1, "error" => "Preencha este campo", "input" => "inputUsername" ];
} 
else if (count($results) > 0)
{
    $error[] = [ "code" => 1, "error" => "Username já utilizado, tente novamente", "input" => "inputUsername" ];
}
else if (!validateName($username))
{
    $error[] = [ "code" => 1, "error" => "O username nome não pode possuir caracteres especiais", "input" => "inputUsername" ];
}
    
if ( empty($firstName) )
{
    $error[] = [ "code" => 1, "error" => "Preencha este campo", "input" => "inputFirstName" ];
} 
else if (!validateName($firstName))
{
    $error[] = [ "code" => 1, "error" => "O primeiro nome não pode possuir caracteres especiais", "input" => "inputFirstName" ];
}

if ( empty($lastName) )
{
    $error[] = [ "code" => 1, "error" => "Preencha este campo", "input" => "inputLastName" ];
} 
else if (!validateName($lastName))
{
    $error[] = [ "code" => 1, "error" => "O último nome não pode possuir caracteres especiais", "input" => "inputLastName" ];
}

if ( empty($email) )
{
    $error[] = [ "code" => 1, "error" => "Preencha este campo", "input" => "inputEmail" ];
}
else if (count($results_email) > 0)
{
    $error[] = [ "code" => 1, "error" => "Email já cadastrado", "input" => "erro" ];
}
else if (!validateEmail($email))
{
    $error[] = [ "code" => 1, "error" => "E-mail Inválido", "input" => "inputEmail" ];
}

if ( empty($password) )
{
    $error[] = [ "code" => 1, "error" => "Preencha este campo", "input" => "inputPassword" ];
}
else if (!validatePassword($password))
{
    $error[] = [ "code" => 1, "error" => "A senha deve conter um mínimo de 8 caracteres, uma letra maiúscula e uma letra minúscula", "input" => "inputPassword" ];
}

if ( empty($confirmPassword) )
{
    $error[] = [ "code" => 1, "error" => "Preencha este campo", "input" => "inputPasswordConfirm" ];
}
else if (!verifyPassword($password, $confirmPassword))
{
    $error[] = [ "code" => 1, "error" => "As senhas precisam ser iguais", "input" => "inputPasswordConfirm" ];
}

if ($error)
{
    echo json_encode($error);
}
else
{
    $stmt = $pdo->query("INSERT INTO `users`(`user_username`, `user_name`, `user_password`, `user_email`, `user_register_date`, `user_role`, `user_status`) VALUES ('". $username ."', '". $firstName ." ". $lastName ."', '". $passwordEncrypt ."', '". $email ."', '". date('Y-m-d') ."', 'request', 0)");
    
    if ( !$stmt )
    {
        $return[] = [ "code" => 1, "error" => "Falha ao registar, tente novamente", "input" => "inputPasswordConfirm" ];
    } 
    else
    {    
        $return[] = [ "code" => 0, "error" => "Registo efetuado com sucesso" ];
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

function validateName($name)
{
    if (preg_match('/[\'"\\;#]/', $name)) 
    {
        return false;
    }
    
    return true;
}

function validateEmail($email) 
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

function validatePassword($password)
{
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password))
    {
        return false;
    } else {
        return true;
    }
}

function encryptPassword($password) 
{
    $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    return $encryptedPassword;
}

function verifyPassword($password, $confirmPassword)
{
    if ($password === $confirmPassword)
    {
        return true;
    } else {
        return false;
    }
}