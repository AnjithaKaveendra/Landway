<?php
require_once __DIR__ . '/config.php';
$action = $_GET['action'] ?? '';
if (!in_array($action, ['get-all', 'toggle'], true)) fail('Invalid action.', 400);
requireMethod($action === 'get-all' ? 'GET' : 'POST');
requireLogin();
$userId = $_SESSION['userSession']['userId'] ?? null;
if (!$userId) fail('Sign in with a customer account to save properties.', 403);
if ($action === 'get-all') {
    respond(['status' => 'success', 'data' => dbQuery('SELECT p.* FROM properties p JOIN favorites f ON f.propertyId = p.id WHERE f.userId = ? ORDER BY f.created_at DESC', [$userId])->get_result()->fetch_all(MYSQLI_ASSOC)]);
}
$id = recordId(getRequestData()['id'] ?? null);
findRecord('properties', $id);
$saved = dbQuery('SELECT propertyId FROM favorites WHERE userId = ? AND propertyId = ?', [$userId, $id])->get_result()->num_rows > 0;
if ($saved) {
    dbQuery('DELETE FROM favorites WHERE userId = ? AND propertyId = ?', [$userId, $id]);
} else {
    dbQuery('INSERT INTO favorites (userId, propertyId) VALUES (?, ?)', [$userId, $id]);
}
respond(['status' => 'success', 'saved' => !$saved, 'message' => $saved ? 'Property removed from saved listings.' : 'Property saved.']);
