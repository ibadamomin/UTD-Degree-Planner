<?php

use DegreePlanner\Models\User\User;
use DegreePlanner\Models\User\Student;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

// Redirect if not logged in
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: login.php?error=Please login");
    exit();
}

// Find user info
$user = User::findUserByNetId($_SESSION['user_id']);
if ($user instanceof Student) {
    $advisor = User::findUserByNetId($user->advisor);
    $advisorName = htmlspecialchars($advisor->getFirstLast());
}

$name = htmlspecialchars($user->getFullName());

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 0 20px 20px 20px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 999;
        }
    </style>

    <link rel="stylesheet" href="./css/global.css"/>
    <link rel="stylesheet" href="./css/advisor-dashboard.css"/>
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
<section id="header">
    <?php
    if ($user instanceof Student) {
        print "<a href=\"student-dashboard.php\">Student Dashboard</a>";

    } else {
        print "<a href=\"advisor-dashboard.php\">Advisor Dashboard</a>";
    }
    ?>
    <a href="logout.php">Logout</a>
</section>
<table>
    <tr>
        <td>
            Name:
        </td>
        <td><?php print htmlspecialchars($name); ?></td>
    </tr>
    <tr>
        <td>Email:</td>
        <td><?php print htmlspecialchars($user->email); ?></td>
    </tr>
    <tr>
        <td>Net ID:</td>
        <td><?php print strtoupper(htmlspecialchars($user->id)); ?></td>
    </tr>
    <tr>
        <td>Password:</td>
        <td>
            <form id="change_password" action="change_password.php" method="post">
                <table>
                    <tr>
                        <td><label for="current_password">Current Password:*</label></td>
                        <td><input type="password" id="current_password" name="current_password" required></td>
                    </tr>
                    <tr>
                        <td><label for="new_password">New Password:*</label></td>
                        <td><input type="password" id="new_password" name="new_password" required></td>
                    </tr>
                    <tr>
                        <td><label for="confirm_password">Confirm Password:*</label></td>
                        <td><input type="password" id="confirm_password" name="confirm_password" required></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <?php
                            print "<input type='hidden' name='net_id' value='" . $_SESSION['user_id'] . "'>"
                            ?>
                            <input type="submit" id="change_password" value="Change Password">
                            <?php
                            if (isset($_GET["pw_err"])) {
                                print "<div style=\"color: red;\">" . htmlspecialchars($_GET["pw_err"]) . "</div>";
                            } elseif (isset($_GET["pw_msg"])) {
                                print "<div style=\"color: green\">" . htmlspecialchars($_GET["pw_msg"]) . "</div>";
                            }
                            ?>
                        </td>
                    </tr>
                </table>

            </form>
        </td>
    </tr>
    <tr>
        <td>Role</td>
        <td><?php print htmlspecialchars(ucwords($user->role)); ?></td>
    </tr>
    <?php
    if ($user instanceof Student) {
        $majors = "";
        foreach ($user->majors as $major) {
            $majors = $majors . htmlspecialchars($major->toString()) . "<br>";
        }
        $student = <<<EOT
        <tr>
            <td>Majors:</td>
            <td>$majors</td>
        </tr>
        <tr>
        <td>Advisor: </td>
        <td>$advisorName</td>
        </tr>
        EOT;
        print $student;
    }
    ?>

</table>


<button id="delete_button" type="button">Delete Account</button>
<?php
if (isset($_GET["delete_error"])) {
    print "<div style=\"color: red;\">" . htmlspecialchars($_GET["delete_error"]) . "</div>";
}
?>

<div id="confirm_delete" class="popup">
    <div style="text-align: right; font-size: 24px;" id="cancel">×</div>
    <form id="delete_user" action="delete_user.php" method="post">
        <h2>Confirm Deletion</h2>
        <p>This will permanently remove all of your data. If you wish to re-use <br>UTD Degree Planner, you'll need
            register again later on.</p>
        <?php
        print "<input type='hidden' name='net_id' value='" . $_SESSION['user_id'] . "'>"
        ?>
        <label for="password">Confirm your password:*</label><br>
        <input type="password" id="password" name="password" required>
        <input type="submit" id="submit" value="Delete My Account">
    </form>

</div>

<script>
    document.getElementById('delete_button').addEventListener('click', function () {
        document.getElementById('confirm_delete').style.display = 'block';
    });

    document.getElementById('cancel').addEventListener('click', function () {
        document.getElementById('confirm_delete').style.display = 'none';
    });
</script>

</body>
</html>

