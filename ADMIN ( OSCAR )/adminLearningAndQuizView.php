<?php
session_start();
include 'connect.php';
include 'adminObtainLearningAndQuiz.php';
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
    <link rel="stylesheet" href="ADMIN_CSS/adminlearningAndQuizView.css">
    <link rel="stylesheet" href="ADMIN_CSS/colour.css">
    <link rel="stylesheet" type="text/css" href="adminPrint.css" media="print">

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
                <input type="checkbox" id="title" class="searchType" name="title" value="title"
                    onclick="uncheckOther('title')">
                <label for="title"> MATERIAL</label><br>
                <input type="checkbox" id="instructor" class="searchType" name="instructor" value="instructor"
                    onclick="uncheckOther('instructor')">
                <label for="instructor"> INSTRUCTOR</label>
            </div>

            <div class="CheckSection">
                <!-- insert dropdown list id into function -->
                <h3 onclick="ShowDropdown('forms')">FORM</h3>
                <div class="check-dropdown-list" id="forms">
                    <label for="form"> <input type="checkbox" id="form" class="formCheckbox" name="material_forms"
                            value="1"> FORM 1</label><br>
                    <label for="form"> <input type="checkbox" id="form" class="formCheckbox" name="material_forms"
                            value="2"> FORM 2</label><br>
                    <label for="form"> <input type="checkbox" id="form" class="formCheckbox" name="material_forms"
                            value="3"> FORM 3</label><br>
                    <label for="form"> <input type="checkbox" id="form" class="formCheckbox" name="material_forms"
                            value="4"> FORM 4</label><br>
                    <label for="form"> <input type="checkbox" id="form" class="formCheckbox" name="material_forms"
                            value="5"> FORM 5</label>
                </div>
            </div>

            <div class="CheckSection">
                <!-- insert dropdown list id into function -->
                <h3 onclick="ShowDropdown('subjects')">SUBJECTS</h3>
                <div class="check-dropdown-list" id="subjects">
                    <label for="english"><input type="checkbox" id="english" class="subjectType" name="material_subject"
                            value="english"> ENGLISH</label><br>
                    <label for="malay"><input type="checkbox" id="malay" class="subjectType" name="material_subject"
                            value="malay"> MALAY</label><br>
                    <label for="mathematics"><input type="checkbox" id="mathematics" class="subjectType"
                            name="material_subject" value="mathematics"> MATHEMATICS</label><br>
                    <label for="science"><input type="checkbox" id="science" class="subjectType" name="material_subject"
                            value="science"> SCIENCE</label><br>
                    <label for="history"><input type="checkbox" id="history" class="subjectType" name="material_subject"
                            value="history"> HISTORY</label><br>
                    <label for="geography"><input type="checkbox" id="geography" class="subjectType"
                            name="material_subject" value="geography"> GEOGRAPHY</label><br>
                    <label for="accounting"><input type="checkbox" id="accounting" class="subjectType"
                            name="material_subject" value="accounting"> ACCOUNTING</label><br>
                    <label for="economy"><input type="checkbox" id="economy" class="subjectType" name="material_subject"
                            value="economy"> ECONOMY</label><br>
                    <label for="business"><input type="checkbox" id="business" class="subjectType"
                            name="material_subject" value="business"> BUSINESS</label><br>
                    <label for="additional_mathematics"><input type="checkbox" id="additional_mathematics"
                            class="subjectType" name="additional_mathematics" value="additional mathematic"> ADDITIONAL
                        MATHEMATICS</label><br>
                    <label for="physics"><input type="checkbox" id="physics" class="subjectType" name="material_subject"
                            value="physics"> PHYSICS</label><br>
                    <label for="chemistry"><input type="checkbox" id="chemistry" class="subjectType"
                            name="material_subject" value="chemistry"> CHEMISTRY</label><br>
                    <label for="biology"><input type="checkbox" id="biology" class="subjectType" name="material_subject"
                            value="biology"> BIOLOGY</label><br>
                </div>
            </div>

            <div class="CheckSection learning_style">
                <!-- insert dropdown list id into function -->
                <h3 onclick="ShowDropdown('learning-style')">LEARNING <br>STYLE</h3>
                <div class="check-dropdown-list" id="learning-style">
                    <label for="learning-style"><input type="checkbox" id="read_write" class="learningStyle"
                            name="material_learning_type" value="read_write"> READ & WRITE</label><br>
                    <label for="visual"><input type="checkbox" id="visual" class="learningStyle"
                            name="material_learning_type" value="visual"> VISUAL</label><br>
                    <label for="audio"><input type="checkbox" id="audio" class="learningStyle"
                            name="material_learning_type" value="audio"> AUDIO</label>
                </div>
            </div>
        </div>
    </div>

    <main>
        <div id="learnAndQuizButtonSwitch">
            <div class="button" onclick="setLearning()"><span>LEARNING MATERIAL</span></div>
            <div class="button" onclick="setQuiz()"><span>QUIZ</span></div>
        </div>

        <div class="Content" id="areaOfInfo">

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
            console.log(selected);
            document.getElementById(selected).classList.toggle("active");
        }
    </script>
</body>

<script src="ADMIN_JS/adminLearningAndQuizView.js"></script>
<script>
    let buttons = document.querySelectorAll('.button span');
    let learningMaterialDetails = <?= json_encode($learningMaterialDetails) ?>;
    let quizDetails = <?= json_encode($quizDetails) ?>;
    window.onload = setLearning();

    function setLearning() {
        currentDisplay = "learningMaterial";
        displayLearningMaterial(learningMaterialDetails);
        buttons[0].style.borderBottom = ('4px #D9D9D9 solid');
        buttons[1].style.borderStyle = ('none');
    }
    function setQuiz() {
        currentDisplay = "quiz";
        displayQuiz(quizDetails);
        buttons[0].style.borderStyle = ('none');
        buttons[1].style.borderBottom = ('4px #D9D9D9 solid');
    }
</script>

</html>