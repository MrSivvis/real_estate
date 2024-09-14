<?php
require('menu.php'); // Wspólne menu nawigacyjne
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Kontakt - Biuro Nieruchomości</title>
    <link rel="stylesheet" href="style.css"> <!-- Link do zewnętrznego pliku CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" /> <!-- Dodanie Leaflet CSS -->
    <style>
        .contact-info-container {
            display: flex; /* Ustawienie flexbox do ułożenia mapy i wizytówki w jednym rzędzie */
            gap: 20px; /* Odstęp między elementami */
            margin-top: 20px; /* Odstęp od formularza */
        }
        .business-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
            width: 45%; /* Szerokość wizytówki */
        }
        .business-card h2 {
            margin-bottom: 10px;
        }
        .business-card p {
            margin: 5px 0;
        }
        #map {
            height: 250px; /* Wysokość mapy */
            width: 45%; /* Szerokość mapy */
        }
    </style>
</head>
<body>
<div class="contact-container">
    <h1 class="page-title">Kontakt z Biurem Nieruchomości</h1>
    <p class="contact-description">Masz pytania? Wypełnij formularz poniżej, albo odwiedź nas osobiście! Serdecznie zapraszamy! </p>
    
    <form class="contact-form" action="send_message.php" method="post">
        <label for="name" class="form-label">Imię i nazwisko:</label>
        <input type="text" id="name" name="name" class="form-input" required>

        <label for="email" class="form-label">Adres email:</label>
        <input type="email" id="email" name="email" class="form-input" required>

        <label for="phone" class="form-label">Numer telefonu:</label>
        <input type="text" id="phone" name="phone" class="form-input" pattern="[0-9]{9}" required>

        <label for="message" class="form-label">Wiadomość:</label>
        <textarea id="message" name="message" rows="5" class="form-input" required></textarea>

        <button type="submit" class="form-submit">Wyślij wiadomość</button>
    </form>

    <!-- Kontener z wizytówką i mapą obok siebie -->
    <div class="contact-info-container">
        <!-- Wizytówka -->
        <div class="business-card">
            <h2>Dane kontaktowe</h2>
            <p><strong>Adres:</strong> ul. Krasińskiego 12, 00-000 Warszawa, Polska</p>
            <p><strong>Telefon:</strong> +48 123 456 789</p>
            <p><strong>Email:</strong> kontakt@biuronieruchomosci.pl</p>
            <p><strong>Godziny otwarcia:</strong> Pon-Pt: 9:00 - 18:00</p>
        </div>

        <!-- Mapa Leaflet -->
        <div id="map"></div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script> <!-- Dodanie Leaflet JS -->
<script>
    // Inicjalizacja mapy Leaflet
    var map = L.map('map').setView([52.274985, 20.990307], 15); // Współrzędne dla ul. Krasińskiego 12, Warszawa

    // Dodanie warstwy mapy
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
    }).addTo(map);

    // Dodanie znacznika na mapie
    L.marker([52.274985, 20.990307]).addTo(map)
        .bindPopup("<b>Biuro Nieruchomości</b><br>ul. Krasińskiego 12, Warszawa")
        .openPopup();
</script>
</body>
</html>