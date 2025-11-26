# Event Manager

## Instalacja

- Wymagania: WordPress 6.x+, PHP 7.4+, aktywny Advanced Custom Fields (pro lub free). Front używa Tailwind Play CDN (`@tailwindcss/browser@4`).
- Skopiuj katalog `event-manager` do `wp-content/plugins/` lub zainstaluj go jako zip z panelu WP.
- W panelu WP aktywuj wtyczkę **Event Manager**.

## Funkcjonalność

- Custom Post Type `event` z taksonomią `city`.
- Pola ACF: data i godzina rozpoczęcia, limit uczestników, opis (WYSIWYG).
- Rejestracje przechowywane w post meta `event_registrations` (imię, email, timestamp).
- Widok pojedynczego eventu zawiera formularz zapisu i statystyki limitu/zapisów.
- Zapisy realizowane przez AJAX (bez przeładowania strony, czysty JS bez jQuery).

## AJAX Endpoints

- Endpoint: `/wp-admin/admin-ajax.php?action=register_event`
- Metoda: `POST`
- Parametry: `event_id` (int), `name` (string), `email` (string), `nonce` (wp nonce `em_register_event`).
- Przykładowy response (sukces):

```json
{
  "success": true,
  "data": {
    "message": "Zapisano na wydarzenie.",
    "registrations": 3,
    "limit": 10
  }
}
```

- Przykładowy response (błąd limitu):

```json
{
  "success": false,
  "data": {
    "message": "Brak miejsc na wydarzenie."
  }
}
```

## Znane ograniczenia / TODO

- Brak panelu admin do przeglądania zapisów (dane tylko w post meta).
- Brak zgód/RODO i eksportu CSV uczestników.
- Brak wyszukiwarki AJAX
