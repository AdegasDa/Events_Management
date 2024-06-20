<?php

include "../../auth/connection.php";

try {
    // Preparar a consulta para evitar SQL Injection
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `user_username` = :username");
    $stmt->execute([':username' => $_SESSION['user']]);
    
    $return = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($return);
} catch (PDOException $e) {
    // Resposta em caso de falha na consulta
    http_response_code(500);
    echo json_encode(['error' => 'Falha na consulta ao banco de dados', 'details' => $e->getMessage()]);
}
?>
