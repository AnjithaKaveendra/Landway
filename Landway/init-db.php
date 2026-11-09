<?php
// Run from a terminal: php init-db.php [--seed]
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Run php init-db.php from a terminal. See PHP_MYSQL_SETUP.md.');
}
require_once __DIR__ . '/app.php';
try {
    if (!preg_match('/^[a-zA-Z0-9_]+$/', DB_NAME)) throw new RuntimeException('DB_NAME must contain only letters, digits, and underscores.');
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // Connect without selecting a database so first-time setup works.
    $server = new mysqli(DB_HOST, DB_USER, DB_PASS, '', DB_PORT);
    $server->query('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $server->select_db(DB_NAME);
    $server->set_charset('utf8mb4');
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    foreach (explode(';', $schema) as $statement) {
        if (trim($statement) !== '') $server->query($statement);
    }
    // Upgrade the existing PHP/MySQL schema without deleting records.
    if ($server->query("SHOW COLUMNS FROM properties LIKE 'ownerUserId'")->num_rows === 0) {
        $server->query('ALTER TABLE properties ADD COLUMN ownerUserId INT NULL AFTER id');
    }
    // Older forms stored title-case types. The frontend uses lowercase values.
    $server->query('UPDATE properties SET propertyType = LOWER(propertyType), propertyStatus = LOWER(propertyStatus)');
    if ((int) $server->query('SELECT COUNT(*) FROM admins')->fetch_row()[0] === 0) {
        $email = getenv('ADMIN_EMAIL') ?: 'admin@landway.com';
        $password = getenv('ADMIN_PASSWORD') ?: '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8 || strlen($password) > 72) {
            throw new RuntimeException('Tables are ready. Set ADMIN_EMAIL and ADMIN_PASSWORD (8 to 72 bytes), then rerun this command to create the first admin.');
        }
        dbQuery('INSERT INTO admins (firstName, lastName, email, password) VALUES (?, ?, ?, ?)',
            ['Admin', 'User', $email, password_hash($password, PASSWORD_DEFAULT)]);
        echo "Administrator created.\n";
    }
    if (in_array('--seed', $argv, true) && (int) $server->query('SELECT COUNT(*) FROM properties')->fetch_row()[0] === 0) {
        $samples = [
            ['Modern Family House', 'house', 'Beautiful 4-bedroom family home with spacious garden, modern kitchen, and attached garage. Perfect for growing families.', '123 Maple Street', 'Springfield', 4, 3, 2500, 350000, 'Air Conditioning,Garden,Security System,Modern Kitchen'],
            ['Premium Land Plot', 'land', 'Prime location land plot with stunning views. Approved for residential construction with all utilities available.', 'Riverside Avenue', 'Hillside', 0, 0, 5000, 180000, 'Utilities Available,Residential Approval,Green Area'],
            ['Downtown Apartment', 'apartment', 'Luxury apartment in the heart of the city. Features modern amenities, gym access, and stunning city views.', '456 Central Plaza', 'Downtown', 2, 2, 1200, 275000, 'Air Conditioning,Gym,24/7 Security'],
            ['Commercial Building', 'commercial', 'Prime commercial property with high foot traffic. Ideal for retail, office, or mixed-use development. Includes parking facilities.', '789 Business Park', 'Industrial Area', 0, 4, 8000, 520000, 'Parking,Elevator,Security System']
        ];
        foreach ($samples as $sample) {
            dbQuery("INSERT INTO properties (propertyTitle, propertyType, propertyDescription, propertyAddress, propertyCity, propertyBedrooms, propertyBathrooms, propertyArea, propertyPrice, features, propertyStatus, propertyCurrency, ownerName, ownerEmail, ownerPhone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'sale', 'USD', 'Landway Team', 'contact@landway.com', '+1 234 567 8900')", $sample);
        }
        echo "Four sample listings added.\n";
    }
    echo 'Database ' . DB_NAME . " is ready. Existing records and admin passwords were preserved.\n";
} catch (Throwable $error) {
    fwrite(STDERR, 'Setup failed: ' . $error->getMessage() . "\n");
    exit(1);
}
