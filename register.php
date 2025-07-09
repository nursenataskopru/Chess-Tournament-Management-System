<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birth_date = $_POST['birth_date'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $gender = $_POST['gender'];
    $education_level = $_POST['education_level'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Kullanıcıyı veritabanına ekleme
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, birth_date, email, phone, city, gender, education_level, password) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$first_name, $last_name, $birth_date, $email, $phone, $city, $gender, $education_level, $password]);

    // Başarıyla kaydedildiğinde yönlendirme
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <title>Kayıt Ol</title>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1>Kayıt Ol</h1>
        <form method="POST">
            <label for="first_name">Ad:</label>
            <input type="text" id="first_name" name="first_name" required placeholder="Adınızı girin">
            
            <label for="last_name">Soyad:</label>
            <input type="text" id="last_name" name="last_name" required placeholder="Soyadınızı girin">
            
            <label for="birth_date">Doğum Tarihi:</label>
            <input type="date" id="birth_date" name="birth_date" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Email adresinizi girin">
            
            <label for="phone">Telefon:</label>
            <input type="tel" id="phone" name="phone" required placeholder="Telefon numaranızı girin">
            
            <label for="city">Şehir:</label>
            <input type="text" id="city" name="city" required placeholder="Yaşadığınız şehri girin">
            
            <label for="gender">Cinsiyet:</label>
            <select id="gender" name="gender" required>
                <option value="male">Erkek</option>
                <option value="female">Kadın</option>
                <option value="other">Diğer</option>
            </select>
            
            <label for="education_level">Eğitim Seviyesi:</label>
            <input type="text" id="education_level" name="education_level" required placeholder="Eğitim seviyenizi girin">
            
            <label for="password">Şifre:</label>
            <input type="password" id="password" name="password" required placeholder="Şifrenizi girin">
            
            <button type="submit">Kayıt Ol</button>
        </form>
    </div>
</body>
</html>
