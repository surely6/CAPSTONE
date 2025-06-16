<?php
session_start();
include('connect.php');
include('font.php');

$_SESSION['user_id'] = 'L001';
if (!isset($_SESSION['instructor_id'])) {
    die("Instructor ID is not set in the session.");
}

$instructorID = $_SESSION['user_id'];
$type = isset($_GET['type']) ? $_GET['type'] : 'all';
?>

<!DOCTYPE html>

<head>
    <link href="style.css" rel="stylesheet">
    <link href="header-format.css" rel="stylesheet">
    <link href="sidebar-format.css" rel="stylesheet">
    <title>INSTRUCTOR</title>
</head>

<body>
    <header>
        <ul>
            <li id="profileDropdown">
                <img src="icon/profile.png" alt="Profile Icon" id="profileIcon"
                    style="width: 50px; height: 50px; cursor: pointer; align-items: center; display: flex; justify-content: center; margin-left: 10px; margin-top: 10px;">
                <div id="profileMenu" class="dropdown-content">
                    <a href="profile.php">My Profile</a>
                    <a href="system_feedback.php">Feedback</a>
                    <a href="#">Summary</a>
                    <a href="#">Logout</a>
                </div>
            </li>
            <li class="options"><a href="">CREATE LEARNING MATERIAL</a></li>
            <li class="options"><a href="">CREATE QUIZ</a></li>
            <li class="options"><a href="">DRAFT</a></li>
            <li id="logo"><a href="">ASSESTIFY</a></li>
        </ul>
        <div class="header-search">
            <form id="search-form" class="search-container" method="GET" action="">
                <input type="text" id="header-search" name="search" placeholder="SEARCH..." autocomplete="off">
                <img src="icon/search.png" alt="Search" width="20px" height="20px" class="search-icon"
                    id="search-button">
            </form>
        </div>
    </header>

    <main>
        <!-- main selection area -->
        <div class="title">
            <h1>
                <?php
                if ($type === 'materials') {
                    echo "MY LEARNING MATERIALS";
                } elseif ($type === 'questions') {
                    echo "MY QUESTIONS";
                } else {
                    echo "MY LIBRARY";
                }
                ?>
            </h1>
        </div>
        <!-- selection -->
        <div id="content"> <!-- grid -->
            <?php
            $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
            $escapedSearch = mysqli_real_escape_string($conn, $searchTerm);

            $query = "";

            if (!empty($searchTerm)) {
                if ($type === 'all') {
                    $query = "
            SELECT material_id AS id, material_title AS title, 'material' AS type, completion_status AS progress
            FROM learning_materials
            WHERE instructor_id = '$instructorID' AND material_title LIKE '%$escapedSearch%'
            UNION
            SELECT quiz_id AS id, quiz_title AS title, 'quiz' AS type, NULL AS progress
            FROM quizzes
            WHERE instructor_id = '$instructorID' AND quiz_title LIKE '%$escapedSearch%'
        ";
                } elseif ($type === 'materials') {
                    $query = "
            SELECT material_id AS id, material_title AS title, 'material' AS type, completion_status AS progress
            FROM learning_materials
            WHERE instructor_id = '$instructorID' AND material_title LIKE '%$escapedSearch%'
        ";
                } elseif ($type === 'questions') {
                    $query = "
            SELECT quiz_id AS id, quiz_title AS title, 'quiz' AS type, NULL AS progress
            FROM quizzes
            WHERE instructor_id = '$instructorID' AND quiz_title LIKE '%$escapedSearch%'
        ";
                }
            } else {
                // Default queries with no search
                if ($type === 'all') {
                    $query = "
            SELECT material_id AS id, material_title AS title, 'material' AS type, completion_status AS progress
            FROM learning_materials
            WHERE instructor_id = '$instructorID'
            UNION
            SELECT quiz_id AS id, quiz_title AS title, 'quiz' AS type, NULL AS progress
            FROM quizzes
            WHERE instructor_id = '$instructorID'
        ";
                } elseif ($type === 'materials') {
                    $query = "
            SELECT material_id AS id, material_title AS title, 'material' AS type, completion_status AS progress
            FROM learning_materials
            WHERE instructor_id = '$instructorID'
        ";
                } elseif ($type === 'questions') {
                    $query = "
            SELECT quiz_id AS id, quiz_title AS title, 'quiz' AS type, NULL AS progress
            FROM quizzes
            WHERE instructor_id = '$instructorID'
        ";
                }
            }

            $result = mysqli_query($conn, $query);

            if (!$result) {
                die("Error fetching data: " . mysqli_error($conn));
            }

            if (mysqli_num_rows($result) === 0 && !empty($searchTerm)) {
                echo "<div class='no-results'>No results found for '<strong>" . htmlspecialchars($searchTerm) . "</strong>'</div>";
            }

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='selection'>";
                echo "<div class='title'>{$row['title']}</div>";

                if ($row['type'] === 'material') {
                    // Get additional material data
                    $materialID = $row['id'];
                    $materialQuery = "SELECT material_learning_type, material_subject FROM learning_materials WHERE material_id = '$materialID'";
                    $materialResult = mysqli_query($conn, $materialQuery);
                    if ($materialData = mysqli_fetch_assoc($materialResult)) {
                        echo "<div class='details'>";
                        echo "<strong>Learning Type:</strong> {$materialData['material_learning_type']}<br>";
                        echo "<strong>Subject:</strong> {$materialData['material_subject']}";
                        echo "</div>";
                    }
                } elseif ($row['type'] === 'quiz') {
                    // Get additional quiz data
                    $quizID = $row['id'];
                    $quizQuery = "SELECT quiz_total_questions, quiz_subject FROM quizzes WHERE quiz_id = '$quizID'";
                    $quizResult = mysqli_query($conn, $quizQuery);
                    if ($quizData = mysqli_fetch_assoc($quizResult)) {
                        echo "<div class='details'>";
                        echo "<strong>Total Questions:</strong> {$quizData['quiz_total_questions']}<br>";
                        echo "<strong>Subject:</strong> {$quizData['quiz_subject']}";
                        echo "</div>";
                    }
                }

                echo "</div>";
            }
            ?>

        </div>
    </main>

    <div id="popupModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modalDetails" class="details"></div>
            <div class="modal-buttons">
                <button><img src="icon/edit.png" alr="Edit">Edit</button>
                <button><img src="icon/summary.png" alr="Summary">Summary</button>
                <button><img src="icon/feedback.png" alr="Feedback">Feedback</button>
            </div>
        </div>
    </div>
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
        //Arrow Up and Down icon for dropdown list
        function ShowDropdown(dropdownID, arrowID) {
            var dropdown = document.getElementById(dropdownID);
            var arrow = document.getElementById(arrowID);
            dropdown.classList.toggle("active");
            if (dropdown.classList.contains("active")) {
                arrow.src = "icon/arrowUp.png";
            } else {
                arrow.src = "icon/arrowDown.png";
            }
        }
        ////////////////////////////////////////////PROFILE
        document.getElementById('profileIcon').addEventListener('click', function (event) {
            event.stopPropagation();
            console.log('Profile icon clicked'); // Debug log
            const profileMenu = document.getElementById('profileMenu');
            profileMenu.classList.toggle('active');
        });

        window.addEventListener('click', function (event) {
            const profileMenu = document.getElementById('profileMenu');
            if (!document.getElementById('profileDropdown').contains(event.target)) {
                console.log('Clicked outside, closing menu'); // Debug log
                profileMenu.classList.remove('active');
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const searchButton = document.getElementById('search-button');
            const searchForm = document.getElementById('search-form');

            // When search icon is clicked, submit the form
            searchButton.addEventListener('click', function () {
                searchForm.submit();
            });

            // Also allow pressing Enter in the search field to submit
            document.getElementById('header-search').addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    searchForm.submit();
                }
            });
        });

        // Get modal elements
        const modal = document.getElementById("popupModal");
        const modalDetails = document.getElementById("modalDetails");
        const closeBtn = document.querySelector(".modal .close");

        // Function to show modal with details
        function showModal(detailsHTML) {
            modalDetails.innerHTML = detailsHTML;
            modal.style.display = "block";
        }

        // Close modal when clicking on the X
        closeBtn.onclick = function () {
            modal.style.display = "none";
        };

        // Close modal if clicking outside the content
        window.onclick = function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };

        // Add click event to selections
        document.querySelectorAll(".selection").forEach(function (card) {
            card.addEventListener("click", function () {
                const details = card.querySelector(".details");
                if (details) {
                    showModal(details.innerHTML);
                }
            });
        });
    </script>
</body>