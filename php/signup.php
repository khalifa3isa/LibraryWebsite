<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Set up a PDO database connection
$dsn = 'mysql:host=localhost;dbname=librasys';
$username = 'root';
$password = 'root';
$options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

try {
  $pdo = new PDO($dsn, $username, $password, $options);
} catch(PDOException $e) {
  echo 'Error connecting to database: ' . $e->getMessage();
  exit();
}

$name = $_POST["name"];
$phoneno = $_POST["phone"];
$email = $_POST["email"];
$pass = $_POST["pass"];
$profile = "student";

if ($profile == "student") {
    $stmt = $pdo->prepare("INSERT INTO student (sname, semail, spass, phoneno) VALUES ('$name', '$email', '$pass', '$phoneno')");
}

try {
  $stmt->execute();  
} catch(PDOException $e) {
  echo 'Error inserting data: ' . $e->getMessage();
  exit();
}
?>
