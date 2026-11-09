<?php
require_once __DIR__ . '/config.php';
$action = $_GET['action'] ?? '';
if (!in_array($action, ['get-all', 'get', 'create', 'update', 'delete'], true)) fail('Invalid action.', 400);
requireMethod(in_array($action, ['get-all', 'get'], true) ? 'GET' : 'POST');
requireAdmin();
if ($action === 'get-all') respond(['status' => 'success', 'data' => dbQuery('SELECT * FROM employees ORDER BY created_at DESC, id DESC')->get_result()->fetch_all(MYSQLI_ASSOC)]);
if ($action === 'get') respond(['status' => 'success', 'data' => findRecord('employees', $_GET['id'] ?? null)]);
$data = getRequestData();
if ($action !== 'create') {
    $id = recordId($data['id'] ?? $_GET['id'] ?? null);
    $existing = findRecord('employees', $id);
    if ($action === 'delete') deleteRecord('employees', $id);
    $data = array_merge($existing, $data);
}
$values = [textField($data, 'employeeId', true, 50), textField($data, 'firstName', true, 100),
    textField($data, 'lastName', true, 100), emailField($data, 'email'),
    textField($data, 'phone', false, 20), textField($data, 'position', true, 100)];
$hireDate = textField($data, 'hireDate', false, 10) ?: date('Y-m-d');
$date = DateTime::createFromFormat('!Y-m-d', $hireDate);
if (!$date || $date->format('Y-m-d') !== $hireDate) fail('Enter a valid hire date.');
$values[] = $hireDate;
if ($action === 'create') {
    $statement = dbQuery('INSERT INTO employees (employeeId, firstName, lastName, email, phone, position, hireDate) VALUES (?, ?, ?, ?, ?, ?, ?)', $values);
    respond(['status' => 'success', 'message' => 'Employee created.', 'id' => $statement->insert_id], 201);
}
$values[] = $id;
dbQuery('UPDATE employees SET employeeId = ?, firstName = ?, lastName = ?, email = ?, phone = ?, position = ?, hireDate = ? WHERE id = ?', $values);
respond(['status' => 'success', 'message' => 'Employee updated.']);
