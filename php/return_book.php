<?php
session_start();
require 'config.php';  // Include the configuration file

if (!isset($_SESSION['semail'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$dsn = 'mysql:host=localhost;dbname=librasys';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);
    $serialno = $data['serialno'];
    $semail = $_SESSION['semail'];

    $stmt = $pdo->prepare('
        UPDATE borrows 
        SET return_date = NOW() 
        WHERE serialno = :serialno 
        AND semail = :semail 
        AND return_date IS NULL
    ');
    $stmt->execute(['serialno' => $serialno, 'semail' => $semail]);

    $stmt = $pdo->prepare('UPDATE books SET is_borrowed = 0 WHERE serialno = :serialno');
    $stmt->execute(['serialno' => $serialno]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
