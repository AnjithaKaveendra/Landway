<?php
require_once __DIR__ . '/config.php';
switch ($_GET['action'] ?? '') {
    case 'create':
        requireMethod('POST');
        $data = getRequestData();
        $values = [textField($data, 'contactName', true, 100), emailField($data, 'contactEmail'),
            textField($data, 'contactPhone', false, 20), textField($data, 'contactSubject', false, 200),
            textField($data, 'contactPropertyType', false, 50), textField($data, 'contactMessageText', true, 20000)];
        $statement = dbQuery('INSERT INTO inquiries (contactName, contactEmail, contactPhone, contactSubject, contactPropertyType, contactMessageText) VALUES (?, ?, ?, ?, ?, ?)', $values);
        respond(['status' => 'success', 'message' => 'Inquiry submitted successfully.', 'id' => $statement->insert_id], 201);
    case 'get-all':
        requireMethod('GET');
        requireAdmin();
        respond(['status' => 'success', 'data' => dbQuery('SELECT * FROM inquiries ORDER BY inquiryDate DESC, id DESC')->get_result()->fetch_all(MYSQLI_ASSOC)]);
    case 'get':
        requireMethod('GET');
        requireAdmin();
        respond(['status' => 'success', 'data' => findRecord('inquiries', $_GET['id'] ?? null)]);
    case 'delete':
        requireMethod('POST');
        requireAdmin();
        deleteRecord('inquiries', getRequestData()['id'] ?? null);
    default:
        fail('Invalid action.', 400);
}
