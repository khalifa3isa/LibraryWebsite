<?php
session_start();
require 'config.php';

if (!isset($_SESSION['semail'])) {
    header("Location: ../templates/index.html");
    exit;
}

$dsn = 'mysql:host=localhost;dbname=librasys';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $semail = $_SESSION['semail'];
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $stmt = $pdo->prepare('
        SELECT b.serialno, b.title, b.author, b.is_borrowed, br.semail AS borrower 
        FROM books b 
        LEFT JOIN borrows br ON b.serialno = br.serialno AND br.return_date IS NULL 
        WHERE b.title LIKE :search OR b.author LIKE :search
    ');
    $stmt->execute(['search' => "%$search%"]);
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
    <title>Library Book List</title>
    <link rel="stylesheet" href="../css/booklist_styles.css">
    <style>
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
            border: none;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #3565E9;
        }
    </style>
</head>
<body>
    <h1>Available Books</h1>
    <div>
        <input type="text" id="search" placeholder="Search by title or author">
        <button onclick="searchBooks()">Search</button>
        <form action="logout.php" method="post" style="display:inline;">
            <button type="submit" class="logout-button">Logout</button>
        </form>
    </div>
    <div id="book-list">
        <?php foreach ($books as $book): ?>
            <div class="book">
                <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                <p>Author: <?php echo htmlspecialchars($book['author']); ?></p>
                <p>Status: <?php echo $book['is_borrowed'] ? 'Borrowed' : 'Available'; ?></p>
                <?php if ($book['is_borrowed'] && $book['borrower'] === $semail): ?>
                    <button onclick="returnBook('<?php echo $book['serialno']; ?>')">Return</button>
                <?php elseif (!$book['is_borrowed']): ?>
                    <button onclick="borrowBook('<?php echo $book['serialno']; ?>')">Borrow</button>
                <?php else: ?>
                    <button disabled>Borrowed</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="notification" class="hidden">
        <p id="notification-message"></p>
    </div>

    <script src="../js/indexeddb.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Load books into IndexedDB
            const books = <?php echo json_encode($books); ?>;
            books.forEach(book => {
                addBookToIndexedDB(book);
            });
        });

        function borrowBook(serialno) {
            fetch('<?php echo BASE_URL; ?>borrow_book.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ serialno: serialno })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showNotification('Book borrowed successfully!');
                    setTimeout(() => location.reload(), 3000);
                } else {
                    showNotification('Failed to borrow book. ' + result.message);
                }
            });
        }

        function returnBook(serialno) {
            fetch('<?php echo BASE_URL; ?>return_book.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ serialno: serialno })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showNotification('Book returned successfully!');
                    setTimeout(() => location.reload(), 3000);
                } else {
                    showNotification('Failed to return book. ' + result.message);
                }
            });
        }

        function showNotification(message) {
            const notification = document.getElementById('notification');
            const messageElem = document.getElementById('notification-message');
            messageElem.textContent = message;
            notification.classList.remove('hidden');

            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }

        function searchBooks() {
            const search = document.getElementById('search').value;
            const url = '<?php echo BASE_URL; ?>dashboard.php?search=' + encodeURIComponent(search);
            window.location.href = url;
        }
    </script>
</body>
</html>
