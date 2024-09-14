<?php
require('db.php');
include('menu.php');

if (isset($_GET['id'])) {
    $property_id = intval($_GET['id']);  // Bezpieczne przekonwertowanie na int
    $sql = "SELECT * FROM properties WHERE id = $property_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $property = $result->fetch_assoc();
        
        // Wyświetlanie ID nieruchomości
        echo "<h1 class='property-title'>Szczegóły Nieruchomości: " . htmlspecialchars($property['title']) . " (ID: " . htmlspecialchars($property['id']) . ")</h1>";

        // Wyświetlanie danych tylko, jeśli są różne od null lub 0
        echo "<div class='property-details'>";
        if (!empty($property['short_description'])) {
            echo "<p><strong></strong> " . htmlspecialchars($property['short_description']) . "</p>";
        }
        if (!empty($property['full_description'])) {
            echo "<p><strong>Opis:</strong> " . htmlspecialchars($property['full_description']) . "</p>";
        }
        if (!empty($property['property_area']) && $property['property_area'] != 0) {
            echo "<p><strong>Powierzchnia:</strong> " . htmlspecialchars($property['property_area']) . " m²</p>";
        }
        if (!empty($property['number_of_rooms']) && $property['number_of_rooms'] != 0) {
            echo "<p><strong>Liczba Pokoi:</strong> " . htmlspecialchars($property['number_of_rooms']) . "</p>";
        }
        if (!empty($property['location'])) {
            echo "<p><strong>Lokalizacja:</strong> " . htmlspecialchars($property['location']) . "</p>";
        }
        if (!empty($property['property_type'])) {
            echo "<p><strong>Typ:</strong> " . htmlspecialchars($property['property_type']) . "</p>";
        }
        if (!empty($property['status'])) {
            echo "<p><strong>Status:</strong> " . ($property['status'] == 'rent' ? 'Wynajem' : 'Sprzedaż') . "</p>";
        }
        if (!empty($property['price']) && $property['price'] != 0) {
            echo "<p><strong>Cena:</strong> " . htmlspecialchars($property['price']) . " PLN</p>";
        }
        echo "</div>";

        // Pobieranie i wyświetlanie zdjęć z tabeli 'images'
        $image_sql = "SELECT * FROM images WHERE property_id = $property_id";
        $image_result = $conn->query($image_sql);
        if ($image_result->num_rows > 0) {
            echo "<h3 class='gallery-title'>Galeria Zdjęć:</h3>";
            echo "<div class='property-gallery'>";
            while ($image = $image_result->fetch_assoc()) {
                echo "<a href='" . htmlspecialchars($image['image_path']) . "' target='_blank'><img src='" . htmlspecialchars($image['image_path']) . "' alt='Zdjęcie nieruchomości' class='property-image'></a>";
            }
            echo "</div>";
        }

        // Przycisk "Dodaj do ulubionych" lub "Usuń z ulubionych"
        echo "<div class='button-container'>";
        if (isset($_SESSION['id'])) {
            $user_id = $_SESSION['id'];
            $favorite_sql = "SELECT * FROM favorites WHERE user_id = $user_id AND property_id = $property_id";
            $favorite_result = $conn->query($favorite_sql);
            
            if ($favorite_result->num_rows > 0) {
                echo "<button type='button' class='remove-favorite btn' data-property-id='" . $property['id'] . "'>Usuń z ulubionych</button>";
            } else {
                echo "<button type='button' class='add-favorite btn' data-property-id='" . $property['id'] . "'>Dodaj do ulubionych</button>";
            }
        }

        // Przycisk "Skontaktuj się w sprawie oferty"
        echo "<button type='button' class='button contact-button' onclick='openContactModal()'>Kliknij, żeby skontaktować się w sprawie oferty</button>";

        // Przycisk powrotu do odpowiedniej strony
        if (isset($_GET['source'])) {
            $source = htmlspecialchars($_GET['source']);
            if ($source === 'rent') {
                echo "<a href='rent.php' class='button button-back'>Powrót do Nieruchomości na Wynajem</a>";
            } elseif ($source === 'buy') {
                echo "<a href='buy.php' class='button button-back'>Powrót do Nieruchomości na Sprzedaż</a>";
            } elseif ($source === 'favorites') {
                echo "<a href='favorites.php' class='button button-back'>Powrót do Ulubionych</a>";
            } elseif ($source === 'manage_properties') {   
                echo "<a href='manage_properties.php' class='button button-back'>Powrót do Zarządzania Nieruchomościami</a>"; 
            }
        } else {
            echo "<a href='rent.php' class='button button-back'>Powrót do Strony Głównej</a>";
        }
        echo "</div>";

    } else {
        echo "<p class='error-message'>Nierucsomość nie została znaleziona.</p>";
    }
} else {
    echo "<p class='error-message'>Nieprawidłowe żądanie.</p>";
}

$conn->close();
?>

<!-- Okno modalne z informacjami kontaktowymi -->
<div id="contactModal" class="modal">
    <div class="modal-content">
        <h2>Kontakt w sprawie oferty</h2>
        <p>Jeżeli jesteś zainteresowany tą ofertą, skontaktuj się z naszym biurem nieruchomości:</p>
        <p><strong>Adres e-mail:</strong> kontakt@biuronieruchomosci.pl</p>
        <p><strong>Numer telefonu:</strong> +48 123 456 789</p>
        <p>Podczas kontaktu prosimy o podanie numeru ID oferty: <strong><?php echo htmlspecialchars($property['id']); ?></strong>.</p>
        <p>Zachęcamy do kontaktu! Nasz zespół jest gotowy odpowiedzieć na wszystkie Twoje pytania.</p>
        <button type="button" class="close-modal-button" onclick="closeContactModal()">Zamknij</button>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
function openContactModal() {
    document.getElementById('contactModal').style.display = 'block';
}

function closeContactModal() {
    document.getElementById('contactModal').style.display = 'none';
}

// Dodaj do ulubionych / Usuń z ulubionych
$(document).on('click', '.add-favorite, .remove-favorite', function() {
    const button = $(this);
    const propertyId = button.data('property-id');
    const action = button.hasClass('add-favorite') ? 'add' : 'remove';
    const url = action === 'add' ? 'add_to_favorites.php' : 'remove_from_favorites.php';

    $.ajax({
        url: url,
        type: 'POST',
        data: { property_id: propertyId },
        success: function(response) {
            console.log("Odpowiedź serwera:", response);  // Logowanie odpowiedzi z serwera do konsoli
            
            // Zawsze wykonuj poniższe, niezależnie od odpowiedzi serwera
            if (action === 'remove') {
                button.removeClass('remove-favorite').addClass('add-favorite');
                button.text('Dodaj do ulubionych');
                alert('Nieruchomość została usunięta z ulubionych!'); // Powiadomienie użytkownika
            } else {
                button.removeClass('add-favorite').addClass('remove-favorite');
                button.text('Usuń z ulubionych');
                alert('Nieruchomość została dodana do ulubionych!'); // Powiadomienie użytkownika
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Błąd AJAX:", textStatus, errorThrown);

            // Nawet jeśli wystąpi błąd, zmieniaj przycisk i pokazuj powiadomienie
            if (action === 'remove') {
                button.removeClass('remove-favorite').addClass('add-favorite');
                button.text('Dodaj do ulubionych');
                alert('Nieruchomość została usunięta z ulubionych!'); // Powiadomienie użytkownika
            } else {
                button.removeClass('add-favorite').addClass('remove-favorite');
                button.text('Usuń z ulubionych');
                alert('Nieruchomość została dodana do ulubionych!'); // Powiadomienie użytkownika
            }
        }
    });
});
</script>