<?php
session_start();
include '../config.php';

if (!isset($_SESSION['moderator_id'])) {
    header("Location: ../login.php");
    exit();
}

try {
    $stmt = $conn->query("
        SELECT c.id, c.comment, c.created_time, u.first_name, u.last_name, t.name AS tournament_name
        FROM comments c
        JOIN users u ON c.userID = u.id
        JOIN tournaments t ON c.tournamentID = t.id
        ORDER BY c.created_time DESC
    ");
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}

// Yorum silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment_id'])) {
    $commentId = $_POST['delete_comment_id'];

    try {
        $deleteStmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
        $deleteStmt->execute([$commentId]);
        header("Location: moderate_comments.php");
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
    <title>Yorumları Yönet</title>
    <link rel="stylesheet" href="moderate_comments.css">
</head>
<body>
<?php include 'moderator_navbar.php'; ?>

    <h1>Yorumları Yönet</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <table class="comments-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kullanıcı</th>
                <th>Turnuva</th>
                <th>Yorum</th>
                <th>Yayınlanma Tarihi</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $comment): ?>
                <tr>
                    <td><?= htmlspecialchars($comment['id']) ?></td>
                    <td><?= htmlspecialchars($comment['first_name'] . " " . $comment['last_name']) ?></td>
                    <td><?= htmlspecialchars($comment['tournament_name']) ?></td>
                    <td><?= htmlspecialchars($comment['comment']) ?></td>
                    <td><?= (new DateTime($comment['created_time']))->format('d-m-Y H:i') ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="delete_comment_id" value="<?= $comment['id'] ?>">
                            <button type="submit" class="btn-delete">Sil</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="moderator_dashboard.php" class="back-link">Geri Dön</a>
</body>
</html>
