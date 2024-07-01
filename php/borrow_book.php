<?php
session_start();
require 'config.php'; 

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

    $stmt = $pdo->prepare('SELECT is_borrowed FROM books WHERE serialno = :serialno');
    $stmt->execute(['serialno' => $serialno]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book && !$book['is_borrowed']) {
        $stmt = $pdo->prepare('UPDATE books SET is_borrowed = 1 WHERE serialno = :serialno');
        $stmt->execute(['serialno' => $serialno]);

        $stmt = $pdo->prepare('INSERT INTO borrows (semail, serialno) VALUES (:semail, :serialno)');
        $stmt->execute(['semail' => $_SESSION['semail'], 'serialno' => $serialno]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Book is already borrowed or does not exist']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
