<?php
include 'include/db_connect.php';

function update_user_level($user_id, $conn) {
    $sql = "SELECT punti FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($punti);
    $stmt->fetch();
    $stmt->close();

    $sql = "SELECT level_number FROM levels WHERE points_required <= ? ORDER BY points_required DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $punti);
    $stmt->execute();
    $stmt->bind_result($level_number);
    $stmt->fetch();
    $stmt->close();

    if (isset($level_number)) {
        $sql = "UPDATE users SET level = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $level_number, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Esegui il test
$user_id = 1; // ID dell'utente da testare
$new_points = 124; // Nuovi punti per il test

$sql = "UPDATE users SET punti = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $new_points, $user_id);
$stmt->execute();
$stmt->close();

update_user_level($user_id, $conn);

// Verifica il livello aggiornato
$sql = "SELECT level FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($level);
$stmt->fetch();
$stmt->close();

echo "User Level: " . $level;

$conn->close();
?>
