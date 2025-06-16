<?php
session_start();
if (empty($_SESSION)) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

include "connect.php";
include "font.php";
include("../block.php");


$quiz_id = $_COOKIE['Quiz_ID'];

if (!(empty($_POST))) {
    $attempt_id = $_POST['attempt'];
    $quiz_id = $_POST['quiz_id'];
    $student_id = $_POST['student_id'];
    $score = $_POST['score'];
    $questions = $_POST['questions'];
    $StudentAnswer = $_POST['answer'];

    $date = date('Y-m-d');
    var_dump($questions);
    var_dump($StudentAnswer);
    echo $date;

    $insert_query = "INSERT INTO attempts
        VALUES ('$attempt_id','$quiz_id','$student_id','$score','$date')";
    $insert_sql = mysqli_query($conn, $insert_query);

    for ($i = 0; $i < count($questions); $i++) {
        $insert_query = "INSERT INTO student_answers
            VALUES ('','$questions[$i]','$attempt_id','" . implode(",", $StudentAnswer[$i]) . "')";
        $insert_sql = mysqli_query($conn, $insert_query);
    }

}

if (isset($_COOKIE['attempt-id'])) {
    $attempt_id = $_COOKIE['attempt-id'];

    $score = 0;
    $StudentAnswer = [];
    $Questions = [];
    $Answer = [];
    $CorrectAnswers = [];

    $data_query = "SELECT a.score, sa.answer_selected, q.question, qa.answer_list, qa.correct_answer FROM attempts AS a
        INNER JOIN student_answers AS sa ON a.attempt_id = sa.attempt_id AND a.attempt_id = $attempt_id
        INNER JOIN questions AS q ON sa.question_id = q.question_id
        INNER JOIN question_answers AS qa ON qa.question_id = q.question_id";

    $data_sql = mysqli_query($conn, $data_query);

    if (mysqli_num_rows($data_sql) > 0) {
        while ($row = mysqli_fetch_row($data_sql)) {
            $score = $row[0];
            array_push($StudentAnswer, (explode(",", $row[1])));
            array_push($Questions, $row[2]);
            array_push($Answer, (explode(",", $row[3])));
            array_push($CorrectAnswers, (explode(",", $row[4])));
        }
    }

    $data_query =
        "SELECT q.quiz_title, q.quiz_level, q.quiz_subject, q.quiz_total_questions, q.quiz_chapter FROM quizzes AS q
        INNER JOIN attempts AS a ON a.quiz_id = q.quiz_id AND a.attempt_id = $attempt_id";

    $data_sql = mysqli_query($conn, $data_query);

    if (mysqli_num_rows($data_sql) > 0) {
        while ($row = mysqli_fetch_row($data_sql)) {
            $title = $row[0];
            $forms = $row[1];
            $subject = $row[2];
            $total_questions = $row[3];
            $chapter = $row[4];
        }
    }

    $percentage = round(($score / $total_questions * 100), 0);
    if ($percentage >= 50) {
        $background = "light-green";
    } else {
        $background = "light-red";
    }

    $wrong = $total_questions - $score;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Complete Selected Quiz Page</title>
    <link rel="stylesheet" href="stuCompleteQuiz.css">
</head>

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
    <main class="score_container">
        <button onclick="window.location.href='stuQuiz.php'">
            < RETURN TO MAIN</button>
                <hr>
                <div id="summary">
                    <div class="title" style="color: white;">
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
                        <div class="category" style="background-color: var(--light-green);">
                            <div class="cat-title">
                                <h1>
                                    TOTAL CORRECT
                                </h1>
                            </div>
                            <p><?php echo $score ?></p>
                        </div>
                        <div class="category" style="background-color: var(--light-red);">
                            <div class="cat-title">
                                <h1>
                                    TOTAL WRONG
                                </h1>
                            </div>
                            <p><?php echo $wrong ?></p>
                        </div>
                        <div class="category" style="background-color: var(--<?php echo $background ?>);">
                            <div class="cat-title">
                                <h1>
                                    ACCURACY
                                </h1>
                            </div>
                            <p><?php echo $percentage ?>%</p>
                        </div>
                        <div class="category">
                            <div class="cat-title">
                                <h1>
                                    TOTAL QUESTIONS
                                </h1>
                            </div>
                            <p><?php echo $total_questions ?></p>
                        </div>
                    </div>
    </main>
    <hr>
    <main class="ans_detail_container">
        <div id="information">
            <div id="content">
                <div class="title">
                    <h1></h1>
                </div>
                <div class="title">
                    <h1>SUBJECT: <?php echo strtoupper($subject) ?></h1>
                    <h1>FORM: <?php echo $forms ?></h1>
                </div>

                <?php for ($i = 0; $i < count($Questions); $i++): ?>
                    <div id="part">
                        <h1>QUESTION <?php echo $i + 1; ?></h1>
                        <div id="question">
                            <p><?php echo htmlspecialchars($Questions[$i]); ?></p>
                        </div>
                        <div id="answer">
                            <?php
                            $correctIndexes = array_map('intval', $CorrectAnswers[$i]);
                            foreach ($Answer[$i] as $j => $ans):
                                $isCorrect = in_array($j, $correctIndexes);
                                ?>
                                <div<?php if ($isCorrect)
                                    echo ' style="background-color: var(--green); color: var(--grey);"'; ?>>
                                    <?php echo htmlspecialchars($ans); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </main>
    <hr>
       <form action="provideFeedbackProcess.php" method="POST" class="feedback_section" onsubmit="return checkFeedback();">
            <label for="quiz_feedback" id="feedback_part"><h1>PROVIDE FEEDBACK</h1></label>
            <textarea id="quiz_feedback" name="quiz_feedback"></textarea>
            <button type="submit">SUBMIT</button>
        </form>     
    <script>
        textarea = document.querySelector("#quiz_feedback");
        textarea.addEventListener('input', autoResize, false);

        function autoResize() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        }

        function checkFeedback() {
            const feedback = document.getElementById('quiz_feedback').value.trim();

            if (feedback === "") {
                const confirmation = confirm("No feedback written. Would you like to provide one?");
                if (confirmation) {
                    return false;
                }
                else {
                    alert("Alright :DD! But please feel free to return and fill in the feedback when you feel like you want to");
                    window.location.href = "stuQuiz.php";
                    return false;
                }
            };
        }
    </script>

    <script>
        let title = <?php echo json_encode(strtoupper($title)) ?>;
        console.log(title)
        let subject = <?php echo json_encode(strtoupper($subject)) ?>;
        console.log(subject)
        let form = <?php echo json_encode($forms) ?>;
        console.log(form)
        let chapter = <?php echo json_encode($chapter) ?>;
        console.log(chapter)

        let question_data = <?= json_encode($Questions) ?>;
        console.log("Question Data:", question_data);

        let student_answer = <?= json_encode($StudentAnswer) ?>;
        console.log("Student Answer:", student_answer);

        let answers = <?= json_encode($Answer) ?>;
        console.log("Answer: ", answers);

        let correct_answers = <?= json_encode($CorrectAnswers) ?>;
        console.log("Correct answers: ", correct_answers);

        let quiz_id = <?= json_encode($quiz_id) ?>;
        console.log("Quiz ID: ", quiz_id);
    </script>


    <script src="stuCompleteQuiz.js"></script>
</body>

</html>