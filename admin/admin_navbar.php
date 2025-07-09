<div class="navbar-container">
    <link rel="stylesheet" href="admin_navbar.css"> 


    <div class="navbar-brand">
        <a href="admin_dashboard.php">Admin Paneli</a>
    </div>

    <div class="navbar-links">
        <a href="user_management.php" class="navbar-btn">Kullanıcı Yönetimi</a>
        <a href="add_tournament.php" class="navbar-btn">Turnuva Ekle</a>
        <a href="tournament_list.php" class="navbar-btn">Turnuva Düzenle</a>

        <?php if (isset($_SESSION['admin_id'])): ?>
            <div class="navbar-profile">
               
                <a href="../logout.php" class="navbar-btn navbar-logout-btn">Çıkış Yap</a>
            </div>
        <?php endif; ?>
    </div>
</div>
