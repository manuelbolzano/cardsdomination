<?php
include '../include/db_connect.php';

function update_all_user_levels($conn) {
    $sql = "SELECT id, punti FROM users";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $user_id = $row['id'];
        $punti = $row['punti'];

        $level_sql = "SELECT level_number FROM levels WHERE points_required <= ? ORDER BY points_required DESC LIMIT 1";
        $level_stmt = $conn->prepare($level_sql);
        $level_stmt->bind_param("i", $punti);
        $level_stmt->execute();
        $level_stmt->bind_result($level_number);
        $level_stmt->fetch();
        $level_stmt->close();

        if (isset($level_number)) {
            $update_sql = "UPDATE users SET level = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $level_number, $user_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }
}

update_all_user_levels($conn);

$conn->close();

header("Location: manage_levels.php");
exit();
?>
