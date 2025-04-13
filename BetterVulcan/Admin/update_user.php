<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowedTypes = ['student', 'teacher', 'admin'];
    $type = $_POST['type'];
    $id = (int)$_POST['id'];

    if (!in_array($type, $allowedTypes)) {
        die("Nieprawidłowy typ użytkownika");
    }

    $table = $type . 's';
    $username = $_POST['username'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    try {
        $sql = "UPDATE $table SET username = ?";
        $params = [$username];
        $types = "s";

        if ($password !== null) {
            $sql .= ", password = ?";
            $params[] = $password;
            $types .= "s";
        }

        if ($type === 'teacher') {
            $sql .= ", subject_id = ?";
            $params[] = (int)$_POST['subject_id'];
            $types .= "i";
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        header("Location: admin_dashboard.php?success=Zaktualizowano użytkownika");
        exit();
    } catch (Exception $e) {
        header("Location: admin_dashboard.php?error=Błąd aktualizacji");
        exit();
    }
}
?>