<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$msnfgndfg = "localhost";
$gdojfjngjdfn = "root";
$igdfjgojdfiog = "";
$kngdjfijnigfd = "fakelibrusdata";


$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $SpecjalnaNazwaDlaUżytkownika = $_POST['username'];
    $SpecjalnaNazwaWPHPDlaHasła = $_POST['password'];
    $MaInformacjeJakaRoleWybralUzytkownik = $_POST['role'];


    $valid_roles = ['students', 'teachers', 'admins'];
    if (!in_array($role, $valid_roles)) {
        header("Location: index.php?error=Invalid%20role%20selected");
        exit();
    }
    
    if ($role == "students"){
        $table = "students";
         if (empty($username) || empty($password)) {
            header("Location: index.php?error=Empty%20fields");
            exit();
             if ($role == "teachers"){
        $table = "teachers";
            if (empty($username) || empty($password)) {
                header("Location: index.php?error=Empty%20fields");
                exit();
            }
            if($role == "admins"){
                $table = "admins";
                if (empty($username) || empty($password)) {
                    header("Location: index.php?error=Empty%20fields");
                    exit();
                }
            }
    }
         }}


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
                    header("Location: Php\student_dashboard.php");
                    break;
                case 'teachers':
                    header("Location: teacher_dashboard.php");
                    break;
                case 'admins':
                    header("Location: admin_dashboard.php");
                    break;
            }
            exit();
        } else {

            echo "Invalid password";
            exit();
        }
    } else {

        header("Location: index.php");
        exit();
    }
    $stmt->close();
}
$conn->close();
?>