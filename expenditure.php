<?php
session_start();
require 'db.php';
require 'function.php';
// logActivity($conn, $user_email, "Adding expenditure");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user'];

// Fetch user's accounts
$stmt = $conn->prepare("SELECT id, account_name, amount FROM accounts WHERE user_email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$accounts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle adding expenditure or income
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_expenditure'])) {
    $category = $_POST['category']; // Income or Expense
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $account_id = $_POST['account_id']; // Selected account

    // Ensure category is valid
    if (!in_array($category, ['income', 'expense'])) {
        die("Invalid category.");
    }

    // Ensure account belongs to the user
    $stmt = $conn->prepare("SELECT amount FROM accounts WHERE id = ? AND user_email = ?");
    $stmt->bind_param("is", $account_id, $user_email);
    $stmt->execute();
    $account = $stmt->get_result()->fetch_assoc();

    if (!$account) {
        die("Invalid account selection.");
    }

    // Insert into expenditures table
    $stmt = $conn->prepare("INSERT INTO expenditures (user_email, account_id, category, amount, description, date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisdss", $user_email, $account_id, $category, $amount, $description, $date);
    $stmt->execute();

    // Update selected account balance
    if ($category === 'income') {
        $conn->query("UPDATE accounts SET amount = amount + $amount WHERE id = $account_id");
    } else {
        $conn->query("UPDATE accounts SET amount = amount - $amount WHERE id = $account_id");
    }
}

// Fetch expenditures
$query = "SELECT e.*, a.account_name FROM expenditures e JOIN accounts a ON e.account_id = a.id WHERE e.user_email = ?";
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

switch ($filter) {
    case 'daily':
        $query .= " AND DATE(e.date) = CURDATE()";
        break;
    case 'weekly':
        $query .= " AND YEARWEEK(e.date, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'monthly':
        $query .= " AND MONTH(e.date) = MONTH(CURDATE()) AND YEAR(e.date) = YEAR(CURDATE())";
        break;
    case 'yearly':
        $query .= " AND YEAR(e.date) = YEAR(CURDATE())";
        break;
}

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$expenditures = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate summary
$stmt = $conn->prepare("SELECT 
       SUM(CASE WHEN category='income' THEN amount ELSE 0 END) AS total_income,
       SUM(CASE WHEN category='expense' THEN amount ELSE 0 END) AS total_expense
FROM expenditures WHERE user_email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();
$total_income = $summary['total_income'] ?? 0;
$total_expense = $summary['total_expense'] ?? 0;
$total_balance = $total_income - $total_expense;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenditure Tracker</title>
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

        .summary {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        .summary .income {
            color: green;
        }

        .summary .expense {
            color: red;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        form input,
        form select,
        form button {
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        form button {
            background: #28a745;
            color: white;
            cursor: pointer;
        }

        form button:hover {
            background: #218838;
        }

        .expenditure-list {
            margin-top: 20px;
        }

        .expenditure-item {
            background: #eee;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .expenditure-item.income {
            border-left: 5px solid green;
            background: #e8f5e9;
        }

        .expenditure-item.expense {
            border-left: 5px solid red;
            background: #fdecea;
        }

        .filter-bar {
            margin-top: 15px;
        }

        .filter-bar a {
            text-decoration: none;
            padding: 5px 10px;
            background: #007bff;
            color: white;
            margin-right: 5px;
            border-radius: 5px;
        }

        .filter-bar a:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <img src="/images/LOGO.png" alt="Logo">
        <h1>BANK</h1>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="account.php">Accounts</a>
            <a href="expenditure.php">Expenditure</a>
            <a href="expenditure_history.php">Analysis</a>
            <a href="history.php">History</a>
            <a href="favorites.php"><span style="color: gold;">★</span> Favorites</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>Expenditure Tracker</h2>
        <form method="post">
            <select name="category">
                <option value="income">Income</option>
                <option value="expense">Expense</option>
            </select>
            <select name="account_id" required>
                <option value="">Select Account</option>
                <?php foreach ($accounts as $acc) { ?>
                    <option value="<?php echo $acc['id']; ?>"><?php echo $acc['account_name'] . " ($" . number_format($acc['amount'], 2) . ")"; ?></option>
                <?php } ?>
            </select>
            <input type="number" name="amount" placeholder="Amount" required>
            <input type="text" name="description" placeholder="Description">
            <input type="date" name="date" required>
            <button type="submit" name="add_expenditure">Add Entry</button>
        </form>
    </div>
</body>

</html>