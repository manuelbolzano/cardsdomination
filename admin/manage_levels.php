<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../include/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_level'])) {
        $level_number = $_POST['level_number'];
        $points_required = $_POST['points_required'];
        $win_coins_min = $_POST['win_coins_min'];
        $win_coins_max = $_POST['win_coins_max'];
        $lose_coins_min = $_POST['lose_coins_min'];
        $lose_coins_max = $_POST['lose_coins_max'];
        $image_url = null;

        if (isset($_FILES['level_image']) && $_FILES['level_image']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "../immagini_livelli/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . basename($_FILES["level_image"]["name"]);
            move_uploaded_file($_FILES["level_image"]["tmp_name"], $target_file);
            $image_url = "immagini_livelli/" . basename($_FILES["level_image"]["name"]);
        }

        $sql = "INSERT INTO levels (level_number, points_required, image_url, win_coins_min, win_coins_max, lose_coins_min, lose_coins_max) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisiiii", $level_number, $points_required, $image_url, $win_coins_min, $win_coins_max, $lose_coins_min, $lose_coins_max);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update_level'])) {
        $level_id = $_POST['level_id'];
        $level_number = $_POST['level_number'];
        $points_required = $_POST['points_required'];
        $win_coins_min = $_POST['win_coins_min'];
        $win_coins_max = $_POST['win_coins_max'];
        $lose_coins_min = $_POST['lose_coins_min'];
        $lose_coins_max = $_POST['lose_coins_max'];
        $image_url = $_POST['existing_image_url'];

        if (isset($_FILES['level_image']) && $_FILES['level_image']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "../immagini_livelli/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . basename($_FILES["level_image"]["name"]);
            move_uploaded_file($_FILES["level_image"]["tmp_name"], $target_file);
            $image_url = "immagini_livelli/" . basename($_FILES["level_image"]["name"]);
        }

        $sql = "UPDATE levels SET level_number = ?, points_required = ?, image_url = ?, win_coins_min = ?, win_coins_max = ?, lose_coins_min = ?, lose_coins_max = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisiisii", $level_number, $points_required, $image_url, $win_coins_min, $win_coins_max, $lose_coins_min, $lose_coins_max, $level_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update_all_levels'])) {
        header("Location: update_all_user_levels.php");
        exit();
    }
}

$levels_result = $conn->query("SELECT * FROM levels ORDER BY points_required ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestisci Livelli Utente</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <h1>Gestisci Livelli Utente</h1>
    <?php include 'menu.php'; ?>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addLevelModal">Aggiungi Livello</button>
    <form method="POST" action="" style="display:inline;">
        <button type="submit" class="btn btn-secondary" name="update_all_levels">Aggiorna Livelli Utenti</button>
    </form>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Numero Livello</th>
                <th>Punti Richiesti</th>
                <th>Immagine</th>
                <th>Monete Vittoria (Min - Max)</th>
                <th>Monete Sconfitta (Min - Max)</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $levels_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['level_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['points_required']); ?></td>
                    <td>
                        <?php if ($row['image_url']): ?>
                            <img src="../<?php echo htmlspecialchars($row['image_url']); ?>" alt="Level Image" style="max-width: 50px;">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['win_coins_min']) . ' - ' . htmlspecialchars($row['win_coins_max']); ?></td>
                    <td><?php echo htmlspecialchars($row['lose_coins_min']) . ' - ' . htmlspecialchars($row['lose_coins_max']); ?></td>
                    <td>
                        <button class="btn btn-info" data-toggle="modal" data-target="#editLevelModal" 
                                data-id="<?php echo $row['id']; ?>" 
                                data-level_number="<?php echo $row['level_number']; ?>" 
                                data-points_required="<?php echo $row['points_required']; ?>"
                                data-image_url="<?php echo $row['image_url']; ?>"
                                data-win_coins_min="<?php echo $row['win_coins_min']; ?>"
                                data-win_coins_max="<?php echo $row['win_coins_max']; ?>"
                                data-lose_coins_min="<?php echo $row['lose_coins_min']; ?>"
                                data-lose_coins_max="<?php echo $row['lose_coins_max']; ?>">
                            Modifica
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal per aggiungere un livello -->
<div class="modal fade" id="addLevelModal" tabindex="-1" aria-labelledby="addLevelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLevelModalLabel">Aggiungi Livello</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="level_number">Numero Livello</label>
                        <input type="number" class="form-control" id="level_number" name="level_number" required>
                    </div>
                    <div class="form-group">
                        <label for="points_required">Punti Richiesti</label>
                        <input type="number" class="form-control" id="points_required" name="points_required" required>
                    </div>
                    <div class="form-group">
                        <label for="win_coins_min">Monete Vittoria Minime</label>
                        <input type="number" class="form-control" id="win_coins_min" name="win_coins_min" required>
                    </div>
                    <div class="form-group">
                        <label for="win_coins_max">Monete Vittoria Massime</label>
                        <input type="number" class="form-control" id="win_coins_max" name="win_coins_max" required>
                    </div>
                    <div class="form-group">
                        <label for="lose_coins_min">Monete Sconfitta Minime</label>
                        <input type="number" class="form-control" id="lose_coins_min" name="lose_coins_min" required>
                    </div>
                    <div class="form-group">
                        <label for="lose_coins_max">Monete Sconfitta Massime</label>
                        <input type="number" class="form-control" id="lose_coins_max" name="lose_coins_max" required>
                    </div>
                    <div class="form-group">
                        <label for="level_image">Immagine del Livello</label>
                        <input type="file" class="form-control" id="level_image" name="level_image">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-primary" name="add_level">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal per modificare un livello -->
<div class="modal fade" id="editLevelModal" tabindex="-1" aria-labelledby="editLevelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLevelModalLabel">Modifica Livello</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="level_id" name="level_id">
                    <input type="hidden" id="existing_image_url" name="existing_image_url">
                    <div class="form-group">
                        <label for="edit_level_number">Numero Livello</label>
                        <input type="number" class="form-control" id="edit_level_number" name="level_number" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_points_required">Punti Richiesti</label>
                        <input type="number" class="form-control" id="edit_points_required" name="points_required" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_win_coins_min">Monete Vittoria Minime</label>
                        <input type="number" class="form-control" id="edit_win_coins_min" name="win_coins_min" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_win_coins_max">Monete Vittoria Massime</label>
                        <input type="number" class="form-control" id="edit_win_coins_max" name="win_coins_max" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_lose_coins_min">Monete Sconfitta Minime</label>
                        <input type="number" class="form-control" id="edit_lose_coins_min" name="lose_coins_min" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_lose_coins_max">Monete Sconfitta Massime</label>
                        <input type="number" class="form-control" id="edit_lose_coins_max" name="lose_coins_max" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_level_image">Immagine del Livello</label>
                        <input type="file" class="form-control" id="edit_level_image" name="level_image">
                    </div>
                    <div class="form-group">
                        <img id="current_image" src="" alt="Current Level Image" style="max-width: 100px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-primary" name="update_level">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#editLevelModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var level_number = button.data('level_number');
        var points_required = button.data('points_required');
        var image_url = button.data('image_url');
        var win_coins_min = button.data('win_coins_min');
        var win_coins_max = button.data('win_coins_max');
        var lose_coins_min = button.data('lose_coins_min');
        var lose_coins_max = button.data('lose_coins_max');

        var modal = $(this);
        modal.find('#level_id').val(id);
        modal.find('#edit_level_number').val(level_number);
        modal.find('#edit_points_required').val(points_required);
        modal.find('#existing_image_url').val(image_url);
        modal.find('#edit_win_coins_min').val(win_coins_min);
        modal.find('#edit_win_coins_max').val(win_coins_max);
        modal.find('#edit_lose_coins_min').val(lose_coins_min);
        modal.find('#edit_lose_coins_max').val(lose_coins_max);
        if (image_url) {
            modal.find('#current_image').attr('src', '../' + image_url).show();
        } else {
            modal.find('#current_image').hide();
        }
    });
</script>
</body>
</html>