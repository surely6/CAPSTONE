<?php
session_start();
include "connect.php";
include "font.php";
include("../block.php");



// if (empty($_SESSION)) {
//     echo "<script>window.location.href='index.php';</script>";
//     exit();
// }
$student_id = $_SESSION['user_id'];

// Get quizess information
$quiz_detail_query = "SELECT 
        q.*,
        i.instructor_name,
        CASE
            WHEN q.completion_status = 1 THEN 'Completed'
            WHEN q.completion_status = 0 THEN 'Not Completed'
            ELSE 'Unknown'
        END AS completion_status_text
        FROM quizzes AS q
        JOIN instructors AS i ON i.instructor_id = q.instructor_id
        WHERE q.completion_status = 1
        ORDER BY quiz_subject ASC";
$quiz_detail_sql = mysqli_query($conn, $quiz_detail_query);
$quiz_detail = [];
if (mysqli_num_rows($quiz_detail_sql) > 0) {
    while ($row = mysqli_fetch_assoc($quiz_detail_sql)) {
        $quiz_detail[] = $row;
    }
}

$material_detail_query = "SELECT 
        lm.material_id, 
        lm.material_subject,
        lm.material_chapter,
        lm.material_level,
        lm.instructor_id
        FROM learning_materials AS lm";
$material_detail_sql = mysqli_query($conn, $material_detail_query);
$material_detail = [];
if (mysqli_num_rows($material_detail_sql) > 0) {
    while ($row = mysqli_fetch_assoc($material_detail_sql)) {
        $material_detail[] = $row;
    }
}

$progressSql = "SELECT 
                    q.quiz_subject, 
                    q.quiz_title, 
                    q.quiz_id, 
                    CASE 
                        WHEN b.quiz_id IS NOT NULL THEN 1 
                        ELSE 0 
                    END AS is_bookmarked
                    FROM quizzes q
                    INNER JOIN bookmarks b ON q.quiz_id = b.quiz_id 
                    WHERE b.student_id = '$student_id'";
$progressResult = mysqli_query($conn, $progressSql);
$progressResult_detail = [];
if (mysqli_num_rows($progressResult) > 0) {
    while ($row = mysqli_fetch_assoc($progressResult)) {
        $progressResult_detail[] = $row;
    }
}

$student_detail_query = "SELECT 
        student_id,
        student_learning_style,
        student_level
        FROM students";
$student_detail_sql = mysqli_query($conn, $student_detail_query);
$student_detail = [];
if (mysqli_num_rows($student_detail_sql) > 0) {
    while ($row = mysqli_fetch_assoc($student_detail_sql)) {
        $student_detail[] = $row;
    }
}

// $student_id = $_SESSION['student_id']; OFFICIAL
$attempt_query = "SELECT * FROM attempts WHERE student_id = '$student_id'";
$attempt_sql = mysqli_query($conn, $attempt_query);
$attempt_detail = [];
if (mysqli_num_rows($attempt_sql)) {
    while ($row = mysqli_fetch_assoc($attempt_sql)) {
        $attempt_detail[] = $row;
    }
}

$pathway_query = "SELECT 
        lp.*,
        s.material_id,
        lm.material_subject,
        lm.material_level,
        lm.material_chapter
        FROM learning_pathways AS lp
        JOIN sequences AS s 
        ON s.pathway_id = lp.pathway_id
        JOIN learning_materials AS lm
        ON lm.material_id = s.material_id";
$pathway_sql = mysqli_query($conn, $pathway_query);
$pathway_detail = [];
if (mysqli_num_rows($pathway_sql) > 0) {
    while ($row = mysqli_fetch_assoc($pathway_sql)) {
        $pathway_detail[] = $row;
    }
}

$progress_detail_query = "SELECT * FROM progress WHERE student_id = '$student_id'";
$progress_detail_sql = mysqli_query($conn, $progress_detail_query);
$progress_detail = [];
if (mysqli_num_rows($progress_detail_sql) > 0) {
    while ($row = mysqli_fetch_assoc($progress_detail_sql)) {
        $progress_detail[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Quiz Page</title>
    <link rel="stylesheet" href="stuLearningMaterial.css">
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
    <sidebar class="wrapper">
        <!-- button to open side bar -->
        <btn id="OpenFilter" onclick="ShowFilter()">&#9776;</btn>
        <!-- the side bar -->
        <div id='filter'>
            <btn id="CloseFilter" onclick="CloseFilter()">FILTER</btn>

            <div class="CheckSection">
                <input type="checkbox" class="all" id="all" name="all" value="all">
                <label for="all"> ALL</label><br>
            </div>

            <input type="text" id="search" placeholder="TITLE...">

            <div class="CheckSection">
                <!-- insert dropdown list id into function -->
                <h3 onclick="ShowDropdown(formList)">FORM</h3>
                <div class="check-dropdown-list" id="formList">
                    <label for="form1"> <input type="checkbox" id="form1" class="formCheckbox" name="form1" value="1">
                        FORM 1</label><br>
                    <label for="form2"> <input type="checkbox" id="form2" class="formCheckbox" name="form2" value="2">
                        FORM 2</label><br>
                    <label for="form3"> <input type="checkbox" id="form3" class="formCheckbox" name="form3" value="3">
                        FORM 3</label><br>
                    <label for="form4"> <input type="checkbox" id="form4" class="formCheckbox" name="form4" value="4">
                        FORM 4</label><br>
                    <label for="form5"> <input type="checkbox" id="form5" class="formCheckbox" name="form5" value="5">
                        FORM 5</label><br>
                </div>
            </div>

            <div class="CheckSection">
                <!-- insert dropdown list id into function -->
                <h3 onclick="ShowDropdown(subject)">SUBJECT</h3>
                <div class="check-dropdown-list" id="subject">
                    <label for="english"> <input type="checkbox" class="matCheckbox" id="english"
                            name="material_subject" value="english"> ENGLISH</label><br>
                    <label for="malay"> <input type="checkbox" class="matCheckbox" id="malay" name="material_subject"
                            value="malay"> MALAY</label><br>
                    <label for="mathematic"> <input type="checkbox" class="matCheckbox" id="mathematic"
                            name="material_subject" value="mathematics"> MATHEMATICS</label><br>
                    <label for="science"> <input type="checkbox" class="matCheckbox" id="fsciencerm"
                            name="material_subject" value="science"> SCIENCE</label><br>
                    <label for="history"> <input type="checkbox" class="matCheckbox" id="history"
                            name="material_subject" value="history"> HISTORY</label><br>
                    <label for="geography"> <input type="checkbox" class="matCheckbox" id="geography"
                            name="material_subject" value="geography"> GEOGRAPHY</label><br>
                    <label for="accounting"> <input type="checkbox" class="matCheckbox" id="accounting"
                            name="material_subject" value="accounting"> ACCOUNTING</label><br>
                    <label for="economy"> <input type="checkbox" class="matCheckbox" id="economy"
                            name="material_subject" value="economy"> ECONOMY</label><br>
                    <label for="business"> <input type="checkbox" class="matCheckbox" id="business"
                            name="material_subject" value="business"> BUSINESS</label><br>
                    <label for="add_math"> <input type="checkbox" class="matCheckbox" id="add_math" name="add_math"
                            value="add_math"> ADD MATH</label><br>
                    <label for="physics"> <input type="checkbox" class="matCheckbox" id="physics"
                            name="material_subject" value="physics"> PHYSICS</label><br>
                    <label for="chemistry"> <input type="checkbox" class="matCheckbox" id="chemistry"
                            name="material_subject" value="chemistry"> CHEMISTRY</label><br>
                    <label for="biology"> <input type="checkbox" class="matCheckbox" id="biology"
                            name="material_subject" value="biology"> BIOLOGY</label><br>
                </div>
            </div>
            <div class="CheckSection">
                <!-- insert dropdown list id into function -->
                <h3 onclick="ShowDropdown(chapter)">CHAPTER</h3>
                <div class="check-dropdown-list" id="chapter">
                    <label for="chapter1"> <input type="checkbox" class="chapCheckbox" id="chapter1" name="chapter1"
                            value="1">1</label><br>
                    <label for="chapter2"> <input type="checkbox" class="chapCheckbox" id="chapter2" name="chapter2"
                            value="2">2</label><br>
                    <label for="chapter3"> <input type="checkbox" class="chapCheckbox" id="chapter3" name="chapter3"
                            value="3">3</label><br>
                    <label for="chapter4"> <input type="checkbox" class="chapCheckbox" id="chapter4" name="chapter4"
                            value="4">4</label><br>
                    <label for="chapter5"> <input type="checkbox" class="chapCheckbox" id="chapter5" name="chapter5"
                            value="5">5</label><br>
                    <label for="chapter6"> <input type="checkbox" class="chapCheckbox" id="chapter6" name="chapter6"
                            value="6">6</label><br>
                    <label for="chapter7"> <input type="checkbox" class="chapCheckbox" id="chapter7" name="chapter7"
                            value="7">7</label><br>
                    <label for="chapter8"> <input type="checkbox" class="chapCheckbox" id="chapter8" name="chapter8"
                            value="8">8</label><br>
                    <label for="chapter9"> <input type="checkbox" class="chapCheckbox" id="chapter9" name="chapter9"
                            value="9">9</label><br>
                    <label for="chapter10"> <input type="checkbox" class="chapCheckbox" id="chapter10" name="chapter10"
                            value="10">10</label><br>
                    <label for="chapter11"> <input type="checkbox" class="chapCheckbox" id="chapter11" name="chapter11"
                            value="11">11</label><br>
                    <label for="chapter12"> <input type="checkbox" class="chapCheckbox" id="chapter12" name="chapter12"
                            value="12">12</label><br>
                    <label for="chapter13"> <input type="checkbox" class="chapCheckbox" id="chapter13" name="chapter13"
                            value="13">13</label><br>
                    <label for="chapter14"> <input type="checkbox" class="chapCheckbox" id="chapter14" name="chapter14"
                            value="14">14</label><br>
                    <label for="chapter15"> <input type="checkbox" class="chapCheckbox" id="chapter15" name="chapter15"
                            value="15">15</label><br>
                </div>
            </div>
        </div>
    </sidebar>

    <main id="main_content">
        <div id="selected_mat" style="display: block;">
            <div class="title">
                <h1>JUST FOR YOU</h1>
            </div>
            <div class="recommend_container"></div>
        </div>

        <div id="all_mat" style="display: none;">
            <div class="title">
                <h1>ALL</h1>
            </div>
            <div class="everything_container"></div>
        </div>

        <dialog id="modal" class="modal-content"></dialog>
    </main>

    <body>


    </body>



    <script>
        let quiz_data = <?= json_encode($quiz_detail) ?>;
        console.log("Quiz Data:", quiz_data);

        let material_data = <?= json_encode($material_detail) ?>;
        console.log("Material Data:", material_data);

        let progress_result = <?= json_encode($progressResult_detail) ?>;
        console.log("Progress Result:", progress_result);

        let student_data = <?= json_encode($student_detail) ?>;
        console.log("Student Data: ", student_data);

        let attempt_data = <?= json_encode($attempt_detail) ?>;
        console.log("Attempt Data:", attempt_data);

        let pathway_data = <?= json_encode($pathway_detail) ?>;
        console.log("Pathway Data: ", pathway_data);

        let progress_data = <?= json_encode($progress_detail) ?>;
        console.log("Progress Data:", progress_data);

        const student_id = "<?php echo $_SESSION['user_id']; ?>";
    </script>



    <script src="stuQuiz.js"></script>
</body>

</html>