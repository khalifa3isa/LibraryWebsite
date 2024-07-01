<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$email = $_POST["username"];
$pass = $_POST["password"];
$profile = $_POST["profile"];

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'root';
$dbname = 'librasys';

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($profile == "student") {
    $stmt = $conn->prepare("SELECT * FROM student WHERE semail = ? AND spass = ?");
} elseif ($profile == "librarian") {
    $stmt = $conn->prepare("SELECT * FROM librarian WHERE lemail = ? AND lpass = ?");
} elseif ($profile == "admin") {
    if ($email == "admin@librasys.com" && $pass == "Admin@123A") {
        session_start();
        $_SESSION['email'] = $email;
        header("Location: ../php/admin_dashboard.php");
        exit();
    } else {
        header("Location: ../templates/login-error.html");
        exit();
    }
} else {
    header("Location: ../templates/login-error.html");
    exit();
}

if (isset($stmt)) {
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        session_start();
        $_SESSION['email'] = $email;

        if ($profile == "student") {
            session_start();
            $_SESSION['semail'] = $email;
            header("Location: dashboard.php");
        } elseif ($profile == "librarian") {
            $_SESSION['lemail'] = $email;
            header("Location: librarian_dashboard.php");
        }
        exit();
    } else {
        header("Location: ../templates/login-error.html");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
