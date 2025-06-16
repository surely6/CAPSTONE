<?php
session_start();
include('connect.php');
include('font.php');
include("../block.php");


if (!isset($_SESSION)) {
    die("Instructor ID is not set in the session.");
}

$instructorID = $_SESSION['user_id'];

$unfinished = [];
$finished = [];

$unfinsihedQUERY = "SELECT `material_id`,`material_title`,`material_level`,`material_subject`,`material_chapter`,`material_learning_type`,`date_made`
FROM `learning_materials` WHERE `instructor_id` = '$instructorID' AND `completion_status` = '0'";
$unfinishedSQL = mysqli_query($conn, $unfinsihedQUERY);

if (mysqli_num_rows($unfinishedSQL) > 0) {
    $unfinished = mysqli_fetch_all($unfinishedSQL, MYSQLI_ASSOC);
}

$finishedQUERY = "SELECT `material_id`,`material_title`,`material_level`,`material_subject`,`material_chapter`,`material_learning_type`,`date_made`
FROM `learning_materials` WHERE `instructor_id` = '$instructorID' AND `completion_status` = '1'";
$finishedSQL = mysqli_query($conn, $finishedQUERY);

if (mysqli_num_rows($finishedSQL) > 0) {
    $finished = mysqli_fetch_all($finishedSQL, MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="./RESOURCES/CSS/header-format.css" rel="stylesheet">
    <link href="./RESOURCES/CSS/sidebar-format.css" rel="stylesheet">
    <link href="./RESOURCES/CSS/colors.css" rel="stylesheet">
    <link href="./RESOURCES/CSS/view material.css" rel="stylesheet">
    <title>view material</title>
</head>

<style>
    main {
        margin-bottom: 80px;
    }
</style>

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
                        <!-- <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/studentHistory.php">History</a> -->
                        <a href="/capstone/logout.php">Logout</a>
                    </div>
                </li>
                <li class="options"><a href="/capstone/INSTRUCTOR ( CHOW )/Learning Material View.php">LEARNING
                        MATERIAL</a></li>
                <li class="options"><a href="/capstone/INSTRUCTOR ( CHOW )/Quiz View.php">QUIZ</a></li>
                <li id="logo"><a>ASSESTIFY</a></li>
            </ul>
        </div>
        <div id="secondary-header" style="justify-content: right;">
            <button id="create" onclick="CreateMaterial()">CREATE MATERIAL</button>
        </div>
    </header>
    <div style="position: relative;">
        <!-- button to open side bar  -->
        <btn id="OpenFilter" onclick="ShowFilter()"><i class="bi bi-list"
                style="cursor: pointer; color: var(--light-grey);"></i></btn>
        <!-- the side bar -->
        <div id='filter'>
            <btn id="CloseFilter" onclick="CloseFilter()"><i class="bi bi-funnel-fill"></i>FILTER</btn>
            <div id="search-area">
                <input type="text" placeholder="SEARCH..." id="search">
                <i class="bi bi-search" onclick="search()"></i>
            </div>
            <div class="CheckSection">
                <!-- insert dropdown list id into function -->
                <h3 onclick="ShowDropdown('learning-style')">LEARNING <br>STYLE</h3>
                <div class="check-dropdown-list" id="learning-style">
                    <label for="learning-style">
                        <input type="checkbox" id="read/write" name="material_learning_type" value="read_write"> READ &
                        WRITE</label>
                    <label for="visual"> <input type="checkbox" id="visual" name="material_learning_type"
                            value="visual"> VISUAL</label>
                    <label for="audio"> <input type="checkbox" id="audio" name="material_learning_type" value="audio">
                        AUDIO</label>
                </div>
            </div>
            <div class="CheckSection">
                <!-- insert dropdown list id into function -->
                <h3 onclick="ShowDropdown('forms')">FORM</h3>
                <div class="check-dropdown-list" id="forms">
                    <label for="form"> <input type="checkbox" id="form" name="material_level" value="1"> FORM 1</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_level" value="2"> FORM 2</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_level" value="3"> FORM 3</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_level" value="4"> FORM 4</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_level" value="5"> FORM 5</label>
                </div>
            </div>
            <div class="CheckSection">
                <!-- insert dropdown list id into function -->
                <h3 onclick="ShowDropdown('subjects')">SUBJECTS</h3>
                <div class="check-dropdown-list" id="subjects">
                    <label for="english"> <input type="checkbox" id="english" name="material_subject" value="english">
                        ENGLISH</label>
                    <label for="malay"> <input type="checkbox" id="malay" name="material_subject" value="malay">
                        MALAY</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_subject" value="mathematics">
                        MATHEMATICS</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_subject" value="science">
                        SCIENCE</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_subject" value="history">
                        HISTORY</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_subject" value="geography">
                        GEOGRAPHY</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_subject" value="accounting">
                        ACCOUNTING</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_subject" value="economy">
                        ECONOMY</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_subject" value="business">
                        BUSINESS</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_subject"
                            value="additional mathematic"> ADDITIONAL MATHEMATICS</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_subject" value="physics">
                        PHYSICS</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_subject" value="chemistry">
                        CHEMISTRY</label>
                    <label for="form"> <input type="checkbox" id="form" name="material_subject" value="biology">
                        BIOLOGY</label>
                </div>
            </div>
        </div>
    </div>

    <main>
        <!-- main selection area -->

        <div class="title">
            <h1>UNFINISHED</h1>
        </div>
        <!-- selection -->
        <div class="Content" id="unfinished">
            <!-- grid -->
            <button id="previous" onclick="previous()"><span>
                    << /span></button>
            <button id="next" onclick="next()"><span>></span></button>
        </div>

        <br><br><br>

        <!-- OSCAR THIS IS THE TITLE -->
        <div class="title">
            <h1>CREATED</h1>
        </div>
        <!-- OSCAR THIS IS THE CONTENT / CONTAINER FOR THE GRID DISPLAY-->
        <div class="Content" id="finished">
            <button id="previousFinished" onclick="previousFinished(finished)"><span>
                    << /span></button>
            <button id="nextFinished" onclick="nextFinished(finished)"><span>></span></button>
        </div>

    </main>
    <style>
        footer {
            padding: 1.5rem 4rem 4rem 2.5rem !important;
            background-color: var(--green) !important;
        }
    </style>
    <?php include("../footer.php"); ?>

    <script>
        let unfinished = <?php echo json_encode($unfinished) ?>;
        console.log(unfinished);
        let finished = <?php echo json_encode($finished) ?>;
        console.log(finished);
    </script>
    <script src="RESOURCES/JAVA/View Material.js"></script>
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

        function EditMaterial(id) {
            document.cookie = "ID = " + id + ";"
            document.cookie = "ORIGIN= Learning Material View.php;"
            window.location.href = "Learning Material Edit.php";
        }

        function SummaryMaterial(id) {
            document.cookie = "ID = " + id + ";"
            window.location.href = "Learning Material Summary.php";
        }

        function CreateMaterial() {
            window.location.href = "Learning Material Creation.php";
        }

        // When the user clicks on show btn, open the filter sec
        function ShowFilter() {
            var filter = document.getElementById("filter");
            if (filter.className == "close") {
                filter.classList.replace("close", "show");
            } else {
                filter.classList.add("show");
            }

            var mainList = document.getElementsByTagName("main");
            var main = mainList[0];
            main.style.marginLeft = "18vw";
        }
        // When the user clicks on close btn, close the filter sec
        function CloseFilter() {
            var filter = document.getElementById("filter");
            filter.classList.replace("show", "close");

            var mainList = document.getElementsByTagName("main");
            var main = mainList[0];
            main.style.marginLeft = "8vw";
            main.style.marginRight = "5vw";
        }

        function ShowDropdown(selected) {
            console.log(selected);
            document.getElementById(selected).classList.toggle("active");
        }
    </script>
</body>