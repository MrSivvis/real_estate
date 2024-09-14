<?php
if (isset($_POST['property_id']) && isset($_POST['user_id']) && isset($_POST['message'])) {
    // Możemy opcjonalnie dodać tutaj walidację lub przetwarzanie danych

    // Wyświetlenie komunikatu o wysłaniu wiadomości
    echo "Wiadomość została wysłana!";
} else {
    echo "Nieprawidłowe dane formularza.";
}
?>