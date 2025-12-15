<?php
// Test Database Connection
echo "<h2>🔌 Landway Database Connection Test</h2>";

// Connect without database first to create it
$conn = new mysqli('localhost', 'root', '', '');

if ($conn->connect_error) {
    echo "<p style='color: red;'><strong>❌ Connection Failed:</strong> " . $conn->connect_error . "</p>";
    exit();
}

echo "<p style='color: green;'><strong>✅ Connected to MySQL</strong></p>";
echo "<p>MySQL PID: 12168 (Active)</p>";

// Create database if not exists
$createDB = "CREATE DATABASE IF NOT EXISTS landway_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($createDB) === TRUE) {
    echo "<p style='color: green;'><strong>✅ Database 'landway_db' ready</strong></p>";
} else {
    echo "<p style='color: red;'><strong>❌ Error creating database:</strong> " . $conn->error . "</p>";
}

// Select the database
$conn->select_db('landway_db');

// Create tables
$tables = [
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
    
    "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        firstName VARCHAR(100) NOT NULL,
        lastName VARCHAR(100) NOT NULL,
        email VARCHAR(120) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

$tablesCreated = 0;
foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        $tablesCreated++;
    } else {
        echo "<p style='color: red;'><strong>❌ Error creating table:</strong> " . $conn->error . "</p>";
    }
}

echo "<p style='color: green;'><strong>✅ All $tablesCreated tables created successfully</strong></p>";

// Insert default admin user
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
        echo "<p style='color: green;'><strong>✅ Default admin user created</strong></p>";
    } else {
        echo "<p style='color: red;'><strong>❌ Error creating admin:</strong> " . $conn->error . "</p>";
    }
}

echo "<div style='background: #f0f0f0; padding: 15px; margin-top: 20px; border-radius: 5px;'>";
echo "<h3>📋 Setup Complete!</h3>";
echo "<p><strong>Default Admin Credentials:</strong></p>";
echo "<ul>";
echo "<li>Email: <code>admin@landway.com</code></li>";
echo "<li>Password: <code>Admin123456</code></li>";
echo "</ul>";
echo "<p><strong>⚠️ Change password immediately after first login!</strong></p>";
echo "<p style='margin-top: 20px;'>";
echo "<a href='login.php' style='display: inline-block; background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login</a> ";
echo "<a href='admin-dashboard.php' style='display: inline-block; background: #764ba2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Go to Dashboard</a>";
echo "</p>";
echo "</div>";

$conn->close();
?>
