<?php
session_start();
include 'config.php';
function makeLinksClickable($text)
{
    $regex = '/(https?:\/\/[^\s]+)/i';
    return preg_replace($regex, '<a href="$1" target="_blank">$1</a>', htmlspecialchars($text));
}
// Turnuva ID'si kontrolü
if (!isset($_GET['id'])) {
    die("Turnuva ID gerekli.");
}

try {
    // Turnuva bilgilerini veritabanından çek
    $stmt = $conn->prepare("SELECT * FROM tournaments WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tournament) {
        die("Turnuva bulunamadı.");
    }

    // Turnuvaya katılanları çek
    $participantsStmt = $conn->prepare("
        SELECT u.id, u.first_name, u.last_name, u.email, u.city, u.ukd
        FROM participants p
        JOIN users u ON p.userID = u.id
        WHERE p.tournamentID = ?
    ");
    $participantsStmt->execute([$_GET['id']]);
    $participants = $participantsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Kullanıcının turnuvaya başvurup başvurmadığını kontrol et
    $userAlreadyJoined = false;
    if (isset($_SESSION['userID'])) {
        $checkStmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM participants 
            WHERE tournamentID = ? AND userID = ?
        ");
        $checkStmt->execute([$_GET['id'], $_SESSION['userID']]);
        $userAlreadyJoined = $checkStmt->fetchColumn() > 0;
    }

    // Turnuvaya başvuru işlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_tournament'])) {
        if (!isset($_SESSION['userID'])) {
            header("Location: login.php");
            exit();
        }

        if (!$userAlreadyJoined) {
            $insertStmt = $conn->prepare("
                INSERT INTO participants (tournamentID, userID) VALUES (?, ?)
            ");
            $insertStmt->execute([$_GET['id'], $_SESSION['userID']]);
            $successMessage = "Turnuvaya başarıyla başvurdunuz!";
            header("Location: tournament_details.php?id=" . $_GET['id']);
            exit();
        } else {
            $errorMessage = "Zaten bu turnuvaya başvurdunuz.";
        }
    }

    // Yorumları çek
    $commentsStmt = $conn->prepare("
        SELECT c.comment, c.created_time, u.first_name, u.last_name, u.profile_photo
        FROM comments c
        JOIN users u ON c.userID = u.id
        WHERE c.tournamentID = ?
        ORDER BY c.created_time DESC
    ");
    $commentsStmt->execute([$_GET['id']]);
    $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Yorum ekleme işlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        if (!isset($_SESSION['userID'])) {
            header("Location: login.php");
            exit();
        }

        $comment = trim($_POST['comment']);
        if (!empty($comment)) {
            $insertStmt = $conn->prepare("
                INSERT INTO comments (tournamentID, userID, comment) VALUES (?, ?, ?)
            ");
            $insertStmt->execute([$_GET['id'], $_SESSION['userID'], $comment]);
            $successMessage = "Yorumunuz başarıyla eklendi!";
            header("Location: tournament_details.php?id=" . $_GET['id']);
            exit();
        } else {
            $errorMessage = "Yorum boş olamaz.";
        }
    }
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tournament['name']); ?> - Turnuva Detayları</title>
    <link rel="stylesheet" href="tournament_details.css">
</head>

<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1><?php echo htmlspecialchars($tournament['name']); ?></h1>
        <div class="tournament-detail">
            <div class="tournament-img">
                <img src="images/<?php echo htmlspecialchars($tournament['image_url']); ?>" alt="Turnuva Görseli">
            </div>
            <div class="tournament-description">
                <small class="tournament-date">Tarih:
                    <?php
                    if ($tournament['date'] instanceof DateTime) {
                        echo htmlspecialchars($tournament['date']->format('d-m-Y H:i'));
                    } else {
                        echo htmlspecialchars((new DateTime($tournament['date']))->format('d-m-Y H:i'));
                    }
                    ?>
                </small>
                <p><?php echo makeLinksClickable($tournament['description']); ?></p>
                <p><?php echo makeLinksClickable($tournament['details']); ?></p>


            </div>
        </div>

        <!-- Katılımcılar -->
        <h2>Turnuvaya Kayıtlı Katılımcılar</h2>
        <?php if (count($participants) > 0): ?>
            <table class="participants-table">
                <thead>
                    <tr>
                        <th>Ad Soyad</th>
                        <th>Email</th>
                        <th>UKD</th>
                        <th>Şehir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants as $participant): ?>
                        <tr class="<?php echo (isset($_SESSION['userID']) && $_SESSION['userID'] == $participant['id']) ? 'highlight' : ''; ?>">
                            <td><?php echo htmlspecialchars($participant['first_name'] . " " . $participant['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($participant['email']); ?></td>
                            <td><?php echo htmlspecialchars($participant['ukd'] ?: 'Bilinmiyor'); ?></td>
                            <td><?php echo htmlspecialchars($participant['city'] ?: 'Şehir Bilgisi Yok'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Henüz kayıtlı bir katılımcı bulunmamaktadır.</p>
        <?php endif; ?>

        <!-- Turnuvaya Başvuru -->
        <h2>Turnuvaya Başvur</h2>
        <?php if (isset($successMessage)): ?>
            <p style="color: green;"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>
        <?php if (isset($errorMessage)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <?php if (!isset($_SESSION['userID'])): ?>
            <p>Başvurmak için <a href="register.php">Kayıt Olun</a> veya <a href="login.php">Giriş Yapın</a>.</p>
        <?php elseif ($userAlreadyJoined): ?>
            <p>Zaten bu turnuvaya başvurdunuz.</p>
        <?php else: ?>
            <form method="POST">
                <button type="submit" name="join_tournament" class="btn-join">Başvur</button>
            </form>
        <?php endif; ?>

        <!-- Yorumlar -->
        <h2>Yorumlar</h2>
        <?php if (count($comments) > 0): ?>
            <div class="comments-section">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="profile-photo">
                            <img src="images/<?php echo htmlspecialchars($comment['profile_photo'] ?: 'defaultProfilePhoto.png'); ?>" alt="Profil Fotoğrafı">
                        </div>
                        <div class="comment-content">
                            <p><strong><?php echo htmlspecialchars($comment['first_name'] . " " . $comment['last_name']); ?>:</strong></p>
                            <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                            <small>Yayınlanma Tarihi: <?php echo htmlspecialchars((new DateTime($comment['created_time']))->format('d-m-Y H:i')); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Henüz yorum yapılmamış. İlk yorumu siz yapın!</p>
        <?php endif; ?>

        <!-- Yorum Yapma -->
        <h2>Yorum Yap</h2>
        <?php if (!isset($_SESSION['userID'])): ?>
            <p>Yorum yapmak için <a href="register.php">Kayıt Olun</a> veya <a href="login.php">Giriş Yapın</a>.</p>
        <?php else: ?>
            <form method="POST" class="comment-form">
                <textarea name="comment" rows="5" placeholder="Yorumunuzu buraya yazın..." required></textarea>
                <button type="submit" class="btn-submit">Gönder</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>