<?php
require('../db.php'); // Połączenie z bazą danych
require('../session.php'); // Sprawdzenie sesji
include('../includes/menu.php'); 
$user_id = $_SESSION['user_id'];

// Pobranie listy nieruchomości
$sql = "SELECT * FROM properties";
$result = $conn->query($sql);

echo "<h1>Lista Nieruchomości</h1>";
echo "<table border='1'>
        <tr>
            <th>Tytuł</th>
            <th>Krótki Opis</th>
            <th>Cena</th>
            <th>Status</th>
            <th>Akcje</th>
            <th>Ulubione</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    $property_id = $row['id'];

    // Sprawdzenie, czy nieruchomość jest w ulubionych
    $sql_check = "SELECT * FROM favorites WHERE user_id = $user_id AND property_id = $property_id";
    $result_check = $conn->query($sql_check);
    $is_favorite = $result_check->num_rows > 0;

    echo "<tr>
            <td>" . $row['title'] . "</td>
            <td>" . $row['short_description'] . "</td>
            <td>" . $row['price'] . "</td>
            <td>" . $row['status'] . "</td>
            <td>
                <a href='edit_property.php?id=" . $row['id'] . "'>Edytuj</a> | 
                <a href='delete_property.php?id=" . $row['id'] . "' onclick=\"return confirm('Czy na pewno chcesz usunąć tę nieruchomość?');\">Usuń</a>
            </td>
            <td>
                <a href='favorites/add_to_favorites.php?id=" . $row['id'] . "'>" . ($is_favorite ? "Usuń z Ulubionych" : "Dodaj do Ulubionych") . "</a>
            </td>
          </tr>";
}
echo "</table>";
?>