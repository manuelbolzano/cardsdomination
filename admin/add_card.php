<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../include/db_connect.php';

// Recupera le categorie
$categories_sql = "SELECT * FROM card_categories";
$categories_result = $conn->query($categories_sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Per debug
    echo "<pre>";
    print_r($_POST);
    print_r($_FILES);
    echo "</pre>";

    $name = $_POST['name'];
    $description = $_POST['description'];
    $power_top = $_POST['power_top'];
    $power_top_right = $_POST['power_top_right'];
    $power_right = $_POST['power_right'];
    $power_bottom_right = $_POST['power_bottom_right'];
    $power_bottom = $_POST['power_bottom'];
    $power_bottom_left = $_POST['power_bottom_left'];
    $power_left = $_POST['power_left'];
    $power_top_left = $_POST['power_top_left'];
    $type = $_POST['type']; // Campo per il tipo di carta
    $image_url = '';

    $target_dir = "../immagini_carte/";

    // Check if the directory exists, if not, create it
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0755, true)) {
            echo "Failed to create directory: " . $target_dir;
            exit();
        }
    }

    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == UPLOAD_ERR_OK) {
        $target_file = $target_dir . basename($_FILES["image_url"]["name"]);
        if (move_uploaded_file($_FILES["image_url"]["tmp_name"], $target_file)) {
            $image_url = "immagini_carte/" . basename($_FILES["image_url"]["name"]);
        } else {
            echo "Error uploading the file.";
            exit();
        }
    } else {
        echo "No file uploaded or upload error.";
    }

    $sql = "INSERT INTO cards (name, description, power_top, power_top_right, power_right, power_bottom_right, power_bottom, power_bottom_left, power_left, power_top_left, image_url, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        exit();
    }

    $stmt->bind_param("ssiiiiiiisss", $name, $description, $power_top, $power_top_right, $power_right, $power_bottom_right, $power_bottom, $power_bottom_left, $power_left, $power_top_left, $image_url, $type);

    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }

    $stmt->close();
    header("Location: cards.php");
    exit();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Card</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1>Add New Card</h1>
    <?php include 'menu.php'; ?>
    <form action="add_card.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" class="form-control" id="description" name="description" required>
        </div>
        <div class="form-group">
            <label for="power_top">Top</label>
            <input type="number" class="form-control" id="power_top" name="power_top" required>
        </div>
        <div class="form-group">
            <label for="power_top_right">Top-Right</label>
            <input type="number" class="form-control" id="power_top_right" name="power_top_right" required>
        </div>
        <div class="form-group">
            <label for="power_right">Right</label>
            <input type="number" class="form-control" id="power_right" name="power_right" required>
        </div>
        <div class="form-group">
            <label for="power_bottom_right">Bottom-Right</label>
            <input type="number" class="form-control" id="power_bottom_right" name="power_bottom_right" required>
        </div>
        <div class="form-group">
            <label for="power_bottom">Bottom</label>
            <input type="number" class="form-control" id="power_bottom" name="power_bottom" required>
        </div>
        <div class="form-group">
            <label for="power_bottom_left">Bottom-Left</label>
            <input type="number" class="form-control" id="power_bottom_left" name="power_bottom_left" required>
        </div>
        <div class="form-group">
            <label for="power_left">Left</label>
            <input type="number" class="form-control" id="power_left" name="power_left" required>
        </div>
        <div class="form-group">
            <label for="power_top_left">Top-Left</label>
            <input type="number" class="form-control" id="power_top_left" name="power_top_left" required>
        </div>
        <div class="form-group">
            <label for="type">Category</label>
            <select class="form-control" id="type" name="type">
                <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <option value="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="image_url">Image</label>
            <input type="file" class="form-control" id="image_url" name="image_url" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Card</button>
    </form>
</div>
</body>
</html>
