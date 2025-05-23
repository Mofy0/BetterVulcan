<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admins') {
    header("Location: ../index.php");
    exit();
}

include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

    <!-- Treść zakładek -->
    <div class="tab-container">
        <h1>Witaj, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <div class="tab-links">
                <button onclick="showTab('create')">Utwórz</button>
                <button onclick="showTab('delete')">Usuń</button>
                <button onclick="showTab('edit')">Edytuj</button>
    </div>
        <!-- Create Tab -->
        <div id="create" class="tab-content active">
            <div class="sub-tabs">
                <button onclick="showSubTab('create-admin')">Admin</button>
                <button onclick="showSubTab('create-class')">Klasa</button>
                <button onclick="showSubTab('create-teacher')">Nauczyciel</button>
            </div>

            <div id="create-admin" class="sub-tab-content">
                <form method="POST" action="create_user.php">
                    <input type="hidden" name="type" value="admin">
                    <input type="text" name="username" placeholder="Login" required>
                    <input type="password" name="password" placeholder="Hasło" required>
                    <button type="submit">Utwórz Admina</button>
                </form>
            </div>

            <div id="create-class" class="sub-tab-content">
                <form method="POST" action="create_class.php" id="classForm">
                    <input type="text" name="class_name" placeholder="Nazwa klasy" required>
                    <input type="number" name="student_count" id="studentCount" placeholder="Liczba uczniów" required
                           min="1" max="50" onchange="generateStudentFields()">
                    
                    <div id="studentFields" class="dynamic-fields"></div>
                    
                    <button type="submit">Utwórz Klasę</button>
                </form>
            </div>

            <div id="create-teacher" class="sub-tab-content">
                <form method="POST" action="create_user.php">
                    <input type="hidden" name="type" value="teacher">
                    <input type="text" name="username" placeholder="Login" required>
                    <input type="password" name="password" placeholder="Hasło" required>
                    <select name="subject_id" required>
                        <?php
                        $subjects = $conn->query("SELECT * FROM subjects");
                        while ($subject = $subjects->fetch_assoc()): ?>
                            <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit">Utwórz Nauczyciela</button>
                </form>
            </div>
        </div>

        <!-- Delete Tab -->
        <div id="delete" class="tab-content">
            <div class="sub-tabs">
                <button onclick="showSubTab('delete-class')">Klasy</button>
                <button onclick="showSubTab('delete-user')">Użytkownicy</button>
            </div>

            <div id="delete-class" class="sub-tab-content">
                <?php
                $classes = $conn->query("SELECT * FROM classes");
                while ($class = $classes->fetch_assoc()): ?>
                    <div class="delete-section">
                        <h3><?= htmlspecialchars($class['name']) ?></h3>
                        <form method="POST" action="delete_entity.php">
                            <input type="hidden" name="type" value="class">
                            <input type="hidden" name="id" value="<?= $class['id'] ?>">
                            <button type="submit">Usuń Klasę</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>

            <div id="delete-user" class="sub-tab-content">
                <h3>Użytkownicy</h3>
                <?php
                $users = $conn->query("
                    SELECT 'student' AS type, id, username FROM students
                    UNION ALL
                    SELECT 'teacher' AS type, id, username FROM teachers
                    UNION ALL
                    SELECT 'admin' AS type, id, username FROM admins
                ");

                while ($user = $users->fetch_assoc()): ?>
                    <div class="delete-section">
                        <span><?= htmlspecialchars($user['username']) ?> (<?= $user['type'] ?>)</span>
                        <form method="POST" action="delete_entity.php" style="display:inline;">
                            <input type="hidden" name="type" value="<?= $user['type'] ?>">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <button type="submit">Usuń</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Edit Tab -->
        <div id="edit" class="tab-content">
    <form id="editForm" method="POST" action="edit_user.php" class="edit-form">
        <h3>Edytuj użytkownika</h3>
        <select id="userType" name="type" required>
            <option value="">Wybierz typ użytkownika</option>
            <option value="student">Uczeń</option>
            <option value="teacher">Nauczyciel</option>
            <option value="admin">Admin</option>
        </select>
        <button type="button" id="loadUsersButton">Dalej</button>

        <div id="userSelectContainer" style="display: none;">
            <select id="userSelect" name="id" required>
                <option value="">Wybierz użytkownika</option>
                <!-- Opcje zostaną załadowane dynamicznie -->
            </select>
            <button type="submit">Edytuj</button>
        </div>
    </form>
</div>

    <script>
            document.getElementById('loadUsersButton').addEventListener('click', function () {
        const userType = document.getElementById('userType').value;

        fetch(`load_users.php?type=${userType}`)
            .then(response => response.json())
            .then(data => {
                const userSelect = document.getElementById('userSelect');
                userSelect.innerHTML = '<option value="">Wybierz użytkownika</option>';

                data.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.username;
                    userSelect.appendChild(option);
                });
                document.getElementById('userSelectContainer').style.display = 'block';
            })
            .catch(error => {
                console.error('Omg cos nie dziala :', error);
            });
    });

        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
        }

        function showSubTab(subTabId) {
            document.querySelectorAll('.sub-tab-content').forEach(tab => {
                tab.style.display = 'none';
            });
            document.getElementById(subTabId).style.display = 'block';
        }

        function generateStudentFields() {
            const count = document.getElementById('studentCount').value;
            const container = document.getElementById('studentFields');
            container.innerHTML = '';
            
            for (let i = 1; i <= count; i++) {
                container.innerHTML += `
                    <div class="student-field">
                        <input type="text" name="students[${i}][login]" placeholder="Login ucznia ${i}" required>
                        <input type="password" name="students[${i}][password]" placeholder="Hasło ucznia ${i}" required>
                    </div>
                `;
            }
        }
        
        // Inicjalizacja
        showTab('create');
        showSubTab('create-admin');
    </script>
</body>
</html>