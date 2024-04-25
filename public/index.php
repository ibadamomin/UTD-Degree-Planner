<?php
require_once __DIR__ . '/../vendor/autoload.php';

session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head><title>Index</title></head>

<body>
<?php

if (isset($_SESSION["user_id"], $_SESSION["role"])) {
    if ($_SESSION["role"] == 'student') {
        header("Location: student-dashboard.php");
    } else {
        header("Location: advisor-dashboard.php");
    }
    exit();

}
?>
<a href="login.php">Login</a>
<a href="register.php">Register</a>
<a href="logout.php">Logout</a>
<a href="student-dashboard.php">Student Dashboard</a>

</body>

</html>
