<?php
require('db.php');  // Połączenie z bazą danych //1
require('session.php');  // Sesja użytkownika
include('menu.php');  // Menu wspólne dla wszystkich stron

// Sprawdzenie, czy użytkownik jest adminem//2
if ($_SESSION['role'] != 'admin') {
    echo "Brak dostępu. Tylko administrator może dodawać nieruchomości.";
    exit;
}

//3
// Dodawanie nieruchomości przechwycenie danych z formularza(czy zostal dodany formularz)
if (isset($_POST['add_property'])) {
    // Pobranie danych z formularza i zabezpieczenie ich przed SQL Injection
    $title = !empty($_POST['title']) ? mysqli_real_escape_string($conn, $_POST['title']) : null;
    //mysqli zabezpiecza dane wejsciowa przed atakami
    $short_description = !empty($_POST['short_description']) ? mysqli_real_escape_string($conn, $_POST['short_description']) : null;
    $full_description = !empty($_POST['full_description']) ? mysqli_real_escape_string($conn, $_POST['full_description']) : null;
//Parametr $conn - jest to obiekt połączenia z bazą danych, który jest wymagany, ponieważ różne połączenia mogą mieć różne ustawienia dotyczące “uciekania” znaków.
//Parametr $_POST['title'] - to dane wejściowe od użytkownika, które są przesyłane przez formularz.
    
// Sprawdzanie wartości i obsługa 0
    $property_area = isset($_POST['property_area']) && $_POST['property_area'] !== '' ? floatval($_POST['property_area']) : null;
    $plot_area = isset($_POST['plot_area']) && $_POST['plot_area'] !== '' ? floatval($_POST['plot_area']) : null;
    $rooms = isset($_POST['number_of_rooms']) && $_POST['number_of_rooms'] !== '' ? intval($_POST['number_of_rooms']) : null;

    $location = !empty($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : null;
    $status = (!empty($_POST['status']) && in_array($_POST['status'], ['rent', 'sale'])) ? mysqli_real_escape_string($conn, $_POST['status']) : null;
    $price = !empty($_POST['price']) ? mysqli_real_escape_string($conn, $_POST['price']) : null;  // Obsługa tekstowego pola price
    $property_type = !empty($_POST['property_type']) ? mysqli_real_escape_string($conn, $_POST['property_type']) : null;
    $notes = !empty($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : null;
    $created_at = date('Y-m-d H:i:s');
    
    // Upewnij się, że id jest prawidłowo ustawione w sesji
    $user_id = isset($_SESSION['id']) ? intval($_SESSION['id']) : null;

    // Sprawdzenie, czy user_id nie jest pusty
    if (empty($user_id)) {
        echo "Błąd: Nieprawidłowy użytkownik.";
        exit;
    }

    // Sprawdzenie, czy wszystkie wymagane pola są poprawne
    if (!$status) {
        echo "Błąd: Status musi być 'rent' lub 'sale'.";
        exit;
    }

    // Tworzenie zapytania SQL $sql to string 
    $sql = "INSERT INTO properties (title, short_description, full_description, property_area, plot_area, number_of_rooms, location, status, price, property_type, notes, created_at, user_id) VALUES (
        '$title', 
        " . ($short_description !== null ? "'$short_description'" : "NULL") . ", 
        " . ($full_description !== null ? "'$full_description'" : "NULL") . ", 
        " . ($property_area !== null ? "'$property_area'" : "NULL") . ", 
        " . ($plot_area !== null ? "'$plot_area'" : "NULL") . ", 
        " . ($rooms !== null ? "'$rooms'" : "NULL") . ", 
        '$location', 
        '$status', 
        '$price', 
        " . ($property_type !== null ? "'$property_type'" : "NULL") . ", 
        " . ($notes !== null ? "'$notes'" : "NULL") . ", 
        '$created_at', 
        '$user_id')";
//4
    // Wykonanie zapytania SQL Funkcja mysqli_query() wysyła zapytanie SQL do 
    //serwera bazodanowego przy użyciu połączenia określonego w zmiennej $conn
    if (mysqli_query($conn, $sql)) { //dwa parametry
        $property_id = mysqli_insert_id($conn);  // Pobieranie ID dodanej nieruchomości

        // Obsługa przesyłania zdjęć
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $file_name = basename($_FILES['images']['name'][$key]);
                $target_file = 'uploads/' . $file_name;  // Katalog docelowy

                if (move_uploaded_file($tmp_name, $target_file)) {
                    // Zapisanie ścieżki zdjęcia w bazie danych
                    $image_sql = "INSERT INTO images (property_id, image_path) VALUES ('$property_id', '$target_file')";
                    mysqli_query($conn, $image_sql);
                } else {
                    echo "<div class='form'>Błąd podczas przesyłania zdjęcia: $file_name</div>";
                }
            }
        }

        echo "<div class='form'>Nieruchomość została dodana pomyślnie. <a href='manage_properties.php'>Wróć do listy nieruchomości</a></div>";
    } else {
        echo "<div class='form'>Błąd podczas dodawania nieruchomości: " . mysqli_error($conn) . "</div>";  // Wyświetlanie błędu SQL
    }
} //1.	Zapytanie SQL jest wykonywane za pomocą funkcji mysqli_query($conn, $sql). 
//To właśnie tutaj zapytanie SQL zapisane w zmiennej $sql jest wysyłane do serwera bazodanowego.
//2.	mysqli_insert_id($conn): Jeśli zapytanie INSERT zakończy się powodzeniem, 
//ta funkcja pobiera ID ostatnio dodanego rekordu w bieżącym połączeniu z bazą danych.

$conn->close();
?>

<form class="form" action="" method="post" enctype="multipart/form-data">
    <h1>Dodaj Nieruchomość</h1>

    <label for="title">Tytuł</label>
    <input type="text" name="title" placeholder="Tytuł" required>

    <label for="short_description">Krótki opis</label>
    <textarea name="short_description" placeholder="Krótki opis"></textarea>

    <label for="full_description">Pełny opis</label>
    <textarea name="full_description" placeholder="Pełny opis"></textarea>

    <label for="property_area">Powierzchnia nieruchomości (m²)</label>
    <input type="number" name="property_area" placeholder="Powierzchnia nieruchomości">

    <label for="plot_area">Powierzchnia działki (m²)</label>
    <input type="number" name="plot_area" placeholder="Powierzchnia działki">

    <label for="number_of_rooms">Liczba pokoi</label>
    <input type="number" name="number_of_rooms" placeholder="Liczba pokoi">

    <label for="location">Lokalizacja</label>
    <input type="text" name="location" placeholder="Lokalizacja" required>

    <label for="status">Status</label>
    <select name="status" required>
        <option value="rent">Wynajem</option>
        <option value="sale">Sprzedaż</option>
    </select>

    <label for="price">Cena (np. 2400/miesiąc lub 2400)</label>
    <input type="text" name="price" placeholder="Cena (np. 2400/miesiąc lub 2400)">

    <label for="property_type">Typ nieruchomości</label>
    <input type="text" name="property_type" placeholder="Typ nieruchomości">

    <label for="notes">Notatki</label>
    <textarea name="notes" placeholder="Notatki"></textarea>

    <label for="images">Dodaj zdjęcia</label>
    <input type="file" name="images[]" multiple onchange="previewImages(event)">
    <div id="imagePreview"></div>

    <input type="submit" name="add_property" value="Dodaj Nieruchomość">

    <!-- Dodany przycisk powrotu do zarządzania nieruchomościami -->
    <a href='manage_properties.php'><button type="button" class="btn btn-back">Powrót do Zarządzania Nieruchomościami</button></a>
</form>

<script>
function previewImages(event) {
    var imagePreview = document.getElementById('imagePreview');
    imagePreview.innerHTML = '';  // Wyczyść poprzednie podglądy

    for (var i = 0; i < event.target.files.length; i++) {
        var file = event.target.files[i];
        var reader = new FileReader();

        reader.onload = function(e) {
            var img = document.createElement('img');  
            img.src = e.target.result;
            img.style.maxWidth = '200px';  // Maksymalna szerokość zdjęcia w podglądzie
            img.style.margin = '10px';
            imagePreview.appendChild(img);
        }

        reader.readAsDataURL(file);
    }
}
</script>
