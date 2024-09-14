<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('db.php');  // Ścieżka do db.php w głównym folderze
require('session.php');  // Ścieżka do session.php w głównym folderze
include('menu.php'); // Wspólne menu nawigacyjne

if (isset($_POST['login'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Znajdowanie użytkownika po loginie
    $sql = "SELECT * FROM users WHERE login='$login'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        // Sprawdzenie hasła przy użyciu password_verify()
        if (password_verify($password, $user['password'])) {
            $_SESSION['login'] = $login;
            $_SESSION['id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name']; // Zapisz imię użytkownika do sesji

            header("Location: index.php");
            exit;
        } else {
            echo "<div class='form-message error'>Nieprawidłowy login lub hasło. <a href='login.php'>Spróbuj ponownie.</a></div>";
        }
    } else {
        echo "<div class='form-message error'>Nieprawidłowy login lub hasło. <a href='login.php'>Spróbuj ponownie.</a></div>";
    }
} else {
?>

<div class="login-container">
    <?php if (isset($_SESSION['name'])): ?>
        <p class="welcome-message">Witaj, <?php echo htmlspecialchars($_SESSION['name']); ?>! Jesteś już zalogowany.</p>
    <?php else: ?>
        <form class="form" method="post" name="login">
            <h1 class="form-title">Logowanie</h1>
            <input type="text" class="form-input" name="login" placeholder="Login" autofocus="true" required />
            <input type="password" class="form-input" name="password" placeholder="Hasło" required />
            <input type="submit" value="Zaloguj" name="submit" class="form-button"/>
            <p class="form-link"><a href="registration.php">Zarejestruj się</a></p>
        </form>
    <?php endif; ?>
</div>

<?php } ?>