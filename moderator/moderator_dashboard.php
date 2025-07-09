<?php
session_start();
include '../config.php';

// Moderatör oturum kontrolü
if (!isset($_SESSION['moderator_id'])) {
    header("Location: ../login.php");
    exit();
}

// Moderatör adını oturumdan al
$moderatorName = htmlspecialchars($_SESSION['moderator_name']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderatör Paneli</title>
    <link rel="stylesheet" href="moderator_dashboard.css">
</head>
<body>
<?php include 'moderator_navbar.php'; ?>

    <header>
        <h1>Moderatör Paneli</h1>
    </header>
    <main>
        <p>Hoşgeldiniz, <strong><?= $moderatorName ?></strong>!</p>
        <ul class="menu">
    <li><a href="moderate_tournaments.php">Turnuvaları Yönet</a></li>
    <li><a href="select_tournament.php">Turnuva Katılımcılarını Yönet</a></li>
    <li><a href="moderate_comments.php">Yorumları Yönet</a></li>
    <li><a href="../logout.php">Çıkış Yap</a></li>
</ul>
    </main>
</body>
</html>

