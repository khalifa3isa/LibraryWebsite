<?php
session_start();
require 'config.php';

if (!isset($_SESSION['lemail'])) {
    header("Location: ../templates/index.html");
    exit;
}

$dsn = 'mysql:host=localhost;dbname=librasys';
$username = 'root';
$password = 'root';
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query('SELECT serialno, title, author, is_borrowed FROM books');
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="../css/booklist_styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            position: relative;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            color: #333;
        }

        h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"], button {
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        a {
            text-decoration: none;
            color: #333;
            margin-left: 10px;
            font-size: 14px;
            position: absolute;
            top: 20px; /* Adjust top positioning */
            right: 20px; /* Adjust right positioning */
        }

        a:hover {
            text-decoration: underline;
        }

        .book {
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .book h3 {
            margin-top: 0;
            font-size: 18px;
            color: #333;
        }

        .book p {
            color: #666;
            margin-bottom: 5px;
        }

        .book button {
            padding: 8px 12px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        .book button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .logout-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3565B9;
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            float: right;
            margin-top: 10px;
        }

        .logout-button:hover {
            background-color: #3565E9;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="logout.php" class="logout-button">Logout</a>
        <h1>Welcome, Librarian!</h1>
        <h2>Manage Books</h2>

        <div class="manage-section">
            <div class="add-book">
                <h3>Add New Book</h3>
                <form action="add_book.php" method="POST">
                    <label for="serialno">Serial Number:</label>
                    <input type="text" id="serialno" name="serialno" required><br><br>
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required><br><br>
                    <label for="author">Author:</label>
                    <input type="text" id="author" name="author" required><br><br>
                    <button type="submit">Add Book</button>
                </form>
            </div>

            <div class="remove-book">
                <h3>Remove Book</h3>
                <form action="remove_book.php" method="POST">
                    <label for="serialno_remove">Serial Number:</label>
                    <input type="text" id="serialno_remove" name="serialno_remove" required><br><br>
                    <button type="submit">Remove Book</button>
                </form>
            </div>
        </div>

        <br><br>

        <h2>Existing Books</h2>
        <div id="book-list">
            <?php foreach ($books as $book): ?>
                <div class="book">
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p>Serial Number: <?php echo htmlspecialchars($book['serialno']); ?></p>
                    <p>Author: <?php echo htmlspecialchars($book['author']); ?></p>
                    <p>Status: <?php echo $book['is_borrowed'] ? 'Borrowed' : 'Available'; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
