<?php
session_start();
include '../config.php';

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Kullanıcı ID'si bulunamadı!");
}

// Kullanıcı bilgilerini çekme kısmı
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Kullanıcı bulunamadı!");
    }
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birth_date = $_POST['birth_date'];
    $ukd = $_POST['ukd'];
    $city = $_POST['city'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $education_level = $_POST['education_level'];
    $role = $_POST['role'];

    // Fotoğraf yükleme işlemi
    $profile_photo = $user['profile_photo']; // Varsayılan olarak mevcut fotoğraf
    if (!empty($_FILES['profile_photo']['name'])) {
        $target_dir = "../images/";
        $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Dosya türü kontrolü
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                $profile_photo = basename($_FILES["profile_photo"]["name"]); // Yüklenen dosya adı
            } else {
                $error = "Fotoğraf yüklenirken bir hata oluştu.";
            }
        } else {
            $error = "Yalnızca JPG, JPEG, PNG ve GIF dosyaları yüklenebilir.";
        }
    }

    try {
        $stmt = $conn->prepare("
            UPDATE users 
            SET email = ?, first_name = ?, last_name = ?, birth_date = ?, ukd = ?, city = ?, phone = ?, gender = ?, education_level = ?, role = ?, profile_photo = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $email,
            $first_name,
            $last_name,
            $birth_date,
            $ukd,
            $city,
            $phone,
            $gender,
            $education_level,
            $role,
            $profile_photo,
            $id
        ]);
        header("Location: user_management.php");
        exit();
    } catch (PDOException $e) {
        $error = "Güncelleme sırasında bir hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Düzenle</title>
    <link rel="stylesheet" href="edit_user.css">
</head>

<body>
    <?php include 'admin_navbar.php'; ?>

    <h1>Kullanıcı Düzenle</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <label for="email">E-posta:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

        <label for="first_name">Ad:</label>
        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>">

        <label for="last_name">Soyad:</label>
        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>">

        <label for="birth_date">Doğum Tarihi:</label>
        <input type="date" id="birth_date" name="birth_date" value="<?= htmlspecialchars($user['birth_date']) ?>">

        <label for="ukd">UKD:</label>
        <input type="number" id="ukd" name="ukd" value="<?= htmlspecialchars($user['ukd']) ?>">

        <label for="city">Şehir:</label>
        <input type="text" id="city" name="city" value="<?= htmlspecialchars($user['city']) ?>">

        <label for="phone">Telefon:</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

        <label for="gender">Cinsiyet:</label>
        <select id="gender" name="gender">
            <option value="Erkek" <?= $user['gender'] === 'Erkek' ? 'selected' : '' ?>>Erkek</option>
            <option value="Kadın" <?= $user['gender'] === 'Kadın' ? 'selected' : '' ?>>Kadın</option>
            <option value="Diğer" <?= $user['gender'] === 'Diğer' ? 'selected' : '' ?>>Diğer</option>
        </select>

        <label for="education_level">Eğitim Seviyesi:</label>
        <input type="text" id="education_level" name="education_level" value="<?= htmlspecialchars($user['education_level']) ?>">

        <label for="role">Rol:</label>
        <select id="role" name="role">
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Kullanıcı</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="moderator" <?= $user['role'] === 'moderator' ? 'selected' : '' ?>>Moderator</option>
        </select>

        <label for="profile_photo">Profil Fotoğrafı:</label>
        <input type="file" id="profile_photo" name="profile_photo" onchange="previewImage(event)">

        <!-- Fotoğraf Önizleme -->
        <div class="preview-container">
            <img id="profile_preview" src="../images/<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profil Fotoğrafı">
        </div>

        <button type="submit">Güncelle</button>
    </form>
    <a href="user_management.php">Geri Dön</a>

    <script>
        function previewImage(event) {
            const fileInput = event.target;
            const previewImage = document.getElementById('profile_preview');

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    </script>
    </form>
    <a href="user_management.php">Geri Dön</a>
</body>

</html>