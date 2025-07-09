<?php
session_start();
include '../config.php'; 

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $details = $_POST['details'];
    $description = $_POST['description'];
    $image_url = null;

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = basename($_FILES["image"]["name"]);
            } else {
                $error = "Resim yüklenirken bir hata oluştu.";
            }
        } else {
            $error = "Yalnızca JPG, JPEG, PNG ve GIF dosyaları yüklenebilir.";
        }
    }

    if (!$error) {
        try {
            $stmt = $conn->prepare("INSERT INTO tournaments (name, details, description, image_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $details, $description, $image_url]);
            header("Location: tournament_list.php"); 
            exit();
        } catch (PDOException $e) {
            $error = "Veritabanına eklenirken bir hata oluştu: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnuva Ekle</title>
    <link rel="stylesheet" href="add_tournament.css">
</head>

<body>
    <?php include 'admin_navbar.php'; ?>

    <h1>Turnuva Ekle</h1>
    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <label for="name">Turnuva Adı:</label>
        <input type="text" id="name" name="name" required>

        <label for="details">Detaylar:</label>
        <textarea id="details" name="details" required></textarea>

        <label for="description">Açıklama:</label>
        <textarea id="description" name="description"></textarea>

        <label for="image">Turnuva Resmi:</label>
        <input type="file" id="image" name="image">

        <button type="submit">Turnuva Ekle</button>
    </form>
    <a href="admin_dashboard.php">Geri Dön</a>
</body>

</html>