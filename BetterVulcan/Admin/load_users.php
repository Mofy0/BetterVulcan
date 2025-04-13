<?php
include 'db_connect.php';

$type = $_GET['type'] ?? '';
$allowedTypes = ['student', 'teacher', 'admin'];

if (!in_array($type, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Nieprawidłowy typ użytkownika']);
    exit;
}

$table = $type . 's';
$result = $conn->query("SELECT id, username FROM $table");

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

header('Content-Type: application/json');
echo json_encode($users);