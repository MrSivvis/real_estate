<?php
require('db.php'); // Załadowanie połączenia z bazą danych
require('session.php'); // Zarządzanie sesjami
include('menu.php'); // Wspólne menu

if (isset($_POST['change_password'])) {
    // Zabezpieczanie danych wejściowych przed SQL Injection
    $old_password = mysqli_real_escape_string($conn, $_POST['old_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $user_id = $_SESSION['id'];

    // Sprawdzenie czy stare hasło jest poprawne
    $sql = "SELECT password FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Weryfikacja starego hasła
        if (password_verify($old_password, $user['password'])) {
            // Aktualizacja nowego hasła
            $new_password_hash = mysqli_real_escape_string($conn, password_hash($new_password, PASSWORD_BCRYPT));
            $sql = "UPDATE users SET password='$new_password_hash' WHERE id='$user_id'";

            if (mysqli_query($conn, $sql)) {
                echo "<div class='alert alert-success'>Hasło zostało zaktualizowane pomyślnie. <a href='profile.php'>Wróć do profilu</a></div>";
            } else {
                echo "<div class='alert alert-danger'>Błąd podczas zmiany hasła: " . mysqli_error($conn) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Stare hasło jest niepoprawne.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Nie znaleziono użytkownika.</div>";
    }
}

$conn->close();
?>

<div class="container">
    <form class="form change-password-form" action="" method="post">
        <h1 class="form-title">Zmień Hasło</h1>
        <input type="password" name="old_password" placeholder="Stare Hasło" class="form-input" required>
        <input type="password" name="new_password" placeholder="Nowe Hasło" class="form-input" required>
        <input type="submit" name="change_password" value="Zmień Hasło" class="form-submit">
    </form>
    
    <!-- Przycisk powrotu do profilu użytkownika -->
    <a href="profile.php" class="form-button back-button">Powrót do Profilu</a>
</div>