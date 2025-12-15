# Admin Dashboard - Visual Guide & Feature Reference

## 🎯 Dashboard Layout

```
┌─────────────────────────────────────────────────────────────┐
│ Navbar: Landway Admin | Profile Dropdown | Logout Button   │
├──────────────────────────────────────────────────────────────┤
│ Sidebar      │  Main Content Area                           │
├──────────────┤                                              │
│ Dashboard    │  Dashboard Header                            │
│ ─────────────┤  - Title: "Admin Dashboard"                  │
│ • Accounts   │  - Subtitle: Management info                 │
│ • Properties │                                              │
│ • Inquiries  │  Statistics Cards:                           │
│ • Employees  │  [👥 Accounts] [🏠 Properties]              │
│              │  [📧 Inquiries] [👔 Employees]              │
│              │                                              │
│              │  Active Section:                             │
│              │  - Section Title with Icon                   │
│              │  - Data Table with Action Buttons            │
│              │  - Each row: View | Delete (or Edit)        │
└──────────────┴──────────────────────────────────────────────┘
```

---

## 📊 Dashboard Sections

### 1. ACCOUNTS SECTION
```
┌─ Manage Accounts ─────────────────────────────────────────┐
│ View and manage all registered user accounts              │
├───────────────────────────────────────────────────────────┤
│ ID │ First │ Last  │ Email        │ Phone  │ Emp ID │ Date │
├────┼───────┼───────┼──────────────┼────────┼────────┼─────┤
│ 1  │ John  │ Doe   │ john@email   │ +1234  │ EMP001 │ Dec1 │
│    │       │       │              │        │        │      │
│    [View]  [Delete]                                      │
└───────────────────────────────────────────────────────────┘
```

**Available Actions:**
- View: Opens modal with full account details
- Delete: Removes account (requires confirmation)

---

### 2. PROPERTIES SECTION
```
┌─ Manage Properties ───────────────────────────────────────┐
│ View and manage all property listings                     │
├───────────────────────────────────────────────────────────┤
│ ID │ Title      │ Type │ Status │ Price  │ City    │ Date  │
├────┼────────────┼──────┼────────┼────────┼─────────┼──────┤
│ 1  │ Modern     │House │ Sale   │ 50,000 │ NY      │ Dec 1 │
│    │ Family     │      │ [badge]│        │         │       │
│    │ House      │      │        │        │         │       │
│    [View]  [Delete]                                       │
└───────────────────────────────────────────────────────────┘
```

**Available Actions:**
- View: Shows complete property information
- Delete: Removes property listing (requires confirmation)

**Status Badges:**
- 🔵 For Sale (blue)
- 🟢 For Rent (green)
- 🟡 For Lease (orange)

---

### 3. INQUIRIES SECTION
```
┌─ Manage Inquiries ────────────────────────────────────────┐
│ View and respond to customer inquiries                    │
├───────────────────────────────────────────────────────────┤
│ ID │ Name   │ Email        │ Phone  │ Subject   │ Type │ Date│
├────┼────────┼──────────────┼────────┼───────────┼──────┼────┤
│ 1  │ Jane   │ jane@email   │ +5678  │ Property  │ Land │Dec1│
│    │ Smith  │              │        │ Inquiry   │      │    │
│    [View]  [Delete]                                      │
└───────────────────────────────────────────────────────────┘
```

**Available Actions:**
- View: Displays inquiry message and details
- Delete: Removes inquiry (requires confirmation)

---

### 4. EMPLOYEES SECTION
```
┌─ Manage Employees ────────────────────────────────────────┐
│ View and manage all employees      [Add Employee]         │
├───────────────────────────────────────────────────────────┤
│ ID │ Emp ID │ First │ Last  │ Email        │ Phone  │ Pos │
├────┼────────┼───────┼───────┼──────────────┼────────┼────┤
│ 1  │ EMP001 │ Alice │ Brown │ alice@email  │ +9012  │ Manager│
│    [Edit]  [Delete]                                      │
└───────────────────────────────────────────────────────────┘
```

**Available Actions:**
- Add Employee: Opens form to add new staff
- Edit: Modify employee information
- Delete: Removes employee (requires confirmation)

**Add Employee Form Fields:**
- First Name (required)
- Last Name (required)
- Email (required, validated)
- Phone (required)
- Position (required)
- Employee ID (required, must be unique)

---

## 🔑 Login & Authentication

### Login Process
```
┌─ Login Page ──────────────────────────────────┐
│                                               │
│  Email:    [admin@landway.com................]│
│  Password: [••••••••••.................]        │
│                                               │
│  ☐ Show Password                              │
│  ☐ Remember me    [Forgot Password?]         │
│                                               │
│        [Sign In]                              │
│                                               │
│  Don't have account? [Create Account]        │
└───────────────────────────────────────────────┘

Admin Credentials:
📧 Email: admin@landway.com
🔐 Password: Admin123456
```

### Session Management
```
Admin Login
    ↓
Check credentials
    ↓
Valid → Store session → Redirect to dashboard
Invalid → Show error message

Logout
    ↓
Clear session
    ↓
Redirect to login
```

---

## 🔐 Password Change

```
┌─ Change Password Dialog ──────────────────────┐
│                                               │
│  Current Password: [••••••••..........]       │
│  New Password:     [••••••••..........]       │
│  Confirm Password: [••••••••..........]       │
│                                               │
│  Error/Success Message Area                  │
│                                               │
│          [Cancel]  [Change Password]         │
└───────────────────────────────────────────────┘

Validation Rules:
✓ Current password must match
✓ New password must be 8+ characters
✓ New passwords must match
✓ Cannot be same as current
```

---

## 👁️ Modal Details Views

### Account Details Modal
```
┌─ Account Details ─────────────────────────────┐
│ First Name:           John                    │
│ Last Name:            Doe                     │
│ Email:                john@example.com        │
│ Phone:                +1234567890             │
│ Employee ID:          EMP001                  │
│ Registration Date:    December 15, 2024      │
│                                               │
│      [Close]  [Delete Account]               │
└───────────────────────────────────────────────┘
```

### Property Details Modal
```
┌─ Property Details ────────────────────────────┐
│ Title:                Modern Family House     │
│ Type:                 House                   │
│ Status:               For Sale                │
│ Price:                50,000                  │
│ Address:              123 Main St             │
│ City:                 New York                │
│ Area:                 2,500 sq ft             │
│ Bedrooms:             4                       │
│ Bathrooms:            2                       │
│ Description:          Beautiful family home  │
│ Listed Date:          December 15, 2024      │
│                                               │
│      [Close]  [Delete Property]              │
└───────────────────────────────────────────────┘
```

### Inquiry Details Modal
```
┌─ Inquiry Details ─────────────────────────────┐
│ Name:                 Jane Smith              │
│ Email:                jane@example.com        │
│ Phone:                +5678901234             │
│ Subject:              Property Inquiry        │
│ Property Type:        Land                    │
│ Message:              I'm interested in your │
│                       property listing...    │
│ Inquiry Date:         December 15, 2024      │
│                                               │
│      [Close]  [Delete Inquiry]               │
└───────────────────────────────────────────────┘
```

---

## 📈 Statistics Dashboard

```
┌────────────────────────────────────────────────┐
│                                                │
│  ┌──────────┬──────────┬──────────┬─────────┐ │
│  │👥       │🏠       │📧       │👔      │ │
│  │          │          │          │         │ │
│  │Accounts  │Properties│Inquiries │Employees│ │
│  │    42    │    15    │    28    │    8    │ │
│  └──────────┴──────────┴──────────┴─────────┘ │
│                                                │
│  Color Code:                                   │
│  🔵 Blue - Accounts                           │
│  🟢 Green - Properties                        │
│  🟡 Yellow - Inquiries                        │
│  🔴 Red - Employees                           │
│                                                │
└────────────────────────────────────────────────┘
```

Updates automatically when data changes!

---

## 🎯 Common Workflows

### Workflow 1: View All Accounts
```
1. Click "Accounts" in sidebar
   ↓
2. Table displays all registered users
   ↓
3. Click "View" to see details
   OR
3. Click "Delete" to remove account
```

### Workflow 2: Add New Employee
```
1. Click "Employees" in sidebar
   ↓
2. Click "Add Employee" button
   ↓
3. Fill employee form:
   - First Name
   - Last Name
   - Email
   - Phone
   - Position
   - Employee ID
   ↓
4. Click "Add Employee"
   ↓
5. Employee appears in table
```

### Workflow 3: Check Customer Inquiry
```
1. Click "Inquiries" in sidebar
   ↓
2. Review inquiry list
   ↓
3. Click "View" to read message
   ↓
4. Delete if spam/processed
   OR
4. Keep for reference
```

### Workflow 4: Review Properties
```
1. Click "Properties" in sidebar
   ↓
2. View all listings with status
   ↓
3. Click "View" for details
   ↓
4. Check completeness
   ↓
5. Delete if needed
```

---

## 🎨 Color Scheme

| Element | Color | Usage |
|---------|-------|-------|
| Primary Background | #2c3e50 | Navbar & Sidebar |
| Secondary Color | #e74c3c | Accent & Buttons |
| Accent | #3498db | Links & Highlights |
| Success | #27ae60 | Success Messages |
| Warning | #f39c12 | Warnings |
| Danger | #e74c3c | Delete Actions |
| Light Background | #ecf0f1 | Table Headers |
| Text Dark | #2c3e50 | Primary Text |
| Text Muted | #7f8c8d | Secondary Text |

---

## ⌨️ Keyboard Navigation

| Key | Action |
|-----|--------|
| Tab | Navigate through form fields |
| Enter | Submit form / Click button |
| Escape | Close modal |
| Shift+Tab | Navigate backwards |

---

## 📱 Responsive Breakpoints

| Device | Width | Layout |
|--------|-------|--------|
| Desktop | > 768px | Full sidebar + content |
| Tablet | 481-768px | Adjusted layout |
| Mobile | < 480px | Hidden sidebar + hamburger |

---

## ✅ Data Validation

### Required Fields
- First Name, Last Name, Email (Accounts)
- Property Title, Type, Status, Price, Area (Properties)
- Name, Email, Subject, Message (Inquiries)
- First Name, Last Name, Email, Position (Employees)

### Validations
- Email format: `user@domain.com`
- Phone: Numeric with optional +/- characters
- Price: Numeric only
- Area: Numeric only
- Passwords: Minimum 8 characters
- Employee ID: Must be unique

---

## 🔔 Notifications

### Success Messages
```
✓ Account deleted successfully!
✓ Employee added successfully!
✓ Password changed successfully!
```

### Error Messages
```
✗ Please fill in all required fields
✗ Email already exists
✗ Invalid email format
✗ Passwords do not match
✗ Employee ID already exists
```

### Confirmations
```
Are you sure you want to delete this account?
This action cannot be undone.
[Cancel] [Confirm]
```

---

## 📊 Data Examples

### Sample Account
```
{
  firstName: "John",
  lastName: "Doe",
  email: "john@example.com",
  phone: "+1234567890",
  employeeId: "EMP001",
  password: "SecurePass123",
  registrationDate: "2024-12-15T10:30:00Z"
}
```

### Sample Property
```
{
  propertyTitle: "Modern Family House",
  propertyType: "House",
  propertyStatus: "sale",
  propertyPrice: "50000",
  propertyAddress: "123 Main St",
  propertyCity: "New York",
  propertyBedrooms: "4",
  propertyBathrooms: "2",
  propertyArea: "2500",
  listedDate: "2024-12-15T10:30:00Z"
}
```

---

This visual guide should help understand the complete dashboard structure and functionality!
