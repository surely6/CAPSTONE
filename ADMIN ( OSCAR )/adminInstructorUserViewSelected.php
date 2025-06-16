<?php
session_start();
include "dateTimeProcesses.php";
include "connect.php";
include 'font.php';
include("../block.php");



$instructorID = $_GET['instructorID'];
$InstructorLastOnline = "HAVE NOT LOGGED IN";

// sqlQ1 and Q2 are the basic info
$sqlQ1 = "SELECT * FROM instructors WHERE instructor_id = '$instructorID';";
$result1 = mysqli_query($conn, $sqlQ1);
$InstructorData1 = mysqli_fetch_array($result1);
$data1 = [
    "InstructorName" => $InstructorData1['instructor_name'],
    "InstructorEmail" => $InstructorData1['instructor_email'],
    "ProfilePic" => !empty($InstructorData1['profile_pic_url'])
        ? $InstructorData1['profile_pic_url']
        : 'uploads/profileIcon.png'
];

$sqlQ2 = "SELECT * FROM user_logs WHERE instructor_id = '$instructorID';";
$result2 = mysqli_query($conn, $sqlQ2);

while ($InstructorTempData = mysqli_fetch_array($result2)) {
    $logStatus = $InstructorTempData['isLogin'];
    $logTime = $InstructorTempData['datetime_of_log'];

    if ((int) $logStatus === 0) {
        $InstructorLastOnline = formatDateTime($logTime);
    }
}

// sqlQ3 and Q4 are the graph's
$sqlQ3 = "SELECT * FROM learning_materials WHERE instructor_id = '$instructorID';";
$result3 = mysqli_query($conn, $sqlQ3);
$totalLearning = mysqli_num_rows($result3);

$sqlQ4 = "SELECT * FROM quizzes WHERE instructor_id = '$instructorID';";
$result4 = mysqli_query($conn, $sqlQ4);
$totalQuiz = mysqli_num_rows($result4);

$graphData = [
    'label' => 'Amount',
    'labels' => ['MODULES', 'QUIZ'],
    'values' => [$totalLearning, $totalQuiz]
];

// learning materials and quiz
// learning material details
$sqlQ1 = "SELECT material_id, material_title, material_level, material_subject, material_chapter, material_learning_type FROM learning_materials WHERE completion_status = '1' AND instructor_id = '$instructorID';";
$result1 = mysqli_query($conn, $sqlQ1);
$learningMaterialDetails = [];


while ($tempData = mysqli_fetch_array($result1)) {
    $learningMaterialDetails[] = [
        "id" => $tempData['material_id'],
        "title" => $tempData['material_title'],
        "level" => $tempData['material_level'],
        "subject" => $tempData['material_subject'],
        "chapter" => $tempData['material_chapter'],
        "learning_type" => $tempData['material_learning_type'],
    ];
}

// just getting name
if ($learningMaterialDetails != null) {
    foreach ($learningMaterialDetails as $index => $rows) {
        $learningMaterialDetails[$index]['instructor_name'] = $data1['InstructorName'];
    }
}
;

// quiz details
$sqlQ3 = "SELECT quiz_id, quiz_title, quiz_subject, quiz_level FROM quizzes WHERE instructor_id = '$instructorID';";
$result3 = mysqli_query($conn, $sqlQ3);
$quizDetails = [];

while ($tempData2 = mysqli_fetch_array($result3)) {
    $quizDetails[] = [
        "id" => $tempData2['quiz_id'],
        "title" => $tempData2['quiz_title'],
        "level" => $tempData2['quiz_level'],
        "subject" => $tempData2['quiz_subject'],
    ];
}

// just getting name
if ($quizDetails != null) {
    foreach ($quizDetails as $index => $rows) {
        $quizDetails[$index]['instructor_name'] = $data1['InstructorName'];
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
    <link rel="stylesheet" href="ADMIN_CSS/adminLearningAndQuizView.css">
    <link rel="stylesheet" href="ADMIN_CSS/colour.css">
    <link rel="stylesheet" type="text/css" href="adminPrint.css" media="print">


    <script>
        function back() {
            window.history.back();
        }
        function deleteInstructor() {
            if (confirm("Are you sure to delete this instructor?") == true) {
                let instructorID = "<?php echo $instructorID ?>";
                let deletion = {
                    "insID": instructorID,
                    "userType": "instructor"
                }
                fetch('rejectAndDeleteUser.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(deletion)
                })
                    .then(response => response.text())
                    .then(after => {
                        window.location.href = "adminInstructorUserView.php";
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
                <li><a>DETAILS</a></li>
                <li id="printButtonBox"><button id="printButton" onclick="window.print()">PRINT</button></li>
                <li id="deleteButtonBox"><button id="deleteButton" onclick="deleteInstructor()">DELETE</button></li>
            </ul>
            <div class="mainInfoArea" style="margin-bottom: 3em;">
                <div class="infoSection row">
                    <div class="userProfileNDetail col-2">
                        <div id="userIconAndName">
                            <img src="/capstone/PROFILE/INSTRUCTOR ( SURELY )/<?php echo $data1["ProfilePic"] ?>">
                            <p><?php echo $data1['InstructorName'] ?></p>
                        </div>

                        <div id="userSmallDetail">
                            <ul>
                                <li id="emailLabel"><a>EMAIL</a></li>
                                <li id="userEmail"><a><?php echo $data1['InstructorEmail'] ?></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="userFurtherDetail col-8 print-page-break">
                        <div class="instructor col-10" id="userStatus" style="margin-bottom: 6em;">
                            <div class="chartWithLegend" id="userDataNLegend" style="height: 60%; margin-top: -6em;">
                                <div id="userData"
                                    style="position: relative; height: 15em; width: 35em; background-color:#D9D9D9; margin-left: 2em; margin-top: 1.5em;">
                                    <canvas id="userChart"></canvas>
                                </div>
                            </div>


                            <ul id="lastOnlineArea">
                                <li id="lastOnlineLabel"><a>LAST ONLINE :</a></li>
                                <li id="lastOnlineData"><a><?php echo $InstructorLastOnline ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="titleOfTable print-page-break" id="tableTitle">
                <li><a>ITEMS MADE</a></li>
            </ul>

            <div id="learnAndQuizButton">
                <h1 class="button" onclick="setLearning()">LEARNING MATERIAL</h1>
                <h1 class="button" onclick="setQuiz()">QUIZ</h1>
            </div>
            <div class="Content" id="areaOfInfo" style="margin-top: 2em;">
            </div>
        </other_stuff>
    </main>
</body>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="ADMIN_JS/adminUserSelectedGraph.js"></script>
<script src="ADMIN_JS/adminLearningAndQuizView.js"></script>
<script>
    let buttons = document.querySelectorAll('.button');
    const id = "<?php echo $instructorID ?>";
    console.log(id);

    const data = <?php echo json_encode($graphData) ?>;
    window.onload = getGraphData(data);

    let learningMaterialDetails = <?= json_encode($learningMaterialDetails) ?>;
    let quizDetails = <?= json_encode($quizDetails) ?>;
    window.onload = setLearning();

    function setLearning() {
        let currentDisplay = "learningMaterial";
        displayLearningMaterial(learningMaterialDetails);
        buttons[0].classList.add('active');
        buttons[1].classList.remove('active');
    }
    function setQuiz() {
        let currentDisplay = "quiz";
        displayQuiz(quizDetails);
        buttons[1].classList.add('active');
        buttons[0].classList.remove('active');
    }
</script>

</html>