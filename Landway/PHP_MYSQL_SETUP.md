# PHP / MySQL setup

## Stack and application directory

- Frontend: HTML rendered by PHP, local CSS, and browser JavaScript. Existing Bootstrap and jQuery assets are retained.
- Backend: PHP 7.4+ with `mysqli` and `mysqlnd` (`get_result` support).
- Database: MySQL 5.7+ or compatible MariaDB, using InnoDB and `utf8mb4`.
- No Node.js build step, Composer dependencies, or external API is required.

Serve the `Landway/` directory containing `index.php` as the application root. Opening PHP files directly or using a static-only preview server will not execute the backend.

## Database configuration

`settings.php` reads these environment variables, with development defaults:

| Variable | Default |
| --- | --- |
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_USER` | `root` |
| `DB_PASS` | empty |
| `DB_NAME` | `landway_db` |

Use a database name containing only letters, digits, and underscores. In Apache, configure environment variables for the PHP process or update the development defaults in `settings.php`. Variables set in a PowerShell session apply to processes launched from that session, not an already running Apache service.

## Initialize or upgrade

Start MySQL, then run from this application directory:

```powershell
$env:ADMIN_EMAIL = 'admin@landway.com'
$adminSecret = Read-Host 'Choose an admin password (8 to 72 bytes)' -AsSecureString
$env:ADMIN_PASSWORD = [System.Net.NetworkCredential]::new('', $adminSecret).Password
& C:\xampp\php\php.exe init-db.php --seed
Remove-Item Env:ADMIN_PASSWORD
```

On other installations, use `php` in place of the XAMPP executable path. The database account needs database creation and schema alteration permissions during initialization.

The initializer connects before selecting a database, creates missing tables, adds `properties.ownerUserId` to the original schema, normalizes existing property types, and creates the first administrator using `ADMIN_EMAIL` and `ADMIN_PASSWORD`. Existing administrator accounts and passwords remain unchanged. No built-in administrator password is installed or displayed on the login page.

`--seed` inserts the four original example listings, priced in USD, only when the catalog is empty. Omit it to start without sample properties. The database is the source of truth: new listings appear on both public pages and the admin dashboard.

Setup is a terminal command. Accessing `init-db.php` in a browser returns HTTP 403. `test-connection.php` is a read-only JSON health check; it does not create databases, tables, or accounts.

### SQL import option

Create and select an empty MySQL database in phpMyAdmin, then import `database/schema.sql`. Run `php init-db.php` afterward to create the first administrator or upgrade an existing schema. The SQL file defines tables; it does not contain an administrator password or sample listings.

## Start PHP

```powershell
& C:\xampp\php\php.exe -S localhost:8000
```

Open `http://localhost:8000/index.php`. Alternatively, place this application directory under Apache's document root and browse its matching URL. Relative page and API links support an application hosted in a subdirectory.

The PHP session directory configured by `session.save_path` must be writable by the PHP process. XAMPP normally uses `C:\xampp\tmp`.

## Pages and data

| Page | Behavior |
| --- | --- |
| `index.php` | Home; administrators are directed to the dashboard |
| `properties.php` | Database listings with type, currency, and price filters |
| `property-detail.php?id=123` | Database-backed details, inquiry form, saved property action, and mortgage calculator |
| `property-detail-1.php` through `property-detail-4.php` | Compatibility redirects to the matching listing ID |
| `register.php`, `login.php` | Customer registration; customer or administrator login |
| `add-property.php` | Authenticated property submission |
| `saved-properties.php` | Current customer's saved listings |
| `contact.php` | Public inquiry submission |
| `admin-dashboard.php` | Administrator account, listing, inquiry, and employee management |
| `about.php`, `policy.php` | Informational pages |
| `Land Way/register.php` | Compatibility redirect to the canonical registration page |

Listing images use the included example image for their property type. The original submission form has no upload field; this conversion does not add image upload handling. Existing static property text is provided by the optional seed data. Legacy browser-storage records are not automatically moved to MySQL.

## API contract

Responses are JSON: `{ "status": "success", "data": ... }` or `{ "status": "error", "message": "..." }`. Creation responses include `id` and HTTP 201. Validation errors use 422, invalid credentials 401, denied access 403, missing records 404, duplicate values 409, and unavailable database access 503.

All writes use POST. Reads use GET. POST requests require the PHP session cookie and `X-CSRF-Token` header. Pages provide a token in `<meta name="csrf-token">`; `js/api.js` attaches it to same-origin `fetch` requests. Other clients can get a token with `GET api-users.php?action=session` and must retain the session cookie. Form posts may provide `csrfToken` instead. Refresh the token after logout.

| Endpoint | Actions and access |
| --- | --- |
| `api-users.php` | Public POST `register`, `login`; GET `session`; POST `logout`; admin GET `get-all`, `get&id=...`; admin POST `update`, `delete` |
| `api-admin.php` | POST `login`, `logout`; admin POST `change-password` |
| `api-properties.php` | Public GET `get-all`, `get&id=...`; authenticated POST `create`; owner or admin POST `update`, `delete` |
| `api-inquiries.php` | Public POST `create`; admin GET `get-all`, `get&id=...`; admin POST `delete` |
| `api-employees.php` | Admin GET `get-all`, `get&id=...`; admin POST `create`, `update`, `delete` |
| `api-favorites.php` | Customer GET `get-all`; customer POST `toggle` with property `id` |

Use `?action=...` on each endpoint. Update and delete requests send `id` in the JSON body. Update endpoints also accept a query `id`; property and employee updates preserve omitted fields.

- Registration: `firstName`, `lastName`, `email`, optional `phone`, and `password` (8–72 bytes).
- Login: `email`, `password`.
- Property creation: required `propertyTitle`, `propertyType` (`house`, `land`, `apartment`, `commercial`), `propertyStatus` (`sale`, `rent`, `lease`), `propertyAddress`, `propertyCity`, positive `propertyPrice`, `ownerName`, `ownerEmail`, `ownerPhone`. Optional description, state, postal code, country, bedrooms, bathrooms, area, year, garage, currency (`LKR`, `USD`, `EUR`, `GBP`), features, and WhatsApp fields use the names in `database/schema.sql`. `features` accepts an array or comma-separated string. The backend assigns ownership from the session.
- Inquiry creation: required `contactName`, `contactEmail`, `contactMessageText`; optional `contactPhone`, `contactSubject`, `contactPropertyType`. Listing inquiry subjects include the property ID and title.
- Employee creation: `employeeId`, `firstName`, `lastName`, `email`, `position`; optional `phone`, `hireDate` (`YYYY-MM-DD`, defaults to today).
- Password change: `currentPassword`, `newPassword`.

PHP sessions determine access; browser storage is not used for identity or records. Passwords are hashed, queries use prepared statements, and stored text is escaped when rendered into HTML. Account API responses omit password hashes. Deleting a customer or property also removes related saved-property records.

## Verify and troubleshoot

From the repository root:

```powershell
python tests/integration.py
python tests/integration.py --browser
```

Tests use a generated `landway_test_*` database, initialize it twice, exercise HTTP APIs and authorization, and remove that database on completion. The optional browser pass covers real forms and navigation with Playwright. Set `PHP_BIN` if PHP is elsewhere; configure the `DB_*` environment variables for your local database server. The test database name is always generated separately from `DB_NAME`.

- Connection failure: start MySQL and verify the same database settings are used by setup and the PHP server.
- Missing schema or ownership column: rerun `php init-db.php` against the intended database.
- Session warnings: give the PHP process access to its configured session directory.
- Expired session / HTTP 403 on a form: refresh the page to obtain its current CSRF token.
- PHP source displayed in the browser: use Apache with PHP enabled or the PHP development server.
- Existing administrator credentials: rerunning initialization preserves them; sign in and use the dashboard's Change Password action.
