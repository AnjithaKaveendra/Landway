CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        firstName VARCHAR(100) NOT NULL,
        lastName VARCHAR(100) NOT NULL,
        email VARCHAR(120) UNIQUE NOT NULL,
        phone VARCHAR(20),
        password VARCHAR(255) NOT NULL,
        registrationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS properties (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ownerUserId INT NULL,
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS inquiries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        contactName VARCHAR(100) NOT NULL,
        contactEmail VARCHAR(120) NOT NULL,
        contactPhone VARCHAR(20),
        contactSubject VARCHAR(200),
        contactPropertyType VARCHAR(50),
        contactMessageText LONGTEXT NOT NULL,
        inquiryDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS employees (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        firstName VARCHAR(100) NOT NULL,
        lastName VARCHAR(100) NOT NULL,
        email VARCHAR(120) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS favorites (
    userId INT NOT NULL,
    propertyId INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (userId, propertyId),
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (propertyId) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
