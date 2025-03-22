<?php
session_start();
require 'db.php';
require 'function.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user']; // Make sure the session variable is set

// logActivity($conn, $user_email, "User logged in");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if email exists and password is correct
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['email'];
        header("Location: home.php");
        exit();
    } else {
        header("Location: login.php?error=Incorrect email or password.");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EXPENDITURE DAILY</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <h1>BANKING EXPENDITURE</h1>
    <div class="div_main">

        <div class="div1">
            <div class="logarr">
                <h1>
                    WELCOME EXPENDITURE
                </h1>
                <img id="img" src="/images/LOGO.png" alt="logo here">
                <br>
                <a href="home.php" style="background-color: greenyellow; color:black">Home</a>
            </div>

            <div class="form1">
                <form action="login.php" method="POST">
                    <lable style="color: cornsilk;">Email :</lable>
                    <input type="email" name="email" placeholder="Enter Gmail" required><br>
                    <lable style="color: cornsilk;">Password :</lable>
                    <input type="password" name="password" placeholder="Enter Password" required><br>
                    <button type="submit" class="buttonl">Login</button>
                    <p class="error">
                        <?php if (isset($_GET['error'])) echo htmlspecialchars($_GET['error']); ?>
                    </p>
                    <p class="register-reminder">
                        Not registered yet? <a href="register.php">Create an account</a>
                    </p>

                </form>
            </div>
        </div>
        <div class="div2">
            <!-- <div class="sizeup"> -->
            <br>
            <br>
            <p id="para_log"><span style="color: blue;text-align: center;text-transform: capitalize; display: flex;justify-content: center;align-items: center;">"Expenditure Daily"</span> Your personal finance tracker, designed to help you manage and analyze your daily expenses efficiently. Unlike traditional online banking, our platform allows you to categorize and track your spending across multiple areas, including education, extracurricular activities, home essentials, and more.<br> With a user-friendly interface, you can log your expenditures, view real-time updates, and generate detailed graphs to visualize your spending habits.<br><br><span style="color: blue;text-align: center;text-transform: capitalize; display: flex;justify-content: center;align-items: center;">Our goal</span><br>
                To provide a seamless and interactive experience, making financial planning easier and more transparent. Sign up today to take control of your daily expenses and make smarter financial decisions.</p>
            <!-- </div> -->

        </div>
    </div>
</body>

</html>