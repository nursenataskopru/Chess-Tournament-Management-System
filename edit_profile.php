<?php
session_start();
include 'config.php';

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

// Kullanıcı bilgilerini veritabanından çek
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['userID']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Kullanıcı bilgileri bulunamadı.";
    exit;
}

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birth_date = $_POST['birth_date'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $gender = $_POST['gender'];
    $education_level = $_POST['education_level'];
    $ukd = $_POST['ukd'];

    // Profil fotoğrafı yükleme işlemi
    $profilePhoto = $user['profile_photo'];
    if (!empty($_FILES['profile_photo']['name'])) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                $profilePhoto = basename($_FILES["profile_photo"]["name"]);
            } else {
                $error = "Profil fotoğrafı yüklenirken bir hata oluştu.";
            }
        } else {
            $error = "Yalnızca JPG, JPEG, PNG ve GIF dosyaları yüklenebilir.";
        }
    }

    if (!isset($error)) {
        // Veritabanını güncelle
        $stmt = $conn->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, birth_date = ?, email = ?, phone = ?, city = ?, gender = ?, education_level = ?, ukd = ?, profile_photo = ?
            WHERE id = ?
        ");
        $stmt->execute([$first_name, $last_name, $birth_date, $email, $phone, $city, $gender, $education_level, $ukd, $profilePhoto, $_SESSION['userID']]);

        // Profil sayfasına yönlendirme
        header("Location: profile.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profili Düzenle</title>
    <link rel="stylesheet" href="edit_profile.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="edit-profile-container">
        <h1>Profili Düzenle</h1>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="first_name">Ad:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

            <label for="last_name">Soyad:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

            <label for="birth_date">Doğum Tarihi:</label>
            <input type="date" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($user['birth_date']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="phone">Telefon:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

            <label for="city">Şehir:</label>
            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>">

            <label for="gender">Cinsiyet:</label>
            <select id="gender" name="gender" required>
                <option value="Erkek" <?php echo $user['gender'] === 'Erkek' ? 'selected' : ''; ?>>Erkek</option>
                <option value="Kadın" <?php echo $user['gender'] === 'Kadın' ? 'selected' : ''; ?>>Kadın</option>
                <option value="Diğer" <?php echo $user['gender'] === 'Diğer' ? 'selected' : ''; ?>>Diğer</option>
            </select>

            <label for="education_level">Eğitim Seviyesi:</label>
            <input type="text" id="education_level" name="education_level" value="<?php echo htmlspecialchars($user['education_level']); ?>">

            <label for="ukd">UKD:</label>
            <input type="number" id="ukd" name="ukd" value="<?php echo htmlspecialchars($user['ukd']); ?>">

            <label for="profile_photo">Profil Fotoğrafı:</label>
            <div class="preview-container">
                <img id="profile_preview" src="images/<?php echo htmlspecialchars($user['profile_photo'] ?: 'defaultProfilePhoto.png'); ?>" alt="Profil Fotoğrafı" class="preview-image">
            </div>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" onchange="previewImage(event)">

            <button type="submit">Güncelle</button>
        </form>

        <a href="profile.php" class="btn">Geri Dön</a>
    </div>

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
</body>

</html>