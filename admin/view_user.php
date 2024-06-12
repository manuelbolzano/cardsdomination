<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../include/db_connect.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $sql = "SELECT id, username, email, punti FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit();
    }

    // Recupera il livello dell'utente in base ai punti
    $punti = $user['punti'];
    $level_sql = "SELECT level_number FROM levels WHERE points_required <= ? ORDER BY points_required DESC LIMIT 1";
    $level_stmt = $conn->prepare($level_sql);
    $level_stmt->bind_param("i", $punti);
    $level_stmt->execute();
    $level_stmt->bind_result($level_number);
    $level_stmt->fetch();
    $level_stmt->close();

    $sql = "SELECT cards.id, cards.name, cards.description, cards.image_url, cards.power_top, cards.power_top_right, cards.power_right, cards.power_bottom_right, cards.power_bottom, cards.power_bottom_left, cards.power_left, cards.power_top_left FROM user_cards 
            JOIN cards ON user_cards.card_id = cards.id WHERE user_cards.user_id = '$user_id'";
    $user_cards = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Invalid user ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="container">
    <h1>View User</h1>
    <?php include 'menu.php'; ?>
    <div class="user-details">
        <h2>User Details</h2>
        <p><strong>ID:</strong> <?php echo $user['id']; ?></p>
        <p><strong>Punti:</strong> <?php echo $user['punti']; ?></p> <!-- Mostra i punti dell'utente -->
        <p><strong>Livello:</strong> <?php echo isset($level_number) ? $level_number : 'N/A'; ?></p> <!-- Mostra il livello dell'utente -->
        <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
    </div>
    <div class="user-cards">
        <h2>User's Cards</h2>
        <div class="cards-container">
            <?php foreach ($user_cards as $card) { ?>
                <div class="card" style="background-image: url('../<?php echo $card['image_url']; ?>');">
                    <div class="value top"><?php echo $card['power_top']; ?></div>
                    <div class="value top-right"><?php echo $card['power_top_right']; ?></div>
                    <div class="value right"><?php echo $card['power_right']; ?></div>
                    <div class="value bottom-right"><?php echo $card['power_bottom_right']; ?></div>
                    <div class="value bottom"><?php echo $card['power_bottom']; ?></div>
                    <div class="value bottom-left"><?php echo $card['power_bottom_left']; ?></div>
                    <div class="value left"><?php echo $card['power_left']; ?></div>
                    <div class="value top-left"><?php echo $card['power_top_left']; ?></div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
</body>
</html>
