<?php

use DegreePlanner\Models\Major\Major;
use DegreePlanner\Models\User\Advisor;
use DegreePlanner\Models\User\User;

require_once __DIR__ . '/../vendor/autoload.php';

$advisor = Advisor::findUserByNetId('pqr456');
$student_users = Advisor::getStudents($advisor->id);

$advisor_majors = array();
foreach ($student_users as $student) {
    foreach ($student->majors as $major) {
        if (!in_array($major, $advisor_majors)) {
            $advisor_majors[] = $major;
        }
    }
}

session_start();

// Redirect if not logged in
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: login.php?error=Please login");
    exit();
}

// Redirect if not advisor
if ($_SESSION["role"] == 'student') {
    header("Location: student-dashboard.php");
    exit();
}

// Redirect if not student or advisor
if ($_SESSION["role"] != 'faculty') {
    header("Location: logout.php");
    exit();
}

$user = User::findUserByNetId($_SESSION["user_id"]);
$student_users = Advisor::getStudents($user->id);


?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Advisor Dashboard</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="initial-scale=1, width=device-width"/>

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
<div class="advisor-dashboard">
    <section class="advisor-dashboard-child"></section>
    <section class="advisor-dashboard-inner">
        <div class="student-directory-parent">
            <div class="student-directory">
                <div class="biomedical-engineering">
                    <h2 class="ecs-student-directory">ECS Student Directory</h2>
                    <div class="biomedical-engineering-inner">
                        <div class="e-c-s-parent">
                            <div class="e-c-s">
                                <div class="biomedical-engineering1">
                                    Biomedical Engineering
                                </div>
                            </div>
                            <div class="computer-engineering">Computer Engineering</div>
                        </div>
                    </div>
                </div>
                <div class="frame-parent">
                    <label>
                        <input class="frame-child" placeholder="search..." type="text"/>
                    </label>

                    <div class="computer-science-wrapper">
                        <div class="computer-science">Computer Science</div>
                    </div>
                </div>
            </div>
            <div class="frame-wrapper">
                <div class="frame-group">
                    <div class="frame-container">
                        <div class="frame-div">
                            <div class="electrical-engineering-parent">
                                <div class="electrical-engineering">
                                    Electrical Engineering
                                </div>
                                <div class="mechanical-engineering">
                                    Mechanical Engineering
                                </div>
                                <div class="software-engineering">Software Engineering</div>
                            </div>
                        </div>
                    </div>
                    <?php
                    foreach ($advisor_majors as $major) {
                        print "<p>" . htmlspecialchars($major->major_name) . "</p>";
                        foreach ($student_users as $student) {
                            if ($student->hasMajor($major)) {
                                print "<p style=\"font-size: 12px;\">" . htmlspecialchars($student->getFullName()) . "</p>";
                            }
                        }
                    }
                    ?>
                    <div class="computer-engineering-parent">
                        <h2 class="computer-engineering1">Computer Engineering</h2>
                        <div class="frame-wrapper1">
                            <div class="a-parent">
                                <h2 class="a">A</h2>
                                <div class="tim-allen">Tim Allen</div>
                            </div>
                        </div>
                    </div>
                    <div class="frame-wrapper2">
                        <div class="z-parent">
                            <h2 class="z">Z</h2>
                            <div class="tim-zllen">Tim Zllen</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</body>
</html>
