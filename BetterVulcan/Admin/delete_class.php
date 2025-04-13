<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classId = $_POST['class_id'];
    $conn->query("DELETE FROM classes WHERE id = $classId");
    header("Location: admin_dashboard.php");
    exit();
}
?>