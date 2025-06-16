<?php
session_start();

include "connect.php";
include "font.php";
include("../block.php");

// if (empty($_SESSION)) {
//     echo "<script>window.location.href='index.php';</script>";
//     exit();
// }

$material_id = $_COOKIE['Material_ID'] ?? '';

$student_id = $_SESSION['user_id'];


$data = array(
    "ID" => $material_id,
    "form" => "",
    "subject" => "",
    "chapter" => "",
    "learning_style" => "",
    "title" => "",
    "description" => "",
    "content" => []
);

$material_detail_query = "SELECT * FROM learning_materials WHERE material_id = '$material_id'";
$material_detail_sql = mysqli_query($conn, $material_detail_query);
$material_detail = [];
if (mysqli_num_rows($material_detail_sql) > 0) {
    while ($row = mysqli_fetch_assoc($material_detail_sql)) {
        $material_detail[] = $row;
    }
}

$material_parts_query = "SELECT * FROM learning_material_parts 
        WHERE material_id = '$material_id'
        ORDER BY part ASC";
$material_parts_sql = mysqli_query($conn, $material_parts_query);
$material_parts_detail = [];
if (mysqli_num_rows($material_parts_sql) > 0) {
    while ($row = mysqli_fetch_assoc($material_parts_sql)) {
        $material_parts_detail[] = $row;
    }
}

$progress_detail_query = "SELECT * FROM progress 
                            WHERE student_id = '$student_id'
                            AND material_id = '$material_id'";
$progress_detail_sql = mysqli_query($conn, $progress_detail_query);
$progress_detail = [];
if (mysqli_num_rows($progress_detail_sql)) {
    while ($row = mysqli_fetch_assoc($progress_detail_sql)) {
        $progress_detail[] = $row;
    }
}

$quiz_detail_query = "SELECT 
        q.*, 
        i.instructor_name
        FROM quizzes AS q
        JOIN instructors AS i ON i.instructor_id = q.instructor_id";
$quiz_detail_sql = mysqli_query($conn, $quiz_detail_query);
$quiz_detail = [];
if (mysqli_num_rows($quiz_detail_sql) > 0) {
    while ($row = mysqli_fetch_assoc($quiz_detail_sql)) {
        $quiz_detail[] = $row;
    }
}

$attempt_query = "SELECT * FROM attempts WHERE student_id = '$student_id'";
$attempt_sql = mysqli_query($conn, $attempt_query);
$attempt_detail = [];
if (mysqli_num_rows($attempt_sql)) {
    while ($row = mysqli_fetch_assoc($attempt_sql)) {
        $attempt_detail[] = $row;
    }
}

$question_detail_query = "SELECT 
                                q.question_id,
                                q.quiz_id,
                                qa.answer_list,
                                qa.correct_answer,
                                qs.question_style 
                                FROM questions AS q
                                INNER JOIN question_answers AS qa ON qa.question_id = q.question_id
                                INNER JOIN question_styles AS qs ON qs.question_style_id = q.question_style_id";
$question_detail_sql = mysqli_query($conn, $question_detail_query);
$question_detail = [];
if (mysqli_num_rows($question_detail_sql) > 0) {
    while ($row = mysqli_fetch_assoc($question_detail_sql)) {
        $question_detail[] = $row;
    }
}

$progressSql = "SELECT 
                    lm.material_subject, 
                    lm.material_title, 
                    lm.material_id, 
                    CASE 
                        WHEN b.material_id IS NOT NULL THEN 1 
                        ELSE 0 
                    END AS is_bookmarked
                    FROM learning_materials lm
                    INNER JOIN bookmarks b ON lm.material_id = b.material_id 
                    WHERE b.student_id = '$student_id'";
$progressResult = mysqli_query($conn, $progressSql);
$progressResult_detail = [];
if (mysqli_num_rows($progressResult) > 0) {
    while ($row = mysqli_fetch_assoc($progressResult)) {
        $progressResult_detail[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Access Selected Learning Material Page</title>
    <link rel="stylesheet" href="stuAccessMaterial.css">
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

    <section class="material_details"></section>

    <main class="container">
        <div class="material_section"></div>
        <hr>

        <form action="provideFeedbackProcess.php" method="POST" class="feedback_section"
            onsubmit="return checkFeedback();">
            <label for="material_feedback" id="feedback_part">
                <h1>PROVIDE FEEDBACK</h1>
            </label>
            <textarea id="material_feedback" name="material_feedback"></textarea>
            <button type="submit">SUBMIT</button>
        </form>
        <hr>

        <div class="relevant_quiz">
            <div class="qjfy">
                <h1>QUIZ JUST FOR YOU</h1>
            </div>
            <hr>
            <div class="recommend_container"></div>
            <dialog id="modal" class="modal-content"></dialog>
        </div>
    </main>

    <script>
        textarea = document.querySelector("#material_feedback");
        textarea.addEventListener('input', autoResize, false);

        function autoResize() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        }

        function checkFeedback() {
            const feedback = document.getElementById('material_feedback').value.trim();
            if (feedback === "") {
                const confirmation = confirm("No feedback written. Would you like to provide one?");
                if (confirmation) {
                    return false;
                }
                else {
                    alert("Alright :DD! But please feel free to return and fill in the feedback when you feel like you want to");
                    window.location.href = "stuLearningMaterial.php";
                    return false;
                }
            };

    </script>

    <script>
            let material_id = <?= json_encode($material_id) ?>;
            console.log("Material ID: ", material_id);

            let material_data = <?= json_encode($material_detail) ?>;
            console.log("Material Data: ", material_data);

            let parts_data = <?= json_encode($material_parts_detail) ?>;
            console.log("Parts Data: ", parts_data);

            let quiz_data = <?= json_encode($quiz_detail) ?>;
            console.log("Quiz Data:", quiz_data);

            let progress_data = <?= json_encode($progress_detail) ?>;
            console.log("Progress Data:", progress_data);

            let attempt_data = <?= json_encode($attempt_detail) ?>;
            console.log("Attempt Data:", attempt_data);

            let question_data = <?= json_encode($question_detail) ?>;
            console.log("Question Data:", question_data);

            let progress_result = <?= json_encode($progressResult_detail) ?>;
            console.log("Progress Result:", progress_result);

            const student_id = "<?php echo $_SESSION['user_id']; ?>";
    </script>


    <script src="stuAccessMaterial.js"></script>
</body>

</html>