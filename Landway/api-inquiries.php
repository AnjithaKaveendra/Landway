<?php
// API: Inquiry Management
require_once 'config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($action) {
    case 'create':
        if ($method === 'POST') {
            createInquiry($_POST);
        }
        break;
    
    case 'get-all':
        if ($method === 'GET') {
            getAllInquiries();
        }
        break;
    
    case 'get':
        if ($method === 'GET') {
            getInquiry($_GET['id'] ?? null);
        }
        break;
    
    case 'delete':
        if ($method === 'POST') {
            deleteInquiry($_POST['id'] ?? null);
        }
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

function createInquiry($data) {
    $conn = getDB();
    
    $contactName = sanitizeInput($data['contactName'] ?? '');
    $contactEmail = sanitizeInput($data['contactEmail'] ?? '');
    $contactPhone = sanitizeInput($data['contactPhone'] ?? '');
    $contactSubject = sanitizeInput($data['contactSubject'] ?? '');
    $contactPropertyType = sanitizeInput($data['contactPropertyType'] ?? '');
    $contactMessageText = sanitizeInput($data['contactMessageText'] ?? '');
    
    if (!$contactName || !$contactEmail || !$contactMessageText) {
        echo json_encode(['status' => 'error', 'message' => 'Required fields missing']);
        return;
    }
    
    $insert = "INSERT INTO inquiries (contactName, contactEmail, contactPhone, contactSubject, 
               contactPropertyType, contactMessageText) 
               VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("ssssss", 
        $contactName, $contactEmail, $contactPhone, $contactSubject, $contactPropertyType, $contactMessageText
    );
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Inquiry submitted successfully', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit inquiry']);
    }
}

function getAllInquiries() {
    $conn = getDB();
    
    $query = "SELECT * FROM inquiries ORDER BY inquiryDate DESC";
    $result = $conn->query($query);
    
    $inquiries = [];
    while ($row = $result->fetch_assoc()) {
        $inquiries[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $inquiries]);
}

function getInquiry($id) {
    $conn = getDB();
    
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Inquiry ID required']);
        return;
    }
    
    $query = "SELECT * FROM inquiries WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        echo json_encode(['status' => 'success', 'data' => $result->fetch_assoc()]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Inquiry not found']);
    }
}

function deleteInquiry($id) {
    $conn = getDB();
    
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Inquiry ID required']);
        return;
    }
    
    $delete = "DELETE FROM inquiries WHERE id = ?";
    $stmt = $conn->prepare($delete);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Inquiry deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
    }
}
?>
