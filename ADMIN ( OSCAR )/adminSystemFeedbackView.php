<?php
session_start();
include 'font.php';
include("../block.php");

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
    <link rel="stylesheet" href="ADMIN_CSS/adminFilter.css">
    <link rel="stylesheet" href="ADMIN_CSS/adminSystemFeedback.css">
    <link rel="stylesheet" href="ADMIN_CSS/colour.css">

    <script>
        function uncheckOther(checkboxId) {
            const otherCheckboxes = document.querySelectorAll('.searchType');
            const checkedCheckbox = document.getElementById(checkboxId);

            if (checkedCheckbox && checkedCheckbox.checked) {
                otherCheckboxes.forEach(checkbox => {
                    if (checkbox.id !== checkboxId) {
                        checkbox.checked = false;
                    }
                });
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

    <!-- filter -->
    <div style="position: relative;">
        <!-- button to open side bar -->
        <btn id="OpenFilter" onclick="ShowFilter()">
            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-list"
                viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
            </svg>
        </btn>
        <!-- the side bar -->
        <div id='filter'>
            <btn id="CloseFilter" onclick="CloseFilter()">FILTER</btn>
            <input type="text" id="searchName" placeholder="SEARCH...">
            <div class="CheckSection">
                <input type="checkbox" id="instructor" class="searchType" name="instructor" value="instructor"
                    onclick="uncheckOther('instructor')">
                <label for="instructor">INSTRUCTOR</label><br>
                <input type="checkbox" id="student" class="searchType" name="student" value="student"
                    onclick="uncheckOther('student')">
                <label for="student">STUDENT</label>
            </div>
        </div>
    </div>


    <main>
        <div class="feedbackArea">
            <ul class="recentNOldest">
                <li><button class="timeFilter"
                        onclick="generateFeedback('recent'); toggleActiveRec()"><a>RECENT</a></button></li>
                <li><button class="timeFilter"
                        onclick="generateFeedback('oldest'); toggleActiveOld()"><a>OLDEST</a></button></li>
            </ul>
        </div>
    </main>


    <script>
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
            main.style.marginLeft = "15vw";
            main.style.marginRight = "0";
            main.style.width = "80vw";
        }
        // When the user clicks on close btn, close the filter sec
        function CloseFilter() {
            var filter = document.getElementById("filter");
            filter.classList.replace("show", "close");

            var mainList = document.getElementsByTagName("main");

            var main = mainList[0];
            main.style.marginLeft = "5vw";
            main.style.marginRight = "5vw";
            main.style.width = "90vw";
        }

        function ShowDropdown(selected) {
            selected.classList.toggle("active");
        }
    </script>
</body>

<script>

</script>
<script src="ADMIN_JS/adminSystemFeedback.js"></script>
<script>
    let buttons = document.querySelectorAll('.timeFilter');
    function toggleActiveRec() {
        buttons[0].classList.add('active');
        buttons[1].classList.remove('active');
    }
    function toggleActiveOld() {
        buttons[1].classList.add('active');
        buttons[0].classList.remove('active');
    }
    window.onload = generateFeedback('recent');
    window.onload = toggleActiveRec();
</script>

</html>