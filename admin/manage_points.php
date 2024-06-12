<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../include/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $win_points_diff_less_equal_3 = $_POST['win_points_diff_less_equal_3'];
    $win_points_diff_greater_3 = $_POST['win_points_diff_greater_3'];
    $lose_points = $_POST['lose_points'];

    // Controlla se esiste una riga con id = 1
    $sql = "SELECT COUNT(*) as count FROM points_config WHERE id = 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        // Se esiste, aggiorna i valori
        $sql = "UPDATE points_config SET win_points_diff_less_equal_3 = ?, win_points_diff_greater_3 = ?, lose_points = ? WHERE id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $win_points_diff_less_equal_3, $win_points_diff_greater_3, $lose_points);
    } else {
        // Se non esiste, inserisci una nuova riga
        $sql = "INSERT INTO points_config (win_points_diff_less_equal_3, win_points_diff_greater_3, lose_points) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $win_points_diff_less_equal_3, $win_points_diff_greater_3, $lose_points);
    }

    $stmt->execute();
    $stmt->close();
}

$sql = "SELECT win_points_diff_less_equal_3, win_points_diff_greater_3, lose_points FROM points_config WHERE id = 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $config = $result->fetch_assoc();
} else {
    // Imposta i valori predefiniti se non ci sono record nella tabella
    $config = [
        'win_points_diff_less_equal_3' => 8,
        'win_points_diff_greater_3' => 15,
        'lose_points' => -5
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestione Punteggi</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1>Gestione Punteggi</h1>
    <?php include 'menu.php'; ?>
    <form method="POST" action="manage_points.php">
        <div class="form-group">
            <label for="win_points_diff_less_equal_3">Punti per vittoria (differenza ≤ 3):</label>
            <input type="number" class="form-control" id="win_points_diff_less_equal_3" name="win_points_diff_less_equal_3" value="<?php echo $config['win_points_diff_less_equal_3']; ?>" required>
        </div>
        <div class="form-group">
            <label for="win_points_diff_greater_3">Punti per vittoria (differenza > 3):</label>
            <input type="number" class="form-control" id="win_points_diff_greater_3" name="win_points_diff_greater_3" value="<?php echo $config['win_points_diff_greater_3']; ?>" required>
        </div>
        <div class="form-group">
            <label for="lose_points">Punti per sconfitta:</label>
            <input type="number" class="form-control" id="lose_points" name="lose_points" value="<?php echo $config['lose_points']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Salva</button>
    </form>
</div>
</body>
</html>
