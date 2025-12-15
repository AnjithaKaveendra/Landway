<?php
// API: User/Account Management
require_once 'config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($action) {
    case 'register':
        if ($method === 'POST') {
            handleUserRegistration();
        }
        break;
    
    case 'login':
        if ($method === 'POST') {
            handleUserLogin();
        }
        break;
    
    case 'get-all':
        if ($method === 'GET') {
            getAllUsers();
        }
        break;
    
    case 'get':
        if ($method === 'GET') {
            getUser($_GET['id'] ?? null);
        }
        break;
    
    case 'delete':
        if ($method === 'POST') {
            deleteUser($_POST['id'] ?? null);
        }
        break;
    
    case 'update':
        if ($method === 'POST') {
            updateUser($_POST['id'] ?? null, $_POST);
        }
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

function handleUserRegistration() {
    $conn = getDB();
    
    $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    
    $firstName = sanitizeInput($data['firstName'] ?? '');
    $lastName = sanitizeInput($data['lastName'] ?? '');
    $email = sanitizeInput($data['email'] ?? '');
    $phone = sanitizeInput($data['phone'] ?? '');
    $password = $data['password'] ?? '';
    
    // Validation
    if (!$firstName || !$lastName || !$email || !$password) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        return;
    }
    
    if (strlen($password) < 8) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters']);
        return;
    }
    
    // Check if email exists
    $checkEmail = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmail);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
        return;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user
    $insert = "INSERT INTO users (firstName, lastName, email, phone, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $phone, $hashedPassword);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
    }
}

function handleUserLogin() {
    $conn = getDB();
    
    $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    
    $email = sanitizeInput($data['email'] ?? '');
    $password = $data['password'] ?? '';
    
    if (!$email || !$password) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password required']);
        return;
    }
    
    $query = "SELECT id, firstName, lastName, email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['userSession'] = [
                'userId' => $user['id'],
                'firstName' => $user['firstName'],
                'lastName' => $user['lastName'],
                'email' => $user['email'],
                'isAdmin' => false
            ];
            
            echo json_encode(['status' => 'success', 'message' => 'Login successful', 'user' => $_SESSION['userSession']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
}

function getAllUsers() {
    $conn = getDB();
    
    $query = "SELECT id, firstName, lastName, email, phone, registrationDate FROM users ORDER BY registrationDate DESC";
    $result = $conn->query($query);
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $users]);
}

function getUser($id) {
    $conn = getDB();
    
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'User ID required']);
        return;
    }
    
    $query = "SELECT id, firstName, lastName, email, phone, registrationDate FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        echo json_encode(['status' => 'success', 'data' => $result->fetch_assoc()]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
}

function deleteUser($id) {
    $conn = getDB();
    
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'User ID required']);
        return;
    }
    
    $delete = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($delete);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
    }
}

function updateUser($id, $data) {
    $conn = getDB();
    
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'User ID required']);
        return;
    }
    
    $firstName = sanitizeInput($data['firstName'] ?? '');
    $lastName = sanitizeInput($data['lastName'] ?? '');
    $phone = sanitizeInput($data['phone'] ?? '');
    
    $update = "UPDATE users SET firstName = ?, lastName = ?, phone = ? WHERE id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("sssi", $firstName, $lastName, $phone, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }
}
?>
