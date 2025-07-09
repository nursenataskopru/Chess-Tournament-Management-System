<?php
session_start();
include '../config.php'; // Veritabanı bağlantısı

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Turnuva ID'sini al
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Turnuva ID'si bulunamadı!");
}

// Mevcut turnuva bilgilerini çek
try {
    $stmt = $conn->prepare("SELECT * FROM tournaments WHERE id = ?");
    $stmt->execute([$id]);
    $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tournament) {
        die("Turnuva bulunamadı!");
    }
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $details = $_POST['details'];
    $description = $_POST['description'];
    $image_url = $tournament['image_url']; // Varsayılan olarak mevcut resim

    // Resim yükleme işlemi
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

    if (!isset($error)) {
        try {
            $stmt = $conn->prepare("UPDATE tournaments SET name = ?, details = ?, description = ?, image_url = ? WHERE id = ?");
            $stmt->execute([$name, $details, $description, $image_url, $id]);
            header("Location: tournament_list.php"); 
            exit();
        } catch (PDOException $e) {
            $error = "Güncelleme sırasında bir hata oluştu: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnuva Düzenle</title>
    <link rel="stylesheet" href="edit_tournament.css">
</head>

<body>
    <?php include 'admin_navbar.php'; ?>

    <h1>Turnuva Düzenle</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <label for="name">Turnuva Adı:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($tournament['name']) ?>" required>

        <label for="details">Detaylar:</label>
        <textarea id="details" name="details" required><?= htmlspecialchars($tournament['details']) ?></textarea>

        <label for="description">Açıklama:</label>
        <textarea id="description" name="description"><?= htmlspecialchars($tournament['description']) ?></textarea>

        <label for="image">Turnuva Resmi:</label>
        <input type="file" id="image" name="image">

        <!-- Mevcut resmi göster -->
        <?php if ($tournament['image_url']): ?>
            <div class="preview-container">
                <img src="../images/<?= htmlspecialchars($tournament['image_url']) ?>" alt="Turnuva Resmi" style="width:100px; height:100px; object-fit:cover; border-radius:10px;">
            </div>
        <?php endif; ?>

        <button type="submit">Güncelle</button>
    </form>
    <a href="tournament_list.php">Geri Dön</a>
</body>

</html>