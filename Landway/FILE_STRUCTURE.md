# Project structure

The repository contains the application in `Landway/` and integration checks in `tests/`.

| Path inside the application | Purpose |
| --- | --- |
| `settings.php` | Database environment variables and development defaults |
| `app.php` | Session initialization, database access, output escaping, shared helpers |
| `config.php` | JSON responses, API validation, access checks, CSRF verification |
| `init-db.php` | Command-line database creation, schema upgrade, first admin, optional seeds |
| `database/schema.sql` | MySQL table definitions |
| `test-connection.php` | Read-only connection/schema health check |
| `api-*.php` | User, admin, property, inquiry, employee, and favorite endpoints |
| `index.php`, `about.php`, `policy.php` | Home and informational pages |
| `login.php`, `register.php` | Authentication forms |
| `properties.php`, `property-detail.php` | Database-backed property catalog and details |
| `saved-properties.php` | Current customer's saved properties |
| `add-property.php`, `contact.php` | Property and inquiry submission forms |
| `admin-dashboard.php` | Administrator interface |
| `includes/` | Shared site navigation and footer |
| `js/` | Browser behavior and API calls |
| `css/` | Site and dashboard styles |
| `img/`, `lib/` | Existing images and local frontend libraries |
| `property-detail-1.php` through `property-detail-4.php` | Compatibility redirects |
| `Land Way/register.php` | Compatibility registration redirect |

The nested `Land Way/` asset copies are retained for compatibility. The application uses the main directory's assets and one registration flow.

See [PHP_MYSQL_SETUP.md](PHP_MYSQL_SETUP.md) for setup and endpoint details.
