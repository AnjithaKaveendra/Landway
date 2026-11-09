<?php
require_once __DIR__ . '/config.php';
requireMethod('POST');
switch ($_GET['action'] ?? '') {
    case 'login':
        $data = getRequestData();
        $email = emailField($data, 'email');
        $password = $data['password'] ?? '';
        $admin = dbQuery('SELECT * FROM admins WHERE email = ?', [$email])->get_result()->fetch_assoc();
        if (!$admin || !is_string($password) || !password_verify($password, $admin['password'])) fail('Invalid email or password.', 401);
        session_regenerate_id(true);
        unset($_SESSION['userSession']);
        $_SESSION['adminSession'] = ['adminId' => (int) $admin['id'], 'firstName' => $admin['firstName'],
            'lastName' => $admin['lastName'], 'email' => $admin['email'], 'isAdmin' => true];
        respond(['status' => 'success', 'message' => 'Admin login successful.', 'admin' => $_SESSION['adminSession']]);
    case 'logout':
        logoutSession();
    case 'change-password':
        requireAdmin();
        $data = getRequestData();
        $current = $data['currentPassword'] ?? '';
        $new = $data['newPassword'] ?? '';
        if (!is_string($new) || strlen($new) < 8 || strlen($new) > 72) fail('Password must contain 8 to 72 bytes.');
        $admin = findRecord('admins', $_SESSION['adminSession']['adminId']);
        if (!is_string($current) || !password_verify($current, $admin['password'])) fail('Current password is incorrect.');
        dbQuery('UPDATE admins SET password = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), $admin['id']]);
        session_regenerate_id(true);
        respond(['status' => 'success', 'message' => 'Password changed successfully.']);
    default:
        fail('Invalid action.', 400);
}
