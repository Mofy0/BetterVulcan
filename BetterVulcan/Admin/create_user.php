<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        // Sprawdź unikalność nazwy użytkownika
        $conn->begin_transaction();
        
        // Określ tabelę docelową
        $table = ($type === 'admin') ? 'admins' : 'teachers';
        
        // Sprawdź czy użytkownik już istnieje
        $check_stmt = $conn->prepare("SELECT id FROM $table WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            throw new Exception("Nazwa użytkownika już istnieje");
        }

        // Stwórz hasło
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Wstaw nowego użytkownika
        $insert_stmt = $conn->prepare("INSERT INTO $table (username, password" . ($type === 'teacher' ? ", subject_id" : "") . ") VALUES (?, ?" . ($type === 'teacher' ? ", ?" : "") . ")");
        
        if ($type === 'teacher') {
            $subject_id = (int)$_POST['subject_id'];
            $insert_stmt->bind_param("ssi", $username, $hashed_password, $subject_id);
        } else {
            $insert_stmt->bind_param("ss", $username, $hashed_password);
        }
        
        $insert_stmt->execute();
        $conn->commit();
        
        header("Location: admin_dashboard.php?success=Użytkownik utworzony");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = urlencode($e->getMessage());
        header("Location: admin_dashboard.php?error=$error_message");
        exit();
    }
}
?>