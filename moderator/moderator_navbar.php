<div class="moderator_navbar-container">
    <link rel="stylesheet" href="moderator_navbar.css"> <!-- Moderatör CSS dosyası -->

    <div class="moderator_navbar-brand">
        <a href="moderator_dashboard.php">Moderatör Paneli</a>
    </div>

    <div class="moderator_navbar-links">
        <a href="moderate_tournaments.php" class="moderator_navbar-btn">Turnuvaları Yönet</a>
        <a href="select_tournament.php" class="moderator_navbar-btn">Turnuva Katılımcılarını Yönet</a>
        <a href="moderate_comments.php" class="moderator_navbar-btn">Yorumları Yönet</a>


        <?php if (isset($_SESSION['moderator_id'])): ?>
            <a href="../logout.php" class="moderator_navbar-btn moderator_navbar-logout-btn">Çıkış Yap</a>
        <?php endif; ?>
    </div>
</div>
