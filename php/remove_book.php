<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serialno = $_POST['serialno_remove'];

    $dsn = 'mysql:host=localhost;dbname=librasys';
    $username = 'root';
    $password = 'root';
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare('DELETE FROM books WHERE serialno = ?');
        $stmt->execute([$serialno]);

        header("Location: ../templates/interstitial.html");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    http_response_code(405);
    exit;
}
?>
