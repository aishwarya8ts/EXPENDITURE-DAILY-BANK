<?php
session_start();
require 'db.php';
require 'function.php';
// logActivity($conn, $user_email, "Home");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenditure Daily - Home</title>
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-image: url(/images/develpoment.jpeg);
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            /* Ensures the image covers the entire screen */
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            /* background-color: #f4f4f4; */
        }

        img {
            width: 60px;
            vertical-align: middle;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 15px;
            color: white;
        }

        .navbar h1 {
            margin: 0;
            font-size: 2em;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }

        .navbar .nav-links {
            display: flex;
            align-items: center;
        }

        .container {
            text-align: center;
            padding: 40px;
        }

        .swiper {
            width: 100%;
            height: 650px;
        }

        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .footer {
            background: #222;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 30px;
        }

        /* New section for three divs in a row */
        .three-sections {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            padding: 20px;
            border-radius: 20px;
            height: 200px;

        }

        .section {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 30%;
        }

        .section h3 {
            font-size: 1.5em;
            margin-bottom: 15px;
        }

        .feedback-form {
            background-color: #fff;
            padding: 20px;
            margin-top: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            display: none;
            /* Hidden by default */
        }

        .feedback-form input,
        .feedback-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .feedback-form button {
            padding: 10px 20px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .feedback-form button:hover {
            background-color: #555;
        }

        .show-feedback-button {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .show-feedback-button:hover {
            background-color: #555;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="logo">
            <img src="/images/LOGO.png" alt="Logo">
            <h1>BANK</h1>
        </div>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="account.php">Accounts</a>
            <a href="expenditure.php">Expenditure</a>
            <a href="expenditure_history.php">Analysis</a>
            <a href="favorites.php"><span style="color: gold;">★</span> Favorites</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>



    <div class="swiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="/images/home1.jpg" alt="Track Expenses"></div>
            <div class="swiper-slide"><img src="/images/home2.jpg" alt="Analyze Spending"></div>
            <div class="swiper-slide"><img src="/images/home3.jpg" alt="Set Financial Goals"></div>
        </div>
        <div class="swiper-pagination"></div>
        <!-- Navigation arrows -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
    <div class="container">
        <h2>Manage Your Finances with Ease</h2>
        <p>Expenditure Daily helps you track your income and expenses effortlessly. Categorize your spending, view trends with charts, and make informed financial decisions.</p>
    </div>

    <!-- Three sections explaining the benefits -->
    <div class="three-sections">
        <div class="section">
            <h3>Helps Younger Children</h3>
            <p>Expenditure Daily provides a simple way for younger children to learn about money management. It introduces them to budgeting, savings, and spending in a fun and interactive way. It's a great tool for building financial literacy from an early age.</p>
        </div>
        <div class="section">
            <h3>Why It's Needed Today</h3>
            <p>In today's world, managing personal finances is more important than ever. With the rise of online payments and digital banking, it's crucial for individuals to track their spending and savings. Expenditure Daily helps people stay on top of their finances, ensuring smarter financial decisions.</p>
        </div>
        <div class="section">
            <h3>How It Helps</h3>
            <p>Expenditure Daily offers real-time tracking of income and expenses, with the ability to categorize spending and set financial goals. The app’s easy-to-use interface and colorful charts make it enjoyable for both kids and adults to track their financial progress and make informed decisions.</p>
        </div>
    </div>

    <!-- Button to show the feedback form -->
    <div class="container">
        <button class="show-feedback-button" onclick="toggleFeedbackForm()">Leave Your Feedback</button>
    </div>

    <!-- Feedback Form -->
    <div class="feedback-form">
        <h2>Leave Your Feedback</h2>
        <form action="submit_feedback.php" method="post">
            <input type="text" name="name" placeholder="Your Name" required><br>
            <textarea name="feedback" placeholder="Your Feedback" rows="4" required></textarea><br>
            <button type="submit">Submit Feedback</button>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2025 Expenditure Daily | Contact: a_@gmail.com</p>
    </div>

    <script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper('.swiper', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev'
            }
        });

        function toggleFeedbackForm() {
            var feedbackForm = document.querySelector('.feedback-form');
            feedbackForm.style.display = (feedbackForm.style.display === 'none' || feedbackForm.style.display === '') ? 'block' : 'none';
        }
    </script>
</body>

</html>