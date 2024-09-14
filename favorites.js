$(document).ready(function() {
    console.log("favorites.js załadowany i gotowy");

    // Obsługa zdarzeń kliknięć dla przycisków "Dodaj do ulubionych" i "Usuń z ulubionych"
    $(document).on('click', '.add-favorite, .remove-favorite', function() {
        const button = $(this);
        const propertyId = button.data('property-id');
        const action = button.hasClass('add-favorite') ? 'add' : 'remove';

        console.log(`Kliknięto przycisk '${action === 'add' ? 'Dodaj do ulubionych' : 'Usuń z ulubionych'}' dla nieruchomości o ID:`, propertyId);

        $.ajax({
            url: action === 'add' ? 'add_to_favorites.php' : 'remove_from_favorites.php',
            type: 'POST',
            data: { property_id: propertyId },
            success: function(response) {
                console.log("Odpowiedź serwera:", response);
                
                // Zmień treść przycisku i jego klasę po pomyślnym dodaniu lub usunięciu
                if (action === 'add') {
                    button.removeClass('add-favorite').addClass('remove-favorite');
                    button.text('Usuń z ulubionych');
                } else {
                    button.removeClass('remove-favorite').addClass('add-favorite');
                    button.text('Dodaj do ulubionych');
                }

                // Dodaj komunikat do dokumentu HTML zamiast alertu
                $('body').prepend(response); // Dodaje komunikat na początku strony

                // Automatyczne ukrycie wiadomości po 3 sekundach
                setTimeout(function() {
                    $('.form-message').fadeOut(500, function() {
                        $(this).remove(); // Usunięcie wiadomości z DOM po ukryciu
                    });
                }, 3000);
            },
            error: function() {
                console.log(`Wystąpił błąd podczas ${action === 'add' ? 'dodawania do ulubionych' : 'usuwania z ulubionych'}.`);
                alert('Wystąpił błąd po stronie serwera. Spróbuj ponownie.');
            }
        });
    });
});