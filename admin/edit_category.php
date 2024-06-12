<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../include/db_connect.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    $name = $_POST['name'];
    $icon = $_POST['existing_icon'];

    // Gestione del caricamento dell'icona
    if (isset($_FILES['icon']) && $_FILES['icon']['error'] == 0) {
        $upload_dir = '../icons/';
        $icon = $upload_dir . basename($_FILES['icon']['name']);
        move_uploaded_file($_FILES['icon']['tmp_name'], $icon);
    }

    $sql = "UPDATE categories SET name = ?, icon = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $icon, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_categories.php");
    exit();
}

// Recupera i dati della categoria
$sql = "SELECT * FROM categories WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifica Categoria</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1>Modifica Categoria</h1>
    <?php include 'menu.php'; ?>

    <form action="edit_category.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Nome:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="icon">Icona:</label>
            <input type="file" class="form-control" id="icon" name="icon">
            <input type="hidden" name="existing_icon" value="<?php echo htmlspecialchars($category['icon']); ?>">
            <img src="<?php echo htmlspecialchars($category['icon']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" style="width: 50px;">
        </div>
        <button type="submit" class="btn btn-primary" name="update_category">Aggiorna Categoria</button>
    </form>
</div>
</body>
</html>
