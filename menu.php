<?php
require_once 'session.php';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nawigacja</title>
    <link rel="stylesheet" href="style.css"> <!-- Załączamy plik CSS -->
</head>
<body>
<nav class="navbar">
    <ul class="navbar-list">
        <li class="navbar-item"><a href="index.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Strona Główna</a></li>
        <li class="navbar-item"><a href="rent.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'rent.php' ? 'active' : ''; ?>">Wynajem</a></li>
        <li class="navbar-item"><a href="buy.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'buy.php' ? 'active' : ''; ?>">Sprzedaż</a></li>
        <li class="navbar-item"><a href="contact.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">Kontakt</a></li>
        <li class="navbar-item"><a href="about.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">O Nas</a></li> <!-- Dodano link "O Nas" -->

        <?php if (isset($_SESSION['login'])): ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li class="navbar-item"><a href="admin.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'active' : ''; ?>">Panel admina</a></li>
            <?php endif; ?>
            <li class="navbar-item"><a href="favorites.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'favorites.php' ? 'active' : ''; ?>">Ulubione</a></li>
            <li class="navbar-item"><a href="profile.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">Profil</a></li>
            <li class="navbar-item"><a href="logout.php" class="navbar-link">Wyloguj</a></li>
        <?php else: ?>
            <li class="navbar-item"><a href="login.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">Zaloguj</a></li>
            <li class="navbar-item"><a href="registration.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'registration.php' ? 'active' : ''; ?>">Zarejestruj</a></li>
        <?php endif; ?>
    </ul>
</nav>

</body>
</html>