<?php
require_once __DIR__ . '/app.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function respond(array $body, $code = 200) {
    http_response_code($code);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
function fail($message, $code = 422) {
    respond(['status' => 'error', 'message' => $message], $code);
}
set_exception_handler(function (Throwable $error) {
    error_log((string) $error);
    if ($error instanceof mysqli_sql_exception && $error->getCode() === 1062) {
        fail('This email or identifier is already registered.', 409);
    }
    fail('The database is unavailable or has not been initialized. Check the server configuration.', 503);
});
function requireMethod($method) {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== $method) {
        header('Allow: ' . $method);
        fail('Method not allowed.', 405);
    }
    if ($method !== 'GET') {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrfToken'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['csrfToken'], $token)) {
            fail('Your session has expired. Refresh the page and try again.', 403);
        }
    }
}
function requireAdmin() {
    if (!isAdmin()) fail('Administrator access required.', isLoggedIn() ? 403 : 401);
}
function requireLogin() {
    if (!isLoggedIn()) fail('Please log in to continue.', 401);
}
function getRequestData() {
    if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) fail('Invalid JSON.', 400);
    } else {
        $data = $_POST;
    }
    return $data;
}
function textField(array $data, $key, $required = false, $max = 255) {
    $value = $data[$key] ?? '';
    if (!is_scalar($value)) fail('Invalid value for ' . $key . '.');
    $value = trim((string) $value);
    if (($required && $value === '') || strlen($value) > $max) fail('Invalid or missing ' . $key . '.');
    return $value;
}
function emailField(array $data, $key, $required = true) {
    $email = textField($data, $key, $required, 120);
    if (($required || $email !== '') && !filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Enter a valid email address.');
    return $email;
}
function recordId($value) {
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) fail('A valid record ID is required.');
    return $id;
}
function findRecord($table, $id, $columns = '*') {
    $row = dbQuery("SELECT $columns FROM $table WHERE id = ?", [recordId($id)])->get_result()->fetch_assoc();
    if (!$row) fail('Record not found.', 404);
    return $row;
}
function deleteRecord($table, $id) {
    $id = recordId($id);
    findRecord($table, $id);
    dbQuery("DELETE FROM $table WHERE id = ?", [$id]);
    respond(['status' => 'success', 'message' => 'Deleted successfully.']);
}
function logoutSession() {
    $_SESSION = [];
    $cookie = session_get_cookie_params();
    setcookie(session_name(), '', ['expires' => time() - 42000, 'path' => $cookie['path'],
        'secure' => $cookie['secure'], 'httponly' => true, 'samesite' => 'Lax']);
    session_destroy();
    respond(['status' => 'success', 'message' => 'Logged out successfully.']);
}
