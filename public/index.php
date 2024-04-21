<?php
require_once __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

session_start();

//$user = User::register("abc123");
//print 'a';
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
<a href="logout.php">Logout</a>

</body>

</html>
