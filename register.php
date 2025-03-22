<?php
session_start();
require 'db.php';
require 'vendor/autoload.php';
require 'function.php';
// logActivity($conn, $user_email, "registering details");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_otp'])) {
    $email = $_POST["email"];
    $_SESSION["otp"] = rand(100000, 999999);
    $_SESSION["email"] = $email;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'aishwaryats2004@gmail.com'; // Replace with your email
        $mail->Password = 'atul yvmb gskm uuip'; // Use an app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('aishwaryats2004@gmail.com', 'Verification');
        $mail->addAddress($email);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "Your OTP code is: " . $_SESSION["otp"];

        $mail->send();
        $_SESSION["otp_sent"] = true;
        header("Location: register.php?step=verify");
        exit();
    } catch (Exception $e) {
        echo "OTP email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_otp'])) {
    if ($_POST["otp"] == $_SESSION["otp"]) {
        $_SESSION["verified"] = true;
        header("Location: register.php?step=complete");
        exit();
    } else {
        echo "Invalid OTP. Please try again.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    if (!isset($_SESSION["verified"]) || $_SESSION["verified"] !== true) {
        die("Email not verified.");
    }

    $username = $_POST["username"];
    $phone_number = $_POST["phone"];
    $email = $_SESSION["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, phone_number, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $phone_number, $email, $password);


    if ($stmt->execute()) {
        unset($_SESSION["otp"], $_SESSION["verified"]);
        header("Location: login.php");
        exit();
    } else {
        echo "Registration failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            background-image: url(/images/login_page_img.jpeg);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }

        h1 {
            color: black;
            text-align: center;
            font-size: 32px;
            margin-bottom: 20px;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 400px;
        }

        input {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .button {
            width: 100%;
            background-color: blue;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }

        .button:hover {
            background-color: darkblue;
        }
    </style>
</head>

<body>
    <h1>Register</h1>
    <p style="color: red">please verify your email while enter into the register then with same email you have to uses for login purpose</p>
    <div class="register-container">
        <?php if (!isset($_GET['step'])) { ?>
            <form action="register.php" method="POST">
                <label>Email:</label>
                <input type="email" name="email" required>
                <button type="submit" name="send_otp" class="button">Send OTP</button>
            </form>
        <?php } elseif ($_GET['step'] == 'verify') { ?>
            <form action="register.php" method="POST">
                <label>Enter OTP:</label>
                <input type="text" name="otp" required>
                <button type="submit" name="verify_otp" class="button">Verify OTP</button>
            </form>
        <?php } elseif ($_GET['step'] == 'complete') { ?>
            <form action="register.php" method="POST">
                <label>Username:</label>
                <input type="text" name="username" required>
                <label>Phone Number:</label>
                <input type="text" name="phone" required>
                <label>Password:</label>
                <input type="password" name="password" required>
                <button type="submit" name="register" class="button">Register</button>
            </form>
        <?php } ?>
        <?php if (isset($_GET['error'])) echo "<p class='error'>" . htmlspecialchars($_GET['error']) . "</p>"; ?>
    </div>
</body>

</html>