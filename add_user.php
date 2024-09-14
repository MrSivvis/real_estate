<?php
require('db.php');
require('session.php');
include('menu.php'); // Wspólne menu

// Sprawdzenie, czy użytkownik jest adminem
if ($_SESSION['role'] != 'admin') {
    echo "<div class='alert alert-danger'>Brak dostępu. Tylko administrator może dodawać użytkowników.</div>";
    exit;
}

// Dodawanie użytkownika
if (isset($_POST['add_user'])) {
    // Zabezpieczanie danych wejściowych przed SQL Injection
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $surname = mysqli_real_escape_string($conn, $_POST['surname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $login = mysqli_real_escape_string($conn, $_POST['login']);
    $password = mysqli_real_escape_string($conn, password_hash($_POST['password'], PASSWORD_BCRYPT));

    // Budowanie zapytania SQL
    $sql = "INSERT INTO users (name, surname, email, phone, role, login, password) VALUES ('$name', '$surname', '$email', '$phone', '$role', '$login', '$password')";

    // Wykonanie zapytania SQL
    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success'>Użytkownik został dodany pomyślnie. <a href='manage_users.php'>Wróć do listy użytkowników</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Błąd podczas dodawania użytkownika: " . mysqli_error($conn) . "</div>";
    }
}

$conn->close();
?>

<div class="container">
    <form class="form add-user-form" action="" method="post">
        <h1 class="form-title">Dodaj Użytkownika</h1>
        <input type="text" name="name" placeholder="Imię" class="form-input" required>
        <input type="text" name="surname" placeholder="Nazwisko" class="form-input" required>
        <input type="email" name="email" placeholder="Email" class="form-input" required>
        <input type="text" name="phone" placeholder="Numer Telefonu" class="form-input" required>
        <input type="text" name="login" placeholder="Login" class="form-input" required>
        <input type="password" name="password" placeholder="Hasło" class="form-input" required>
        <select name="role" class="form-select">
            <option value="user">Użytkownik</option>
            <option value="admin">Administrator</option>
        </select>
        <input type="submit" name="add_user" value="Dodaj Użytkownika" class="form-submit">
    </form>
</div>