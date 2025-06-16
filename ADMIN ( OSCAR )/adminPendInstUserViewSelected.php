<?php
session_start();
include "connect.php";
include 'font.php';
include("../block.php");

// write sql to get the id's name, email and image/pdf or something idk
$instructorID = $_GET['instructorID'];

$sql = "SELECT * FROM instructors WHERE instructor_id = '$instructorID';";
$result = mysqli_query($conn, $sql);
$InstructorData = mysqli_fetch_array($result);
$data = [
    "InstructorName" => $InstructorData['instructor_name'],
    "InstructorEmail" => $InstructorData['instructor_email'],
    "InstructorProof" => $InstructorData['instructor_certificate']
];


$src = 'data:image/jpeg;base64,' . base64_encode($data['InstructorProof']);

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

        function acceptInstructor() {
            if (confirm("Are you sure to accept this instructor?") == true) {
                let instructorID = "<?php echo $instructorID ?>";
                let approval = {
                    "insID": instructorID,
                    "status": "1"
                }
                fetch('acceptInstructor.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(approval)
                })
                    .then(response => response.text())
                    .then(after => {
                        window.location.href = "adminPendInstUserView.php";
                    }
                    )
            }
        }

        function rejectInstructor() {
            if (confirm("Are you sure to reject this instructor?") == true) {
                let instructorID = "<?php echo $instructorID ?>";
                let rejection = {
                    "insID": instructorID,
                    "userType": "pend_instructor"
                }
                fetch('rejectAndDeleteUser.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(rejection)
                })
                    .then(response => response.text())
                    .then(after => {
                        window.location.href = "adminPendInstUserView.php";
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
                <li id="acceptButtonBox"><button id="acceptButton" onclick="acceptInstructor()">ACCEPT</button></li>
                <li id="rejectButtonBox"><button id="rejectButton" onclick="rejectInstructor()">REJECT</button></li>
            </ul>
            <div class="mainInfoArea" style="margin-bottom: 3em;">
                <div class="infoSection row">
                    <div class="userProfileNDetail col-2" style="height: 17em;">
                        <div id="userSmallDetail" style="height: 90%; width: 90%; margin-top: .75em;">
                            <ul style="gap: .5em;">
                                <!-- didnt change the id name cause they both share the same style anyways -->
                                <li id="emailLabel"><a>NAME</a></li>
                                <li id="userEmail"><a><?php echo $data['InstructorName'] ?></a></li>
                                <li id="emailLabel"><a>EMAIL</a></li>
                                <li id="userEmail"><a><?php echo $data['InstructorEmail'] ?></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="userFurtherDetail col-8 print-page-break">
                        <div class="col-10" id="userStatus" style="margin-bottom: 6em;">
                            <img src="<?php echo $src; ?>" alt="image"
                                style="width: 90%; height: 90%; border-radius: 10px; margin-top: 1em;">
                        </div>
                    </div>
                </div>


                <div>

                </div>
            </div>

        </other_stuff>
    </main>
</body>

<script>
    const instructorID = "<?php echo $instructorID ?>";
    console.log(instructorID);
</script>

</html>