<?php
session_start();
include '../config.php'; 

// Moderatör oturum kontrolü
if (!isset($_SESSION['moderator_id'])) {
    header("Location: ../login.php");
    exit();
}

try {
    // Turnuvaları veritabanından çekme
    $stmt = $conn->query("SELECT id, name, date, details FROM tournaments ORDER BY date DESC");
    $tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}

// Turnuva silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];

    try {
        // İlgili turnuvaya ait katılımcı kayıtlarını silme
        $deleteParticipantsStmt = $conn->prepare("DELETE FROM participants WHERE tournamentID = ?");
        $deleteParticipantsStmt->execute([$deleteId]);

        // İlgili turnuvayı silme
        $stmt = $conn->prepare("DELETE FROM tournaments WHERE id = ?");
        $stmt->execute([$deleteId]);

        header("Location: moderate_tournaments.php"); 
        exit();
    } catch (PDOException $e) {
        $error = "Silme sırasında bir hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnuvaları Yönet</title>
    <link rel="stylesheet" href="moderate_tournaments.css">
</head>
<body>
<?php include 'moderator_navbar.php'; ?>

    <h1>Turnuvaları Yönet</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <table class="moderator-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ad</th>
                <th>Tarih</th>
                <th>Detaylar</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tournaments as $tournament): ?>
                <tr>
                    <td><?= htmlspecialchars($tournament['id']) ?></td>
                    <td><?= htmlspecialchars($tournament['name']) ?></td>
                    <td><?= (new DateTime($tournament['date']))->format('d-m-Y H:i') ?></td>
                    <td><?= htmlspecialchars($tournament['details']) ?></td>
                    <td>
    <div class="moderator-actions">
        <a href="edit_tournament.php?id=<?= $tournament['id'] ?>" class="btn-edit">Düzenle</a>
        <form method="POST" action="" style="display:inline;">
            <input type="hidden" name="delete_id" value="<?= $tournament['id'] ?>">
            <button type="submit" class="btn-delete">Sil</button>
        </form>
    </div>
</td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="moderator_dashboard.php" class="moderator-back-link">Geri Dön</a>
</body>
</html>
