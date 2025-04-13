<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();

        // Create class
        $stmt = $conn->prepare("INSERT INTO classes (name) VALUES (?)");
        $stmt->bind_param("s", $_POST['class_name']);
        $stmt->execute();
        $classId = $conn->insert_id;

        // Create students
        if(isset($_POST['students'])) {
            $studentStmt = $conn->prepare("INSERT INTO students (username, password, class_id) VALUES (?, ?, ?)");
            
            foreach ($_POST['students'] as $student) {
                $hashedPassword = password_hash($student['password'], PASSWORD_DEFAULT);
                $studentStmt->bind_param("ssi", $student['login'], $hashedPassword, $classId);
                $studentStmt->execute();
            }
        }

        $conn->commit();
        header("Location: admin_dashboard.php?success=Klasa utworzona");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: admin_dashboard.php?error=Błąd tworzenia klasy");
        exit();
    }
}
?>