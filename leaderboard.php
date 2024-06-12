<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'include/db_connect.php';

$sql = "SELECT username, punti, profile_image FROM users ORDER BY punti DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en" class="hof">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Classifica</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="classificabox">
<?php include 'include/menu.php'; ?>
<div class="container">
    <h1>Classifica</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Punti</th>
                <th>Avatar</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $position = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $position . "</td>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>" . htmlspecialchars($row['punti']) . "</td>";
                echo "<td>";
                if ($row['profile_image']) {
                    echo "<img src='" . htmlspecialchars($row['profile_image']) . "' alt='Profile Image' style='max-width: 50px;'>";
                }
                echo "</td>";
                echo "</tr>";
                $position++;
            }
            ?>
        </tbody>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>