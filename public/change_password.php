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
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($net_id) || empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        header("Location: profile.php?pw_err=Please enter password");
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        header("Location: profile.php?pw_err=Passwords don't match");
        exit();
    }

    $msg = User::changePassword($net_id, $currentPassword, $newPassword);
    if ($msg) {
        header("Location: profile.php?pw_msg=Success%21");

    } else {
        header("Location: profile.php?delete_error=$msg");
    }
} else {
    header("Location: index.php");
}
exit();
