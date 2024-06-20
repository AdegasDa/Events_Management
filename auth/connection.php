<?php

require "../vendor/autoload.php";

// Configurações do banco de dados
$servername = "172.18.0.2";
$username = "atw";
$password = "ATW_password1.";
$database = "atw";

// Tentativa de conexão
try
{
    // Criando uma nova conexão PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password);

    // Configurando o modo de erro do PDO para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	session_start();
}
catch ( PDOException $e )
{
    // Se houver algum erro na conexão, será capturado aqui
    echo 'Erro de conexão: ' . $e->getMessage();
    exit;
}