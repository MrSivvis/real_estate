<?php
require('db.php');
require('session.php');

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// Pobranie ID użytkownika z sesji
$user_id = $_SESSION['id'];

// Usunięcie użytkownika z bazy danych
$sql = "DELETE FROM users WHERE id = '$user_id'"; // ID użytkownika wstawione bez bind_param

if (mysqli_query($conn, $sql)) {
    // Wylogowanie użytkownika i usunięcie sesji
    session_destroy();
    echo "<script>alert('Twoje konto zostało pomyślnie usunięte.'); window.location.href='index.php';</script>";
} else {
    echo "<p class='error-message'>Błąd podczas usuwania konta: " . mysqli_error($conn) . "</p>";
}

mysqli_close($conn);
?>