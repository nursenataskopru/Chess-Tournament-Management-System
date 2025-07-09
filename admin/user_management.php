<?php
session_start();
include '../config.php'; 

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

try {
    // Tüm kullanıcı bilgilerini çekme sorgusu
    $query = $conn->query("SELECT id, email, first_name, last_name, birth_date, ukd, city, phone, gender, education_level, role, profile_photo FROM users");
    $users = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Yönetimi</title>
    <link rel="stylesheet" href="user_management.css">
</head>

<body>
    <?php include 'admin_navbar.php'; ?>

    <h1>Kullanıcı Yönetimi</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>E-posta</th>
            <th>Ad</th>
            <th>Soyad</th>
            <th>Doğum Tarihi</th>
            <th>UKD</th>
            <th>Şehir</th>
            <th>Telefon</th>
            <th>Cinsiyet</th>
            <th>Eğitim Seviyesi</th>
            <th>Rol</th>
            <th>Profil Fotoğrafı</th>
            <th>İşlem</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['first_name']) ?></td>
                <td><?= htmlspecialchars($user['last_name']) ?></td>
                <td><?= htmlspecialchars($user['birth_date']) ?></td>
                <td><?= htmlspecialchars($user['ukd']) ?></td>
                <td><?= htmlspecialchars($user['city']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>
                <td><?= htmlspecialchars($user['gender']) ?></td>
                <td><?= htmlspecialchars($user['education_level']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td>
                    <img src="../images/<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profil Fotoğrafı" style="width:50px; height:50px; border-radius:50%;">
                </td>
                <td>
                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn-edit">Düzenle</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <a href="admin_dashboard.php">Geri Dön</a>
</body>

</html>