<?php
session_start();
require 'config.php';

if (!isset($_SESSION['email'])) {
    header("Location: ../templates/index.html");
    exit;
}

if (!isset($_GET['lemail'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Librarian email not provided']);
    exit;
}

$lemail = $_GET['lemail'];

$dsn = 'mysql:host=localhost;dbname=librasys';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('DELETE FROM librarian WHERE lemail = :lemail');

    $stmt->bindParam(':lemail', $lemail);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Librarian deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to delete librarian']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
 