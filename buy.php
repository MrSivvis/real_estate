<?php
require('db.php');  // Połączenie z bazą danych
require('session.php');  // Sesja użytkownika
include('menu.php');  // Wspólne menu dla wszystkich stron

echo "<h1 class='page-title'>Nieruchomości na Sprzedaż</h1>";

// Sprawdzenie, czy użytkownik jest zalogowany
$isLoggedIn = isset($_SESSION['id']);

// Wyszukiwanie, sortowanie i filtrowanie
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$filterType = isset($_GET['property_type']) ? $_GET['property_type'] : '';
$filterLocation = isset($_GET['location']) ? $_GET['location'] : '';
$filterRooms = isset($_GET['rooms']) ? $_GET['rooms'] : '';
$filterMinPrice = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$filterMaxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$filterMinArea = isset($_GET['min_area']) ? $_GET['min_area'] : '';
$filterMaxArea = isset($_GET['max_area']) ? $_GET['max_area'] : '';
$filterMinPlotArea = isset($_GET['min_plot_area']) ? $_GET['min_plot_area'] : '';
$filterMaxPlotArea = isset($_GET['max_plot_area']) ? $_GET['max_plot_area'] : '';

// Budowanie zapytania SQL
$sql = "SELECT p.*, 
               (SELECT image_path FROM images WHERE property_id = p.id LIMIT 1) AS thumbnail 
        FROM properties p 
        WHERE p.status = 'sale'";  // 'sale' to wartość z tabeli, która oznacza rodzaj transakcji

// Wyszukiwanie
if (!empty($search)) {
    $sql .= " AND (p.title LIKE '%$search%' OR p.location LIKE '%$search%' OR p.short_description LIKE '%$search%')";
}

// Filtrowanie po typie nieruchomości
if (!empty($filterType)) {
    if ($filterType == 'inne') {
        $sql .= " AND p.property_type NOT IN ('dom', 'mieszkanie')";
    } else {
        $sql .= " AND p.property_type = '$filterType'";
    }
}

// Filtrowanie po lokalizacji
if (!empty($filterLocation)) {
    $sql .= " AND p.location LIKE '%$filterLocation%'";
}

// Filtrowanie po liczbie pokoi
if (!empty($filterRooms)) {
    if ($filterRooms == '4') {
        $sql .= " AND p.number_of_rooms >= 4";
    } else {
        $sql .= " AND p.number_of_rooms = $filterRooms";
    }
}

// Filtrowanie po cenie
if (!empty($filterMinPrice)) {
    $sql .= " AND p.price >= $filterMinPrice";
}
if (!empty($filterMaxPrice)) {
    $sql .= " AND p.price <= $filterMaxPrice";
}

// Filtrowanie po powierzchni
if (!empty($filterMinArea)) {
    $sql .= " AND p.property_area >= $filterMinArea";
}
if (!empty($filterMaxArea)) {
    $sql .= " AND p.property_area <= $filterMaxArea";
}

// Filtrowanie po powierzchni działki
if (!empty($filterMinPlotArea)) {
    $sql .= " AND p.plot_area >= $filterMinPlotArea";
}
if (!empty($filterMaxPlotArea)) {
    $sql .= " AND p.plot_area <= $filterMaxPlotArea";
}

// Sortowanie
if ($sort == 'price_asc') {
    $sql .= " ORDER BY p.price ASC";
} elseif ($sort == 'price_desc') {
    $sql .= " ORDER BY p.price DESC";
} elseif ($sort == 'area_asc') {
    $sql .= " ORDER BY p.property_area ASC";
} elseif ($sort == 'area_desc') {
    $sql .= " ORDER BY p.property_area DESC";
} elseif ($sort == 'rooms_asc') {
    $sql .= " ORDER BY p.number_of_rooms ASC";
} elseif ($sort == 'rooms_desc') {
    $sql .= " ORDER BY p.number_of_rooms DESC";
}

$result = $conn->query($sql);

?>

<!-- Formularz wyszukiwania, filtrowania i sortowania -->
<?php if ($isLoggedIn): ?>
<form id="filter-form" method="GET" action="buy.php" class="filter-form">
    <input type="text" name="search" placeholder="Wyszukaj..." value="<?php echo htmlspecialchars($search); ?>">

    <input type="text" name="location" placeholder="Lokalizacja" value="<?php echo htmlspecialchars($filterLocation); ?>">

    <select name="property_type">
        <option value="">Wszystkie typy</option>
        <option value="dom" <?php if ($filterType == 'dom') echo 'selected'; ?>>Dom</option>
        <option value="mieszkanie" <?php if ($filterType == 'mieszkanie') echo 'selected'; ?>>Mieszkanie</option>
        <option value="inne" <?php if ($filterType == 'inne') echo 'selected'; ?>>Inne</option>
    </select>

    <select name="rooms">
        <option value="">Liczba pokoi</option>
        <option value="1" <?php if ($filterRooms == '1') echo 'selected'; ?>>1</option>
        <option value="2" <?php if ($filterRooms == '2') echo 'selected'; ?>>2</option>
        <option value="3" <?php if ($filterRooms == '3') echo 'selected'; ?>>3</option>
        <option value="4" <?php if ($filterRooms == '4') echo 'selected'; ?>>4+</option>
    </select>

    <!-- Dodaj przycisk do rozwijania dodatkowych filtrów -->
    <button type="button" id="toggle-filters" class="toggle-filters-btn">Dodaj więcej filtrów</button>

    <!-- Dodatkowe filtry -->
    <div id="additional-filters" style="display: none;">
        <input type="number" name="min_price" placeholder="Cena od" value="<?php echo htmlspecialchars($filterMinPrice); ?>">
        <input type="number" name="max_price" placeholder="Cena do" value="<?php echo htmlspecialchars($filterMaxPrice); ?>">

        <input type="number" name="min_area" placeholder="Powierzchnia od (m²)" value="<?php echo htmlspecialchars($filterMinArea); ?>">
        <input type="number" name="max_area" placeholder="Powierzchnia do (m²)" value="<?php echo htmlspecialchars($filterMaxArea); ?>">

        <!-- Nowe pole filtrujące dla powierzchni działki -->
        <input type="number" name="min_plot_area" placeholder="Powierzchnia działki od (m²)" value="<?php echo htmlspecialchars($filterMinPlotArea); ?>">
        <input type="number" name="max_plot_area" placeholder="Powierzchnia działki do (m²)" value="<?php echo htmlspecialchars($filterMaxPlotArea); ?>">

        <select name="sort">
            <option value="">Sortuj</option>
            <option value="price_asc" <?php if ($sort == 'price_asc') echo 'selected'; ?>>Cena rosnąco</option>
            <option value="price_desc" <?php if ($sort == 'price_desc') echo 'selected'; ?>>Cena malejąco</option>
            <option value="area_asc" <?php if ($sort == 'area_asc') echo 'selected'; ?>>Powierzchnia rosnąco</option>
            <option value="area_desc" <?php if ($sort == 'area_desc') echo 'selected'; ?>>Powierzchnia malejąco</option>
            <option value="rooms_asc" <?php if ($sort == 'rooms_asc') echo 'selected'; ?>>Pokoje rosnąco</option>
            <option value="rooms_desc" <?php if ($sort == 'rooms_desc') echo 'selected'; ?>>Pokoje malejąco</option>
        </select>
    </div>

    <button type="submit">Filtruj</button>
</form>
<?php endif; ?>

<?php
// Wyświetlanie wyników
if ($result->num_rows > 0) {
    echo "<div class='properties-list'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='property-item'>";
        
        // Wyświetlanie miniaturki, jeśli jest dostępna
        if (!empty($row['thumbnail'])) {
            echo "<div class='property-thumbnail'><img src='" . htmlspecialchars($row['thumbnail']) . "' alt='Miniaturka' style='max-width: 150px; max-height: 150px;'></div>";
        } else {
            echo "<div class='property-thumbnail'><img src='default-thumbnail.jpg' alt='Brak zdjęcia' style='max-width: 150px; max-height: 150px;'></div>";  // Domyślna miniaturka
        }

        // Wyświetlanie szczegółów nieruchomości
        echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
        echo "<p>" . htmlspecialchars($row['short_description']) . "</p>";
        echo "<p>Lokalizacja: " . htmlspecialchars($row['location']) . "</p>";
        echo "<p>Typ nieruchomości: " . htmlspecialchars($row['property_type']) . "</p>";
        echo "<p>Cena: " . htmlspecialchars($row['price']) . " PLN</p>";

        // Powierzchnia
        if (!empty($row['property_area']) && $row['property_area'] != 0) {
            echo "<p>Powierzchnia nieruchomości: " . htmlspecialchars($row['property_area']) . " m²</p>";
        }
        if (!empty($row['plot_area']) && $row['plot_area'] != 0) {
            echo "<p>Powierzchnia działki: " . htmlspecialchars($row['plot_area']) . " m²</p>";
        }

        // Link do szczegółów i przycisk dodawania do ulubionych (tylko dla zalogowanych użytkowników)
        if ($isLoggedIn) {
            echo "<a href='property_details.php?id=" . $row['id'] . "&source=buy'>Szczegóły</a>";
            $user_id = $_SESSION['id'];
            $favorite_sql = "SELECT * FROM favorites WHERE user_id = $user_id AND property_id = " . $row['id'];
            $favorite_result = $conn->query($favorite_sql);
            
            if ($favorite_result->num_rows > 0) {
                echo " | <button class='remove-favorite' data-property-id='" . $row['id'] . "'>Usuń z ulubionych</button>";
            } else {
                echo " | <button class='add-favorite' data-property-id='" . $row['id'] . "'>Dodaj do ulubionych</button>";
            }
        }

        echo "</div>";  // Zamykanie .property-item
    }
    echo "</div>";  // Zamykanie .properties-list
} else {
    echo "<p>Brak nieruchomości na sprzedaż.</p>";
}

$conn->close();
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="favorites.js"></script>
<script>
$(document).ready(function() {
    // Kliknięcie przycisku do rozwijania filtrów
    $('#toggle-filters').on('click', function() {
        $('#additional-filters').slideToggle(); // Animacja rozwijania/zamykania
    });
});
</script>