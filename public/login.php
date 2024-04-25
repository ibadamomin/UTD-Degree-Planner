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


<!-- <!DOCTYPE html>
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
</html> -->

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1, width=device-width" />

    <link rel="stylesheet" href="./global.css" />
    <link rel="stylesheet" href="./index.css" />
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
          <!-- <div class="user-wrapper">
            <div class="users">
              <div class="student">Student</div>
              <div class="student">Advisor</div>
            </div>
          </div> -->
          <div class="input-wrapper">
            <input class="net-id" id="net_id" placeholder="Net ID" type="text" required/>
          </div>
          <div class="input-wrapper">
            <input class="password" id="password" placeholder="Password" type="text" required/>
          </div>
        </div>
        <div class="button-wrapper">
          <button class="button" type="submit" value="submit"> Log in </button>
        </div>
      </form>
    </div>
  </body>
</html>
