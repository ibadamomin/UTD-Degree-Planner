<?php

use DegreePlanner\Models\User\Student;
use DegreePlanner\Models\User\User;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

// Redirect if not logged in
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: login.php?error=Please login");
    exit();
}

// Redirect if not student
if ($_SESSION["role"] == 'faculty') {
    header("Location: advisor-dashboard.php");
    exit();
}

if ($_SESSION["role"] != 'student') {
    header("Location: logout.php");
    exit();
}

// Find user info
$user = User::findUserByNetId($_SESSION['user_id']);
if (!($user instanceof Student)) {
    header("Location: login.php");
    exit();
}
$name = htmlspecialchars($user->getFullName());

// Get major info
$student_majors = $user->majors;

// Get advisor name
$advisor = User::findUserByNetId($user->advisor);
if ($advisor != null) {
    $advisor = htmlspecialchars($advisor->getFirstLast());
}

?>


<!DOCTYPE html>
<html lang="">
<head>
    <title>Student Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
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
                                                src="images/polygon-1.svg"
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
                                                    src="images/polygon-2.svg"
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
                                                    src="images/polygon-2.svg"
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
                        <canvas id="pie-chart"></canvas>
                        <script>
                            function parseCSV(csv) {
                                const lines = csv.split('\n');
                                const result = [];
                                const headers = lines[0].split(',');

                                for (let i = 1; i < lines.length; i++) {
                                    const obj = {};
                                    const currentLine = lines[i].split(',');

                                    for (let j = 0; j < headers.length; j++) {
                                        obj[headers[j]] = currentLine[j];
                                    }

                                    result.push(obj);
                                }

                                return result;
                            }

                            // Count occurrences of each course type
                            function countCourseTypes(data) {
                                const counts = {};

                                data.forEach(course => {
                                    const courseType = course.course_type;
                                    counts[courseType] = (counts[courseType] || 0) + 1;
                                });

                                return counts;
                            }

                            const csvData = `course_id,course_prefix,course_number,course_name,course_type,credits
                                        1,RHET,1302,Rhetoric,Core,3
                                        2,ECS,3390,Professional and Technical Communication,Core,3
                                        3,MATH,2417,Calculus I,Core,3
                                        4,PHYS,2325,Mechanics,Core,3
                                        5,PHYS,2326,Electromagnetism and Waves,Core,3
                                        6,GOVT,2305,American National Government,Core,3
                                        7,GOVT,2306,State and Local Government,Core,3
                                        8,MATH,2419,Calculus II,Core,4
                                        9,PHYS,2125,Physics Laboratary I,Core,3
                                        10,ECS,1100,Introduction to Engineering and Computer Science,Major Requirements,1
                                        11,CS,1200,Introduction to Computer Science and Software Engineering,Major Requirements,2
                                        12,CS,1136,Computer Science Laboratory,Major Requirements,1
                                        13,CS,1336,Programming Fundamentals,Major Requirements,1
                                        14,CS,1337,Computer Science I,Major Requirements,3
                                        15,CS,2305,Discrete Mathematics for Computing I,Major Requirements,3
                                        16,CS,2336,Computer Science II,Major Requirements,3
                                        17,CS,2340,Computer Architecture,Major Requirements,3
                                        18,MATH,2418,Linear Algebra,Major Requirements,3
                                        19,PHYS,2126,Physics Laboratory II,Major Requirements,1
                                        20,CS,3162,Professional Responsibility in Computer Science and Software Engineering,Major Requirements,3
                                        21,CS,3305,Discrete Mathematics for Computing II,Major Requirements,3
                                        22,CS,3341,Probability and Statistics in Computer Science and Software Engineering,Major Requirements,3
                                        23,CS,3345,Data Structures and Introduction to Algorithmic Analysis,Major Requirements,3
                                        24,CS,3354,Software Engineering,Major Requirements,3
                                        25,CS,3377,Systems Programming in UNIX and Other Environments,Major Requirements,3
                                        26,CS,3390,Professional and Technical Communication,Major Requirements,1
                                        27,CS,4141,Digital Systems Laboratory,Major Requirements,1
                                        28,CS,4337,Programming Language Paradigms,Major Requirements,3
                                        29,CS,4341,Digital Logic and Computer Design,Major Requirements,3
                                        30,CS,4347,Database Systems,Major Requirements,3
                                        31,CS,4348,Operating Systems Concepts,Major Requirements,3
                                        32,CS,4349,Advanced Algorithm Design and Analysis,Major Requirements,3
                                        33,CS,4384,Automata Theory,Major Requirements,3
                                        34,CS,4485,Computer Science Project,Major Requirements,4
                                        35,CS,4352,Human-Computer Interaction I,Major Requirements,3
                                        36,CS,4353,Human-Computer Interaction II,Major Requirements,3`;

                            const courses = parseCSV(csvData);
                            const counts = countCourseTypes(courses);

                            const labels = Object.keys(counts);
                            const data = Object.values(counts);

                            const ctx = document.getElementById('pie-chart').getContext('2d');

                            new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        data: data,
                                        backgroundColor: [
                                            '#FF6384',
                                            '#36A2EB',
                                        ]
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    legend: {
                                        position: 'right',
                                    }
                                }
                            });
                        </script>
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