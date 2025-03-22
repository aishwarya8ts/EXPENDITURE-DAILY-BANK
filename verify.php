
<?php
require 'db.php';

if (isset($_GET['code'])) {
    $verification_code = $_GET['code'];

    // Check if verification code exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE verification_code = ?");
    $stmt->bind_param("s", $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Update email_verified status
        $stmt = $conn->prepare("UPDATE users SET email_verified = TRUE WHERE verification_code = ?");
        $stmt->bind_param("s", $verification_code);
        $stmt->execute();

        header("Location: login.php?message=Email verified! You can now log in.");
        exit();
    } else {
        echo "Invalid verification code.";
    }
}
?>
