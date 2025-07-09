<?php
session_start();
include 'config.php';

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

// Kullanıcı bilgilerini veritabanından çek
$stmt = $conn->prepare("
    SELECT first_name, last_name, birth_date, email, phone, city, gender, education_level, ukd, profile_photo 
    FROM users 
    WHERE id = ?
");
$stmt->execute([$_SESSION['userID']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Kullanıcı bilgileri bulunamadı.";
    exit;
}

// Kullanıcı bilgilerini oturuma kaydet
$_SESSION['userProfile'] = [
    'first_name' => $user['first_name'],
    'last_name' => $user['last_name'],
    'birth_date' => $user['birth_date'],
    'email' => $user['email'],
    'phone' => $user['phone'],
    'city' => $user['city'],
    'gender' => $user['gender'],
    'education_level' => $user['education_level'],
    'ukd' => $user['ukd'],
    'profile_photo' => $user['profile_photo'] ? $user['profile_photo'] : 'defaultProfilePhoto.png'
];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilim</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="profile-container">
        <h1>Profilim</h1>
        <div class="profile-info">
            <!-- Profil Fotoğrafı -->
            <img src="images/<?php echo htmlspecialchars($_SESSION['userProfile']['profile_photo']); ?>" alt="Profil Fotoğrafı" class="profile-picture">

            <!-- Kullanıcı Bilgileri -->
            <div class="profile-details">
                <p><strong>Ad:</strong> <?php echo htmlspecialchars($_SESSION['userProfile']['first_name']); ?></p>
                <p><strong>Soyad:</strong> <?php echo htmlspecialchars($_SESSION['userProfile']['last_name']); ?></p>
                <p><strong>Doğum Tarihi:</strong> <?php echo htmlspecialchars($_SESSION['userProfile']['birth_date']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['userProfile']['email']); ?></p>
                <p><strong>Telefon:</strong> <?php echo htmlspecialchars($_SESSION['userProfile']['phone']); ?></p>
                <p><strong>Şehir:</strong> <?php echo htmlspecialchars($_SESSION['userProfile']['city']); ?></p>
                <p><strong>Cinsiyet:</strong> <?php echo htmlspecialchars($_SESSION['userProfile']['gender']); ?></p>
                <p><strong>Eğitim Seviyesi:</strong> <?php echo htmlspecialchars($_SESSION['userProfile']['education_level']); ?></p>
                <p><strong>UKD:</strong> <?php echo htmlspecialchars($_SESSION['userProfile']['ukd']); ?></p>
            </div>
        </div>

        <!-- Profili Düzenle Butonu -->
        <a href="edit_profile.php" class="btn">Profili Düzenle</a>
    </div>
</body>
</html>
