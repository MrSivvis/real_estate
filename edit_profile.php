<?php
require('db.php');  // Załadowanie pliku db.php do połączenia z bazą danych
require('session.php');  // Załadowanie pliku session.php do zarządzania sesjami
include('menu.php');  // Załadowanie wspólnego menu

// Pobieranie danych użytkownika
$user_id = $_SESSION['id'];
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    echo "<div class='alert alert-danger'>Błąd: Nie znaleziono użytkownika.</div>";
    exit;
}

// Aktualizacja danych użytkownika
if (isset($_POST['edit_profile'])) {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Walidacja emaila
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='alert alert-danger'>Błędny format adresu e-mail.</div>";
    } elseif (!preg_match("/^[0-9]{9}$/", $phone)) {
        // Walidacja numeru telefonu (9 cyfr)
        echo "<div class='alert alert-danger'>Błędny format numeru telefonu. Powinien zawierać 9 cyfr.</div>";
    } else {
        $sql = "UPDATE users SET name='$name', surname='$surname', email='$email', phone='$phone' WHERE id='$user_id'";
        
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>Profil został zaktualizowany pomyślnie. <a href='profile.php'>Wróć do profilu</a></div>";
        } else {
            echo "<div class='alert alert-danger'>Błąd podczas aktualizacji profilu: " . $conn->error . "</div>";
        }
    }
}

$conn->close();
?>

<div class="container">
    <form class="form edit-profile-form" action="" method="post">
        <h1 class="form-title">Edytuj Profil</h1>
        
        <label for="name" class="form-label">Imię:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="form-input" required>
        
        <label for="surname" class="form-label">Nazwisko:</label>
        <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>" class="form-input" required>
        
        <label for="email" class="form-label">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-input" required>
        
        <label for="phone" class="form-label">Numer Telefonu:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="form-input" required pattern="[0-9]{9}">
        
        <input type="submit" name="edit_profile" value="Zaktualizuj Profil" class="form-submit">
    </form>

    <!-- Przycisk powrotu do profilu -->
    <a href="profile.php" class="form-button back-button">Wróć do profilu</a>
</div>