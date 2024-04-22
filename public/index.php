<?php
require_once __DIR__ . '/../vendor/autoload.php';

session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head><title>Index</title></head>

<body>
<?php
if (isset($_SESSION['user_id'])) {
    print "<p>Welcome " . htmlspecialchars($_SESSION['user_id']) . "</p>";
}
?>
<a href="login.php">Login</a>
<a href="register.php">Register</a>
<a href="logout.php">Logout</a>

</body>

</html>
