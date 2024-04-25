<?php

use DegreePlanner\Models\Major\Major;
use DegreePlanner\Models\User\User;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

// Redirect if not logged in
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: login.php?error=Please login");
    exit();
}

// Redirect if not student
if ($_SESSION["role"] != 'student') {
    header("Location: advisor-dashboard.php");
    exit();
}

// Find user info
$user = User::findUserByNetId($_SESSION['user_id']);
if ($user == null) {
    header("Location: login.php");
    exit();
}
$name = htmlspecialchars($user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name);

// Get major info
$student_majors = Major::getStudentMajors($user->id);

// Get advisor name
$advisor = User::findUserByNetId($user->advisor);
if ($advisor != null) {
    $advisor = htmlspecialchars($advisor->first_name . ' ' . $advisor->last_name);
}

?>


<!DOCTYPE html>
<html lang="">
<head>
    <title>Student Dashboard</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="initial-scale=1, width=device-width"/>

    <link rel="stylesheet" href="./css/global.css"/>
    <link rel="stylesheet" href="./css/student-dashboard.css"/>
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

<div class="student-dashboard">
    <div class="student-dashboard-header">
        <a href="logout.php">Logout</a>

    </div>
    <main class="student-dashboard-inner">
        <section class="dashboard-section">
            <div class="dashboard-frame">
                <div class="user-info">
                    <div class="user-rectangle">
                        <div class="user-info-text">
                            <div class="user-name-class">
                                <!-- Change this however @frontend-->
                                <?php print $name . " - " . strtoupper($user->id) ?>
                            </div>
                            <div class="user-major">
                                <?php
                                foreach ($student_majors as $major) {
                                    print "<p>" . $major->degree_type . " - " . $major->major_name . "</p>";
                                }

                                if ($advisor != null) {
                                    print "<p>Advisor: " . $advisor . "</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="degree-progess">
                    <div class="degree-progess-rectangle">
                        <div class="degree-progress-parent">
                            <div class="degree-progress-text">Degree Progress</div>
                        </div>
                        <div class="degree-categories">
                            <div class="core-curriculum">
                                <div class="core-curriculum-parent">
                                    <div class="core-curriculum-text">Core Curriculum</div>
                                    <div class="polygon-frame">
                                        <img
                                                class="frame-child19"
                                                alt=""
                                                src="./public/polygon-1.svg"
                                        />
                                    </div>
                                </div>
                                <div class="comm-parent">
                                    <div class="comm">Comm.</div>
                                    <div class="rectangle-parent3">
                                        <div class="frame-child21"></div>
                                        <div class="math-parent">
                                            <div class="math">Math</div>
                                            <div class="frame-child22"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="major-requirements">
                                <div class="frame-parent11">
                                    <div class="major-requirements-parent">
                                        <div class="major-requirements-text">Major Requirements</div>
                                        <div class="polygon-wrapper1">
                                            <img
                                                    class="frame-child23"
                                                    loading="lazy"
                                                    alt=""
                                                    src="./public/polygon-2.svg"
                                            />
                                        </div>
                                    </div>
                                    <div class="rectangle-parent4">
                                        <div class="frame-child25"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="free-electives">
                                <div class="frame-parent12">
                                    <div class="free-electives-parent">
                                        <div class="free-electives-text">Free Electives</div>
                                        <div class="polygon-wrapper2">
                                            <img
                                                    class="frame-child26"
                                                    loading="lazy"
                                                    alt=""
                                                    src="./public/polygon-2.svg"
                                            />
                                        </div>
                                    </div>
                                    <div class="rectangle-parent5">
                                        <div class="frame-child28"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="available-classes">
                    <div class="rectangle-parent6">
                        <div class="frame-wrapper7">
                            <div class="ellipse-parent">
                                <div class="frame-child30"></div>
                                <div class="ellipse-group">
                                    <div class="frame-child31"></div>
                                    <div class="frame-child32"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="frame-wrapper8">
                        <div class="core-curriculum-group">
                            <div class="core-curriculum1">Core Curriculum</div>
                            <div class="major-requirements1">Major Requirements</div>
                            <div class="free-electives1">Free Electives</div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>

<!-- shows student name - major
courses taken
courses need to take -->