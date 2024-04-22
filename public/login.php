<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DegreePlanner\Models\User\User;

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $net_id = htmlspecialchars(trim($_POST['net_id']));
    $password = $_POST['password'];

    if (empty($net_id) || empty($password)) {
        header("Location: login.php?error=Invalid username or password");
        exit();
    }
    $user = User::login($net_id, $password);
    if (!$user) {
        header("Location: login.php?error=Invalid username or password");
        exit();
    }
    var_dump($user);
    $_SESSION['user_id'] = htmlspecialchars($user->id);
    header("Location: index.php");
    exit();

}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<form id="login" action="login.php" method="POST">
    <label for="net_id">Net ID: </label>
    <input type="text" id="net_id" name="net_id" required>*<br>

    <label for="password">Password: </label>
    <input type="password" id="password" name="password" required>*<br>

    <button type="submit" value="Submit">Submit</button>

</form>
<?php
if (isset($_GET['error'])) {
    echo "<p style='color: red;'>" . $_GET['error'] . "</php>";
}
?>
</body>
</html>

