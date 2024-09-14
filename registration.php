<?php
require('db.php');
include('menu.php'); // Wspólne menu nawigacyjne
if (isset($_POST['login'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $phone = $_POST['phone'];

    // Walidacja formularza
    if (empty($login) || empty($password) || empty($email) || empty($name) || empty($surname) || empty($phone)) {
        echo "<div class='form-message error'>Wszystkie pola są wymagane. <a href='registration.php'>Spróbuj ponownie.</a></div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='form-message error'>Niepoprawny adres email. <a href='registration.php'>Spróbuj ponownie.</a></div>";
    } elseif (!preg_match("/^[0-9]{9}$/", $phone)) { // Polski numer telefonu składa się z 9 cyfr
        echo "<div class='form-message error'>Niepoprawny numer telefonu. Wprowadź 9 cyfr. <a href='registration.php'>Spróbuj ponownie.</a></div>";
    } else {
        // Sprawdzenie, czy login jest już wykorzystany
        $checkLoginSql = "SELECT * FROM users WHERE login='$login'";
        $checkResult = $conn->query($checkLoginSql);

        if ($checkResult->num_rows > 0) {
            echo "<div class='form-message error'>Ten login jest już zajęty. Wybierz inny. <a href='registration.php'>Spróbuj ponownie.</a></div>";
        } else {
            // Hashowanie hasła dla bezpieczeństwa
            $hashed_password = md5($password);

            // Przygotowanie zapytania SQL
            $sql = "INSERT INTO users (login, password, email, name, surname, phone) VALUES ('$login', '$hashed_password', '$email', '$name', '$surname', '$phone')";

            if ($conn->query($sql) === TRUE) {
                echo "<div class='form-message success'>Rejestracja zakończona sukcesem! <a href='login.php'>Zaloguj się</a></div>";
            } else {
                echo "<div class='form-message error'>Błąd podczas rejestracji: " . $conn->error . "</div>";
            }
        }
    }
} else {
?>
<div class="registration-container">
    <form class="form" action="" method="post">
        <h1 class="form-title">Rejestracja</h1>
        <input type="text" class="form-input" name="login" placeholder="Login" required />
        <input type="password" class="form-input" name="password" placeholder="Hasło" required />
        <input type="text" class="form-input" name="name" placeholder="Imię" required />
        <input type="text" class="form-input" name="surname" placeholder="Nazwisko" required />
        <input type="email" class="form-input" name="email" placeholder="Email" required />
        <!-- Zmodyfikowane pole do wpisywania numeru telefonu -->
        <input type="text" class="form-input" name="phone" placeholder="Numer telefonu" pattern="[0-9]{9}" required />
        <input type="submit" name="submit" value="Zarejestruj się" class="form-button">
        <p class="form-link"><a href="login.php">Zaloguj się</a></p>
    </form>
</div>
<!-- Sekcja zalet logowania i rejestracji -->
<div class="benefits-container">
    <h2 class="benefits-title">Dlaczego warto się zarejestrować i zalogować?</h2>
    
    <p class="benefit-text"><strong>Spersonalizowane doświadczenie:</strong> Zarejestrowani użytkownicy mogą dostosować swoje preferencje, aby szybciej znaleźć interesujące oferty.</p>
    
    <p class="benefit-text"><strong>Dodawanie nieruchomości do ulubionych:</strong> Po zalogowaniu możesz dodawać nieruchomości do ulubionych i wracać do nich w każdej chwili.</p>
    
    <p class="benefit-text"><strong>Dostęp do szczegółowych informacji:</strong> Tylko zalogowani użytkownicy mają pełny dostęp do szczegółowych opisów, zdjęć i danych kontaktowych dotyczących nieruchomości.</p>
    
    <p class="benefit-text"><strong>Powiadomienia o nowych ofertach:</strong> Bądź na bieżąco z najnowszymi ofertami, które spełniają Twoje kryteria.</p>
    
    <p class="benefit-text"><strong>Łatwiejsze zarządzanie swoimi preferencjami:</strong> Użytkownicy mogą łatwo filtrować, sortować i przeszukiwać nieruchomości według swoich preferencji.</p>
    
   
</div>

<?php } ?>