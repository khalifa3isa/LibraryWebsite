<?php
session_start();
require 'config.php';

if (!isset($_SESSION['email'])) {
    header("Location: ../templates/index.html");
    exit;
}

$dsn = 'mysql:host=localhost;dbname=librasys';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query('SELECT * FROM librarian');
    $librarians = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <script src="../js/indexdb.js"></script>
    <title>Librasys Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        h1, h2, h3 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        form {
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 14px;
        }
        .form-group button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #c82333;
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
<div class="container">
    <h1>Librasys Admin Dashboard</h1>
    <h2>Manage Librarians</h2>

    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Action</th>
        </tr>
        <?php foreach ($librarians as $librarian): ?>
            <tr>
                <td><?php echo htmlspecialchars($librarian['lname']); ?></td>
                <td><?php echo htmlspecialchars($librarian['lemail']); ?></td>
                <td><?php echo htmlspecialchars($librarian['phoneno']); ?></td>
                <td><button class="delete-btn" onclick="deleteLibrarian('<?php echo $librarian['lemail']; ?>')">Delete</button></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Add New Librarian</h3>
    <form id="addLibrarianForm">
        <div class="form-group">
            <label for="lname">Name:</label>
            <input type="text" id="lname" name="lname" required>
        </div>
        <div class="form-group">
            <label for="lemail">Email:</label>
            <input type="email" id="lemail" name="lemail" required>
        </div>
        <div class="form-group">
            <label for="lpass">Password:</label>
            <input type="password" id="lpass" name="lpass" required>
        </div>
        <div class="form-group">
            <label for="phoneno">Phone Number:</label>
            <input type="text" id="phoneno" name="phoneno" required>
        </div>
        <div class="form-group">
            <button type="submit">Add Librarian</button>
        </div>
    </form>
</div>

<script>
    function deleteLibrarian(lemail) {
        if (confirm('Are you sure you want to delete this librarian?')) {
            fetch(`delete_librarian.php?lemail=${encodeURIComponent(lemail)}`, {
                method: 'DELETE'
            }).then(response => {
                if (response.ok) {
                    alert('Librarian deleted successfully.');
                    location.reload(); 
                } else {
                    alert('Error deleting librarian.');
                }
            }).catch(error => {
                console.error('Error:', error);
            });
        }
    }

    document.getElementById('addLibrarianForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        fetch('add_librarian.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                alert('Librarian added successfully.');
                location.reload();
            } else {
                alert('Error adding librarian.');
            }
        }).catch(error => {
            console.error('Error:', error);
        });
    });
</script>

</body>
</html>
