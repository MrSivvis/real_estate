<?php
require('db.php');  // Ścieżka do pliku z połączeniem z bazą danych
require('session.php');  // Ścieżka do pliku obsługującego sesje użytkownika

header('Content-Type: application/json');  // Ustawienie nagłówka JSON

if (isset($_POST['property_id']) && isset($_SESSION['id'])) {
    $property_id = intval($_POST['property_id']);
    $user_id = intval($_SESSION['id']);
    
    // Sprawdź, czy nieruchomość już jest w ulubionych
    $check_sql = "SELECT * FROM favorites WHERE user_id = $user_id AND property_id = $property_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows == 0) {
        // Dodaj nieruchomość do ulubionych
        $sql = "INSERT INTO favorites (user_id, property_id) VALUES ($user_id, $property_id)";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Nieruchomość została dodana do ulubionych!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Wystąpił błąd podczas dodawania do ulubionych: " . $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Nieruchomość już jest w ulubionych."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Nieprawidłowe żądanie."]);
}

$conn->close();
?>