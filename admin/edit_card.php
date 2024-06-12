<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../include/db_connect.php';

if (isset($_GET['id'])) {
    $card_id = $_GET['id'];
    $sql = "SELECT * FROM cards WHERE id = '$card_id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $card = $result->fetch_assoc();
    } else {
        echo "Card not found.";
        exit();
    }
} else {
    echo "Invalid card ID.";
    exit();
}

// Recupera le categorie
$categories_sql = "SELECT * FROM card_categories";
$categories_result = $conn->query($categories_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    $type = $_POST['type']; // Nuovo campo per il tipo di carta
    $image_url = $card['image_url'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../immagini_carte/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = "immagini_carte/" . basename($_FILES["image"]["name"]);
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    $sql = "UPDATE cards SET name='$name', description='$description', power_top='$power_top', power_top_right='$power_top_right', power_right='$power_right', power_bottom_right='$power_bottom_right', power_bottom='$power_bottom', power_bottom_left='$power_bottom_left', power_left='$power_left', power_top_left='$power_top_left', image_url='$image_url', type='$type' WHERE id='$card_id'";
    
    if ($conn->query($sql) === TRUE) {
        echo "Card updated successfully.";
    } else {
        echo "Error updating card: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Card</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1>Edit Card</h1>
    <?php include 'menu.php'; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $card['name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" required><?php echo $card['description']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="power_top">Top:</label>
            <input type="number" class="form-control" id="power_top" name="power_top" value="<?php echo $card['power_top']; ?>" required>
        </div>
        <div class="form-group">
            <label for="power_top_right">Top-Right:</label>
            <input type="number" class="form-control" id="power_top_right" name="power_top_right" value="<?php echo $card['power_top_right']; ?>" required>
        </div>
        <div class="form-group">
            <label for="power_right">Right:</label>
            <input type="number" class="form-control" id="power_right" name="power_right" value="<?php echo $card['power_right']; ?>" required>
        </div>
        <div class="form-group">
            <label for="power_bottom_right">Bottom-Right:</label>
            <input type="number" class="form-control" id="power_bottom_right" name="power_bottom_right" value="<?php echo $card['power_bottom_right']; ?>" required>
        </div>
        <div class="form-group">
            <label for="power_bottom">Bottom:</label>
            <input type="number" class="form-control" id="power_bottom" name="power_bottom" value="<?php echo $card['power_bottom']; ?>" required>
        </div>
        <div class="form-group">
            <label for="power_bottom_left">Bottom-Left:</label>
            <input type="number" class="form-control" id="power_bottom_left" name="power_bottom_left" value="<?php echo $card['power_bottom_left']; ?>" required>
        </div>
        <div class="form-group">
            <label for="power_left">Left:</label>
            <input type="number" class="form-control" id="power_left" name="power_left" value="<?php echo $card['power_left']; ?>" required>
        </div>
        <div class="form-group">
            <label for="power_top_left">Top-Left:</label>
            <input type="number" class="form-control" id="power_top_left" name="power_top_left" value="<?php echo $card['power_top_left']; ?>" required>
        </div>
        <div class="form-group">
            <label for="type">Category:</label>
            <select class="form-control" id="type" name="type">
                <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <option value="<?php echo $category['name']; ?>" <?php if ($card['type'] == $category['name']) echo 'selected'; ?>><?php echo $category['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="image">Image:</label>
            <input type="file" class="form-control" id="image" name="image">
            <?php if ($card['image_url']): ?>
                <img src="<?php echo '../' . $card['image_url']; ?>" alt="Card Image" style="width: 100px; height: auto;">
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
</body>
</html>
