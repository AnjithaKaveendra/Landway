# Landway

A real estate website using **HTML, CSS, and JavaScript** for the frontend, **PHP** for the backend, and **MySQL** for persistent data. Bootstrap and jQuery are included locally; no Node.js build or JavaScript application server is required.

The application is in `Landway/`. It supports customer registration and login, property listings and details, saved properties, contact inquiries, and an admin dashboard for accounts, properties, inquiries, employees, and password changes.

## Run locally

Requirements: PHP 7.4+ with `mysqli` and `mysqlnd`, and MySQL 5.7+ or a compatible MariaDB server. XAMPP includes these components.

1. Start MySQL in XAMPP.
2. Open PowerShell in this repository and configure the first administrator:

   ```powershell
   cd Landway
   $env:ADMIN_EMAIL = 'admin@landway.com'
   $adminSecret = Read-Host 'Choose an admin password (8 to 72 bytes)' -AsSecureString
   $env:ADMIN_PASSWORD = [System.Net.NetworkCredential]::new('', $adminSecret).Password
   & C:\xampp\php\php.exe init-db.php --seed
   Remove-Item Env:ADMIN_PASSWORD
   & C:\xampp\php\php.exe -S localhost:8000
   ```

3. Visit **http://localhost:8000**. Sign in with the administrator you configured, or register a customer account.

`--seed` adds the four original example properties only when the properties table is empty. Omit it for an empty catalog. Repeating setup preserves existing records and administrator passwords. Existing databases are upgraded to include listing ownership and saved properties; old browser-storage records are not imported automatically.

Default development database settings are `127.0.0.1:3306`, user `root`, empty password, database `landway_db`. Configure `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASS`, and `DB_NAME` as environment variables, or edit `Landway/settings.php` for your local installation. The same settings must apply to setup and the PHP server.

See [the setup and API guide](Landway/PHP_MYSQL_SETUP.md) for Apache, SQL imports, troubleshooting, and endpoint details.

## Verification

```powershell
python tests/integration.py
python tests/integration.py --browser
```

Tests create and remove a separate `landway_test_*` database and stop their own PHP server. They never select the development database for test writes. `PHP_BIN` can override the PHP executable. Browser verification additionally requires Python Playwright and its Chromium browser.
