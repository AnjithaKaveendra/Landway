# Landway Admin Dashboard

## Overview
The Landway Admin Dashboard is a comprehensive management system for administrators to view and manage all platform data including accounts, properties, inquiries, and employees.

## Features

### 1. **Accounts Management**
- View all registered user accounts
- Display user information (name, email, phone, employee ID, registration date)
- Delete user accounts
- View detailed account information

### 2. **Properties Management**
- View all property listings submitted by users
- Display property details (title, type, status, price, location, area, bedrooms, bathrooms)
- Delete properties from the platform
- View complete property information

### 3. **Inquiries Management**
- View all customer inquiries from the contact form
- Display inquiry details (name, email, phone, subject, property type, message, date)
- Delete inquiries
- Track all customer communications

### 4. **Employees Management**
- View all employees in the system
- Add new employees with form
- Display employee information (ID, name, email, phone, position, hire date)
- Delete employees
- Edit employee information (feature available for future development)

### 5. **Dashboard Statistics**
- Real-time count of total accounts
- Real-time count of total properties
- Real-time count of total inquiries
- Real-time count of total employees

### 6. **Admin Functions**
- Secure admin login system
- Change password functionality
- Admin profile management
- Secure logout

## Admin Credentials

### Default Admin Account
- **Email:** `admin@landway.com`
- **Password:** `Admin123456`

**⚠️ IMPORTANT:** Change these credentials immediately after first login for security purposes.

## How to Access the Dashboard

1. Navigate to the **Login Page** (`login.html`)
2. Enter the admin email: `admin@landway.com`
3. Enter the admin password: `Admin123456`
4. Click "Sign In"
5. You will be redirected to the **Admin Dashboard** (`admin-dashboard.html`)

## Dashboard Structure

### Navigation
- **Sidebar Menu:** Quick navigation between sections
  - Accounts
  - Properties
  - Inquiries
  - Employees

- **Top Navigation Bar:** Admin profile and logout options

### Main Content Areas

#### Accounts Section
- **Table View:** List of all registered accounts
- **View Details:** Click "View" button to see complete account information
- **Delete Account:** Remove user accounts (permanent action)

#### Properties Section
- **Table View:** List of all property listings
- **View Details:** Click "View" button to see complete property information including:
  - Property specifications (bedrooms, bathrooms, area)
  - Pricing information
  - Location details
  - Owner information
- **Delete Property:** Remove listings from platform

#### Inquiries Section
- **Table View:** List of all customer inquiries
- **View Details:** Click "View" button to see complete inquiry message
- **Delete Inquiry:** Remove inquiries from system

#### Employees Section
- **Table View:** List of all employees
- **Add Employee:** Click "Add Employee" button to open form
- **Employee Form Fields:**
  - First Name
  - Last Name
  - Email
  - Phone
  - Position
  - Employee ID
- **Edit Employee:** Modify employee information
- **Delete Employee:** Remove employee from system

## Data Storage

All dashboard data is stored in the browser's **localStorage**:

- **Registered Users:** `registeredUsers` (array)
- **Properties:** `properties` (array)
- **Inquiries:** `inquiries` (array)
- **Employees:** `employees` (array)
- **Admin Session:** `adminSession` (object)

### Data Structure

#### User Account Object
```javascript
{
  firstName: "string",
  lastName: "string",
  email: "string",
  phone: "string",
  employeeId: "string",
  password: "string",
  registrationDate: "ISO Date"
}
```

#### Property Object
```javascript
{
  propertyTitle: "string",
  propertyType: "string",
  propertyStatus: "string",
  propertyDescription: "string",
  propertyAddress: "string",
  propertyCity: "string",
  propertyState: "string",
  propertyZipCode: "string",
  propertyCountry: "string",
  propertyBedrooms: "number",
  propertyBathrooms: "number",
  propertyArea: "number",
  propertyYearBuilt: "number",
  propertyGarage: "number",
  propertyPrice: "number",
  propertyCurrency: "string",
  features: ["array"],
  ownerName: "string",
  ownerEmail: "string",
  ownerPhone: "string",
  ownerWhatsApp: "string",
  listedDate: "ISO Date"
}
```

#### Inquiry Object
```javascript
{
  contactName: "string",
  contactEmail: "string",
  contactPhone: "string",
  contactSubject: "string",
  contactPropertyType: "string",
  contactMessageText: "string",
  inquiryDate: "ISO Date"
}
```

#### Employee Object
```javascript
{
  firstName: "string",
  lastName: "string",
  email: "string",
  phone: "string",
  position: "string",
  employeeId: "string",
  hireDate: "ISO Date"
}
```

## Security Features

1. **Authentication:** Admin-only access with email/password
2. **Session Management:** Admin session stored in localStorage
3. **Password Protection:** Change password feature for account security
4. **Data Validation:** All inputs validated before processing
5. **Confirmation Dialogs:** Delete actions require confirmation

## Files Included

- **admin-dashboard.html** - Main dashboard interface
- **css/admin-dashboard.css** - Dashboard styling
- **js/admin-dashboard.js** - Dashboard functionality and data management
- **login.html** - Updated with admin login functionality
- **Land Way/register.html** - Updated to save user data to localStorage
- **contact.html** - Updated to save inquiries to localStorage
- **add-property.html** - Updated to save properties to localStorage

## Usage Tips

1. **Adding Employees:**
   - Click "Add Employee" button in the Employees section
   - Fill in all required fields
   - Employee ID must be unique
   - Click "Add Employee" to save

2. **Viewing Details:**
   - Click the "View" button on any table row
   - A modal will display complete information
   - Close the modal to return to the table

3. **Deleting Data:**
   - Click the "Delete" button on any table row
   - Confirm the deletion when prompted
   - Action is permanent and cannot be undone

4. **Changing Password:**
   - Click on your profile name in top-right corner
   - Select "Change Password"
   - Enter current password and new password
   - Confirm new password matches

5. **Logout:**
   - Click on your profile name in top-right corner
   - Select "Logout"
   - You will be redirected to the login page

## Responsive Design

The dashboard is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile devices

Mobile menu automatically appears on smaller screens.

## Browser Compatibility

The dashboard works on all modern browsers:
- Chrome 60+
- Firefox 55+
- Safari 11+
- Edge 79+

## Future Enhancements

Potential features for future development:
1. Backend API integration for data persistence
2. Advanced search and filtering
3. Data export to CSV/PDF
4. Analytics and reporting
5. User activity logs
6. Email notifications
7. Two-factor authentication
8. Role-based access control
9. Advanced employee management
10. Property image gallery viewing

## Troubleshooting

### Dashboard not loading data
- Clear browser cache and localStorage
- Ensure forms are properly saving data
- Check browser console for errors

### Admin login not working
- Verify email and password are correct
- Check if localStorage is enabled
- Clear login form and try again

### Data not appearing in tables
- Check if data has been submitted through forms
- Verify localStorage contains data (open DevTools)
- Refresh the page

## Support

For issues or questions regarding the admin dashboard, please contact the development team or refer to the main Landway documentation.

## License

This admin dashboard is part of the Landway project and follows the same licensing terms.
