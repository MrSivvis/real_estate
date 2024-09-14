<?php
require('db.php'); 
require('session.php'); 
include('menu.php'); 

// Sprawdzenie, czy użytkownik jest adminem
if ($_SESSION['role'] != 'admin') {
    echo "<div class='alert alert-danger'>Brak dostępu. Tylko administrator może zarządzać użytkownikami.</div>";
    exit;
}
?>

<div class="admin-panel">
    <h1 class="admin-title">Panel Administratora</h1>
    <p class="admin-description">Wybierz jedną z opcji poniżej:</p>

    <div class="admin-options">
        <a href="manage_users.php"><button class="admin-button">Zarządzaj Użytkownikami</button></a>
        <a href="manage_properties.php"><button class="admin-button">Zarządzaj Nieruchomościami</button></a>
    </div>
</div>