<?php
// API: Admin Login
require_once 'config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($action) {
    case 'login':
        if ($method === 'POST') {
            handleAdminLogin();
        }
        break;
    
    case 'change-password':
        if ($method === 'POST') {
            changeAdminPassword();
        }
        break;
    
    case 'logout':
        session_start();
        session_destroy();
        echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

function handleAdminLogin() {
    $conn = getDB();
    
    $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    
    $email = sanitizeInput($data['email'] ?? '');
    $password = $data['password'] ?? '';
    
    if (!$email || !$password) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password required']);
        return;
    }
    
    $query = "SELECT id, firstName, lastName, email, password FROM admins WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        if (password_verify($password, $admin['password'])) {
            session_start();
            $_SESSION['adminSession'] = [
                'adminId' => $admin['id'],
                'firstName' => $admin['firstName'],
                'lastName' => $admin['lastName'],
                'email' => $admin['email'],
                'isAdmin' => true
            ];
            
            echo json_encode(['status' => 'success', 'message' => 'Admin login successful', 'admin' => $_SESSION['adminSession']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Admin not found']);
    }
}

function changeAdminPassword() {
    $conn = getDB();
    
    session_start();
    
    if (!isset($_SESSION['adminSession'])) {
        echo json_encode(['status' => 'error', 'message' => 'Admin not authenticated']);
        return;
    }
    
    $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    
    $currentPassword = $data['currentPassword'] ?? '';
    $newPassword = $data['newPassword'] ?? '';
    $adminId = $_SESSION['adminSession']['adminId'];
    
    if (!$currentPassword || !$newPassword) {
        echo json_encode(['status' => 'error', 'message' => 'Current and new passwords required']);
        return;
    }
    
    if (strlen($newPassword) < 8) {
        echo json_encode(['status' => 'error', 'message' => 'New password must be at least 8 characters']);
        return;
    }
    
    // Get current password
    $query = "SELECT password FROM admins WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        if (password_verify($currentPassword, $admin['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            
            $update = "UPDATE admins SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($update);
            $stmt->bind_param("si", $hashedPassword, $adminId);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Password changed successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to change password']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Admin not found']);
    }
}
?>
