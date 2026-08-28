# Accent2 — memorie proiect

## Stack

- **Framework:** CodeIgniter 3 + HMVC (Modular Extensions / MX)
- **PHP:** 7.4 (php-fpm via `.htaccess`)
- **DB:** MySQL, baza `accent2`, credențiale în `app/config/database.php`
- **Workspace:** `/var/www/html/accent2`

## URL-uri

| Rol | URL |
|-----|-----|
| Frontend | `http://192.168.231.19/accent2/` |
| Backend mascat | `http://192.168.231.19/accent2/actmanager241` |
| Backend real | `https://192.168.231.19/accent2/backend/` |
| Fileman | `http://192.168.231.19/accent2/fileman/` |

`base_url` în `app/config/config.php` este **HTTPS** — link-urile generate folosesc `https://`.

## Entry point și routing

1. **Apache** [`.htaccess`](.htaccess): `RewriteBase /accent2/`; tot ce nu e fișier static → `index.php?/$uri`
2. **`index.php`**: flocker POST lock, versiune newux, selectare temă (`newux` / `pay24`), `ENVIRONMENT=production`, bootstrap CI
3. **Rute** [`app/config/routes.php`](app/config/routes.php): `actmanager241` → `backend/allowlogin`, default `home`
4. **HMVC**: module în `app/modules/{Module}/controllers/`; router MX în `app/third_party/MX/`

## Teme

| Context | Temă | Activare |
|---------|------|----------|
| Frontend curent | **newux** | `?newux=` implicit sau cookie `newuxtheme=1` |
| Backend / admin | **accent** | URI `/backend` sau `/actmanager241` |
| App mobile Pay24 | **pay24** | `?pay24`, cookie `pay24theme`, header app |

Config temă: `app/config/theme.php` + library `app/libraries/Theme.php`.
View-uri teme: `themes/{theme}/`; `VIEWPATH` = rădăcina proiectului.

## Backend — acces protejat

1. **`/actmanager241`** → `Backend::allowlogin()` setează sesiune `allowbk=1`, apoi `index()`
2. **`/backend/*`** fără `allowbk` → HTTP 403 (`MX_Controller`)
3. Login: `backend/account/login` (permisiune `backend-access`)
4. Permisiuni: `app/config/permissions.php` + library `User`

## Fileman (RoxyFileman)

- **Direct:** `/fileman/` — exclus din rewrite CI
- **Securitate:** `fileman/php/security.inc.php` bootstrapează CI; necesită `$_SESSION['FILEMAN_ACTIVE']`
- Flag setat în `themes/accent/addons/fileman/index.php` pentru useri cu `backend-access`
- **CKEditor backend:** `backend/file_manager/upload` + `backend/file_manager/files` (CKFinder)

## Email

- **Admin UI:** `backend/general/email` — suportă **ambele** metode Office 365
- **A) Microsoft Graph (OAuth2):** Tenant ID, Client ID, Client Secret; permisiune `Mail.Send` (Application); **fără Redirect URI**
- **B) SMTP:**
  - autenticat: `smtp.office365.com:587` + TLS + user/parolă
  - relay: `*.mail.protection.outlook.com:25`, crypto `none`, fără user (IP pe allowlist)
- **Stocare:** `ac_option` (`email_settings`) via Options_model; override opțional `email.local.php`
- **Modul trimitere:** `Mailer` → `O365_mailer` (Graph) sau CI Email SMTP
- **Test:** butoane „Testează conexiunea” + „Trimite email de test” pe pagina de setări

### Office 365 — ce să ceri de la admin

**Pentru Graph:** Tenant ID, Client ID, Client Secret, confirmare `Mail.Send` + admin consent, mailbox-uri expeditor.

**Pentru SMTP relay:** host (ex. `accenttravel-ro.mail.protection.outlook.com`), port (25), dacă e nevoie de auth, și dacă IP-ul serverului e pe connector.

## Module principale

`Home`, `Cms`, `Trip`, `Travelfuse`, `Paralela45`, `Account`, `Backend`, `Mailer`, `Forms`, `Newsletter`, `Error`

## Restricții importante

- **Nu rula** `artisan test`, `migrate:fresh`, `db:wipe`, import SQL fără cerere explicită
- **Nu modifica** `specs/`, `info-proiect/`, mockup-uri, PDF-uri de specificație
- **Nu comite** secrete (`.env`, `email.local.php`, parole SMTP)

## Fișiere cheie

- Bootstrap: `index.php`, `.htaccess`
- Config: `app/config/config.php`, `routes.php`, `theme.php`, `database.php`, `email.php`
- Securitate backend: `app/modules/Backend/controllers/Backend.php`, `app/third_party/MX/Controller.php`
- SPA newux partials: `app/controllers/Newux.php` → `themes/newux/views/`
