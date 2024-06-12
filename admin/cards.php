<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../include/db_connect.php';

$sql = "SELECT * FROM cards";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cards</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1>Cards</h1>
    <?php include 'menu.php'; ?>
    <a href="add_card.php" class="btn btn-primary mb-3">Add New Card</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>⬆️</th>
                <th>↗️</th>
                <th>➡️</th>
                <th>↘️</th>
                <th>⬇️</th>
                <th>↙️</th>
                <th>⬅️</th>
                <th>↖️</th>
                <th>Image</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><a href="edit_card.php?id=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['power_top']; ?></td>
                    <td><?php echo $row['power_top_right']; ?></td>
                    <td><?php echo $row['power_right']; ?></td>
                    <td><?php echo $row['power_bottom_right']; ?></td>
                    <td><?php echo $row['power_bottom']; ?></td>
                    <td><?php echo $row['power_bottom_left']; ?></td>
                    <td><?php echo $row['power_left']; ?></td>
                    <td><?php echo $row['power_top_left']; ?></td>
                    <td><img src="../<?php echo $row['image_url']; ?>" alt="<?php echo $row['name']; ?>" style="width: 50px;"></td>
                    <td><?php echo $row['type']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
