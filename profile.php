<?php
require('db.php');
require('session.php');
include('menu.php');

// Pobieranie danych użytkownika z bazy
$user_id = $_SESSION['id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    echo "<p class='error-message'>Błąd: Nie znaleziono użytkownika.</p>";
    exit;
}
?>

<div class="profile-container">
    <h1 class="profile-title">Profil Użytkownika</h1>

    <p><strong>Imię:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
    <p><strong>Nazwisko:</strong> <?php echo htmlspecialchars($user['surname']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Numer Telefonu:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>

    <div class="profile-actions">
    <a href="edit_profile.php" class="profile-button">Edytuj Profil</a> | 
    <a href="change_password.php" class="profile-button">Zmień Hasło</a> | 
    <a href="delete_account.php" class="profile-button" onclick="return confirm('Czy na pewno chcesz usunąć swoje konto? Ta operacja jest nieodwracalna.')">Usuń Konto</a>
</div>
    </div>
</div>

<?php
$conn->close();
?>