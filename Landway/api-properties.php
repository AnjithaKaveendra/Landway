<?php
require_once __DIR__ . '/config.php';
$action = $_GET['action'] ?? '';
if (in_array($action, ['get-all', 'get'], true)) {
    requireMethod('GET');
    if ($action === 'get') {
        $property = findRecord('properties', $_GET['id'] ?? null);
        $property['features'] = propertyFeatures($property);
        respond(['status' => 'success', 'data' => $property]);
    }
    $rows = dbQuery('SELECT * FROM properties ORDER BY listedDate DESC, id DESC')->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach ($rows as &$row) $row['features'] = propertyFeatures($row);
    respond(['status' => 'success', 'data' => $rows]);
}
if (!in_array($action, ['create', 'update', 'delete'], true)) fail('Invalid action.', 400);
requireMethod('POST');
requireLogin();
$data = getRequestData();
if ($action !== 'create') {
    $id = recordId($data['id'] ?? $_GET['id'] ?? null);
    $existing = findRecord('properties', $id);
    if (!isAdmin() && (int) ($existing['ownerUserId'] ?? 0) !== (int) ($_SESSION['userSession']['userId'] ?? 0)) {
        fail('You can only change your own listings.', 403);
    }
    if ($action === 'delete') deleteRecord('properties', $id);
    $data = array_merge($existing, $data);
}
$fields = ['propertyTitle' => 150, 'propertyType' => 50, 'propertyStatus' => 50,
    'propertyDescription' => 20000, 'propertyAddress' => 255, 'propertyCity' => 100,
    'propertyState' => 100, 'propertyZipCode' => 20, 'propertyCountry' => 100,
    'propertyGarage' => 50, 'propertyCurrency' => 10, 'ownerName' => 100,
    'ownerPhone' => 20, 'ownerWhatsApp' => 20];
$values = [];
foreach ($fields as $field => $limit) {
    $values[$field] = textField($data, $field, in_array($field,
        ['propertyTitle', 'propertyType', 'propertyStatus', 'propertyAddress', 'propertyCity', 'ownerName', 'ownerPhone'], true), $limit);
}
if (!in_array($values['propertyType'], ['house', 'land', 'apartment', 'commercial'], true)) fail('Invalid property type.');
if (!in_array($values['propertyStatus'], ['sale', 'rent', 'lease'], true)) fail('Invalid property status.');
$values['propertyCurrency'] = $values['propertyCurrency'] ?: 'LKR';
if (!in_array($values['propertyCurrency'], ['LKR', 'USD', 'EUR', 'GBP'], true)) fail('Invalid currency.');
$values['ownerEmail'] = emailField($data, 'ownerEmail');
foreach (['propertyBedrooms', 'propertyBathrooms', 'propertyArea', 'propertyYearBuilt', 'propertyPrice'] as $field) {
    $raw = $data[$field] ?? 0;
    if ($raw === '') $raw = 0;
    if (!is_numeric($raw) || !is_finite((float) $raw) || (float) $raw < 0) fail('Invalid ' . $field . '.');
    $integer = in_array($field, ['propertyBedrooms', 'propertyBathrooms', 'propertyYearBuilt'], true);
    $max = $field === 'propertyPrice' ? 9999999999999.99 : ($field === 'propertyArea' ? 99999999.99 : 9999);
    if ((float) $raw > $max || ($integer && (float) $raw !== (float) (int) $raw)) fail('Invalid ' . $field . '.');
    $values[$field] = $integer ? (int) $raw : (float) $raw;
}
if ($values['propertyPrice'] <= 0) fail('Price must be greater than zero.');
$features = $data['features'] ?? '';
if (is_array($features)) {
    foreach ($features as $feature) if (!is_string($feature)) fail('Invalid features.');
    $features = implode(',', $features);
}
$values['features'] = textField(['features' => $features], 'features', false, 2000);
if ($action === 'create') {
    $values['ownerUserId'] = $_SESSION['userSession']['userId'] ?? null;
    $columns = implode(', ', array_keys($values));
    $placeholders = implode(', ', array_fill(0, count($values), '?'));
    $statement = dbQuery("INSERT INTO properties ($columns) VALUES ($placeholders)", array_values($values));
    respond(['status' => 'success', 'message' => 'Property created.', 'id' => $statement->insert_id], 201);
}
$assignments = implode(', ', array_map(function ($field) { return "$field = ?"; }, array_keys($values)));
dbQuery("UPDATE properties SET $assignments WHERE id = ?", array_merge(array_values($values), [$id]));
respond(['status' => 'success', 'message' => 'Property updated.']);
