<div class="navbar-container">
    <link rel="stylesheet" href="navbar.css">

    <div class="navbar-brand">
         <!-- Logo -->
         <a href="index.php">
            <img src="images/logo.png" alt="Site Logosu" class="navbar-logo">
            Satranç Turnuvaları
        </a>
    </div>
    <div class="navbar-links">
        <?php if (isset($_SESSION['userID'])): ?>
            <?php
            // Kullanıcı bilgilerini oturumdan alma
            $userProfile = $_SESSION['userProfile'];
            $navbarProfilePhoto = "images/" . htmlspecialchars($userProfile['profile_photo']);
            $userName = htmlspecialchars($userProfile['first_name'] . ' ' . $userProfile['last_name']);
            ?>
            <div class="navbar-profile">
                <!-- Profil Fotoğrafı -->
                <a href="profile.php">
                    <img src="<?php echo $navbarProfilePhoto; ?>" alt="Profil Fotoğrafı" class="navbar-profile-img">
                </a>

                <!-- Kullanıcı Adı -->
                <a href="profile.php" class="navbar-username"><?php echo $userName; ?></a>

                <!-- Çıkış Yap -->
                <a href="logout.php" class="navbar-btn navbar-logout-btn">Çıkış yap</a>
            </div>
        <?php else: ?>
            <a href="login.php" class="navbar-btn">Giriş Yap</a>
            <a href="register.php" class="navbar-btn">Kayıt ol</a>
        <?php endif; ?>
    </div>
</div>
