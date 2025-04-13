<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "fakelibrusdata";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $valid_roles = ['students', 'teachers', 'admins']; // Wszystkie role jakie sa dowzwolone 
    if (!in_array($role, $valid_roles)) {
        header("Location: index.php?error=Invalid%20role%20selected");
        exit();
    }

    // Determine the table based on the role
    $table = ""; // zmienna do przechowywania jaka tabela ma byc uzyta
    switch ($role) {
        case 'students':
            $table = "students";
            break;
        case 'teachers':
            $table = "teachers";
            break;
        case 'admins':
            $table = "admins";
            break;
        default:
            header("Location: index.php?error=Invalid%20role%20selected");
            exit();
    }

    $stmt = $conn->prepare("SELECT * FROM $table WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            echo $role;
            switch ($role) {
                case 'students':
                    header("Location: Student\student_dashboard.php");
                    break;
                case 'teachers':
                    header("Location: Teacher/teacher_dashboard.php");
                    break;
                case 'admins':
                    header("Location: Admin/admin_dashboard.php");
                    break;
            }
            exit();
        } else {
            echo "Invalid password";
            exit();
        }
    } else {
        // Niem ma takiego uzytkownika
        header("Location: index.php");
        exit();
    }
    $stmt->close();
}
$conn->close();
?>