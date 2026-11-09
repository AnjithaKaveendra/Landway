<?php
require_once __DIR__ . '/app.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
try {
    dbQuery('SELECT 1');
    foreach (['users', 'properties', 'inquiries', 'employees', 'admins', 'favorites'] as $table) {
        dbQuery("SELECT 1 FROM $table LIMIT 1");
    }
    dbQuery('SELECT ownerUserId FROM properties LIMIT 1');
    echo json_encode(['status' => 'success', 'message' => 'MySQL connection and schema are ready.']);
} catch (Throwable $error) {
    error_log((string) $error);
    http_response_code(503);
    echo json_encode(['status' => 'error', 'message' => 'Check the database settings and run php init-db.php.']);
}
