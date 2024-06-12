<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'include/db_connect.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT cards.name, cards.description, cards.image_url, cards.power_top, cards.power_right, cards.power_bottom, cards.power_left, cards.power_top_right, cards.power_bottom_right, cards.power_bottom_left, cards.power_top_left FROM user_cards 
        JOIN cards ON user_cards.card_id = cards.id WHERE user_cards.user_id = '$user_id'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="dashboard">
    <?php include 'include/menu.php'; ?>
    <div class="container contentpage">


    <div class="row">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="card-container col-md-4">
                <div class="card" onclick="this.classList.toggle('card-flip');">
                    <div class="card-front" style="background-image: url('<?php echo $row['image_url']; ?>');">
                        <div class="value top"><?php echo $row['power_top']; ?></div>
                        <div class="value top-right"><?php echo $row['power_top_right']; ?></div>
                        <div class="value right"><?php echo $row['power_right']; ?></div>
                        <div class="value bottom-right"><?php echo $row['power_bottom_right']; ?></div>
                        <div class="value bottom"><?php echo $row['power_bottom']; ?></div>
                        <div class="value bottom-left"><?php echo $row['power_bottom_left']; ?></div>
                        <div class="value left"><?php echo $row['power_left']; ?></div>
                        <div class="value top-left"><?php echo $row['power_top_left']; ?></div>
                    </div>
                    <div class="card-back">
                        <h2><?php echo $row['name']; ?></h2>
                        <p><?php echo $row['description']; ?></p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
   
    </div>
    <div class="pulsantegiocaora">
        <a href="game.php"><img src="img/UI/giocaora.png" alt="point" style=""></a>
    </div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>