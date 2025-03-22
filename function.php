<?php
require 'db.php'; // Ensure database connection

function logActivity($conn, $user_email, $action, $details = null)
{
    $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_email, $action, $details);
    $stmt->execute();
    $stmt->close();
}
