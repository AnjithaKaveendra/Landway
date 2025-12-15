# Landway PHP/MySQL Conversion - Setup Guide

## 📋 Overview
The Landway project has been converted from a client-side localStorage system to a full PHP/MySQL backend with comprehensive CRUD operations for:
- User Accounts (Registration, Login, Management)
- Properties (Create, Read, Update, Delete)
- Inquiries (Submit, View, Delete)
- Employees (Add, View, Update, Delete)
- Admin Authentication & Management

## 🔧 Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server with PHP support
- Web browser with JavaScript enabled

## 📁 Project Structure

### Core Files
- `config.php` - Database connection configuration
- `init-db.php` - Database initialization script (run once)

### API Endpoints (RESTful)
- `api-users.php` - User management endpoints
- `api-properties.php` - Property management endpoints
- `api-inquiries.php` - Inquiry management endpoints
- `api-employees.php` - Employee management endpoints
- `api-admin.php` - Admin authentication and management

### PHP Pages (Frontend)
- `login.php` - User/Admin login page
- `register.php` - New user registration
- `contact.php` - Customer inquiry form
- `add-property.php` - Property listing form
- `admin-dashboard.php` - Admin management dashboard
- `index.php`, `properties.php`, etc. - Main site pages (unchanged)

### JavaScript Files
- `js/admin-dashboard-db.js` - Admin dashboard logic (database version)

## 🚀 Installation Steps

### Step 1: Database Setup
1. Open phpMyAdmin or MySQL console
2. Create a database called `landway_db`:
   ```sql
   CREATE DATABASE landway_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Navigate to your project root in browser:
   ```
   http://localhost/Landway/init-db.php
   ```
   This will:
   - Create all necessary tables
   - Create a default admin user
   - Set up proper indexes and relationships

### Step 2: Update Database Configuration
Edit `config.php` with your database credentials:
```php
define('DB_HOST', 'localhost');  // Your database host
define('DB_USER', 'root');       // Your MySQL username
define('DB_PASS', '');           // Your MySQL password
define('DB_NAME', 'landway_db'); // Database name
```

### Step 3: Verify Installation
1. Go to `http://localhost/Landway/login.php`
2. Login with demo admin account:
   - Email: `admin@landway.com`
   - Password: `Admin123456`

3. Access admin dashboard at `http://localhost/Landway/admin-dashboard.php`

## 📚 API Documentation

### User Management (`api-users.php`)

#### Register User
```
POST /api-users.php?action=register
Body: { firstName, lastName, email, phone, password }
Response: { status, message, id }
```

#### User Login
```
POST /api-users.php?action=login
Body: { email, password }
Response: { status, message, user }
```

#### Get All Users
```
GET /api-users.php?action=get-all
Response: { status, data: [users] }
```

#### Get User by ID
```
GET /api-users.php?action=get&id=1
Response: { status, data: user }
```

#### Update User
```
POST /api-users.php?action=update&id=1
Body: { firstName, lastName, phone }
Response: { status, message }
```

#### Delete User
```
POST /api-users.php?action=delete
Body: { id }
Response: { status, message }
```

### Property Management (`api-properties.php`)

#### Create Property
```
POST /api-properties.php?action=create
Body: { propertyTitle, propertyType, propertyStatus, propertyDescription, 
        propertyAddress, propertyCity, propertyBedrooms, propertyBathrooms,
        propertyArea, propertyPrice, propertyCurrency, features, ownerName, ownerEmail, ownerPhone }
Response: { status, message, id }
```

#### Get All Properties
```
GET /api-properties.php?action=get-all
Response: { status, data: [properties] }
```

#### Get Property by ID
```
GET /api-properties.php?action=get&id=1
Response: { status, data: property }
```

#### Update Property
```
POST /api-properties.php?action=update&id=1
Body: { propertyTitle, propertyStatus, propertyPrice }
Response: { status, message }
```

#### Delete Property
```
POST /api-properties.php?action=delete
Body: { id }
Response: { status, message }
```

### Inquiry Management (`api-inquiries.php`)

#### Create Inquiry
```
POST /api-inquiries.php?action=create
Body: { contactName, contactEmail, contactPhone, contactSubject, 
        contactPropertyType, contactMessageText }
Response: { status, message, id }
```

#### Get All Inquiries
```
GET /api-inquiries.php?action=get-all
Response: { status, data: [inquiries] }
```

#### Get Inquiry by ID
```
GET /api-inquiries.php?action=get&id=1
Response: { status, data: inquiry }
```

#### Delete Inquiry
```
POST /api-inquiries.php?action=delete
Body: { id }
Response: { status, message }
```

### Employee Management (`api-employees.php`)

#### Create Employee
```
POST /api-employees.php?action=create
Body: { employeeId, firstName, lastName, email, phone, position, hireDate }
Response: { status, message, id }
```

#### Get All Employees
```
GET /api-employees.php?action=get-all
Response: { status, data: [employees] }
```

#### Get Employee by ID
```
GET /api-employees.php?action=get&id=1
Response: { status, data: employee }
```

#### Update Employee
```
POST /api-employees.php?action=update&id=1
Body: { firstName, lastName, email, phone, position }
Response: { status, message }
```

#### Delete Employee
```
POST /api-employees.php?action=delete
Body: { id }
Response: { status, message }
```

### Admin Management (`api-admin.php`)

#### Admin Login
```
POST /api-admin.php?action=login
Body: { email, password }
Response: { status, message, admin }
```

#### Change Admin Password
```
POST /api-admin.php?action=change-password
Body: { currentPassword, newPassword }
Response: { status, message }
```

#### Admin Logout
```
POST /api-admin.php?action=logout
Response: { status, message }
```

## 🗄️ Database Tables

### users
- id (INT, Primary Key, Auto Increment)
- firstName (VARCHAR 100)
- lastName (VARCHAR 100)
- email (VARCHAR 120, Unique)
- phone (VARCHAR 20)
- password (VARCHAR 255, Hashed)
- registrationDate (TIMESTAMP)

### properties
- id (INT, Primary Key)
- propertyTitle (VARCHAR 150)
- propertyType (VARCHAR 50)
- propertyStatus (VARCHAR 50) - sale, rent, lease
- propertyDescription (LONGTEXT)
- propertyAddress (VARCHAR 255)
- propertyCity (VARCHAR 100)
- propertyState (VARCHAR 100)
- propertyZipCode (VARCHAR 20)
- propertyCountry (VARCHAR 100)
- propertyBedrooms (INT)
- propertyBathrooms (INT)
- propertyArea (DECIMAL 10,2)
- propertyYearBuilt (INT)
- propertyGarage (VARCHAR 50)
- propertyPrice (DECIMAL 15,2)
- propertyCurrency (VARCHAR 10)
- features (LONGTEXT) - Comma-separated
- ownerName (VARCHAR 100)
- ownerEmail (VARCHAR 120)
- ownerPhone (VARCHAR 20)
- ownerWhatsApp (VARCHAR 20)
- listedDate (TIMESTAMP)

### inquiries
- id (INT, Primary Key)
- contactName (VARCHAR 100)
- contactEmail (VARCHAR 120)
- contactPhone (VARCHAR 20)
- contactSubject (VARCHAR 200)
- contactPropertyType (VARCHAR 50)
- contactMessageText (LONGTEXT)
- inquiryDate (TIMESTAMP)

### employees
- id (INT, Primary Key)
- employeeId (VARCHAR 50, Unique)
- firstName (VARCHAR 100)
- lastName (VARCHAR 100)
- email (VARCHAR 120, Unique)
- phone (VARCHAR 20)
- position (VARCHAR 100)
- hireDate (DATE)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

### admins
- id (INT, Primary Key)
- firstName (VARCHAR 100)
- lastName (VARCHAR 100)
- email (VARCHAR 120, Unique)
- password (VARCHAR 255, Hashed)

## 🔐 Security Features

### Password Security
- All passwords are hashed using PHP's `password_hash()` with BCRYPT algorithm
- Password verification uses `password_verify()` for secure comparison

### Input Validation
- All user inputs are sanitized using `sanitizeInput()` function
- Email validation for all email fields
- Type validation for numeric fields

### Database Security
- Prepared statements used for all queries (prevents SQL injection)
- Parameterized queries with proper type binding

### CORS Headers
- API endpoints include proper CORS headers for cross-origin requests
- Content-Type set to application/json

## 👤 Default Admin Account
- **Email:** admin@landway.com
- **Password:** Admin123456

⚠️ **Important:** Change this password immediately after first login!

## 🔄 User Workflows

### Registration Flow
1. User visits `/register.php`
2. Fills registration form with email and password
3. Form sends POST request to `api-users.php?action=register`
4. API validates input and checks email uniqueness
5. Password is hashed and user data stored in database
6. User redirected to login page

### Login Flow
1. User/Admin visits `/login.php`
2. Enters email and password
3. Form first tries admin login via `api-admin.php?action=login`
4. If not admin, tries user login via `api-users.php?action=login`
5. Session is created on server
6. User redirected to appropriate dashboard (admin or user)

### Admin Dashboard Flow
1. Admin logs in and session is verified
2. Dashboard loads and authenticates user
3. AJAX calls fetch data from all API endpoints
4. Data is rendered in tables with CRUD operation buttons
5. Each action (View/Edit/Delete) sends request to appropriate API endpoint

### Property Listing Flow
1. Logged-in user visits `/add-property.php`
2. Fills comprehensive property form
3. Form sends POST request to `api-properties.php?action=create`
4. Property data stored in database with owner information
5. User redirected to properties listing page

### Inquiry Flow
1. Customer fills inquiry form on `/contact.php`
2. Form sends POST request to `api-inquiries.php?action=create`
3. Inquiry data stored in database with timestamp
4. Admin can view all inquiries in dashboard

## 📝 Session Management

### Server-Side Sessions
- PHP sessions are used for authenticated users
- Session data stored in `$_SESSION` array
- Admin sessions include: adminId, firstName, lastName, email, isAdmin=true
- User sessions include: userId, firstName, lastName, email, isAdmin=false

### Session Protection
- Admin routes check for `$_SESSION['adminSession']['isAdmin']`
- User routes check for `$_SESSION['userSession']`
- Unauthorized users redirected to login page

## 🐛 Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check `config.php` credentials match your database
- Ensure database `landway_db` exists

### "Access Denied" on Admin Dashboard
- Check if admin session exists
- Verify admin email in database matches login
- Clear browser session and login again

### 404 Errors on API Calls
- Ensure API files (`api-*.php`) are in project root
- Check file permissions (should be readable)
- Verify fetch URLs in JavaScript match file paths

### Password Hash Errors
- Ensure PHP version supports `password_hash()` (PHP 5.5+)
- Check password field in database is VARCHAR(255)

## 🔄 Migration Notes

### From localStorage to Database
- Old HTML files remain in `Land Way/` subdirectory
- New PHP files in root directory
- Update all navigation links from `.html` to `.php`
- Remove localStorage references from client code

### File Name Changes
- `login.html` → `login.php`
- `register.html` → `register.php`
- `contact.html` → `contact.php`
- `add-property.html` → `add-property.php`
- `admin-dashboard.html` → `admin-dashboard.php`
- `admin-dashboard.js` → `admin-dashboard-db.js`

## 🚀 Next Steps

1. **Backup Database Regularly**
   - Implement automated database backups
   - Export SQL backups weekly

2. **Add Features**
   - Email notifications on inquiries
   - User profile pages
   - Property image uploads
   - Advanced search filters

3. **Performance Optimization**
   - Add database indexes on frequently searched fields
   - Implement pagination for large result sets
   - Cache frequently accessed data

4. **Security Hardening**
   - Implement rate limiting for login attempts
   - Add CSRF tokens to forms
   - Implement two-factor authentication
   - Regular security audits

5. **Production Deployment**
   - Move `config.php` outside web root
   - Use environment variables for credentials
   - Enable HTTPS
   - Implement web application firewall (WAF)

## 📞 Support

For issues or questions:
1. Check this documentation
2. Review API endpoint documentation
3. Check browser console for JavaScript errors
4. Check server error logs: `error_log`
5. Test API endpoints using tools like Postman

---

**Conversion Date:** December 15, 2025
**PHP Version Required:** 7.4+
**MySQL Version Required:** 5.7+
