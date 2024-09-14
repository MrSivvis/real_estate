<?php
require('db.php');
require('session.php');
include('menu.php'); // Dodanie wspólnego menu

// Sprawdzenie, czy użytkownik jest adminem
if ($_SESSION['role'] != 'admin') {
    echo "<div class='form-message error'>Brak dostępu. Tylko administrator może zarządzać nieruchomościami.</div>";
    exit;
}

// Usuwanie nieruchomości
if (isset($_GET['delete'])) {
    $property_id = $_GET['delete'];
    $sql = "DELETE FROM properties WHERE id = $property_id";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='form-message success'>Nieruchomość została usunięta pomyślnie.</div>";
    } else {
        echo "<div class='form-message error'>Błąd podczas usuwania nieruchomości: " . $conn->error . "</div>";
    }
}

// Wyszukiwanie, sortowanie i filtrowanie
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$filterRooms = isset($_GET['rooms']) ? $_GET['rooms'] : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';
$filterMinPrice = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$filterMaxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$filterMinArea = isset($_GET['min_area']) ? $_GET['min_area'] : '';
$filterMaxArea = isset($_GET['max_area']) ? $_GET['max_area'] : '';
$filterMinPlotArea = isset($_GET['min_plot_area']) ? $_GET['min_plot_area'] : '';
$filterMaxPlotArea = isset($_GET['max_plot_area']) ? $_GET['max_plot_area'] : '';
$filterID = isset($_GET['id']) ? $_GET['id'] : '';

// Budowanie zapytania SQL
$sql = "SELECT * FROM properties WHERE 1=1"; // '1=1' dla ułatwienia dodawania kolejnych warunków

// Wyszukiwanie
if (!empty($search)) {
    $sql .= " AND (title LIKE '%$search%' OR location LIKE '%$search%')";
}

// Filtrowanie po ID
if (!empty($filterID)) {
    $sql .= " AND id = $filterID";
}

// Filtrowanie po liczbie pokoi
if (!empty($filterRooms)) {
    if ($filterRooms == '4') {
        $sql .= " AND number_of_rooms >= 4";
    } else {
        $sql .= " AND number_of_rooms = $filterRooms";
    }
}

// Filtrowanie po statusie
if (!empty($filterStatus)) {
    $sql .= " AND status = '$filterStatus'";
}

// Filtrowanie po cenie
if (!empty($filterMinPrice)) {
    $sql .= " AND price >= $filterMinPrice";
}
if (!empty($filterMaxPrice)) {
    $sql .= " AND price <= $filterMaxPrice";
}

// Filtrowanie po powierzchni nieruchomości
if (!empty($filterMinArea)) {
    $sql .= " AND property_area >= $filterMinArea";
}
if (!empty($filterMaxArea)) {
    $sql .= " AND property_area <= $filterMaxArea";
}

// Filtrowanie po powierzchni działki
if (!empty($filterMinPlotArea)) {
    $sql .= " AND plot_area >= $filterMinPlotArea";
}
if (!empty($filterMaxPlotArea)) {
    $sql .= " AND plot_area <= $filterMaxPlotArea";
}

// Sortowanie
if ($sort == 'price_asc') {
    $sql .= " ORDER BY price ASC";
} elseif ($sort == 'price_desc') {
    $sql .= " ORDER BY price DESC";
} elseif ($sort == 'area_asc') {
    $sql .= " ORDER BY property_area ASC";
} elseif ($sort == 'area_desc') {
    $sql .= " ORDER BY property_area DESC";
} elseif ($sort == 'rooms_asc') {
    $sql .= " ORDER BY number_of_rooms ASC";
} elseif ($sort == 'rooms_desc') {
    $sql .= " ORDER BY number_of_rooms DESC";
}

$result = $conn->query($sql);
?>

<h1>Zarządzanie Nieruchomościami</h1>
<a href='add_property.php'><button class='btn'>Dodaj Nieruchomość</button></a>
<a href='admin.php'><button class='btn btn-back'>Powrót do Panelu Admina</button></a>

<!-- Formularz wyszukiwania, filtrowania i sortowania -->
<form id="filter-form" method="GET" action="manage_properties.php" class="filter-form">
    <input type="text" name="search" placeholder="Wyszukaj..." value="<?php echo htmlspecialchars($search); ?>">
    
    <input type="number" name="id" placeholder="ID" value="<?php echo htmlspecialchars($filterID); ?>">

    <select name="rooms">
        <option value="">Liczba pokoi</option>
        <option value="1" <?php if ($filterRooms == '1') echo 'selected'; ?>>1</option>
        <option value="2" <?php if ($filterRooms == '2') echo 'selected'; ?>>2</option>
        <option value="3" <?php if ($filterRooms == '3') echo 'selected'; ?>>3</option>
        <option value="4" <?php if ($filterRooms == '4') echo 'selected'; ?>>4+</option>
    </select>

    <select name="status">
        <option value="">Wszystkie statusy</option>
        <option value="rent" <?php if ($filterStatus == 'rent') echo 'selected'; ?>>Wynajem</option>
        <option value="sale" <?php if ($filterStatus == 'sale') echo 'selected'; ?>>Sprzedaż</option>
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

    <button type="submit" class="btn btn-filter">Filtruj</button>
</form>

<?php
// Wyświetlanie nieruchomości
if ($result->num_rows > 0) {
    echo "<table class='property-table'>
            <tr>
                <th>ID</th>
                <th>Tytuł</th>
                <th>Liczba Pokoi</th>
                <th>Status</th>
                <th>Powierzchnia</th>
                <th>Powierzchnia Działki</th>
                <th>Cena</th>
                <th>Akcje</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['title']) . "</td>
                <td>" . htmlspecialchars($row['number_of_rooms']) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td>" . htmlspecialchars($row['property_area']) . " m²</td>
                <td>" . htmlspecialchars($row['plot_area']) . " m²</td>
                <td>" . htmlspecialchars($row['price']) . " PLN</td>
                <td>
                    <a href='property_details.php?id=" . htmlspecialchars($row['id']) . "&source=manage_properties' class='btn btn-details'>Szczegóły</a> |
                    <a href='edit_property.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-edit'>Edytuj</a> |
                    <a href='manage_properties.php?delete=" . htmlspecialchars($row['id']) . "' class='btn btn-delete' onclick=\"return confirm('Czy na pewno chcesz usunąć tę nieruchomość?')\">Usuń</a>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p class='form-message'>Brak nieruchomości w systemie.</p>";
}

$conn->close();
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Kliknięcie przycisku do rozwijania filtrów
    $('#toggle-filters').on('click', function() {
        $('#additional-filters').slideToggle(); // Animacja rozwijania/zamykania
    });
});
</script>