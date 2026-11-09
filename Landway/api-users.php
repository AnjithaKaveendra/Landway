<?php
require_once __DIR__ . '/config.php';
$action = $_GET['action'] ?? '';
$publicColumns = 'id, firstName, lastName, email, phone, registrationDate';

switch ($action) {
    case 'session':
        requireMethod('GET');
        respond(['status' => 'success', 'user' => $_SESSION['userSession'] ?? null,
            'admin' => $_SESSION['adminSession'] ?? null, 'csrfToken' => $_SESSION['csrfToken']]);
    case 'logout':
        requireMethod('POST');
        logoutSession();
    case 'register':
        requireMethod('POST');
        $data = getRequestData();
        $first = textField($data, 'firstName', true, 100);
        $last = textField($data, 'lastName', true, 100);
        $email = emailField($data, 'email');
        $phone = textField($data, 'phone', false, 20);
        $password = $data['password'] ?? '';
        if (!is_string($password) || strlen($password) < 8 || strlen($password) > 72) fail('Password must contain 8 to 72 bytes.');
        $statement = dbQuery('INSERT INTO users (firstName, lastName, email, phone, password) VALUES (?, ?, ?, ?, ?)',
            [$first, $last, $email, $phone, password_hash($password, PASSWORD_DEFAULT)]);
        respond(['status' => 'success', 'message' => 'Registration successful.', 'id' => $statement->insert_id], 201);
    case 'login':
        requireMethod('POST');
        $data = getRequestData();
        $email = emailField($data, 'email');
        $password = $data['password'] ?? '';
        $user = dbQuery('SELECT * FROM users WHERE email = ?', [$email])->get_result()->fetch_assoc();
        if (!$user || !is_string($password) || !password_verify($password, $user['password'])) fail('Invalid email or password.', 401);
        session_regenerate_id(true);
        unset($_SESSION['adminSession']);
        $_SESSION['userSession'] = ['userId' => (int) $user['id'], 'firstName' => $user['firstName'],
            'lastName' => $user['lastName'], 'email' => $user['email'], 'isAdmin' => false];
        respond(['status' => 'success', 'message' => 'Login successful.', 'user' => $_SESSION['userSession']]);
    case 'get-all':
        requireMethod('GET');
        requireAdmin();
        respond(['status' => 'success', 'data' => dbQuery("SELECT $publicColumns FROM users ORDER BY registrationDate DESC, id DESC")->get_result()->fetch_all(MYSQLI_ASSOC)]);
    case 'get':
        requireMethod('GET');
        requireAdmin();
        respond(['status' => 'success', 'data' => findRecord('users', $_GET['id'] ?? null, $publicColumns)]);
    case 'delete':
        requireMethod('POST');
        requireAdmin();
        deleteRecord('users', getRequestData()['id'] ?? null);
    case 'update':
        requireMethod('POST');
        requireAdmin();
        $data = getRequestData();
        $id = recordId($data['id'] ?? $_GET['id'] ?? null);
        findRecord('users', $id);
        dbQuery('UPDATE users SET firstName = ?, lastName = ?, phone = ? WHERE id = ?',
            [textField($data, 'firstName', true, 100), textField($data, 'lastName', true, 100), textField($data, 'phone', false, 20), $id]);
        respond(['status' => 'success', 'message' => 'Account updated.']);
    default:
        fail('Invalid action.', 400);
}
