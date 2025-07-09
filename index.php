<?php
session_start();
include 'config.php';

try {
    $stmt = $conn->prepare("SELECT * FROM tournaments ORDER BY date ASC");
    $stmt->execute();
    $tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching tournaments: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satranç Turnuvaları</title>
    <link rel="stylesheet" href="index-style.css"> <!-- Index'e özel stil -->


</head>

<body class="index-page">
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- İçerik -->
    <div class="index-container">
        <h1>Satranç Turnuvaları</h1>
        <ul class="index-list-group">
            <?php foreach ($tournaments as $tournament): ?>
                <div class="index-list-group-item" onclick="location.href='tournament_details.php?id=<?php echo $tournament['id']; ?>'" style="cursor: pointer;">
                    <div class="tournament-item">
                        <!-- Görsel -->
                        <div class="tournament-img-container">
                            <img src="images/<?php echo htmlspecialchars($tournament['image_url']); ?>" alt="Turnuva Görseli" class="tournament-img">
                        </div>

                        <!-- Açıklamalar -->
                        <div class="tournament-details">
                            <small class="tournament-date">Tarih:
                                <?php
                                if ($tournament['date'] instanceof DateTime) {
                                    echo htmlspecialchars($tournament['date']->format('d-m-Y H:i'));
                                } else {
                                    echo htmlspecialchars((new DateTime($tournament['date']))->format('d-m-Y H:i'));
                                }
                                ?>
                            </small>
                            <h3>
                                <a href="tournament_details.php?id=<?php echo $tournament['id']; ?>">
                                    <?php echo htmlspecialchars($tournament['name']); ?>
                                </a>
                            </h3>
                            <p><?php echo htmlspecialchars($tournament['details']); ?></p>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </ul>
    </div>

</body>

</html>