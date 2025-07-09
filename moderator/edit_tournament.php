<?php
session_start();
include '../config.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Turnuva ID gerekli.");
}

$stmt = $conn->prepare("SELECT * FROM tournaments WHERE id = ?");
$stmt->execute([$id]);
$tournament = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $details = $_POST['details'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("UPDATE tournaments SET name = ?, details = ?, date = ? WHERE id = ?");
    $stmt->execute([$name, $details, $date, $id]);

    header("Location: moderate_tournaments.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="edit_tournament.css">

    <title>Turnuva Düzenle</title>
</head>
<body>
    <form method="POST">
        <label for="name">Turnuva Adı:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($tournament['name']) ?>" required>
        
        <label for="details">Detaylar:</label>
        <textarea id="details" name="details"><?= htmlspecialchars($tournament['details']) ?></textarea>
        
        <label for="date">Tarih:</label>
        <input type="datetime-local" id="date" name="date" value="<?= htmlspecialchars($tournament['date']) ?>" >
        
        <button type="submit">Kaydet</button>
    </form>
</body>
</html>
