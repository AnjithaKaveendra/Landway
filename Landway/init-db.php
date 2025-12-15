<?php
// Database initialization and schema creation
require_once 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Create database if not exists
$createDB = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($createDB) === TRUE) {
    echo "Database created successfully.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db(DB_NAME);

// Create tables
$tables = [
    // Users/Accounts table
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        firstName VARCHAR(100) NOT NULL,
        lastName VARCHAR(100) NOT NULL,
        email VARCHAR(120) UNIQUE NOT NULL,
        phone VARCHAR(20),
        password VARCHAR(255) NOT NULL,
        registrationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    // Properties table
    "CREATE TABLE IF NOT EXISTS properties (
        id INT AUTO_INCREMENT PRIMARY KEY,
        propertyTitle VARCHAR(150) NOT NULL,
        propertyType VARCHAR(50) NOT NULL,
        propertyStatus VARCHAR(50) NOT NULL,
        propertyDescription LONGTEXT,
        propertyAddress VARCHAR(255) NOT NULL,
        propertyCity VARCHAR(100) NOT NULL,
        propertyState VARCHAR(100),
        propertyZipCode VARCHAR(20),
        propertyCountry VARCHAR(100),
        propertyBedrooms INT,
        propertyBathrooms INT,
        propertyArea DECIMAL(10, 2),
        propertyYearBuilt INT,
        propertyGarage VARCHAR(50),
        propertyPrice DECIMAL(15, 2) NOT NULL,
        propertyCurrency VARCHAR(10) DEFAULT 'LKR',
        features LONGTEXT,
        ownerName VARCHAR(100),
        ownerEmail VARCHAR(120),
        ownerPhone VARCHAR(20),
        ownerWhatsApp VARCHAR(20),
        listedDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",
    
    // Inquiries table
    "CREATE TABLE IF NOT EXISTS inquiries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        contactName VARCHAR(100) NOT NULL,
        contactEmail VARCHAR(120) NOT NULL,
        contactPhone VARCHAR(20),
        contactSubject VARCHAR(200),
        contactPropertyType VARCHAR(50),
        contactMessageText LONGTEXT NOT NULL,
        inquiryDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    // Employees table
    "CREATE TABLE IF NOT EXISTS employees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employeeId VARCHAR(50) UNIQUE NOT NULL,
        firstName VARCHAR(100) NOT NULL,
        lastName VARCHAR(100) NOT NULL,
        email VARCHAR(120) UNIQUE NOT NULL,
        phone VARCHAR(20),
        position VARCHAR(100),
        hireDate DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",
    
    // Admin users table
    "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        firstName VARCHAR(100) NOT NULL,
        lastName VARCHAR(100) NOT NULL,
        email VARCHAR(120) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

// Execute table creation
foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table created successfully.<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

// Insert default admin user if not exists
$adminEmail = 'admin@landway.com';
$adminPassword = password_hash('Admin123456', PASSWORD_BCRYPT);
$checkAdmin = "SELECT id FROM admins WHERE email = ?";
$stmt = $conn->prepare($checkAdmin);
$stmt->bind_param("s", $adminEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $insertAdmin = "INSERT INTO admins (firstName, lastName, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertAdmin);
    $adminFirstName = "Admin";
    $adminLastName = "User";
    $stmt->bind_param("ssss", $adminFirstName, $adminLastName, $adminEmail, $adminPassword);
    
    if ($stmt->execute()) {
        echo "Default admin user created successfully.<br>";
    } else {
        echo "Error creating admin user: " . $conn->error . "<br>";
    }
}

$conn->close();
echo "Database initialization complete!";
?>
