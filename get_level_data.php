<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Utente non autenticato']);
    exit();
}

include 'include/db_connect.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT l.level_number, l.coins_min_loss, l.coins_max_loss, l.coins_min_win, l.coins_max_win 
        FROM users u
        JOIN levels l ON u.level_id = l.id
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $level_data = $result->fetch_assoc();
    header("Content-Type: application/json");
    echo json_encode($level_data);
} else {
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Dati del livello non trovati']);
}

$stmt->close();
$conn->close();
?>