<?php
session_start();
include 'db_connect.php';


$student = $conn->query("
    SELECT students.*, classes.name AS class_name 
    FROM students 
    JOIN classes ON students.class_id = classes.id 
    WHERE students.username = '{$_SESSION['username']}'
")->fetch_assoc();

$subjects = $conn->query("
    SELECT DISTINCT subjects.id, subjects.name 
    FROM grades 
    JOIN subjects ON grades.subject_id = subjects.id 
    WHERE grades.student_id = {$student['id']}
");

$grades = [];
$gradesQuery = $conn->query("
    SELECT grades.*, subjects.name AS subject_name, teachers.username AS teacher_name 
    FROM grades 
    JOIN subjects ON grades.subject_id = subjects.id 
    JOIN teachers ON grades.teacher_id = teachers.id 
    WHERE grades.student_id = {$student['id']}
");

while ($grade = $gradesQuery->fetch_assoc()) {
    $grades[$grade['subject_id']][] = $grade;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Ucznia</title>
    <link rel="stylesheet" href="student.css">
</head>
<body>
    <div class="student-container">
        <header class="student-header">
            <h1>Witaj, <?= htmlspecialchars($student['username']) ?>!</h1>
            <p>Klasa: <?= htmlspecialchars($student['class_name']) ?></p>
        </header>

        <div class="grades-container">
            <div class="subjects-column">
                <h2>Przedmioty</h2>
                <ul class="subject-list">
                    <?php while ($subject = $subjects->fetch_assoc()): ?>
                        <li data-subject-id="<?= $subject['id'] ?>">
                            <?= htmlspecialchars($subject['name']) ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <div class="grades-column">
                <h2>Oceny</h2>
                <div class="grades-display">
                    <?php foreach ($grades as $subjectId => $subjectGrades): ?>
                        <div class="subject-grades" data-subject-id="<?= $subjectId ?>">
                            <?php foreach ($subjectGrades as $grade): ?>
                                <div class="grade-item">
                                    <span class="grade-value"><?= $grade['grade'] ?></span>
                                    <span class="grade-info">
                                        Nauczyciel: <?= htmlspecialchars($grade['teacher_name']) ?><br>
                                        Data: <?= $grade['date'] ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.subject-list li').forEach(item => {
            item.addEventListener('click', function() {
                const subjectId = this.getAttribute('data-subject-id');
                
                document.querySelectorAll('.subject-grades').forEach(grades => {
                    grades.style.display = 'none';
                });
                
                document.querySelector(`.subject-grades[data-subject-id="${subjectId}"]`).style.display = 'block';
            });
        });

        // Pokaz pierwszy przedmiot na starcie
        document.querySelector('.subject-list li:first-child')?.click();
    </script>
</body>
</html>