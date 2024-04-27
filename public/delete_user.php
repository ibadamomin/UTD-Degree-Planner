<?php

use DegreePlanner\Models\User\User;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

// Redirect if not logged in
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: login.php?error=Please login");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $net_id = htmlspecialchars(trim($_POST['net_id']));
    $password = $_POST['password'];

    if (empty($net_id) || empty($password)) {
        header("Location: profile.php?delete_error=Please enter password ");
        exit();
    }

    $msg = User::deleteUser($net_id, $password);
    if ($msg) {
        header("Location: logout.php");
    } else {
        header("Location: profile.php?delete_error=$msg");
    }
} else {
    header("Location: index.php");
}
exit();

?>

