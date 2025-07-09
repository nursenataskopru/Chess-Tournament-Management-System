<?php
session_start();
include '../config.php'; 

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php"); 
    exit();
}

$adminName = htmlspecialchars($_SESSION['admin_name']);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css"> 
</head>

<body>
    <?php include 'admin_navbar.php'; ?>

    <header>
        <h1>Admin Dashboard</h1>
    </header>
    <main>
        <p>Hoşgeldiniz, <strong><?= $adminName ?></strong>!</p>
        <ul class="menu">
            <li><a href="user_management.php">Kullanıcı Yönetimi</a></li>
            <li><a href="add_tournament.php">Turnuva Ekle</a></li>
            <li><a href="tournament_list.php">Turnuva Düzenle</a></li>
            <li><a href="../logout.php">Çıkış Yap</a></li>
        </ul>
    </main>
</body>

</html>