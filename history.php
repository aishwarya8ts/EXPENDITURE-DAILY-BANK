<?php
session_start();
require 'db.php';
require 'function.php';
// logActivity($conn, $user_email, "attending history list");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user'];

$stmt = $conn->prepare("SELECT * FROM account_history WHERE user_email = ? ORDER BY action_date DESC");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$history = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Account History</title>
    <style>
        body {
            background-image: url(/images/develpoment.jpeg);
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            /* Ensures the image covers the entire screen */
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        img {
            width: 60px;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #333;
            padding: 15px;
            color: white;
        }

        .navbar h1 {
            flex-grow: 1;
            text-align: center;
            margin: 0;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .account-item {
            background: #eee;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .fav-button {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        .fav-button.favorited {
            color: red;
        }

        .delete-button {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-button:hover {
            background-color: darkred;
        }

        button {
            padding: 8px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .green-button {
            background: #28a745;
            color: white;
        }

        .green-button:hover {
            background: #218838;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <img src="logo.png" alt="Logo">
        <h1>BANK</h1>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="account.php">Accounts</a>
            <a href="expenditure.php">Expenditure Check</a>
            <a href="expenditure_history.php">Analysis</a>
            <a href="history.php">History</a>
            <a href="favorites.php"><span style="color: gold;">★</span> Favorites</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <h2>Transaction History</h2>
    <table border="1">
        <tr>
            <th>Action</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>
        <?php foreach ($history as $entry) : ?>
            <tr>
                <td><?= ucfirst($entry['action']) ?></td>
                <td>$<?= number_format($entry['amount'], 2) ?></td>
                <td><?= $entry['action_date'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>