<?php
require('db.php');  // Połączenie z bazą danych
require('session.php');  // Sesja użytkownika
include('menu.php');  // Menu wspólne dla wszystkich stron

echo "<h1 class='page-title'>Ulubione Nieruchomości</h1>";

$user_id = $_SESSION['id'];

// Pobranie listy ulubionych nieruchomości dla zalogowanego użytkownika wraz z miniaturkami
$sql = "SELECT p.*, 
               (SELECT image_path FROM images WHERE property_id = p.id LIMIT 1) AS thumbnail 
        FROM properties p 
        JOIN favorites f ON p.id = f.property_id 
        WHERE f.user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='properties-list'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='property-item'>";
        
        // Wyświetlanie miniaturki, jeśli jest dostępna
        if (!empty($row['thumbnail'])) {
            echo "<div class='property-thumbnail'><img src='" . htmlspecialchars($row['thumbnail']) . "' alt='Miniaturka'></div>";
        } else {
            echo "<div class='property-thumbnail'><img src='default-thumbnail.jpg' alt='Brak zdjęcia'></div>";  // Domyślna miniaturka
        }

        // Wyświetlanie tytułu nieruchomości
        echo "<h2 class='property-title'>" . htmlspecialchars($row['title']) . "</h2>";

        // Wyświetlanie lokalizacji
        echo "<p class='property-location'>Lokalizacja: " . htmlspecialchars($row['location']) . "</p>";
        
        // Wyświetlanie typu nieruchomości
        echo "<p class='property-type'>Typ nieruchomości: " . ($row['status'] == 'rent' ? 'Wynajem' : 'Sprzedaż') . "</p>";

        // Wyświetlanie ceny
        echo "<p class='property-price'>Cena: " . htmlspecialchars($row['price']) . " PLN</p>";

        // Wyświetlanie powierzchni nieruchomości i/lub działki, jeśli są dostępne
        if (!empty($row['property_area']) && $row['property_area'] != 0) {
            echo "<p class='property-area'>Powierzchnia nieruchomości: " . htmlspecialchars($row['property_area']) . " m²</p>";
        }
        if (!empty($row['plot_area']) && $row['plot_area'] != 0) {
            echo "<p class='plot-area'>Powierzchnia działki: " . htmlspecialchars($row['plot_area']) . " m²</p>";
        }

        // Link do szczegółów nieruchomości oraz przycisk "Usuń z ulubionych"
        echo "<a href='property_details.php?id=" . $row['id'] . "&source=favorites' class='details-link'>Szczegóły</a>";
        echo " | <button type='button' class='remove-favorite btn' data-property-id='" . $row['id'] . "'>Usuń z ulubionych</button>";

        echo "</div>";  // Zamykanie .property-item
    }
    echo "</div>";  // Zamykanie .properties-list
} else {
    echo "<p class='alert alert-warning'>Brak ulubionych nieruchomości.</p>";
}

$conn->close();
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="favorites.js"></script>