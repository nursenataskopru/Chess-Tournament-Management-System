<?php
session_start();
include '../config.php';

// Moderatör oturum kontrolü
if (!isset($_SESSION['moderator_id'])) {
    header("Location: ../login.php");
    exit();
}

// Turnuva seçimi kontrolü
$tournament_id = $_GET['tournament_id'] ?? null;

if (!$tournament_id) {
    die("Turnuva ID'si belirtilmedi!");
}

// Turnuvaya katılan kişileri çek
try {
    $stmt = $conn->prepare("
        SELECT users.id, users.first_name, users.last_name, users.email, participants.id AS participant_id
        FROM users
        INNER JOIN participants ON users.id = participants.userID
        WHERE participants.tournamentID = ?
    ");
    $stmt->execute([$tournament_id]);
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}

// Kullanıcı ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user_id'])) {
    $add_user_id = $_POST['add_user_id'];

    try {
        $stmt = $conn->prepare("
            INSERT INTO participants (tournamentID, userID) VALUES (?, ?)
        ");
        $stmt->execute([$tournament_id, $add_user_id]);
        $success = "Kullanıcı başarıyla turnuvaya eklendi.";
        header("Location: moderate_users.php?tournament_id=" . $tournament_id);
        exit();
    } catch (PDOException $e) {
        $error = "Kullanıcı eklenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Tüm kullanıcıları çek (turnuvaya eklenmemiş olanlar, admin ve moderatör hariç)
try {
    $stmt = $conn->prepare("
        SELECT id, first_name, last_name, email
        FROM users
        WHERE role = 'user'
        AND id NOT IN (
            SELECT userID
            FROM participants
            WHERE tournamentID = ?
        )
    ");
    $stmt->execute([$tournament_id]);
    $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnuva Katılımcılarını Yönet</title>
    <link rel="stylesheet" href="moderate_users.css">
</head>
<body>
<?php include 'moderator_navbar.php'; ?>

    <h1>Turnuva Katılımcılarını Yönet</h1>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <h2>Turnuva Katılımcıları</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ad</th>
                <th>Soyad</th>
                <th>Email</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($participants as $participant): ?>
                <tr>
                    <td><?= htmlspecialchars($participant['id']) ?></td>
                    <td><?= htmlspecialchars($participant['first_name']) ?></td>
                    <td><?= htmlspecialchars($participant['last_name']) ?></td>
                    <td><?= htmlspecialchars($participant['email']) ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="remove_participant_id" value="<?= $participant['participant_id'] ?>">
                            <button type="submit" class="btn-delete">Çıkar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Yeni Katılımcı Ekle</h2>
    <?php if (count($all_users) > 0): ?>
        <form method="POST">
            <label for="add_user_id">Kullanıcı Seç:</label>
            <select id="add_user_id" name="add_user_id" required>
                <option value="">Bir kullanıcı seçin</option>
                <?php foreach ($all_users as $user): ?>
                    <option value="<?= $user['id'] ?>">
                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['email'] . ')') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Ekle</button>
        </form>
    <?php else: ?>
        <p>Tüm kullanıcılar zaten turnuvaya kayıtlı.</p>
    <?php endif; ?>

    <a href="select_tournament.php" class="btn-back">Başka Turnuva Seç</a>
</body>
</html>
