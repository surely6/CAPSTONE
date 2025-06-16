<?php
session_start();
include "dateTimeProcesses.php";
include "connect.php";
include "font.php";
include("../block.php");


$studentID = $_GET['studentID'];

$StudentInfo = [];
$StudentLastOnline = "HAVE NOT LOGGED IN";

$sqlQ1 = "SELECT * FROM students WHERE student_id = '$studentID';";
$result1 = mysqli_query($conn, $sqlQ1);
$StudentData1 = mysqli_fetch_array($result1);
$data1 = [
    "StudentName" => $StudentData1['student_name'],
    "StudentEmail" => $StudentData1['student_email'],
    "StudentLevel" => 'FORM ' . $StudentData1['student_level'],
    "StudentLearningStyle" => strtoupper($StudentData1['student_learning_style']),
    "ProfilePic" => !empty($StudentData1['profile_pic_url'])
        ? $StudentData1['profile_pic_url']
        : 'profileIcon/profileIcon.png'
];

if ($data1["StudentLearningStyle"] == "READ_WRITE") {
    $data1["StudentLearningStyle"] = "READ & WRITE";
}


$sqlQ2 = "SELECT * FROM user_logs WHERE student_id = '$studentID';";
$result2 = mysqli_query($conn, $sqlQ2);

while ($StudentTempData = mysqli_fetch_array($result2)) {

    $logStatus = $StudentTempData['isLogin'];
    $logTime = $StudentTempData['datetime_of_log'];

    $data2[] = [
        "isLogin" => $logStatus,
        "timeOfLog" => $logTime
    ];

    if ((int) $logStatus === 0) {
        $StudentLastOnline = formatDateTime($logTime);
    }
}

// just in case there is no such user logs or they have only logged in once
if (mysqli_num_rows($result2) <= 1) {
    $StudentWeekOnline = "0 HOURS";
    $StudentMonthOnline = "0 HOURS";
    $StudentAllTimeOnline = "0 HOURS";
} else {
    $StudentWeekOnline = getWeekHours($data2) . " HOURS" ?? "0 HOURS";
    $StudentMonthOnline = getMonthHours($data2) . " HOURS" ?? "0 HOURS";
    $StudentAllTimeOnline = getAllTimeHours($data2) . " HOURS" ?? "0 HOURS";
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
    <link rel="stylesheet" href="ADMIN_CSS/colour.css">
    <link rel="stylesheet" type="text/css" href="adminPrint.css" media="print">


    <script>
        function back() {
            window.history.back();
        }

        function deleteStudent() {
            if (confirm("Are you sure to delete this student?") == true) {
                let studentID = "<?php echo $studentID ?>";
                let deletion = {
                    "stuID": studentID,
                    "userType": "student"
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
                        window.location.href = "adminStudentUserView.php";
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
                <li id="deleteButtonBox"><button id="deleteButton" onclick="deleteStudent()">DELETE</button></li>
            </ul>
            <div class="mainInfoArea">
                <div class="infoSection row">
                    <div class="userProfileNDetail col-2">
                        <div id="userIconAndName">
                            <img src="/capstone/PROFILE/STUDENT ( PIKER )/<?php echo $data1["ProfilePic"]; ?>"
                                alt="Profile Picture">
                            <p><?php echo $data1["StudentName"] ?></p>
                        </div>

                        <div id="userSmallDetail">
                            <ul>
                                <li id="userForm"><a><?php echo $data1["StudentLevel"] ?></a></li>
                                <li id="emailLabel"><a>EMAIL</a></li>
                                <li id="userEmail"><a><?php echo $data1["StudentEmail"] ?></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="userFurtherDetail col-8 print-page-break">
                        <div class="col-10" id="userStatus" style="margin-bottom: 6em;">
                            <ul id="detailMainLabel">
                                <li class="detailLabel"><a>THIS WEEK</a></li>
                                <li class="detailLabel"><a>THIS MONTH</a></li>
                                <li class="detailLabel"><a>TOTAL TIME</a></li>
                            </ul>

                            <ul id="timeData" style="height: 4.5em;">
                                <li id="weekTimeLabel"><a><?php echo $StudentWeekOnline ?></a></li>
                                <li id="monthTimeLabel"><a><?php echo $StudentMonthOnline ?></a></li>
                                <li id="totalTimeLabel"><a><?php echo $StudentAllTimeOnline ?></a></li>
                            </ul>

                            <ul id="learnStyleArea">
                                <li id="learnStyleLabel"><a>LEARNING STYLE :</a></li>
                                <li id="learnStyleData"><a><?php echo $data1["StudentLearningStyle"] ?></a></li>
                            </ul>

                            <ul id="lastOnlineArea">
                                <li id="lastOnlineLabel"><a>LAST ONLINE :</a></li>
                                <li id="lastOnlineData"><a><?php echo $StudentLastOnline ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </other_stuff>
    </main>
</body>
<script>
    const studentID = "<?php echo $studentID; ?>";
    console.log("Student ID is ", studentID);
</script>

</html>