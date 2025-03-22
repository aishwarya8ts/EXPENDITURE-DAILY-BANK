<?php
session_start();
require 'db.php';
require 'function.php';
// logActivity($conn, $user_email, "activity details");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM activity_log ORDER BY timestamp DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Activity Log</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            color: black;
            /* Ensure text is black */
        }

        th {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <h1>Website Activity Log</h1>
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
    <table>
        <tr>
            <th>User</th>
            <th>Action</th>
            <th>Details</th>
            <th>Timestamp</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                <td><?php echo htmlspecialchars($row['action']); ?></td>
                <td><?php echo htmlspecialchars($row['details']); ?></td>
                <td><?php echo $row['timestamp']; ?></td>
            </tr>

        <?php endwhile; ?>
    </table>
</body>

</html>