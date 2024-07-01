<?php
session_start();
require 'config.php';

if (!isset($_SESSION['email'])) {
    header("Location: ../templates/index.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lname = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
    $lemail = filter_var($_POST['lemail'], FILTER_SANITIZE_EMAIL);
    $lpass = $_POST['lpass']; 
    $phoneno = filter_var($_POST['phoneno'], FILTER_SANITIZE_STRING);

    if (!filter_var($lemail, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        exit;
    }

    $dsn = 'mysql:host=localhost;dbname=librasys';
    $username = 'root';
    $password = 'root';

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare('SELECT * FROM librarian WHERE lemail = :lemail');
        $stmt->bindParam(':lemail', $lemail);
        $stmt->execute();
        $existingLibrarian = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingLibrarian) {
            http_response_code(400);
            echo json_encode(['error' => 'Librarian already exists']);
            exit;
        }

        $stmt = $pdo->prepare('INSERT INTO librarian (lname, lemail, lpass, phoneno) VALUES (:lname, :lemail, :lpass, :phoneno)');
        
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':lemail', $lemail);
        $stmt->bindParam(':lpass', $lpass); 
        $stmt->bindParam(':phoneno', $phoneno);

        if ($stmt->execute()) {
            echo json_encode(['success' => 'Librarian added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Unable to add librarian']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>
