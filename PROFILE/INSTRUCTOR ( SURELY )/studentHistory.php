<?php
session_start();
include('connect.php');
include('font.php');
include("bootstrapFile.html");
include("../STUDENT ( PIKER )/header.php");
include("../../block.php");


$studentId = $_SESSION['user_id'];


$studentStyleSql = "SELECT student_learning_style FROM students WHERE student_id = '$studentId'";
$studentStyleResult = mysqli_query($conn, $studentStyleSql);
$studentStyleRow = mysqli_fetch_assoc($studentStyleResult);
$studentLearningStyle = $studentStyleRow['student_learning_style'] ?? '';

// all history tab
$allHistorySql = "
    SELECT 
        p.material_id AS id,
        m.material_title AS title,
        m.material_subject AS subject,
        'material' AS type,
        p.progress AS progress_data,
        (
            CASE 
                WHEN (SELECT COUNT(*) FROM learning_material_parts WHERE learning_material_parts.material_id = m.material_id) > 0 
                THEN FLOOR(((LENGTH(p.progress) - LENGTH(REPLACE(p.progress, ',', '')) + 1) / 
                    (SELECT COUNT(*) FROM learning_material_parts WHERE learning_material_parts.material_id = m.material_id)) * 100) 
                ELSE 0 
            END
        ) AS status,
        p.last_datetime AS activity_date,
        i.instructor_name AS instructor_name
    FROM progress p
    LEFT JOIN learning_materials m ON p.material_id = m.material_id
    LEFT JOIN instructors i ON i.instructor_id = m.instructor_id
    WHERE p.student_id = '$studentId' AND p.progress > 0
          AND m.material_learning_type = '$studentLearningStyle'


    UNION ALL

    SELECT 
        a.quiz_id AS id,
        q.quiz_title AS title,
        q.quiz_subject AS subject,
        'quiz' AS type,
        a.score AS progress_data,
        ROUND((a.score / q.quiz_total_questions) * 100) AS status,
        a.date_of_attempt AS activity_date,
        i.instructor_name AS instructor_name
    FROM attempts a
    LEFT JOIN quizzes q ON a.quiz_id = q.quiz_id
    LEFT JOIN instructors i ON i.instructor_id = q.instructor_id
    WHERE a.student_id = '$studentId'

    ORDER BY activity_date DESC";
$allHistory = mysqli_query($conn, $allHistorySql);


// learning materials
$materialsSql = "SELECT 
    p.material_id AS id, 
    p.progress, 
    m.material_title, 
    instructors.instructor_name, 
    m.material_subject, 
    (SELECT COUNT(*) FROM learning_material_parts WHERE learning_material_parts.material_id = m.material_id) AS total_parts,
    CASE 
        WHEN (SELECT COUNT(*) FROM learning_material_parts WHERE learning_material_parts.material_id = m.material_id) > 0 
        THEN FLOOR(((LENGTH(p.progress) - LENGTH(REPLACE(p.progress, ',', '')) + 1) / 
            (SELECT COUNT(*) FROM learning_material_parts WHERE learning_material_parts.material_id = m.material_id)) * 100) 
        ELSE 0 
    END AS progress_percentage
    FROM progress p
    JOIN learning_materials m ON p.material_id = m.material_id
    JOIN instructors ON m.instructor_id = instructors.instructor_id
    WHERE p.student_id = '$studentId' AND p.progress > 0
          AND m.material_learning_type = '$studentLearningStyle'

    ORDER BY p.last_datetime DESC";
$materials = mysqli_query($conn, $materialsSql);

// quiz attempts - Updated with score calculation
$quizzesSql = "SELECT 
                a.attempt_id AS id, 
                a.score, 
                q.quiz_title, 
                q.quiz_subject, 
                q.quiz_total_questions,
                ROUND((a.score / q.quiz_total_questions) * 100) AS score_percentage,
                instructors.instructor_name
            FROM attempts a
            JOIN quizzes q ON a.quiz_id = q.quiz_id
            JOIN instructors ON q.instructor_id = instructors.instructor_id
            WHERE a.student_id = '$studentId'
            ORDER BY a.date_of_attempt DESC";
$quizzes = mysqli_query($conn, $quizzesSql);

?>

<!DOCTYPE html>

<head>
    <link href="style.css" rel="stylesheet">
    <link href="header-format.css" rel="stylesheet">
    <link href="sidebar-format.css" rel="stylesheet">
    <link href="studentHistory.css" rel="stylesheet">
    <title>STUDENT HISTORY</title>
</head>

<body>
    <!-- <header>
        <ul>
            <li id="profile"><a href="">PROFILE</a></li>
            <li class="options"><a href="">LEARNING MATERIAL</a></li>
            <li class="options"><a href="">QUIZ</a></li>
            <li id="logo"><a href="">LOGO</a></li>
        </ul>
    </header> -->

    <div class="container mt-4">
        <h1 class="text-center" style="color: #005c0a;">My History</h1>
        <ul class="nav nav-tabs mt-5" id="historyTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button"
                    role="tab" aria-controls="all" aria-selected="true">All</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials"
                    type="button" role="tab" aria-controls="materials" aria-selected="false">Materials</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="quizzes-tab" data-bs-toggle="tab" data-bs-target="#quizzes" type="button"
                    role="tab" aria-controls="quizzes" aria-selected="false">Quizzes</button>
            </li>
        </ul>
        <div class="tab-content mt-3" id="historyTabsContent">
            <!--all tab -->
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                <input type="text" class="form-control mb-5 mt-4" id="searchAll" placeholder="Search All">
                <div class="row" id="allTab">
                    <?php while ($row = mysqli_fetch_assoc($allHistory)) { ?>
                        <div class="col-md-4 mb-3">
                            <div class="card" data-id="<?php echo $row['id']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php echo $row['title'] ?>
                                    </h5>
                                    <hr>
                                    <p class="card-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-person" viewBox="0 0 16 16">
                                            <path
                                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z" />
                                        </svg>
                                        <i><?php echo $row['instructor_name'] ?? 'N/A'; ?></i>
                                    </p>

                                    <p class="card-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-book" viewBox="0 0 16 16">
                                            <path
                                                d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746a2 2 0 0 1-.593 1.508c-.387.408-.975.652-1.703.75.62.173 1.27.26 1.94.26.713 0 1.42-.114 2.11-.33.386-.13.661-.31.952-.59.28-.28.423-.66.423-1.08V2.687c0-.568-.124-1.198-.404-1.738C10.763.067 9.47 0 8.293 0 5.925 0 4.056.59 2.5 1.5 1.347 2.302 0 3.545 0 5.022v9.044c0 .324.065.636.187.926.122.29.295.544.52.75.224.206.495.358.786.45.58.183 1.243.26 1.943.26.703 0 1.35-.084 1.943-.26.29-.092.562-.244.786-.45.225-.206.398-.46.52-.75.122-.29.187-.602.187-.926V2.687c0-.796-.124-1.403-.357-1.822.253.14.508.263.774.368z" />
                                        </svg>
                                        <?php echo $row['subject'] ?>
                                    </p>
                                    <p class="card-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-tag" viewBox="0 0 16 16">
                                            <path
                                                d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0" />
                                            <path
                                                d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1m0 5.586 7 7L13.586 9l-7-7H2z" />
                                        </svg>
                                        <?php echo $row['type'] ?>
                                    </p>

                                    <?php if ($row['type'] == 'quiz') { ?>
                                        <p class="score-display">
                                            Score: <?php echo $row['status']; ?>%
                                        </p>
                                    <?php } else { ?>
                                        <div class="progress-text">
                                            PROGRESS<span><?php echo $row['status']; ?>%</span>
                                        </div>
                                        <div class="progress-materials">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: <?php echo $row['status']; ?>%;"></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!--materials tab-->
            <div class="tab-pane fade" id="materials" role="tabpanel" aria-labelledby="materials-tab">
                <input type="text" class="form-control mb-3" id="searchMaterials" placeholder="Search Materials">
                <div class="row" id="materialTab">
                    <?php while ($row = mysqli_fetch_assoc($materials)) { ?>
                        <div class="col-md-4 mb-3">
                            <div class="card" data-id="<?php echo $row['id']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $row['material_title']; ?></h5>
                                    <hr>
                                    <p class="card-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-person" viewBox="0 0 16 16">
                                            <path
                                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z" />
                                        </svg><i> <?php echo $row['instructor_name']; ?></i>
                                    </p>
                                    <p class="card-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-book" viewBox="0 0 16 16">
                                            <path
                                                d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746a2 2 0 0 1-.593 1.508c-.387.408-.975.652-1.703.75.62.173 1.27.26 1.94.26.713 0 1.42-.114 2.11-.33.386-.13.661-.31.952-.59.28-.28.423-.66.423-1.08V2.687c0-.568-.124-1.198-.404-1.738C10.763.067 9.47 0 8.293 0 5.925 0 4.056.59 2.5 1.5 1.347 2.302 0 3.545 0 5.022v9.044c0 .324.065.636.187.926.122.29.295.544.52.75.224.206.495.358.786.45.58.183 1.243.26 1.943.26.703 0 1.35-.084 1.943-.26.29-.092.562-.244.786-.45.225-.206.398-.46.52-.75.122-.29.187-.602.187-.926V2.687c0-.796-.124-1.403-.357-1.822.253.14.508.263.774.368z" />
                                        </svg> <?php echo $row['material_subject']; ?>
                                    </p>

                                    <div class="progress-text">
                                        PROGRESS<span><?php echo $row['progress_percentage']; ?>%</span>
                                    </div>
                                    <div class="progress-materials">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: <?php echo $row['progress_percentage']; ?>%;"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!--quizzes tab-->
            <div class="tab-pane fade" id="quizzes" role="tabpanel" aria-labelledby="quizzes-tab">
                <input type="text" class="form-control mb-3" id="searchQuizzes" placeholder="Search Quizzes">
                <div class="row" id="quizTab">
                    <?php while ($row = mysqli_fetch_assoc($quizzes)) { ?>
                        <div class="col-md-4 mb-3">
                            <div class="card" data-id="<?php echo $row['id']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $row['quiz_title']; ?></h5>
                                    <hr>
                                    <p class="card-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-person" viewBox="0 0 16 16">
                                            <path
                                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z" />
                                        </svg><i> <?php echo $row['instructor_name']; ?></i>
                                    </p>
                                    <p class="card-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-book" viewBox="0 0 16 16">
                                            <path
                                                d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746a2 2 0 0 1-.593 1.508c-.387.408-.975.652-1.703.75.62.173 1.27.26 1.94.26.713 0 1.42-.114 2.11-.33.386-.13.661-.31.952-.59.28-.28.423-.66.423-1.08V2.687c0-.568-.124-1.198-.404-1.738C10.763.067 9.47 0 8.293 0 5.925 0 4.056.59 2.5 1.5 1.347 2.302 0 3.545 0 5.022v9.044c0 .324.065.636.187.926.122.29.295.544.52.75.224.206.495.358.786.45.58.183 1.243.26 1.943.26.703 0 1.35-.084 1.943-.26.29-.092.562-.244.786-.45.225-.206.398-.46.52-.75.122-.29.187-.602.187-.926V2.687c0-.796-.124-1.403-.357-1.822.253.14.508.263.774.368z" />
                                        </svg> <?php echo $row['quiz_subject']; ?>
                                    </p>

                                    <div class="score-details mt-3">
                                        <p class="score-display">
                                            Score: <?php echo $row['score_percentage']; ?>%
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>


    <?php include("../../footer.php"); ?>


    <script>
        //search
        document.getElementById('searchAll').addEventListener('input', function () {
            filterCards('allTab', this.value);
        });
        document.getElementById('searchMaterials').addEventListener('input', function () {
            filterCards('materialTab', this.value);
        });
        document.getElementById('searchQuizzes').addEventListener('input', function () {
            filterCards('quizTab', this.value);
        });

        function filterCards(containerId, input) {
            const container = document.getElementById(containerId);
            const cards = Array.from(container.getElementsByClassName('col-md-4'));

            input = input.toLowerCase().trim();

            cards.forEach(cardCol => {
                const card = cardCol.querySelector('.card');
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const subject = card.querySelector('.card-text:nth-of-type(2)').textContent.toLowerCase();
                const author = card.querySelector('.card-text:nth-of-type(1)').textContent.toLowerCase();

                const matches = title.includes(input) ||
                    subject.includes(input) ||
                    author.includes(input) ||
                    input === '';

                cardCol.style.display = matches ? '' : 'none';
            });
        }

        //redirect to page
        document.addEventListener('DOMContentLoaded', function () {
            function handleCardClick(event) {
                const card = event.target.closest('.card');
                if (!card) return;

                // Prevent click if clicking on a button or interactive element
                if (event.target.closest('button, .bookmarkBtn, a')) return;

                // Get card type and id
                let cardType = 'material';
                let contentId = null;

                // For All tab
                if (card.closest('#allTab')) {
                    // Get type from the type <p> (last .card-text)
                    const cardTexts = card.querySelectorAll('.card-text');
                    if (cardTexts.length > 0) {
                        const typeText = cardTexts[cardTexts.length - 1].textContent.toLowerCase();
                        cardType = typeText.includes('quiz') ? 'quiz' : 'material';
                    }
                    // Get id from data attribute
                    contentId = card.getAttribute('data-id');
                }
                // For Materials tab
                else if (card.closest('#materialTab')) {
                    cardType = 'material';
                    contentId = card.getAttribute('data-id');
                }
                // For Quizzes tab
                else if (card.closest('#quizTab')) {
                    cardType = 'quiz';
                    contentId = card.getAttribute('data-id');
                }

                if (!contentId) return;

                // Redirect based on type
                if (cardType === 'material') {
                    window.location.href = `/capstone/STUDENT ( LING )/stuLearningMaterial.php?material_id=${contentId}`;
                } else {
                    window.location.href = `/capstone/STUDENT ( LING )/stuAccessQuiz.php?quiz_id=${contentId}`;
                }
            }

            document.getElementById('allTab').addEventListener('click', handleCardClick);
            document.getElementById('materialTab').addEventListener('click', handleCardClick);
            document.getElementById('quizTab').addEventListener('click', handleCardClick);
        });

        document.addEventListener('DOMContentLoaded', function () {
            const url = new URLSearchParams(window.location.search);
            const tabSelection = url.get('tab');

            if (tabSelection) {
                const selectedTab = document.getElementById(`${tabSelection}-tab`);
                if (selectedTab) {
                    const tabTrigger = new bootstrap.Tab(selectedTab);
                    tabTrigger.show();
                }
            }
        });
    </script>
</body>