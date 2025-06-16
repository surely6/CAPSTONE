<?php
session_start();
include 'font.php';
include("../block.php");

?>


<!DOCTYPE html>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
    crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN</title>

    <link rel="stylesheet" href="ADMIN_CSS/adminHeader.css">
    <link rel="stylesheet" href="ADMIN_CSS/adminDashboard.css">
    <link rel="stylesheet" href="ADMIN_CSS/colour.css">
    <link rel="stylesheet" type="text/css" href="adminPrint.css" media="print">

    <script>
        function UserButton() {
            document.querySelector('.user').classList.toggle("active");
            subOption1 = document.getElementById("subOptions1");
            subOption2 = document.getElementById("subOptions2");

            if (subOption2.style.display == "block") {
                subOption2.style.display = "none";
            }
            else {
                if (subOption1.style.display == "block") {
                    subOption1.style.display = "none";
                    subOption2.style.display = "block";
                }
                subOption2.style.display = "block";
            }
        }

        function ProfileButton() {
            document.querySelector('.profileDrop').classList.toggle("active");
            subOption1 = document.getElementById("subOptions1");
            subOption2 = document.getElementById("subOptions2");

            if (subOption1.style.display == "block") {
                subOption1.style.display = "none";
            }
            else {
                if (subOption2.style.display == "block") {
                    document.querySelector('.user').classList.toggle("active");
                    subOption2.style.display = "none";
                    subOption1.style.display = "block";
                }
                subOption1.style.display = "block";
            }
        }

        function LogOut() {
            window.location.href = "logout.php";
        }
    </script>
</head>

<body>
    <header>
        <ul>
            <li id="logo"><a href="adminDashboard.php">ASSESTIFY</a></li>

            <li id="profile">
                <a><img src="profile.png" alt="profile" onclick="ProfileButton()" class="profileDrop"></a>
                <div id="subOptions1" class="subOptions" style="position: absolute;">
                    <a onclick="LogOut()">LOG OUT</a>
                </div>
            </li>


            <li class="options"><a href="adminSystemFeedbackView.php">SYSTEM<br>FEEDBACK</a></li>
            <li class="options"><a href="adminLearningAndQuizView.php">LEARNING<br>MATERIALS</a></li>
            <li class="options">
                <a href="#" onclick="UserButton()" class="user">USER &nbsp;
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-chevron-down" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708" />
                    </svg>
                </a>
                <div id="subOptions2" class="subOptions" style="position: absolute;">
                    <a href="adminStudentUserView.php">STUDENT</a>
                    <a href="adminInstructorUserView.php">INSTRUCTOR</a>
                    <a href="adminPendInstUserView.php">PENDING INSTRUCTOR</a>
                </div>
            </li>
            <li class="options"><a href="adminDashboard.php">DASHBOARD</a></li>
        </ul>
    </header>

    <other_stuff class="SummaryInformation">
        <br>
        <div id="printButtonDiv">
            <button id="printButton" onclick="window.print()">PRINT</button>
        </div>
        <div class="StudentInfo">
            <b>STUDENT</b>
            <div id="StudentSummary">
                <div id="StudentButton">
                    <div class="button buttons1"
                        onclick="getGraphData('studentLearningStyle'); setButton('.buttons1 span', 0)"><span>LEARNING
                            STYLE</span></div>
                    <div class="button buttons1" onclick="getGraphData('studentForm'); setButton('.buttons1 span', 1)">
                        <span>FORM</span>
                    </div>
                </div>


                <div class="chartWithLegend" id="StudentDataNLegend">
                    <div id="StudentData">
                        <canvas id="StudentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <br>
        <div class="ModuleInfo print-page-break">
            <b>LEARNING MATERIAL</b>
            <div id="ModuleSummary">
                <div id="ModuleButton">
                    <div class="button buttons2"
                        onclick="getGraphData('moduleLearningStyle'); setButton('.buttons2 span', 0)"><span>LEARNING
                            STYLE</span></div>
                    <div class="button buttons2" onclick="getGraphData('moduleForm'); setButton('.buttons2 span', 1)">
                        <span>FORM</span>
                    </div>
                    <div class="button buttons2"
                        onclick="getGraphData('moduleSubject'); setButton('.buttons2 span', 2)"><span>SUBJECT</span>
                    </div>
                </div>

                <div class="chartWithLegend" id="ModuleDataNLegend">
                    <div id="ModuleData">
                        <canvas id="ModuleChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <br>
        <div class="QuizInfo print-page-break">
            <b>QUIZ</b>
            <div id="QuizSummary" style="margin-bottom: 50px;">
                <div id="QuizButton">
                    <div class="button buttons3" onclick="getGraphData('quizForm'); setButton('.buttons3 span', 0)">
                        <span>FORM</span>
                    </div>
                    <div class="button buttons3" onclick="getGraphData('quizSubject'); setButton('.buttons3 span', 1)">
                        <span>SUBJECT</span>
                    </div>
                </div>

                <div class="chartWithLegend" id="QuizDataNLegend">
                    <div id="QuizData">
                        <canvas id="QuizChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </other_stuff>
    <?php include("../footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="ADMIN_JS/adminDashboardGraphs.js"></script>
    <script>
        window.onload = getGraphData('studentLearningStyle');
        window.onload = getGraphData('moduleLearningStyle');
        window.onload = getGraphData('quizForm');
        window.onload = setButton('.buttons1 span', 0);
        window.onload = setButton('.buttons2 span', 0);
        window.onload = setButton('.buttons3 span', 0);
    </script>
</body>

</html>