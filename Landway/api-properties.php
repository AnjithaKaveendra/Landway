<?php
// API: Property Management
require_once 'config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($action) {
    case 'create':
        if ($method === 'POST') {
            createProperty($_POST);
        }
        break;
    
    case 'get-all':
        if ($method === 'GET') {
            getAllProperties();
        }
        break;
    
    case 'get':
        if ($method === 'GET') {
            getProperty($_GET['id'] ?? null);
        }
        break;
    
    case 'update':
        if ($method === 'POST') {
            updateProperty($_POST['id'] ?? null, $_POST);
        }
        break;
    
    case 'delete':
        if ($method === 'POST') {
            deleteProperty($_POST['id'] ?? null);
        }
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

function createProperty($data) {
    $conn = getDB();
    
    $propertyTitle = sanitizeInput($data['propertyTitle'] ?? '');
    $propertyType = sanitizeInput($data['propertyType'] ?? '');
    $propertyStatus = sanitizeInput($data['propertyStatus'] ?? '');
    $propertyDescription = sanitizeInput($data['propertyDescription'] ?? '');
    $propertyAddress = sanitizeInput($data['propertyAddress'] ?? '');
    $propertyCity = sanitizeInput($data['propertyCity'] ?? '');
    $propertyState = sanitizeInput($data['propertyState'] ?? '');
    $propertyZipCode = sanitizeInput($data['propertyZipCode'] ?? '');
    $propertyCountry = sanitizeInput($data['propertyCountry'] ?? '');
    $propertyBedrooms = intval($data['propertyBedrooms'] ?? 0);
    $propertyBathrooms = intval($data['propertyBathrooms'] ?? 0);
    $propertyArea = floatval($data['propertyArea'] ?? 0);
    $propertyYearBuilt = intval($data['propertyYearBuilt'] ?? 0);
    $propertyGarage = sanitizeInput($data['propertyGarage'] ?? '');
    $propertyPrice = floatval($data['propertyPrice'] ?? 0);
    $propertyCurrency = sanitizeInput($data['propertyCurrency'] ?? 'LKR');
    $features = sanitizeInput($data['features'] ?? '');
    $ownerName = sanitizeInput($data['ownerName'] ?? '');
    $ownerEmail = sanitizeInput($data['ownerEmail'] ?? '');
    $ownerPhone = sanitizeInput($data['ownerPhone'] ?? '');
    $ownerWhatsApp = sanitizeInput($data['ownerWhatsApp'] ?? '');
    
    if (!$propertyTitle || !$propertyAddress || !$propertyCity) {
        echo json_encode(['status' => 'error', 'message' => 'Required fields missing']);
        return;
    }
    
    $insert = "INSERT INTO properties (propertyTitle, propertyType, propertyStatus, propertyDescription, 
               propertyAddress, propertyCity, propertyState, propertyZipCode, propertyCountry, 
               propertyBedrooms, propertyBathrooms, propertyArea, propertyYearBuilt, propertyGarage, 
               propertyPrice, propertyCurrency, features, ownerName, ownerEmail, ownerPhone, ownerWhatsApp) 
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("ssssssssssiiddsssssss", 
        $propertyTitle, $propertyType, $propertyStatus, $propertyDescription,
        $propertyAddress, $propertyCity, $propertyState, $propertyZipCode, $propertyCountry,
        $propertyBedrooms, $propertyBathrooms, $propertyArea, $propertyYearBuilt, $propertyGarage,
        $propertyPrice, $propertyCurrency, $features, $ownerName, $ownerEmail, $ownerPhone, $ownerWhatsApp
    );
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Property created successfully', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create property']);
    }
}

function getAllProperties() {
    $conn = getDB();
    
    $query = "SELECT * FROM properties ORDER BY listedDate DESC";
    $result = $conn->query($query);
    
    $properties = [];
    while ($row = $result->fetch_assoc()) {
        $row['features'] = explode(',', $row['features']);
        $properties[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $properties]);
}

function getProperty($id) {
    $conn = getDB();
    
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Property ID required']);
        return;
    }
    
    $query = "SELECT * FROM properties WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $property = $result->fetch_assoc();
        $property['features'] = explode(',', $property['features']);
        echo json_encode(['status' => 'success', 'data' => $property]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Property not found']);
    }
}

function updateProperty($id, $data) {
    $conn = getDB();
    
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Property ID required']);
        return;
    }
    
    $propertyTitle = sanitizeInput($data['propertyTitle'] ?? '');
    $propertyStatus = sanitizeInput($data['propertyStatus'] ?? '');
    $propertyPrice = floatval($data['propertyPrice'] ?? 0);
    
    $update = "UPDATE properties SET propertyTitle = ?, propertyStatus = ?, propertyPrice = ? WHERE id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssdi", $propertyTitle, $propertyStatus, $propertyPrice, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Property updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }
}

function deleteProperty($id) {
    $conn = getDB();
    
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Property ID required']);
        return;
    }
    
    $delete = "DELETE FROM properties WHERE id = ?";
    $stmt = $conn->prepare($delete);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Property deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
    }
}
?>
