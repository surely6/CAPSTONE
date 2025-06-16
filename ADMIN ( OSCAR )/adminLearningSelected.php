<?php
session_start();
include 'connect.php';
include 'font.php';
include("../block.php");

$ID = $_GET['materialID'];

$data = array(
    "ID" => $ID,
    "form" => "",
    "subject" => "",
    "chapter" => "",
    "learning_style" => "",
    "title" => "",
    "description" => "",
    "content" => []
);

$MaterialQuery = "SELECT * FROM learning_materials WHERE material_id = $ID";
$MaterialSQL = mysqli_query($conn, $MaterialQuery);

$array = mysqli_fetch_assoc($MaterialSQL);

$instructorID = $array['instructor_id'];
$sqlQ2 = "SELECT * FROM instructors WHERE instructor_id = '$instructorID';";
$result2 = mysqli_query($conn, $sqlQ2);
$instructorDetails = mysqli_fetch_array($result2);

$PartsQuery = "SELECT material_content FROM learning_material_parts WHERE material_id = $ID ORDER BY part ASC";
$PartsSQL = mysqli_query($conn, $PartsQuery);

$totalAttempt = mysqli_num_rows($PartsSQL);

if (mysqli_num_rows($PartsSQL) > 0) {
    while ($row = mysqli_fetch_row($PartsSQL)) {
        array_push($data['content'], $row[0]);
    }
}

$data['form'] = $array['material_level'];
$data['subject'] = $array['material_subject'];
$data['chapter'] = $array['material_chapter'];
$data['learning_style'] = $array['material_learning_type'];
$data['title'] = $array['material_title'];
$data['description'] = $array['material_description'];

$StudentsQuery =
    "SELECT progress, last_datetime, student_name FROM progress 
    INNER JOIN students ON progress.student_id = students.student_id AND material_id = $ID
    ORDER BY last_datetime DESC";
$StudentsSQL = mysqli_query($conn, $StudentsQuery);

$StudentCompletedData = [];
$StudentInProgressData = [];

$completedAttempt = 0;
$inProgressAttempt = 0;

if (mysqli_num_rows($StudentsSQL) > 0) {
    while ($row = mysqli_fetch_assoc($StudentsSQL)) {
        $info = array(
            "name" => $row['student_name'],
            "progress" => "",
            "date" => ""
        );
        $current = explode(",", $row['progress']);
        $progress = count($current);
        $percentage = round(($progress / $totalAttempt) * 100, 0);
        $info['progress'] = $percentage;

        $originalDate = $row['last_datetime'];
        $newDate = date("d/m/Y", strtotime($originalDate));
        $info['date'] = $newDate;

        if ($percentage == 100) {
            $completedAttempt++;
            array_push($StudentCompletedData, $info);
        } else {
            $inProgressAttempt++;
            array_push($StudentInProgressData, $info);
        }
    }
}

$FeedbackQuery =
    "SELECT feedback, date_made, student_name FROM learning_material_feedback
    INNER JOIN students ON learning_material_feedback.student_id = students.student_id AND material_id = $ID
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
                <li><a><?php echo strtoupper($array['material_title']) ?></a></li>
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
                                COMPLETION RATING
                            </h1>
                        </div>
                        <div id="detail">
                            <div id="chart">
                                <canvas id="myChart"></canvas>
                            </div>
                            <div id="chart-detail">
                                <div class="category" style="background-color: var(--light-green);">
                                    <div class="cat-title">
                                        <h1 style="font-size: 23px;">
                                            COMPLETED
                                        </h1>
                                    </div>
                                    <p><?php echo $completedAttempt ?></p>
                                    <p style="font-size: 2vw;">STUDENTS</p>
                                </div>
                                <div class="category" style="background-color: var(--light-orange);">
                                    <div class="cat-title">
                                        <h1>
                                            IN PROGRESS
                                        </h1>
                                    </div>
                                    <p><?php echo $inProgressAttempt ?></p>
                                    <p style="font-size: 2vw;">STUDENTS</p>
                                </div>
                                <div class="category" style="background-color: var(--grey);">
                                    <div class="cat-title" style="background-color: var(--grey);">
                                        <h1>
                                            TOTAL
                                        </h1>
                                    </div>
                                    <p style="background-color: var(--grey);"><?php echo $totalAttempt ?></p>
                                    <p style="font-size: 2vw;" style="background-color: var(--grey);">STUDENTS</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="header print-page-break">
                <div class="button" onclick="SetContent()"><span>CONTENT</span></div>
                <div class="button" onclick="SetUser()"><span>USER LIST</span></div>
                <div class="button" onclick="SetFeedback()"><span>FEEDBACK</span></div>
                <div id="filter">
                    <select name="status" id="status" style="display: block;">
                        <option value="all" selected>ALL</option>
                        <option value="completed">COMPLETED</option>
                        <option value="in-progress">IN PROGRESS</option>
                    </select>
                    <select name="time" id="time" style="display: block;">
                        <option value="latest" selected>LATEST</option>
                        <option value="oldest">OLDEST</option>
                    </select>
                </div>
            </div>
            <div id="information" style="width: 100%;">
            </div>

        </other_stuff>
    </main>
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let Material = <?php echo json_encode($data) ?>;
    let StudentCompleted = <?php echo json_encode($StudentCompletedData) ?>;
    let StudentInProgress = <?php echo json_encode($StudentInProgressData) ?>;
    let Completed = <?php echo $completedAttempt ?>;
    console.log(Completed);
    let InProgress = <?php echo $inProgressAttempt ?>;
    let Feedback = <?php echo json_encode($FeedbackData) ?>;
</script>
<script src="ADMIN_JS/adminLearningSelectedInfo.js"></script>
<script>
    const ctx = document.getElementById('myChart');

    console.log(Completed);

    const data = {
        labels: [
            'Completed',
            'In Progress'
        ],
        datasets: [{
            data: [Completed, InProgress],
            backgroundColor: [
                'rgb(74, 183, 136)',
                'rgb(242, 144, 16)'
            ],
            hoverOffset: 7
        }]
    };

    const config = {
        type: 'doughnut',
        data: data,
        options: {
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    };

    new Chart(ctx, config);
</script>

</html>