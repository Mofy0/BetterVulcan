<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pokaż formularz edycji
    $type = $_POST['type'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    $allowedTypes = ['student', 'teacher', 'admin'];
    if (!in_array($type, $allowedTypes) || $id === 0) {
        die("Nieprawidłowe żądanie");
    }

    $table = $type . 's';
    $stmt = $conn->prepare("SELECT * FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        die("Użytkownik nie istnieje");
    }

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Edytuj użytkownika</title>
        <style>
            .edit-form { max-width: 500px; margin: 20px; }
            .edit-form input { margin: 10px 0; padding: 8px; width: 100%; }
        </style>
    </head>
    <body>
        <form method="POST" action="update_user.php" class="edit-form">
            <input type="hidden" name="type" value="<?= $type ?>">
            <input type="hidden" name="id" value="<?= $id ?>">
            
            <label>Login:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            
            <label>Nowe hasło (pozostaw puste aby nie zmieniać):</label>
            <input type="password" name="password">
            
            <?php if($type === 'teacher'): ?>
                <label>Przedmiot:</label>
                <select name="subject_id" required>
                    <?php
                    $subjects = $conn->query("SELECT * FROM subjects");
                    while ($subject = $subjects->fetch_assoc()): ?>
                        <option value="<?= $subject['id'] ?>" <?= $subject['id'] == $user['subject_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($subject['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            <?php endif; ?>
            
            <button type="submit">Zapisz zmiany</button>
        </form>
    </body>
    </html>
    <?php
}
?>