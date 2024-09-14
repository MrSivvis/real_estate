<?php
require('db.php');
require('session.php');
include('menu.php');

// Sprawdzenie, czy użytkownik jest adminem
if ($_SESSION['role'] != 'admin') {
    echo "<div class='form-message error'>Brak dostępu. Tylko administrator może zarządzać użytkownikami.</div>";
    exit;
}

// Usuwanie użytkownika
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    
    // Sprawdzenie, czy usuwany użytkownik to aktualnie zalogowany admin
    if ($user_id == $_SESSION['id']) {
        echo "<div class='form-message error'>Nie możesz usunąć samego siebie.</div>";
    } else {
        $sql = "DELETE FROM users WHERE id = $user_id AND role != 'admin'"; // Admin nie może usunąć innego admina
        if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
            echo "<div class='form-message success'>Użytkownik został usunięty pomyślnie.</div>";
        } else {
            echo "<div class='form-message error'>Błąd podczas usuwania użytkownika: Użytkownik może być administratorem lub nie istnieje.</div>";
        }
    }
}

// Pobieranie danych z formularza
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$filterRole = isset($_GET['role']) ? $_GET['role'] : '';

// Budowanie zapytania SQL
$sql = "SELECT * FROM users WHERE 1=1";

// Wyszukiwanie
if (!empty($search)) {
    $sql .= " AND (name LIKE '%$search%' OR surname LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
}

// Filtrowanie po roli
if (!empty($filterRole)) {
    $sql .= " AND role = '$filterRole'";
}

// Sortowanie
if ($sort == 'id_asc') {
    $sql .= " ORDER BY id ASC";
} elseif ($sort == 'id_desc') {
    $sql .= " ORDER BY id DESC";
} elseif ($sort == 'name_asc') {
    $sql .= " ORDER BY name ASC";
} elseif ($sort == 'name_desc') {
    $sql .= " ORDER BY name DESC";
}

$result = $conn->query($sql);

// Formularz wyszukiwania, filtrowania i sortowania
?>
<h1>Zarządzanie Użytkownikami</h1>
<a href='add_user.php'><button class='btn'>Dodaj Użytkownika</button></a>
<a href='admin.php'><button class='btn btn-back'>Powrót do Panelu Admina</button></a>

<form id="filter-form" method="GET" action="manage_users.php" class="filter-form">
    <input type="text" name="search" placeholder="Wyszukaj..." value="<?php echo htmlspecialchars($search); ?>">

    <select name="role">
        <option value="">Wszystkie role</option>
        <option value="user" <?php if ($filterRole == 'user') echo 'selected'; ?>>Użytkownik</option>
        <option value="admin" <?php if ($filterRole == 'admin') echo 'selected'; ?>>Administrator</option>
    </select>

    <select name="sort">
        <option value="">Sortuj</option>
        <option value="id_asc" <?php if ($sort == 'id_asc') echo 'selected'; ?>>ID rosnąco</option>
        <option value="id_desc" <?php if ($sort == 'id_desc') echo 'selected'; ?>>ID malejąco</option>
        <option value="name_asc" <?php if ($sort == 'name_asc') echo 'selected'; ?>>Imię rosnąco</option>
        <option value="name_desc" <?php if ($sort == 'name_desc') echo 'selected'; ?>>Imię malejąco</option>
    </select>

    <button type="submit" class="btn btn-filter">Filtruj</button>
</form>

<?php
// Wyświetlanie użytkowników
if ($result->num_rows > 0) {
    echo "<table class='user-table'>
            <tr>
                <th>ID</th>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>Email</th>
                <th>Numer Telefonu</th>
                <th>Rola</th>
                <th>Akcje</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>" . htmlspecialchars($row['surname']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td>" . htmlspecialchars($row['phone']) . "</td>
                <td>" . htmlspecialchars($row['role']) . "</td>
                <td>
                    <a href='edit_user.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-edit'>Edytuj</a> |
                    <a href='manage_users.php?delete=" . htmlspecialchars($row['id']) . "' class='btn btn-delete' onclick=\"return confirm('Czy na pewno chcesz usunąć tego użytkownika?')\">Usuń</a>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p class='form-message'>Brak użytkowników w systemie.</p>";
}

$conn->close();
?>