<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Kullanıcıyı email ile sorgulama
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kullanıcı şifresini doğrulama
        if ($user && password_verify($password, $user['password'])) {
            // Kullanıcının rolüne göre yönlendirme
            if ($user['role'] === 'admin') {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['first_name'] . ' ' . $user['last_name'];
                header("Location: admin/admin_dashboard.php");
            } elseif ($user['role'] === 'moderator') {
                $_SESSION['moderator_id'] = $user['id'];
                $_SESSION['moderator_name'] = $user['first_name'] . ' ' . $user['last_name'];
                header("Location: moderator/moderator_dashboard.php");
            } else {
                $_SESSION['userID'] = $user['id'];
                $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name'];
                header("Location: profile.php");
            }
            exit;
        } else {
            $error = "Geçersiz e-posta veya şifre!";
        }
    } catch (PDOException $e) {
        $error = "Bir hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Giriş Yap</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Email adresinizi girin">

            <label for="password">Şifre:</label>
            <input type="password" id="password" name="password" required placeholder="Şifrenizi girin">

            <button type="submit">Giriş Yap</button>
        </form>
        <div class="register-link">
            <p>Hesabın yok mu?</p>
            <a href="register.php" class="register-button">Kayıt Ol</a>
        </div>
    </div>
</body>

</html>