<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../include/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = $_POST['category_name'];
    $category_icon = $_FILES['category_icon'];

    $target_dir = "../category_icons/";
    $target_file = $target_dir . basename($category_icon["name"]);
    move_uploaded_file($category_icon["tmp_name"], $target_file);

    $sql = "INSERT INTO card_categories (name, icon_url) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $category_name, $target_file);
    $stmt->execute();
    $stmt->close();
}

$sql = "SELECT * FROM card_categories";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1>Manage Categories</h1>
    <?php include 'menu.php'; ?>
    <form action="manage_categories.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="category_name">Category Name:</label>
            <input type="text" class="form-control" id="category_name" name="category_name" required>
        </div>
        <div class="form-group">
            <label for="category_icon">Category Icon:</label>
            <input type="file" class="form-control" id="category_icon" name="category_icon" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Category</button>
    </form>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Icon</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><img src="../<?php echo $row['icon_url']; ?>" alt="<?php echo $row['name']; ?>" style="width: 50px;"></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
