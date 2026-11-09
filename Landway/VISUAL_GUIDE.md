# Interface guide

The conversion retains the site's Bootstrap-based styling, property-card classes, detail layout, and admin dashboard styles.

Public navigation links to Home, About, Properties, List Property, and Contact. Customers additionally see Saved Properties and Logout; administrators see Dashboard and Logout. The navigation collapses on mobile.

The property catalog offers type, currency, and price filters. Detail pages show listing values stored in MySQL, an inquiry form, saved-property action, sharing and printing, and a mortgage calculator. Missing records display a 404 message; database failures display an unavailable message.

The admin dashboard has Accounts, Properties, Inquiries, and Employees sections with database counts, record tables, and detail dialogs. Its account menu offers Change Password and Logout.

Authentication pages use the existing form styling. Login works for customer and administrator accounts. No administrator password is displayed in the interface.

See [PHP_MYSQL_SETUP.md](PHP_MYSQL_SETUP.md) for setup and [ADMIN_QUICK_START.md](ADMIN_QUICK_START.md) for administrator usage.
