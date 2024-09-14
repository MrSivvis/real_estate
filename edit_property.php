<?php
require('db.php');
require('session.php');
include('menu.php');

// Sprawdzenie, czy użytkownik jest adminem
if ($_SESSION['role'] != 'admin') {
    echo "<div class='alert alert-danger'>Brak dostępu. Tylko administrator może edytować nieruchomości.</div>";
    exit;
}

// Pobieranie danych nieruchomości
if (isset($_GET['id'])) {
    $property_id = intval($_GET['id']);  // Bezpieczniejsze przekonwertowanie na int
    $sql = "SELECT * FROM properties WHERE id = $property_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $property = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-warning'>Nie znaleziono nieruchomości.</div>";
        exit;
    }

    // Pobieranie zdjęć nieruchomości
    $images = [];
    $image_sql = "SELECT * FROM images WHERE property_id = $property_id";
    $image_result = $conn->query($image_sql);
    if ($image_result->num_rows > 0) {
        while ($row = $image_result->fetch_assoc()) {
            $images[] = $row;
        }
    }
} else {
    echo "<div class='alert alert-warning'>Nieprawidłowe żądanie.</div>";
    exit;
}

// Aktualizacja nieruchomości
if (isset($_POST['edit_property'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $short_description = !empty($_POST['short_description']) ? mysqli_real_escape_string($conn, $_POST['short_description']) : null;
    $full_description = !empty($_POST['full_description']) ? mysqli_real_escape_string($conn, $_POST['full_description']) : null;
    
    // Sprawdzanie wartości i obsługa 0
    $property_area = isset($_POST['property_area']) && $_POST['property_area'] !== '' ? floatval($_POST['property_area']) : null;
    $plot_area = isset($_POST['plot_area']) && $_POST['plot_area'] !== '' ? floatval($_POST['plot_area']) : null;
    $rooms = isset($_POST['rooms']) && $_POST['rooms'] !== '' ? intval($_POST['rooms']) : null;

    $location = !empty($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : null;
    $status = !empty($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : null;
    $price = !empty($_POST['price']) ? mysqli_real_escape_string($conn, $_POST['price']) : null;
    $type = !empty($_POST['type']) ? mysqli_real_escape_string($conn, $_POST['type']) : null;
    $notes = !empty($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : null;

    // Tworzenie zapytania SQL do aktualizacji nieruchomości
    $sql = "UPDATE properties SET 
            title='$title', 
            short_description=" . ($short_description !== null ? "'$short_description'" : "NULL") . ", 
            full_description=" . ($full_description !== null ? "'$full_description'" : "NULL") . ",
            property_area=" . ($property_area !== null ? "'$property_area'" : "NULL") . ", 
            plot_area=" . ($plot_area !== null ? "'$plot_area'" : "NULL") . ", 
            number_of_rooms=" . ($rooms !== null ? "'$rooms'" : "NULL") . ", 
            location=" . ($location !== null ? "'$location'" : "NULL") . ",
            status=" . ($status !== null ? "'$status'" : "NULL") . ",
            price=" . ($price !== null ? "'$price'" : "NULL") . ",
            property_type=" . ($type !== null ? "'$type'" : "NULL") . ", 
            notes=" . ($notes !== null ? "'$notes'" : "NULL") . " 
            WHERE id = $property_id";

    if ($conn->query($sql) === TRUE) {
        // Usuwanie wybranych zdjęć
        if (isset($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $delete_image_id) {
                $delete_sql = "DELETE FROM images WHERE id = $delete_image_id";
                $conn->query($delete_sql);
            }
        }

        // Dodawanie nowych zdjęć
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $file_name = basename($_FILES['images']['name'][$key]);
                $target_file = 'uploads/' . $file_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    // Zapisanie ścieżki zdjęcia w bazie danych
                    $image_sql = "INSERT INTO images (property_id, image_path) VALUES ('$property_id', '$target_file')";
                    mysqli_query($conn, $image_sql);
                } else {
                    echo "<div class='alert alert-danger'>Błąd podczas przesyłania zdjęcia: $file_name</div>";
                }
            }
        }

        echo "<div class='alert alert-success'>Nieruchomość została zaktualizowana pomyślnie. <a href='manage_properties.php'>Wróć do listy nieruchomości</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Błąd podczas aktualizacji nieruchomości: " . $conn->error . "</div>";
    }
}

$conn->close();
?>

<div class="container">
    <form class="form edit-property-form" action="" method="post" enctype="multipart/form-data">
        <h1 class="form-title">Edytuj Nieruchomość</h1>

        <label for="title">Tytuł</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" class="form-input" required>
        
        <label for="short_description">Krótki opis</label>
        <textarea name="short_description" class="form-textarea"><?php echo htmlspecialchars($property['short_description']); ?></textarea>
        
        <label for="full_description">Pełny opis</label>
        <textarea name="full_description" class="form-textarea"><?php echo htmlspecialchars($property['full_description']); ?></textarea>
        
        <label for="property_area">Powierzchnia nieruchomości (m²)</label>
        <input type="number" name="property_area" value="<?php echo htmlspecialchars($property['property_area']); ?>" class="form-input">
        
        <label for="plot_area">Powierzchnia działki (m²)</label>
        <input type="number" name="plot_area" value="<?php echo htmlspecialchars($property['plot_area']); ?>" class="form-input">
        
        <label for="rooms">Liczba pokoi</label>
        <input type="number" name="rooms" value="<?php echo htmlspecialchars($property['number_of_rooms']); ?>" class="form-input">
        
        <label for="location">Lokalizacja</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($property['location']); ?>" class="form-input" required>
        
        <label for="status">Status</label>
        <select name="status" class="form-select" required>
            <option value="rent" <?php if($property['status'] == 'rent') echo 'selected'; ?>>Wynajem</option>
            <option value="sale" <?php if($property['status'] == 'sale') echo 'selected'; ?>>Sprzedaż</option>
        </select>
        
        <label for="price">Cena</label>
        <input type="text" name="price" value="<?php echo htmlspecialchars($property['price']); ?>" class="form-input" required>
        
        <label for="type">Typ nieruchomości</label>
        <input type="text" name="type" value="<?php echo htmlspecialchars($property['property_type']); ?>" class="form-input" required>
        
        <label for="notes">Notatki</label>
        <textarea name="notes" class="form-textarea"><?php echo htmlspecialchars($property['notes']); ?></textarea>

        <!-- Wyświetlanie istniejących zdjęć z możliwością usunięcia -->
        <h3 class="form-subtitle">Istniejące Zdjęcia:</h3>
        <?php foreach ($images as $image) { ?>
            <div class="image-preview">
                <a href="<?php echo $image['image_path']; ?>" target="_blank">
                    <img src="<?php echo $image['image_path']; ?>" class="image-thumbnail">
                </a>
                <input type="checkbox" name="delete_images[]" value="<?php echo $image['id']; ?>"> Usuń to zdjęcie
            </div>
        <?php } ?>

        <!-- Dodawanie nowych zdjęć z podglądem -->
        <label for="images">Dodaj nowe zdjęcia</label>
        <input type="file" name="images[]" multiple onchange="previewImages(event)" class="form-file-input">
        <div id="imagePreview" class="image-preview-container"></div>

        <input type="submit" name="edit_property" value="Zaktualizuj Nieruchomość" class="form-submit">
        
        <!-- Przycisk powrotu do zarządzania nieruchomościami -->
        <a href='manage_properties.php'><button type="button" class="btn btn-back">Powrót do Zarządzania Nieruchomościami</button></a>
        
        <!-- Przycisk odświeżania -->
        <button id="refreshButton" type="button" class="refresh-button">Odśwież ofertę</button>
    </form>
</div>

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
            img.style.maxWidth = '200px';
            img.style.margin = '10px';
            imagePreview.appendChild(img);
        }

        reader.readAsDataURL(file);
    }
}

// Dodanie funkcji odświeżania dla przycisku "Odśwież ofertę"
document.getElementById('refreshButton').addEventListener('click', function() {
    location.reload();  // Odśwież stronę
});
</script>