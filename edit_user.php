<?php
require('db.php');
require('session.php');
include('menu.php');

// Sprawdzenie, czy użytkownik jest adminem
if ($_SESSION['role'] != 'admin') {
    echo "<div class='alert alert-danger'>Brak dostępu. Tylko administrator może edytować użytkowników.</div>";
    exit;
}

// Pobieranie danych użytkownika
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);  // Bezpieczniejsze przekonwertowanie na int
    $sql = "SELECT * FROM users WHERE id = $user_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-warning'>Nie znaleziono użytkownika.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-warning'>Nieprawidłowe żądanie.</div>";
    exit;
}

// Aktualizacja użytkownika
if (isset($_POST['edit_user'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $surname = mysqli_real_escape_string($conn, $_POST['surname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $sql = "UPDATE users SET name='$name', surname='$surname', email='$email', phone='$phone', role='$role' WHERE id = $user_id";

    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Użytkownik został zaktualizowany pomyślnie. <a href='manage_users.php'>Wróć do listy użytkowników</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Błąd podczas aktualizacji użytkownika: " . $conn->error . "</div>";
    }
}

$conn->close();
?>

<div class="container">
    <form class="form edit-user-form" action="" method="post">
        <h1 class="form-title">Edytuj Użytkownika</h1>
        
        <label for="name" class="form-label">Imię:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="form-input" required>
        
        <label for="surname" class="form-label">Nazwisko:</label>
        <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>" class="form-input" required>
        
        <label for="email" class="form-label">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-input" required>
        
        <label for="phone" class="form-label">Numer Telefonu:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="form-input" required pattern="[0-9]{9}">
        
        <label for="role" class="form-label">Rola:</label>
        <select id="role" name="role" class="form-select">
            <option value="user" <?php if($user['role'] == 'user') echo 'selected'; ?>>Użytkownik</option>
            <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>Administrator</option>
        </select>
        
        <input type="submit" name="edit_user" value="Zaktualizuj Użytkownika" class="form-submit">
    </form>

    <!-- Przycisk powrotu do zarządzania użytkownikami -->
    <a href="manage_users.php" class="form-button back-button">Wróć do Zarządzania Użytkownikami</a>
</div>