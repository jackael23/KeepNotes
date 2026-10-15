# KeepNotes

A Google Keep–style notes app built with **Vue 3** and **Laravel**. Users sign up, write rich-text notes, set reminders, and move notes between Notes, Reminders, Archive and Trash. Originally built as a client project.

## Features

- **Notes** – create, edit and search notes with a rich-text editor (TinyMCE)
- **Reminders** – set a date and time on a note and get an in-app alert
- **Archive & Trash** – archive notes, move them to the trash, restore them, or delete them permanently (soft deletes)
- **Accounts** – registration, login and logout with Laravel Fortify and Sanctum (hashed passwords)
- **Private by default** – every note belongs to a user; the API only ever returns or changes the signed-in user's own notes

## Tech stack

| Layer | Tools |
| --- | --- |
| Frontend | Vue 3, Vue Router, Pinia, Vuetify 3, TinyMCE, Axios, Vite |
| Backend | Laravel 9 (PHP 8), Laravel Fortify, Laravel Sanctum (SPA cookie auth) |
| Database | MySQL |
| Tests | PHPUnit feature tests (SQLite in-memory) |

## How it works

```
Vue 3 SPA  ──(Axios, Sanctum session cookie)──▶  Laravel API  ──▶  MySQL
```

- All note endpoints (`/api/get_notes`, `/api/add_note`, `/api/edit_note`, `/api/edit_action`) sit behind `auth:sanctum`.
- Queries go through `$request->user()->notes()`, so a user can never read or change someone else's note — those requests return **404**.
- Input is validated; invalid data returns **422** with messages, which the frontend shows in a popup.

## Running it locally

Requirements: PHP 8.0+, Composer, Node.js, MySQL.

```bash
composer install
cp .env.example .env          # then set DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan key:generate
php artisan migrate

npm install
npm run dev                   # Vite dev server

php artisan serve             # http://127.0.0.1:8000
```

## Tests

```bash
php artisan test
```

The feature tests in `tests/Feature/NotesSecurityTest.php` check that:

- guests cannot use the notes API (401)
- users only see their own notes
- users cannot edit or trash another user's note (404)
- invalid input returns validation errors (422)
- long notes are saved in full

## Security fixes (Oct 2026)

The first version had a few problems that were fixed and covered by tests:

- note endpoints were public → now require login
- notes were not tied to a user → added `user_id` and scoped every query
- an old lookup route compared plain-text passwords → removed (Fortify only)
- note content was limited to 255 characters → now `LONGTEXT`
- save errors were silently ignored → validation + error messages

## Author

Michael C. Leonida — [Portfolio](https://micleoportfolio.netlify.app/)
