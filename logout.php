<?php
session_start();
session_destroy(); // Zakończenie sesji

include('menu.php'); // Dodanie wspólnego menu nawigacyjnego
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wylogowano</title>
    <link rel="stylesheet" href="style.css"> <!-- Link do wspólnego pliku CSS -->
</head>
<body>
    <div class="logout-container">
        <h1>Wylogowano pomyślnie</h1>
        <p>Zostałeś wylogowany z systemu. <a href="login.php">Zaloguj się ponownie</a>.</p>
    </div>
</body>
</html>