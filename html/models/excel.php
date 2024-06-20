<?php include "../../auth/connection.php";

$stmt = $pdo->prepare("SELECT `users`.`user_id`, `users`.`user_username`, `users`.`user_email` FROM ((`events` INNER JOIN `events_users` ON `events`.`event_id` = `events_users`.`event_id`) INNER JOIN `users` ON `events_users`.`user_id` = `users`.`user_id`) WHERE `events`.`event_id` = ?");
$stmt->execute([$_GET['id']]);

if ( $stmt->rowCount() > 0 )
{
    $filename = "subscribed_event" . $_GET['id'] . ".csv";
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, array("Nome do Evento: " . $_GET['name']), ";");
    
    fputcsv($output, array('user_id', 'user_username', 'user_email'), ";");
    
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
    {
        fputcsv($output, $row, ";");
    }
    
    fclose($output);
}
else
{
     echo "There is no data to export";
}