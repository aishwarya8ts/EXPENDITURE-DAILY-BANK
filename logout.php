<?php
session_start();
session_destroy();
setcookie("user_email", "", time() - 3600, "/"); // Remove cookie
header("Location: index.html");
exit();
