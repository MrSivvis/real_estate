<?php
require('db.php');
require('session.php');

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['id'])) {
    echo "<div class='form-message error'>Musisz być zalogowany, aby wykonać tę operację.</div>";
    exit;
}

$user_id = $_SESSION['id'];
$property_id = $_POST['property_id'];

// Usunięcie nieruchomości z ulubionych
$sql = "DELETE FROM favorites WHERE user_id = $user_id AND property_id = $property_id";
if ($conn->query($sql) === TRUE) {
    echo "<div class='form-message success'>Nieruchomość została usunięta z ulubionych.</div>";
} else {
    echo "<div class='form-message error'>Błąd podczas usuwania nieruchomości z ulubionych: " . $conn->error . "</div>";
}

$conn->close();
?>