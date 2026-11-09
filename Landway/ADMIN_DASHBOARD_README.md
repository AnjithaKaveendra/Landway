# Admin dashboard

`admin-dashboard.php` renders the dashboard only for an authenticated administrator. `js/admin-dashboard-db.js` loads accounts, properties, inquiries, and employees through PHP JSON APIs. Counts and tables come from MySQL.

Supported interface actions include viewing and deleting accounts, listings, and inquiries; adding, viewing, and deleting employees; changing the current administrator password; and logging out. Update APIs for accounts, properties, and employees are also available; their fields are documented in [PHP_MYSQL_SETUP.md](PHP_MYSQL_SETUP.md).

Submitted text is escaped when rendered into dashboard tables and dialogs. API authorization is enforced on the server. Session cookies and CSRF tokens protect requests that change data.

`js/admin-dashboard.js` is a compatibility entry point for the database script. The application has one dashboard implementation and no browser-storage database.

See [Admin quick start](ADMIN_QUICK_START.md) to sign in and [the setup guide](PHP_MYSQL_SETUP.md) to initialize or upgrade MySQL.
