<?php
require_once __DIR__ . '/settings.php';

if (session_status() !== PHP_SESSION_ACTIVE && PHP_SAPI !== 'cli') {
    session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax',
        'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'use_strict_mode' => true]);
}
if (PHP_SAPI !== 'cli' && empty($_SESSION['csrfToken'])) {
    $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
}

function e($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function getDB() {
    static $connection;
    if (!$connection) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        $connection->set_charset('utf8mb4');
    }
    return $connection;
}

function dbQuery($sql, array $params = []) {
    $statement = getDB()->prepare($sql);
    if ($params) {
        $types = '';
        foreach ($params as $value) {
            $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
        }
        $statement->bind_param($types, ...$params);
    }
    $statement->execute();
    return $statement;
}

function isAdmin() {
    return !empty($_SESSION['adminSession']['isAdmin']);
}

function isLoggedIn() {
    return isAdmin() || !empty($_SESSION['userSession']['userId']);
}

function propertyImage(array $property) {
    $images = ['house' => 'img/property-1.jpg', 'land' => 'img/property-2.jpg',
        'apartment' => 'img/property-3.jpg', 'commercial' => 'img/property-4.jpg'];
    return $images[$property['propertyType']] ?? 'img/property-1.jpg';
}

function propertyFeatures(array $property) {
    return array_values(array_filter(array_map('trim', explode(',', $property['features'] ?? ''))));
}
