<?php
session_start();
include 'connect.php';
include 'font.php';
include("../block.php");

$ID = $_GET['quizID'];

$data = array(
    "ID" => $ID,
    "form" => "",
    "subject" => "",
    "title" => "",
    "description" => "",
    "question" => [],
    "type" => [],
    "answer" => [],
    "correct" => []
);

//obtain quiz details
$QuizQuery = "SELECT * FROM quizzes WHERE quiz_id = $ID";
$QuizSQL = mysqli_query($conn, $QuizQuery);

$array = mysqli_fetch_assoc($QuizSQL);


$instructorID = $array['instructor_id'];
$sqlQ2 = "SELECT * FROM instructors WHERE instructor_id = '$instructorID';";
$result2 = mysqli_query($conn, $sqlQ2);
$instructorDetails = mysqli_fetch_array($result2);
//obtain questions & answer & correct
$QuestionQuery =
    "SELECT question_id, question, question_style FROM questions 
    INNER JOIN question_styles ON question_styles.question_style_id = questions.question_style_id AND quiz_id = $ID 
    ORDER BY question_id ASC";
$QuestionSQL = mysqli_query($conn, $QuestionQuery);

$QuestionID = [];

if (mysqli_num_rows($QuestionSQL) > 0) {
    while ($row = mysqli_fetch_assoc($QuestionSQL)) {
        array_push($data['question'], $row['question']);
        array_push($data['type'], $row['question_style']);
        array_push($QuestionID, $row['question_id']);
    }
}

foreach ($QuestionID as $id) {
    $AnswerQuery = "SELECT answer_list, correct_answer FROM question_answers 
        WHERE question_id = $id ORDER BY answer_id ASC";
    $AnswerSQL = mysqli_query($conn, $AnswerQuery);
    if (mysqli_num_rows($AnswerSQL) > 0) {
        while ($row = mysqli_fetch_assoc($AnswerSQL)) {
            array_push($data['answer'], explode(",", $row['answer_list']));
            array_push($data['correct'], explode(",", $row['correct_answer']));
        }
    }
}

$totalQuestion = $array['quiz_total_questions'];
$data['form'] = $array['quiz_level'];
$data['subject'] = $array['quiz_subject'];
$data['title'] = $array['quiz_title'];
$data['description'] = $array['quiz_description'];

$StudentsQuery =
    "SELECT score, date_of_attempt, student_name FROM attempts 
    INNER JOIN students ON attempts.student_id = students.student_id AND quiz_id = $ID
    ORDER BY date_of_attempt DESC";
$StudentsSQL = mysqli_query($conn, $StudentsQuery);

$StudentPassData = [];
$StudentFailData = [];
$scorePercent = [];
$totalUser = mysqli_num_rows($StudentsSQL);

if (mysqli_num_rows($StudentsSQL) > 0) {
    while ($row = mysqli_fetch_assoc($StudentsSQL)) {
        $info = array(
            "name" => $row['student_name'],
            "score" => $row['score'],
            "date" => ""
        );

        $temp = round((($row['score'] / $totalQuestion) * 100), 0);
        array_push($scorePercent, $temp);

        $originalDate = $row['date_of_attempt'];
        $newDate = date("d/m/Y", strtotime($originalDate));
        $info['date'] = $newDate;

        if ($info['score'] > round(($totalQuestion / 2), 0)) {
            array_push($StudentPassData, $info);
        } else {
            array_push($StudentFailData, $info);
        }
    }
}


$FeedbackQuery =
    "SELECT feedback, date_made, student_name FROM quiz_feedbacks
    INNER JOIN students ON quiz_feedbacks.student_id = students.student_id AND quiz_id = $ID
    ORDER BY date_made DESC";
$FeedbackSQL = mysqli_query($conn, $FeedbackQuery);

$FeedbackData = [];

if (mysqli_num_rows($FeedbackSQL) > 0) {
    while ($row = mysqli_fetch_assoc($FeedbackSQL)) {
        $info = array(
            "name" => $row['student_name'],
            "feedback" => $row['feedback'],
            "date" => ""
        );

        $originalDate = $row['date_made'];
        $newDate = date("d/m/Y", strtotime($originalDate));
        $info['date'] = $newDate;

        array_push($FeedbackData, $info);
    }
}

// find percentage
$background;
var_dump($scorePercent);
$total = array_sum($scorePercent);
if ($totalUser == 0) {
    $percentage = 0;
} else {
    $percentage = round(($total / $totalUser), 0);
}

if ($percentage >= 50) {
    $background = "light-green";
} else {
    $background = "light-red";
}
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
    <link rel="stylesheet" href="ADMIN_CSS/adminSelectedView.css">
    <link rel="stylesheet" href="ADMIN_CSS/adminLearningAndQuizSelect.css">
    <link rel="stylesheet" href="ADMIN_CSS/colour.css">
    <link rel="stylesheet" type="text/css" href="adminPrint.css" media="print">


    <script>
        function back() {
            window.history.back();
        }

        function deleteMaterial() {
            if (confirm("Are you sure to delete this learning material?") == true) {
                let materialID = "<?php echo $ID ?>";
                let deletion = {
                    "matID": materialID,
                    "deleteType": "learningMaterial"
                }
                fetch('deleteLearningAndQuiz.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(deletion)
                })
                    .then(response => response.text())
                    .then(after => {
                        window.location.href = "adminLearningAndQuizView.php";
                    }
                    )
            }

        }
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
    <br>
    <main>
        <other_stuff class="userInfo">
            <ul class="titleOfTable no-print">
                <li id="backButton" onclick="back()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                        class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                    </svg>
                </li>
                <li><a><?php echo strtoupper($array['quiz_title']) ?></a></li>
                <li id="printButtonBox"><button id="printButton" onclick="window.print()">PRINT</button></li>
                <li id="deleteButtonBox"><button id="deleteButton" onclick="deleteMaterial()">DELETE</button></li>
            </ul>
            <div class="mainInfoArea">
                <div class="infoSection row">
                    <div class="userProfileNDetail col-2">
                        <div id="userIconAndName">
                            <p>CREATOR</p>
                            <img src="profile.png" alt="profile" onclick="profilePage()">
                            <p><?php echo $instructorDetails['instructor_name'] ?></p>
                        </div>

                        <div id="userSmallDetail">
                            <ul>
                                <li id="emailLabel"><a>EMAIL</a></li>
                                <li id="userEmail"><a><?php echo $instructorDetails['instructor_email'] ?></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-8 print-page-break itemStats" id="summary" style="width: 59em;">
                        <div class="title" style="border-radius: 0;">
                            <h1>
                                OVERALL ACCURACY
                            </h1>
                        </div>
                        <div id="percentage">
                            <div id="progress-bar">
                                <div id="indicator" style="width: <?php echo $percentage ?>%;">
                                    <span
                                        style="background-color: var(--<?php echo $background ?>); color: var(--dark-grey);"><?php echo $percentage ?>%</span>
                                </div>
                            </div>
                        </div>
                        <div id="quiz-detail">
                            <div class="category" style="background-color: var(--<?php echo $background ?>);">
                                <div class="cat-title">
                                    <h1>
                                        SUCCESS RATE
                                    </h1>
                                </div>
                                <p><?php echo $percentage ?>%</p>
                            </div>
                            <div class="category">
                                <div class="cat-title" style="background-color: var(--grey);">
                                    <h1>
                                        TOTAL STUDENTS
                                    </h1>
                                </div>
                                <p style="background-color: var(--grey);"><?php echo $totalUser ?></p>
                                <p style="font-size: 1.5vw; background-color: var(--grey);">STUDENTS</p>
                            </div>
                            <div class="category">
                                <div class="cat-title" style="background-color: var(--grey);">
                                    <h1>
                                        TOTAL QUESTIONS
                                    </h1>
                                </div>
                                <p style="background-color: var(--grey);"><?php echo $totalQuestion ?></p>
                                <p style="font-size: 1.5vw; background-color: var(--grey);">QUESTIONS</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="header print-page-break">
                    <div class="button" onclick="SetContent()"><span>CONTENT</span></div>
                    <div class="button" onclick="SetUser()"><span>USER LIST</span></div>
                    <div class="button" onclick="SetFeedback()"><span>FEEDBACK</span></div>
                    <div id="filter">
                        <select name="status" id="status">
                            <option value="all" selected>ALL</option>
                            <option value="pass">PASS</option>
                            <option value="fail">FAIL</option>
                        </select>
                        <select name="time" id="time">
                            <option value="latest" selected>LATEST</option>
                            <option value="oldest">OLDEST</option>
                        </select>
                    </div>
                </div>
                <div id="information">

                </div>
    </main>


    </other_stuff>
    </main>
    <script>
        let Quiz = <?php echo json_encode($data) ?>;
        let StudentPass = <?php echo json_encode($StudentPassData) ?>;
        let StudentFail = <?php echo json_encode($StudentFailData) ?>;
        let Feedback = <?php echo json_encode($FeedbackData) ?>;
        let TotalQuestion = <?php echo $totalQuestion ?>;
    </script>
    <script src="ADMIN_JS/adminQuizSelectedInfo.js"></script>
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>