<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'include/db_connect.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT username, punti FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $punti);
$stmt->fetch();
$stmt->close();

// Recupera il livello dell'utente in base ai punti
$level_sql = "SELECT level_number, win_coins_min, win_coins_max, lose_coins_min, lose_coins_max FROM levels WHERE points_required <= ? ORDER BY points_required DESC LIMIT 1";
$level_stmt = $conn->prepare($level_sql);
$level_stmt->bind_param("i", $punti);
$level_stmt->execute();
$level_stmt->bind_result($level_number, $win_coins_min, $win_coins_max, $lose_coins_min, $lose_coins_max);
$level_stmt->fetch();
$level_stmt->close();

$sql = "SELECT cards.id, name, description, power_top, power_right, power_bottom, power_left, power_top_right, power_bottom_right, power_bottom_left, power_top_left, image_url FROM user_cards 
        JOIN cards ON user_cards.card_id = cards.id WHERE user_cards.user_id = ? LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_cards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$sql = "SELECT id, name, description, power_top, power_right, power_bottom, power_left, power_top_right, power_bottom_right, power_bottom_left, power_top_left, image_url FROM cards ORDER BY RAND() LIMIT 5";
$ai_cards = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Game</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="thegame">
    <div class="scoreboard">
        <div class="score">
            <div class="coretableleft">
               <strong> Intelligenza Artificiale</strong>
            </div>
            <div class="coretableright">
                Punti: <span id="ai-score">0</span>
            </div>
        </div>
    </div>
<div class="container">
    <h2>AI's Cards</h2>
    <div class="carte-avversario">
        <div class="cards-container" data-side="ai">
            <div id="ai-cards-data" style="display: none;"><?php echo json_encode($ai_cards); ?></div>
            <?php foreach ($ai_cards as $card) { ?>
                <div class="card ai-card-<?php echo $card['id']; ?>" data-card-id="<?php echo $card['id']; ?>" data-card-side="ai" style="background-image: url('<?php echo $card['image_url']; ?>');">
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
    <div class="board-container">
            <div class="board">
                <div class="cell" id="cell-0" onclick="selectCell(0)"></div>
                <div class="cell" id="cell-1" onclick="selectCell(1)"></div>
                <div class="cell" id="cell-2" onclick="selectCell(2)"></div>
                <div class="cell" id="cell-3" onclick="selectCell(3)"></div>
                <div class="cell" id="cell-4" onclick="selectCell(4)"></div>
                <div class="cell" id="cell-5" onclick="selectCell(5)"></div>
                <div class="cell" id="cell-6" onclick="selectCell(6)"></div>
                <div class="cell" id="cell-7" onclick="selectCell(7)"></div>
                <div class="cell" id="cell-8" onclick="selectCell(8)"></div>
            </div>
        </div>
    <div class="game-container">
        <div class="cards-container" data-side="user">
            <h2>Your Cards</h2>
            <div id="user-cards-data" style="display: none;"><?php echo json_encode($user_cards); ?></div>
            <?php foreach ($user_cards as $card) { ?>
                <div class="card user-card-<?php echo $card['id']; ?>" data-card-id="<?php echo $card['id']; ?>" data-card-side="user" style="background-image: url('<?php echo $card['image_url']; ?>');" onclick="selectCard(<?php echo $card['id']; ?>)">
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

<div class="scoreboard scoreutente">
    <div class="score">
        <div class="coretableleft">
           <strong> <a href="#" data-toggle="modal" data-target="#userInfoModal"><?php echo htmlspecialchars($username); ?></a></strong>
        </div>
        <div class="coretableright">
            Punti: <span id="user-score">0</span>
        </div>
    </div>
</div>

<!-- Modal per informazioni utente -->
<div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userInfoModalLabel"><?php echo htmlspecialchars($username); ?>'s Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Username: <?php echo htmlspecialchars($username); ?></p>
                <p>Punti: <?php echo htmlspecialchars($punti); ?></p>
                <p>Monete: <span id="userCoins">0</span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal per fine partita -->
<div class="modal fade" id="endGameModal" tabindex="-1" aria-labelledby="endGameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="endGameModalLabel">Fine della partita</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="endGameMessage"></p>
            </div>
            <div class="modal-footer">
                <a href="dashboard.php" class="btn btn-primary">Prosegui</a>
            </div>
        </div>
    </div>
</div>

<div id="level-info" style="display: none;" data-level-number="<?php echo $level_number; ?>" data-win-coins-min="<?php echo $win_coins_min; ?>" data-win-coins-max="<?php echo $win_coins_max; ?>" data-lose-coins-min="<?php echo $lose_coins_min; ?>" data-lose-coins-max="<?php echo $lose_coins_max; ?>"></div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="js/game.js"></script>
</body>
</html>