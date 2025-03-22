<?php
session_start();
require 'db.php';
require 'function.php';
// logActivity($conn, $user_email, "banks add or vies of bank");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user'];

// Handle adding an account
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_account'])) {
    $account_name = $_POST['account_name'];
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("INSERT INTO accounts (user_email, account_name, amount, is_favorite, created_at) VALUES (?, ?, ?, 0, NOW())");
    $stmt->bind_param("ssd", $user_email, $account_name, $amount);
    $stmt->execute();
}

// Handle updating amount
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_amount'])) {
    $account_id = $_POST['account_id'];
    $extra_amount = $_POST['extra_amount'];

    if (!is_numeric($extra_amount) || $extra_amount <= 0) {
        echo "<script>alert('Invalid amount entered.');</script>";
    } else {
        $stmt = $conn->prepare("UPDATE accounts SET amount = amount + ? WHERE id = ? AND user_email = ?");
        $stmt->bind_param("dis", $extra_amount, $account_id, $user_email);
        $stmt->execute();
    }
}

// Handle removing amount
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_money'])) {
    $account_id = $_POST['account_id'];
    $remove_amount = $_POST['remove_amount'];

    if (!is_numeric($remove_amount) || $remove_amount <= 0) {
        echo "<script>alert('Invalid amount entered.');</script>";
    } else {
        $stmt = $conn->prepare("UPDATE accounts SET amount = amount - ? WHERE id = ? AND user_email = ?");
        $stmt->bind_param("dis", $remove_amount, $account_id, $user_email);
        $stmt->execute();
    }
}

// Handle marking as favorite
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['favorite'])) {
    $account_id = $_POST['account_id'];

    $stmt = $conn->prepare("UPDATE accounts SET is_favorite = NOT is_favorite WHERE id = ? AND user_email = ?");
    $stmt->bind_param("is", $account_id, $user_email);
    $stmt->execute();
}

// Handle deleting account
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
    $account_id = $_POST['account_id'];

    $stmt = $conn->prepare("DELETE FROM accounts WHERE id = ? AND user_email = ?");
    $stmt->bind_param("is", $account_id, $user_email);
    $stmt->execute();
}

// Fetch all accounts
$stmt = $conn->prepare("SELECT * FROM accounts WHERE user_email = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$accounts = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Account Details</title>
    <style>
        body {
            background-image: url(/images/develpoment.jpeg);
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            /* Ensures the image covers the entire screen */
            background-attachment: fixed;
            /* Keeps the background fixed when scrolling */
            font-family: Arial, sans-serif;
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
        <img src="/images/LOGO.png" alt="Logo">
        <h1>BANK</h1>
        <div class="nav-links">
            <a href="index.html">Home</a>
            <a href="account.php">Accounts</a>
            <a href="expenditure.php">Expenditure</a>
            <a href="expenditure_history.php">Analysis</a>
            <a href="history.php">History</a>
            <a href="favorites.php"><span style="color: gold;">★</span> Favorites</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Add Account</h2>
        <form method="post">
            <input type="text" name="account_name" placeholder="Account Name" required>
            <input type="number" name="amount" placeholder="Initial Amount" required>
            <button type="submit" name="add_account" class="green-button">Add Account</button>
        </form>

        <div class="account-list">
            <h2>Your Accounts</h2>
            <?php foreach ($accounts as $account) : ?>
                <div class="account-item">
                    <span>
                        <strong><?= htmlspecialchars($account['account_name']) ?></strong> -
                        $<?= number_format($account['amount'], 2) ?>
                    </span>
                    <form method="post">
                        <input type="hidden" name="account_id" value="<?= $account['id'] ?>">
                        <input type="number" name="extra_amount" placeholder="Add Money">
                        <button type="submit" name="update_amount" class="green-button">+</button>
                        <input type="number" name="remove_amount" placeholder="Remove Money">
                        <button type="submit" name="remove_money" class="green-button">-</button>
                        <button type="submit" name="favorite" class="fav-button <?= $account['is_favorite'] ? 'favorited' : '' ?>">
                            <?= $account['is_favorite'] ? '❤️' : '🖤' ?>
                        </button>
                        <button type="submit" name="delete_account" class="delete-button" onclick="return confirm('Are you sure you want to delete this account?')">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>