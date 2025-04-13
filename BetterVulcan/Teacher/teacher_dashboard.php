<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teachers') {
    header("Location: ../index.php");
    exit();
}

include 'db_connect.php';

// Pobierz informacje o nauczycielu i przypisanym przedmiocie
$teacher = $conn->query("
    SELECT teachers.*, subjects.name AS subject_name 
    FROM teachers 
    LEFT JOIN subjects ON teachers.subject_id = subjects.id 
    WHERE teachers.username = '{$_SESSION['username']}'
")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Nauczyciela</title>
    <link rel="stylesheet" href="teacher.css">
</head>
<body>
    <div class="container">
        <!-- Lewy panel -->
        <div class="left-panel">
            <h1>Witaj, <?= htmlspecialchars($teacher['username']) ?>!</h1>
            <h2>Przypisany przedmiot: <?= htmlspecialchars($teacher['subject_name']) ?></h2>
        </div>

        <!-- Prawy panel -->
        <div class="right-panel">
            <h2>Dodaj ocenę</h2>
            <div class="grade-form">
                <form method="POST" action="add_grade.php">
                    <label for="student_id">Wybierz ucznia:</label>
                    <select id="student_id" name="student_id" required>
                        <?php
                        $students = $conn->query("
                            SELECT students.id, students.username, classes.name AS class_name 
                            FROM students 
                            LEFT JOIN classes ON students.class_id = classes.id
                        ");
                        while ($student = $students->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($student['id']) . "'>
                                " . htmlspecialchars($student['username']) . " (Klasa: " . htmlspecialchars($student['class_name']) . ")
                            </option>";
                        }
                        ?>
                    </select>

                    <label for="grade">Wystaw ocenę:</label>
                    <input id="grade" type="number" step="0.5" min="1" max="6" name="grade" placeholder="Ocena" required>

                    <input type="hidden" name="subject_id" value="<?= htmlspecialchars($teacher['subject_id']) ?>">
                    <input type="hidden" name="teacher_id" value="<?= htmlspecialchars($teacher['id']) ?>">

                    <button type="submit">Dodaj ocenę</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>