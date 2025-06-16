<?php
# include('connect.php');
include('font.php');
?>
<!DOCTYPE html>

<head>
    <link href="style.css" rel="stylesheet">
    <link href="header-format.css" rel="stylesheet">
    <link href="sidebar-format.css" rel="stylesheet">
    <!-- <title>first page</title> -->
</head>

<body>
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
    <header>
        <ul>
            <li id="profileDropdown">
                <img src="../../INSTRUCTOR ( CHOW )/RESOURCES/profile.png" alt="Profile Icon" id="profileIcon"
                    style="width: 50px; height: 50px; cursor: pointer; align-items: center; display: flex; justify-content: center; margin-left: 10px; margin-top: 10px;position:relaive;">
                <div id="profileMenu" class="dropdown-content" style="display: none;">
                    <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/profile.php">My Profile</a>
                    <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/system_feedback.php">Feedback</a>
                    <!-- <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/studentBookmark.php">History</a> -->
                    <a href="/capstone/logout.php">Logout</a>
                </div>
            </li>
            <li class="options"><a href="/capstone/INSTRUCTOR ( CHOW )/Learning Material View.php">LEARNING MATERIAL</a>
            </li>
            <li class="options"><a href="/capstone/INSTRUCTOR ( CHOW )/Quiz View.php">QUIZ</a></li>
            <li id="logo" style="margin-left: 65px;"><a
                    href="/capstone/INSTRUCTOR ( CHOW )/Learning Material View.php">ASSESTIFY</a></li>
        </ul>
    </header>
    <main>
        <!-- main selection area
        <div class="title">
            <h1>JUST 4 U</h1>
        </div> -->
        <!-- selection -->
        <!-- <div id="content"> grid -->
        <!-- <div class="selection"> options -->
        <!-- <div class="title">TITLE</div>
                <div class="progress">
                    <div id="show-progress">PROGRESS<div>19%</div></div>
                    <div class="progress-bar">
                        <div style="width: 19%;"></div> -->
        <!-- </div>
                </div>
            </div> 
        </div> -->
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

        document.querySelector('#profileIcon').addEventListener("click", function () {
            let profile = document.querySelector('#profileMenu');
            if (profile.style.display == "none") {
                profile.style.display = "block";
            } else {
                profile.style.display = "none";
            }
        });
    </script>
</body>