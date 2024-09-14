<?php
require('../db/db.php');
require('../auth/session.php');

if ($_SESSION['role'] != 'admin') {
    echo "<div class='alert alert-danger'>Brak dostępu. Tylko administrator może usuwać nieruchomości.</div>";
    exit;
}

if (isset($_GET['id'])) {
    $property_id = $_GET['id'];

    // Usunięcie nieruchomości z bazy danych
    $sql = "DELETE FROM properties WHERE id = $property_id";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Nieruchomość została usunięta pomyślnie. <a href='../admin/manage_properties.php'>Zarządzaj nieruchomościami.</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Błąd podczas usuwania nieruchomości: " . $conn->error . "</div>";
    }
} else {
    echo "<div class='alert alert-warning'>Nieprawidłowe żądanie.</div>";
}

$conn->close();
?>