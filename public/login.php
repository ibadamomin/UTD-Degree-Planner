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

    $_SESSION['user_id'] = htmlspecialchars($user->id);
    $_SESSION['role'] = htmlspecialchars($user->role);
    header("Location: index.php");
    exit();

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1, width=device-width" />

    <link rel="stylesheet" href="../frontEnd/global.css" />
    <link rel="stylesheet" href="../frontEnd/index.css" />
    <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600;700&display=swap"
    />
    <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Proxima Nova:wght@400&display=swap"
    />
</head>
<body>
<div class="login-screen-dark">
    <form class="frame-form" id="login" action="login.php" method="POST">
        <div class="login-wrapper">
            <div class="welcome-back-parent">
                <h1 class="welcome-back">Welcome back!</h1>
                <div class="enter-your-utd">Enter your UTD Net ID and password</div>
            </div>
            <div class="input-wrapper">
                <input class="net-id" id="net_id" name="net_id" placeholder="Net ID" type="text" required/>
            </div>
            <div class="input-wrapper">
                <input class="password" id="password" name="password" placeholder="Password" type="password" required/>
            </div>
        </div>
        <div class="button-wrapper">
            <button class="button" type="submit" value="submit"> Log in </button>
        </div>
        <div class="button-wrapper">
            <a href="register.php" class="button">Register</a>
        </div>
        <div style="color: red;">
        <?php
        if (isset($_GET['error'])) {
            echo $_GET['error'];
        }
        ?>
        </div>
    </form>

</div>
</body>
</html>
