<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowedTypes = ['class', 'student', 'teacher', 'admin'];
    $type = $_POST['type'];
    $id = (int)$_POST['id'];

    if (!in_array($type, $allowedTypes)) {
        die("Nieprawidłowy typ");
    }

    $table = $type === 'class' ? 'classes' : $type . 's';
    
    try {
        $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        header("Location: admin_dashboard.php?success=Usunięto pomyślnie");
        exit();
    } catch (Exception $e) {
        header("Location: admin_dashboard.php?error=Błąd usuwania");
        exit();
    }
}
?>