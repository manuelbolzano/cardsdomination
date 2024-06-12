<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/your/php-error.log'); // Assicurati che il percorso sia corretto

session_start();
include 'include/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $data = json_decode(file_get_contents('php://input'), true);

    // Log per debug
    error_log('Data received: ' . print_r($data, true));

    if (isset($data['points']) && isset($data['user_score']) && isset($data['ai_score']) && isset($data['coins'])) {
        $points = $data['points'];
        $user_score = $data['user_score'];
        $ai_score = $data['ai_score'];
        $coins = $data['coins'];

        // Ottieni il punteggio corrente e le monete correnti
        $sql = "SELECT punti, monete FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($current_points, $current_coins);
            $stmt->fetch();
            $stmt->close();
        } else {
            error_log('Prepare failed: (' . $conn->errno . ') ' . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Errore nella preparazione della query']);
            exit();
        }

        // Log per debug
        error_log('Points: ' . $points);
        error_log('User Score: ' . $user_score);
        error_log('AI Score: ' . $ai_score);
        error_log('Current Points: ' . $current_points);
        error_log('Current Coins: ' . $current_coins);

        // Recupera la configurazione dei punti
        $sql = "SELECT win_points_diff_less_equal_3, win_points_diff_greater_3, lose_points FROM points_config WHERE id = 1"; // Assicurati che ci sia solo una riga nella tabella points_config
        $result = $conn->query($sql);
        if ($result) {
            $config = $result->fetch_assoc();
            $result->close();
        } else {
            error_log('Errore durante l\'esecuzione della query points_config: ' . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Errore durante il recupero della configurazione dei punti']);
            exit();
        }

        // Log per debug
        error_log('Points config: ' . print_r($config, true));

        // Determina i punti da aggiungere o rimuovere basandosi sulla configurazione
        if ($user_score > $ai_score) {
            $difference = $user_score - $ai_score;
            if ($difference > 3) {
                $points = $config['win_points_diff_greater_3'];
            } elseif ($difference > 0) {
                $points = $config['win_points_diff_less_equal_3'];
            } else {
                $points = 0;
            }
        } elseif ($user_score < $ai_score) {
            $points = -$config['lose_points'];
        } else {
            $points = 0;
        }

        // Log per debug
        error_log('Calculated Points: ' . $points);

        // Calcola il nuovo punteggio
        $new_points = $current_points + $points;
        if ($new_points < 0) {
            $new_points = 0;
        }

        // Calcola le nuove monete
        $new_coins = $current_coins + $coins;
        if ($new_coins < 0) {
            $new_coins = 0;
        }

        // Aggiorna il punteggio e le monete nel database
        $sql = "UPDATE users SET punti = ?, monete = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iii", $new_points, $new_coins, $user_id);
            if ($stmt->execute()) {
                error_log('Punti aggiornati: ' . $new_points);
                error_log('Monete aggiornate: ' . $new_coins);
                echo json_encode(['success' => true, 'new_points' => $new_points, 'new_coins' => $new_coins]);
            } else {
                error_log('Errore durante l\'esecuzione della query: ' . $stmt->error);
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento del punteggio e delle monete']);
            }
            $stmt->close();
        } else {
            error_log('Prepare failed: (' . $conn->errno . ') ' . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Errore nella preparazione della query di aggiornamento']);
        }
    } else {
        error_log('Dati mancanti: ' . print_r($data, true));
        echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
    }
} else {
    error_log('Metodo non consentito');
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
}

$conn->close();
?>