<?php
if (isset($_POST['property_id']) && isset($_POST['user_id']) && isset($_POST['message'])) {
    // Możemy opcjonalnie dodać tutaj walidację lub przetwarzanie danych

    // Wyświetlenie komunikatu o wysłaniu wiadomości
    echo "<div class='message success'>Wiadomość została wysłana!</div>";
} else {
    echo "<div class='message error'>Nieprawidłowe dane formularza.</div>";
}
?>