<?php
session_start();
require 'db.php';
require 'function.php';
// logActivity($conn, $user_email, "expenditure history adding");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user'];
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

$query = "SELECT category, SUM(amount) as total_amount FROM expenditures WHERE user_email = ?";

switch ($filter) {
    case 'daily':
        $query .= " AND DATE(date) = CURDATE()";
        break;
    case 'weekly':
        $query .= " AND YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'monthly':
        $query .= " AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
        break;
    case 'yearly':
        $query .= " AND YEAR(date) = YEAR(CURDATE())";
        break;
}

if ($category_filter) {
    $query .= " AND category = '" . $category_filter . "'";
}
if ($date_filter) {
    $query .= " AND DATE(date) = '" . $date_filter . "'";
}

$query .= " GROUP BY category";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$expenditures = $result->fetch_all(MYSQLI_ASSOC);

$stmt = $conn->prepare("SELECT SUM(CASE WHEN category='income' THEN amount ELSE 0 END) AS total_income,
       SUM(CASE WHEN category='expense' THEN amount ELSE 0 END) AS total_expense
FROM expenditures WHERE user_email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();
$total_income = $summary['total_income'] ?? 0;
$total_expense = $summary['total_expense'] ?? 0;
$total_balance = $total_income - $total_expense;

$categories = [];
$amounts = [];
foreach ($expenditures as $row) {
    $categories[] = $row['category'];
    $amounts[] = $row['total_amount'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenditure History</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .summary div {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }

        .income {
            background: lightgreen;
            color: green;
        }

        .expense {
            background: lightcoral;
            color: red;
        }

        .filter-bar a {
            margin-right: 10px;
            text-decoration: none;
        }

        #expenseChart {
            max-width: 100%;
            height: 400px !important;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <h1>Expenditure History</h1>
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
        <div class="filter-bar">
            <a href="?filter=daily">Daily</a>
            <a href="?filter=weekly">Weekly</a>
            <a href="?filter=monthly">Monthly</a>
            <a href="?filter=yearly">Yearly</a>
        </div>
        <form method="get">
            <input type="date" name="date">
            <select name="category">
                <option value="">All Categories</option>
                <option value="food">Food</option>
                <option value="education">Education</option>
            </select>
            <button type="submit">Filter</button>
        </form>
        <div class="summary">
            <div class="income">Income: $<?php echo $total_income; ?></div>
            <div class="expense">Expenses: $<?php echo $total_expense; ?></div>
            <div>Total: $<?php echo $total_balance; ?></div>
        </div>
        <div style="width: 100%; max-width: 600px; margin: auto;">
            <label for="chartType">Choose Chart Type:</label>
            <select id="chartType">
                <option value="pie">Pie Chart</option>
                <option value="bar">Bar Chart</option>
                <option value="line">Line Chart</option>
            </select>
            <canvas id="expenseChart"></canvas>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const ctx = document.getElementById("expenseChart").getContext("2d");
                let chartType = "pie";

                const data = {
                    labels: <?php echo json_encode($categories); ?>,
                    datasets: [{
                        data: <?php echo json_encode($amounts); ?>,
                        backgroundColor: ["#ff6384", "#36a2eb", "#ffce56", "#4bc0c0"]
                    }]
                };

                const options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: "bottom"
                        }
                    }
                };

                let expenseChart = new Chart(ctx, {
                    type: chartType,
                    data: data,
                    options: options
                });

                document.getElementById("chartType").addEventListener("change", function() {
                    const newType = this.value;

                    expenseChart.destroy();
                    expenseChart = new Chart(ctx, {
                        type: newType,
                        data: data,
                        options: options
                    });
                });
            });
        </script>
    </div>
</body>

</html>