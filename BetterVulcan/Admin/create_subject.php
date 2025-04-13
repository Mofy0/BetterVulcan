<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subjectName = $_POST['subject_name'];
    $conn->query("INSERT INTO subjects (name) VALUES ('$subjectName')");
    header("Location: admin_dashboard.php");
    exit();
}
?>