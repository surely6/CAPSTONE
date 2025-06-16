<?php
session_start();

include "connect.php";
include "font.php";
include("../block.php");

// if (empty($_SESSION)) {
//     echo "<script>window.location.href='index.php';</script>";
//     exit();
// }



if (isset($_GET['quiz_id']) && !empty($_GET['quiz_id'])) {
    $quiz_id = intval($_GET['quiz_id']);
    setcookie('Quiz_ID', $quiz_id, time() + 3600, '/');
} elseif (isset($_COOKIE['Quiz_ID'])) {
    $quiz_id = intval($_COOKIE['Quiz_ID']);
}

$quiz_detail_query = "SELECT * FROM quizzes WHERE quiz_id = $quiz_id";
$quiz_detail_sql = mysqli_query($conn, $quiz_detail_query);
$quiz_detail = [];
if (mysqli_num_rows($quiz_detail_sql) > 0) {
    while ($row = mysqli_fetch_assoc($quiz_detail_sql)) {
        $quiz_detail[] = $row;
    }
}

$question_detail_query = "SELECT 
                                q.question_id,
                                q.quiz_id,
                                q.question,
                                q.question_style_id,
                                qa.answer_list,
                                qa.correct_answer
                                FROM questions AS q
                                INNER JOIN question_answers AS qa ON qa.question_id = q.question_id AND q.quiz_id = $quiz_id
                                ORDER BY q.question_id ASC";
$question_detail_sql = mysqli_query($conn, $question_detail_query);
$question_detail = [];
if (mysqli_num_rows($question_detail_sql) > 0) {
    while ($row = mysqli_fetch_assoc($question_detail_sql)) {
        $question_detail[] = $row;
    }
}

$student_id = $_SESSION['user_id'];

$attempt_id = 0;
$attempt_id_query = "SELECT MAX(attempt_id) FROM attempts";
$attempt_id_sql = mysqli_query($conn, $attempt_id_query);

$row = mysqli_fetch_row($attempt_id_sql);
$attempt_id = $row[0] + 1;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Access Selected Quiz Page</title>
    <link rel="stylesheet" href="stuAccessQuiz.css">
</head>
<style>
    li#profileDropdown {
        position: relative;
        width: fit-content;
    }

    #profileMenu {
        z-index: 3;
        position: absolute;
        top: 70%;
        right: 0;
    }

    #profileMenu a {
        z-index: 1;
        background-color: var(--light-grey);
        padding: .5vh 1vw;
    }
</style>

<body>
    <header>
        <ul>
            <li id="profileDropdown">
                <img src="/capstone/INSTRUCTOR ( CHOW ) /RESOURCES/profile.png" alt="Profile Icon" id="profileIcon"
                    style="width: 50px; height: 50px; cursor: pointer; align-items: center; display: flex; justify-content: center; margin-left: 10px; margin-top: 10px;position:relaive;">
                <div id="profileMenu" class="dropdown-content" style="display: none;">
                    <a href="/capstone/PROFILE/STUDENT ( PIKER )/studentProfile.php">My Profile</a>
                    <a href="/capstone/PROFILE/STUDENT ( PIKER )/managePath.php">Learning Path</a>
                    <a href="/capstone/PROFILE/STUDENT ( PIKER )/studentBookmark.php">Bookmark</a>
                    <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/studentHistory.php">History</a>
                    <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/system_feedback.php">Feedback</a>
                    <a href="/capstone/logout.php">Logout</a>
                </div>
            </li>
            <li class="options"><a href="/capstone/STUDENT ( LING )/stuLearningMaterial.php">LEARNING MATERIAL</a></li>
            <li class="options"><a href="/capstone/STUDENT ( LING )/stuQuiz.php">QUIZ</a></li>
            <li id="profile"><a href="/capstone/PROFILE/STUDENT ( PIKER )/studentDashboard.php">DASH BOARD</a></li>
            <li id="logo" style="margin-left: 65px;"><a
                    href="/capstone/PROFILE/STUDENT ( PIKER )/studentDashboard.php">ASSESTIFY</a></li>
        </ul>
    </header>
    <script>
        document.querySelector('#profileIcon').addEventListener("click", function () {
            let profile = document.querySelector('#profileMenu');
            if (profile.style.display == "none") {
                console.log('open')
                profile.style.display = "block";
            } else {
                console.log('close')
                profile.style.display = "none";
            }
        })
    </script>

    <section class="material_details">
    </section>
    <hr>
    <main class="container">
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        let quiz_data = <?= json_encode($quiz_detail) ?>;
        console.log("Quiz Data:", quiz_data);


        let question_data = <?= json_encode($question_detail) ?>;
        console.log("Question Data:", question_data);

        let quiz_id = <?= json_encode($quiz_id) ?>;
        console.log("Quiz ID: ", quiz_id);

        let student_id = <?= json_encode($student_id) ?>;
        console.log("Student ID: ", student_id);

        let attempt_id = <?= json_encode($attempt_id) ?>;
        console.log("Attempt ID: ", attempt_id);
    </script>

    <script src="stuAccessQuiz.js"></script>
</body>

</html>