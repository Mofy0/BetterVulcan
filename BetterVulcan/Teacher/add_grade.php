<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'];
    $subjectId = $_POST['subject_id'];
    $teacherId = $_POST['teacher_id'];
    $grade = $_POST['grade'];
    $date = date('Y-m-d');

    $stmt = $conn->prepare("
        INSERT INTO grades (student_id, subject_id, teacher_id, grade, date)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiids", $studentId, $subjectId, $teacherId, $grade, $date);
    $stmt->execute();

    header("Location: teacher_dashboard.php");
    exit();
}
?>