<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('db.php');  // Ścieżka do db.php w głównym folderze
include('menu.php'); // Wspólne menu
?>

<h1 class="page-title">Witamy w Biurze Nieruchomości</h1>
<p class="intro-text">Oferujemy najlepsze nieruchomości na sprzedaż i wynajem. Wybierz jedną z opcji poniżej, aby zobaczyć nasze oferty:</p>

<!-- Sekcja zachęcająca do rejestracji i logowania -->
<?php if (!isset($_SESSION['login'])): ?>
<div class="registration-info">
    <h2 class="info-title">Zarejestruj się i zaloguj, aby uzyskać pełny dostęp!</h2>
    <p class="info-text">Rejestracja i logowanie zapewnia naszym użytkownikom wiele dodatkowych korzyści, w tym:</p>
    
    <div class="benefit">
        <strong>Dodawanie nieruchomości do ulubionych:</strong> Zalogowani użytkownicy mogą zapisywać swoje ulubione oferty nieruchomości, aby łatwo do nich wrócić w przyszłości.
    </div>
    
    <div class="benefit">
        <strong>Dostęp do szczegółowych informacji o ofertach:</strong> Po zalogowaniu możesz przeglądać pełne opisy, zdjęcia oraz informacje o nieruchomościach.
    </div>
    
    <div class="benefit">
        <strong>Powiadomienia o nowych ofertach:</strong> Zarejestrowani użytkownicy mogą otrzymywać powiadomienia o nowych nieruchomościach, które spełniają ich kryteria.
    </div>
    
    <div class="benefit">
        <strong>Łatwiejsze i szybsze wyszukiwanie ofert:</strong> Logowanie pozwala na korzystanie z zaawansowanych filtrów wyszukiwania i sortowania ofert.
    </div>

    <p class="info-text">Nie czekaj, <a href="register.php">zarejestruj się teraz</a> i zacznij korzystać ze wszystkich funkcji! Masz już konto? <a href="login.php">Zaloguj się</a> tutaj.</p>
</div>
<?php endif; ?>

<!-- Informacje o kontakcie -->
<p class="intro-text">Skontaktuj się z nami, aby uzyskać więcej informacji o naszych usługach i ofertach.</p>
<!-- Informacje o kontakcie -->

<div class="button-container">
    <a href="rent.php"><button class="btn action-button">Nieruchomości na Wynajem</button></a>
    <a href="buy.php"><button class="btn action-button">Nieruchomości na Sprzedaż</button></a>
</div>



