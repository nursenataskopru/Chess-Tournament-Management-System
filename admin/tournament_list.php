<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

try {
    $stmt = $conn->query("SELECT id, name, date FROM tournaments");
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
    <title>Turnuva Listesi</title>
    <link rel="stylesheet" href="tournament_list.css">
</head>

<body>
    <?php include 'admin_navbar.php'; ?>

    <h1>Turnuva Listesi</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ad</th>
                <th>Tarih</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tournaments as $tournament): ?>
                <tr>
                    <td><?= htmlspecialchars($tournament['id']) ?></td>
                    <td><?= htmlspecialchars($tournament['name']) ?></td>
                    <td>
                        <?php
                        if ($tournament['date'] instanceof DateTime) {
                            echo htmlspecialchars($tournament['date']->format('d-m-Y H:i'));
                        } else {
                            echo htmlspecialchars((new DateTime($tournament['date']))->format('d-m-Y H:i'));
                        }
                        ?>
                    </td>

                    <td>
                        <a href="edit_tournament.php?id=<?= $tournament['id'] ?>">Düzenle</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="admin_dashboard.php">Geri Dön</a>
</body>

</html>