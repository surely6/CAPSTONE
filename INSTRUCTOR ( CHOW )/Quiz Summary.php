<?php
session_start();

include('connect.php');
include('font.php');
include("../block.php");


$ID = $_COOKIE['ID'];

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
if ($total > 0) {
    $percentage = round(($total / $totalUser), 0);
} else {
    $percentage = 0;
}

if ($percentage >= 50) {
    $background = "light-green";
} else {
    $background = "light-red";
}
?>

<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <link href="./RESOURCES/CSS/header-format.css" rel="stylesheet">
    <link href="./RESOURCES/CSS/colors.css" rel="stylesheet">
    <link href="./RESOURCES/CSS/summary material.css" rel="stylesheet">
    <title>Summary Quiz</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <header>
        <div id="main-header">
            <ul>
                <li id="profileDropdown">
                    <img src="RESOURCES/profile.png" alt="Profile Icon" id="profileIcon"
                        style="width: 50px; height: 50px; cursor: pointer; align-items: center; display: flex; justify-content: center; margin-left: 10px; margin-top: 10px;position:relaive;">
                    <div id="profileMenu" class="dropdown-content" style="display: none;">
                        <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/profile.php">My Profile</a>
                        <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/system_feedback.php">Feedback</a>
                        <!-- <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/studentBookmark.php">History</a> -->
                        <a href="/capstone/logout.php">Logout</a>
                    </div>
                </li>
                <li class="options"><a href="Learning Material View.php">LEARNING MATERIAL</a></li>
                <li class="options"><a href="Quiz View.php">QUIZ</a></li>
                <li id="logo"><a href="Quiz View.php">ASSESTIFY</a></li>
            </ul>
        </div>
    </header>
    <main>
        <!-- main selection area -->
        <div class="title">
            <h1>
                <?php echo $data['title'] ?>
                <div>
                    <button style="background-color: var(--red);" onclick="DeleteQuiz(<?php echo $ID ?>)">
                        DELETE</button>
                    <button style="background-color: var(--blue);" onclick="EditQuiz(<?php echo $ID ?>)"> EDIT</button>
                    <button style="background-color: var(--blue);" onclick="window.print()"> PRINT</button>
                </div>
            </h1>
        </div>

        <div id="summary">
            <div class="title">
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
                    <div class="cat-title">
                        <h1>
                            TOTAL STUDENTS
                        </h1>
                    </div>
                    <p><?php echo $totalUser ?></p>
                    <p style="font-size: 1.5vw;">STUDENTS</p>
                </div>
                <div class="category">
                    <div class="cat-title">
                        <h1>
                            TOTAL QUESTIONS
                        </h1>
                    </div>
                    <p><?php echo $totalQuestion ?></p>
                    <p style="font-size: 1.5vw;">QUESTIONS</p>
                </div>
            </div>
        </div>

        <div class="header">
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
            <div id="content" style="display: block;">
                <div class="title">
                    <h1>SUBJECT: ENGLISH</h1>
                    <h1>FORM: 1</h1>
                </div>
                <div class="section">
                    <h1><span>TITLE:</span></h1>
                    <p id="title">LMAO</p>
                    <br>
                    <h1><span>DESCRIPTION:</span></h1>
                    <p id="description">THIS IS FUNNY</p>
                </div>
                <div id="part">
                    <h1>QUESTION 1<div style="float: right;">single answer</div>
                    </h1>
                    <div id="question">
                        <p>dick</p>
                    </div>
                    <div id="answer">
                        <div>among</div>
                        <div>us</div>
                        <div>red</div>
                        <div style="background-color: var(--green); color: var(--grey);">sus</div>
                    </div>
                </div>
            </div>
            <div id="users" style="display: none;">
                <table>
                    <tr>
                        <th style="width: 10%;">NO</th>
                        <th style="width: 45%;">NAME</th>
                        <th style="width: 20%;">SCORE</th>
                        <th style="width: 25%;">DATE DONE</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Johnny Long John</td>
                        <td style="color: var(--green);">3/3</td>
                        <td>11/09/2000</td>
                    </tr>
                </table>
            </div>
            <div id="feedback" style="display: none;">
                <div id="info">
                    <h1>PART 1</h1>
                    <span>Created On 11/09/2050</span>
                    <p>suck mah dick</p>
                </div>
                <div id="info">
                    <h1>PART 1</h1>
                    <span>Created On 11/09/2050</span>
                    <p>suck mah dick</p>
                </div>
                <div id="info">
                    <h1>PART 1</h1>
                    <span>Created On 11/09/2050</span>
                    <p>suck mah dick</p>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4/dist/chart.umd.min.js"></script>
    <script>
        let Quiz = <?php echo json_encode($data) ?>;
        let StudentPass = <?php echo json_encode($StudentPassData) ?>;
        let StudentFail = <?php echo json_encode($StudentFailData) ?>;
        let Feedback = <?php echo json_encode($FeedbackData) ?>;
        let TotalQuestion = <?php echo $totalQuestion ?>;

        function DeleteQuiz(id) {
            if (confirm('Are you sure? All info related to this material will be deleted')) {
                $.ajax({
                    method: "POST",
                    url: "Upload Quiz.php",
                    data: {
                        ID: id,
                        form: '',
                        subject: '',
                        title: '',
                        description: '',
                        delete: true,
                        saved: true,
                        completion: false,
                        question: [],
                        question_type: [],
                        answer: [],
                        correct: []
                    }
                })

                    .done(function (response) {
                        console.log(response);
                    });
                window.location.href = "Quiz View.php";
            }
        }

        function EditQuiz(id) {
            document.cookie = "ID = " + id + ";"
            document.cookie = "ORIGIN = Quiz Summary.php;"
            window.location.href = "Quiz Edit.php";
        }

        document.querySelector('#profileIcon').addEventListener("click", function () {
            let profile = document.querySelector('#profileMenu');
            if (profile.style.display == "none") {
                profile.style.display = "block";
            } else {
                profile.style.display = "none";
            }
        });
    </script>
    <script src="RESOURCES/JAVA/Summarize Quiz.js"></script>
</body>