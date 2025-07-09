<?php
session_start();
include '../config.php';

// Moderatör oturum kontrolü
if (!isset($_SESSION['moderator_id'])) {
    header("Location: ../login.php");
    exit();
}

// Turnuvaları çek
try {
    $stmt = $conn->query("SELECT id, name FROM tournaments");
    $tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnuva Seç</title>
    <link rel="stylesheet" href="select_tournament.css">
</head>
<body>
<?php include 'moderator_navbar.php'; ?>

    <h1>Turnuva Seç</h1>
    <div class="tournament-list">
        <?php if (count($tournaments) > 0): ?>
            <ul>
                <?php foreach ($tournaments as $tournament): ?>
                    <li>
                        <span class="tournament-name"><?= htmlspecialchars($tournament['name']) ?></span>
                        <form method="GET" action="moderate_users.php" style="display:inline;">
                            <input type="hidden" name="tournament_id" value="<?= $tournament['id'] ?>">
                            <button type="submit" class="btn-select">Seç</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Hiç turnuva bulunamadı.</p>
        <?php endif; ?>
    </div>
    <a href="moderator_dashboard.php" class="btn-back">Geri Dön</a>
</body>
</html>
