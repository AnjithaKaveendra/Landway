# Admin quick start

Follow [PHP / MySQL setup](PHP_MYSQL_SETUP.md) to initialize the database and create the first administrator. Existing administrator accounts and passwords are preserved when setup is repeated.

1. Start MySQL and serve this directory with PHP.
2. Open `login.php` and enter your administrator credentials.
3. The server creates a PHP session and opens `admin-dashboard.php`.
4. Use Accounts, Properties, Inquiries, or Employees in the sidebar to view database records. View and Delete actions work on those records; Add Employee creates a database employee.
5. Use the account menu to change your password or log out.

There is no built-in administrator password. Customer registration creates a customer account and cannot grant administrator access. Private API routes check the PHP session on every request.

For configuration, endpoint fields, troubleshooting, and verification, see [the setup guide](PHP_MYSQL_SETUP.md).
