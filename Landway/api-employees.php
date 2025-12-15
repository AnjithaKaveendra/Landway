<?php
// API: Employee Management
require_once 'config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($action) {
    case 'create':
        if ($method === 'POST') {
            createEmployee($_POST);
        }
        break;
    
    case 'get-all':
        if ($method === 'GET') {
            getAllEmployees();
        }
        break;
    
    case 'get':
        if ($method === 'GET') {
            getEmployee($_GET['id'] ?? null);
        }
        break;
    
    case 'update':
        if ($method === 'POST') {
            updateEmployee($_POST['id'] ?? null, $_POST);
        }
        break;
    
    case 'delete':
        if ($method === 'POST') {
            deleteEmployee($_POST['id'] ?? null);
        }
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

function createEmployee($data) {
    $conn = getDB();
    
    $employeeId = sanitizeInput($data['employeeId'] ?? '');
    $firstName = sanitizeInput($data['firstName'] ?? '');
    $lastName = sanitizeInput($data['lastName'] ?? '');
    $email = sanitizeInput($data['email'] ?? '');
    $phone = sanitizeInput($data['phone'] ?? '');
    $position = sanitizeInput($data['position'] ?? '');
    $hireDate = sanitizeInput($data['hireDate'] ?? date('Y-m-d'));
    
    if (!$employeeId || !$firstName || !$lastName || !$email) {
        echo json_encode(['status' => 'error', 'message' => 'Required fields missing']);
        return;
    }
    
    // Check if employee ID or email exists
    $checkEmployee = "SELECT id FROM employees WHERE employeeId = ? OR email = ?";
    $stmt = $conn->prepare($checkEmployee);
    $stmt->bind_param("ss", $employeeId, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Employee ID or Email already exists']);
        return;
    }
    
    $insert = "INSERT INTO employees (employeeId, firstName, lastName, email, phone, position, hireDate) 
               VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("sssssss", $employeeId, $firstName, $lastName, $email, $phone, $position, $hireDate);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Employee created successfully', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create employee']);
    }
}

function getAllEmployees() {
    $conn = getDB();
    
    $query = "SELECT * FROM employees ORDER BY created_at DESC";
    $result = $conn->query($query);
    
    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $employees]);
}

function getEmployee($id) {
    $conn = getDB();
    
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Employee ID required']);
        return;
    }
    
    $query = "SELECT * FROM employees WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        echo json_encode(['status' => 'success', 'data' => $result->fetch_assoc()]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Employee not found']);
    }
}

function updateEmployee($id, $data) {
    $conn = getDB();
    
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Employee ID required']);
        return;
    }
    
    $firstName = sanitizeInput($data['firstName'] ?? '');
    $lastName = sanitizeInput($data['lastName'] ?? '');
    $email = sanitizeInput($data['email'] ?? '');
    $phone = sanitizeInput($data['phone'] ?? '');
    $position = sanitizeInput($data['position'] ?? '');
    
    $update = "UPDATE employees SET firstName = ?, lastName = ?, email = ?, phone = ?, position = ? WHERE id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("sssssi", $firstName, $lastName, $email, $phone, $position, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Employee updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }
}

function deleteEmployee($id) {
    $conn = getDB();
    
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Employee ID required']);
        return;
    }
    
    $delete = "DELETE FROM employees WHERE id = ?";
    $stmt = $conn->prepare($delete);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Employee deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
    }
}
?>
