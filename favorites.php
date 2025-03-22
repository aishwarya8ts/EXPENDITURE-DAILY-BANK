<?php
session_start();
require 'db.php';
require 'function.php';
// logActivity($conn, $user_email, "add favorites");


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user'];

// Fetch only favorite accounts
$stmt = $conn->prepare("SELECT * FROM accounts WHERE user_email = ? AND is_favorite = 1 ORDER BY created_at DESC");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$favorites = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Accounts</title>
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

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #333;
            padding: 15px;
            color: white;
        }

        .navbar img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
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
            text-align: center;
        }

        .account-item {
            background: #fff3cd;
            padding: 15px;
            margin-top: 10px;
            border-radius: 5px;
            border-left: 5px solid gold;
            font-size: 18px;
        }

        .empty-message {
            font-size: 16px;
            color: gray;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <img src="/images/LOGO.png" alt="Logo">
        <h1>BANK</h1>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="account.php">Accounts</a>
            <a href="expenditure.php">Expenditure Check</a>
            <a href="expenditure_history.php">Analysis</a>
            <a href="history.php">History</a>
            <a href="favorites.php"><span style="color: gold;">★</span> Favorites</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Favorite Accounts</h2>
        <?php if (count($favorites) > 0): ?>
            <?php foreach ($favorites as $account): ?>
                <div class="account-item">
                    <?= htmlspecialchars($account['account_name']); ?> - $<?= number_format($account['amount'], 2); ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-message">No favorite accounts yet.</p>
        <?php endif; ?>
    </div>
</body>

</html>