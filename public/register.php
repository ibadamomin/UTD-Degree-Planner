<?php
require_once __DIR__ . '/../vendor/autoload.php';

use DegreePlanner\Models\User\User;
//$net_id = '123';
//$email = 'test@test.com';
//$password = '123';
//$first_name = 'first';
//$middle_name = 'mid';
//$last_name = 'las';
//$role = 'student';
//$result = User::register($net_id, $email, $password, $first_name, $middle_name, $last_name, $role);

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $net_id = htmlspecialchars(trim($_POST['net_id']));
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $middle_name = htmlspecialchars(trim($_POST['middle_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $role = ($_POST['role'] === 'faculty') ? 'faculty' : 'student';


    // Make sure the required fields aren't empty.
    if (empty($net_id) || empty($email) || empty($password) || empty($first_name) || empty($last_name)) {
        header("Location: register.php?error=Invalid username or password");
        exit();
    }

    $result = User::register($net_id, $email, $password, $first_name, $middle_name, $last_name, $role);
    if ($result === true) {
        $_SESSION['user_id'] = $net_id;
        $_SESSION['role'] = $role;
    } else {
        print $result;
        header("Location: register.php?error=$result");
        exit();
    }

    // Redirect user
    header("Location: index.php");
    exit();
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="initial-scale=1, width=device-width"/>

    <link rel="stylesheet" href="../frontEnd/global.css"/>
    <link rel="stylesheet" href="../frontEnd/register.css"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600;700&display=swap"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Proxima Nova:wght@400&display=swap"/>
</head>

<body>
<div class="login-screen-dark">
    <form class="frame-form" id="register" action="register.php" method="POST">
        <div class="welcome-back-parent">
            <h1 class="welcome-back">Get Started</h1>
            <div class="enter-your-utd">* required</div>
        </div>

        <label class="label-text" for="net_id">Net ID</label>
        <div class="input-wrapper">
            <input class="input-text" type="text" id="net_id" name="net_id" placeholder="Net ID*"/><br/>
        </div>

        <label class="label-text" for="email">Email: </label>
        <div class="input-wrapper">
            <input class="input-text" type="email" id="email" name="email" placeholder="Email*" required/><br/>
        </div>

        <label class="label-text" for="password">Password: </label>
        <div class="input-wrapper">
            <input class="input-text" type="password" id="password" name="password" placeholder="Password*"/><br/>
        </div>

        <label class="label-text" for="first_name">First Name: </label>
        <div class="input-wrapper">
            <input class="input-text" type="text" id="first_name" name="first_name" placeholder="First Name*" required/><br/>
        </div>

        <label class="label-text" for="middle_name">Middle Name: </label>
        <div class="input-wrapper">
            <input class="input-text" type="text" id="middle_name" name="middle_name" placeholder="Middle Name"/><br/>
        </div>

        <label class="label-text" for="last_name">Last Name: </label>
        <div class="input-wrapper">
            <input class="input-text" type="text" id="last_name" name="last_name" placeholder="Last Name*"
                   required/><br/>
        </div>

        <label class="label-text" for="role">Role: </label>
        <div class="role-wrapper">
            <select class="select-role" name="role" id="role" required>
                <option value="" disabled selected>Role*</option>
                <option value="student">Student</option>
                <option value="faculty">Faculty</option>
            </select><br/>
        </div>
        <div id="studentOptions" style="display: none;">
            <!-- Additional options for students -->
            <label class="label-text" for="degree_selection">Degree:</label>
            <div class="role-wrapper">
                <select class="select-role" name="degree_selection" id="degree_selection" required>
                    <option value="Bachelor of Science">Bachelor of Science</option>
                </select><br/>
            </div>
            <label class="label-text" for="degree_major">Major:</label>
            <div class="role-wrapper">
                <select class="select-role" name="degree_major" id="degree_major" required>
                    <option value="Computer Science">Computer Science</option>
                </select>
            </div>

            <script>
                document.getElementById("role").addEventListener("change", function () {
                    var role = this.value;
                    var studentOptionsDiv = document.getElementById("studentOptions");

                    if (role === "student") {
                        studentOptionsDiv.style.display = "block";
                    } else {
                        studentOptionsDiv.style.display = "none";
                    }
                });
            </script>
        </div>
        <div class="button-wrapper">
            <button class="button" type="submit" value="Submit">Submit</button>
        </div>
    </form>
    <?php
    if (isset($_GET['error'])) {
        echo "<p style='color: red;'>" . htmlspecialchars($_GET['error']) . "</php>";
    }
    ?>
</div>
</body>
</html>
