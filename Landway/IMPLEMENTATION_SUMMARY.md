# Conversion summary

Landway uses HTML, CSS, and browser JavaScript for its interface, PHP for request handling and server sessions, and MySQL for persistent records. Bootstrap and jQuery remain local frontend assets. No application build step is needed.

- Public property cards and detail pages use the same MySQL records as the admin dashboard.
- Customer registration, both login flows, property submission, contact forms, employees, and password changes use PHP APIs.
- Saved listings persist in MySQL and are available through the customer navigation.
- All active navigation uses PHP URLs. Original numbered detail URLs and the nested registration URL redirect to their canonical pages.
- The initializer creates a database before selecting it, upgrades the existing schema, and supports optional example listings.
- PHP sessions enforce authentication and authorization. Prepared statements, input validation, CSRF tokens, password hashing, and output escaping are implemented.

Existing database records and administrator passwords are preserved by setup. Data stored by the earlier browser-only implementation is not automatically imported. Property images use the included example image for each property type; image upload is outside the original form's capabilities.

Use [PHP_MYSQL_SETUP.md](PHP_MYSQL_SETUP.md) for installation and API details. The repository's `tests/integration.py` verifies the real PHP/MySQL APIs in a disposable database, with optional Playwright browser coverage.
