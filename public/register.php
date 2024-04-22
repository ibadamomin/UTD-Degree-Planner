<?php
require_once __DIR__ . '/../vendor/autoload.php';

use DegreePlanner\Models\User\User;

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
    <title>Login</title>
</head>
<body>
<form id="register" action="register.php" method="POST">
    <label for="net_id">Net ID: </label>
    <input type="text" id="net_id" name="net_id" required>*<br>

    <label for="email">Email: </label>
    <input type="email" id="email" name="email" required>*<br>

    <label for="password">Password: </label>
    <input type="password" id="password" name="password" required>*<br>

    <label for="first_name">First Name: </label>
    <input type="text" id="first_name" name="first_name" required>*<br>

    <label for="middle_name">Middle Name: </label>
    <input type="text" id="middle_name" name="middle_name"><br>

    <label for="last_name">Last Name: </label>
    <input type="text" id="last_name" name="last_name" required>*<br>

    <label for="role">Role: </label>
    <select name="role" id="role" required>
        <option value="student">Student</option>
        <option value="faculty">Faculty</option>
    </select>*<br>

    <button type="submit" value="Submit">Submit</button>

</form>
<?php
if (isset($_GET['error'])) {
    echo "<p style='color: red;'>" . htmlspecialchars($_GET['error']) . "</php>";
}
?>
</body>
</html>
