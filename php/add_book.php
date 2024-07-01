<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serialno = $_POST['serialno'];
    $title = $_POST['title'];
    $author = $_POST['author'];

    $dsn = 'mysql:host=localhost;dbname=librasys';
    $username = 'root';
    $password = 'root';

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare('INSERT INTO books (serialno, title, author) VALUES (?, ?, ?)');
        $stmt->execute([$serialno, $title, $author]);

        header("Location: ../templates/interstitial.html");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    http_response_code(405);
    exit;
}
?>
