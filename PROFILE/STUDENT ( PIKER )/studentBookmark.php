<?php
session_start();
include("connection.php");
include("header.php");
include("bootstrapFile.html");
include("../../block.php");


$studentId = $_SESSION['user_id'];


$studentStyleSql = "SELECT student_learning_style FROM students WHERE student_id = '$studentId'";
$studentStyleResult = mysqli_query($conn, $studentStyleSql);
$studentStyleRow = mysqli_fetch_assoc($studentStyleResult);
$studentLearningStyle = $studentStyleRow['student_learning_style'] ?? '';

//all tab
$allBookmarksSql = "SELECT bookmarks.*, 
                        learning_materials.material_title AS material_title, 
                        learning_materials.material_subject AS material_subject, 
                        quizzes.quiz_title AS quiz_title, 
                        quizzes.quiz_subject AS quiz_subject, 
                        instructors.instructor_name,
                        CASE 
                            WHEN bookmarks.material_id IS NOT NULL THEN 1 
                            WHEN bookmarks.quiz_id IS NOT NULL THEN 1 
                            ELSE 0 
                        END AS is_bookmarked
                    FROM bookmarks
                    LEFT JOIN learning_materials 
                        ON bookmarks.material_id = learning_materials.material_id
                        AND learning_materials.material_learning_type = '$studentLearningStyle'
                    LEFT JOIN quizzes 
                        ON bookmarks.quiz_id = quizzes.quiz_id
                    LEFT JOIN instructors 
                        ON instructors.instructor_id = COALESCE(learning_materials.instructor_id, quizzes.instructor_id)
                    WHERE student_id = '$studentId'";
$allBookmarks = mysqli_query($conn, $allBookmarksSql);

//material tab
$materialsSql = "SELECT bookmarks.*, learning_materials.material_title, learning_materials.material_subject, instructors.instructor_name, 
                CASE 
                    WHEN bookmarks.material_id IS NOT NULL THEN 1 
                    ELSE 0 
                END AS is_bookmarked
            FROM bookmarks
            INNER JOIN learning_materials ON bookmarks.material_id = learning_materials.material_id 
            INNER JOIN instructors ON instructors.instructor_id = learning_materials.instructor_id 
            WHERE student_id = '$studentId' 
                AND bookmarks.quiz_id IS NULL
                AND learning_materials.material_learning_type = '$studentLearningStyle'";
$materials = mysqli_query($conn, $materialsSql);

//quiz tab
$quizzesSql = "SELECT bookmarks.*, quizzes.quiz_title, quizzes.quiz_subject, instructors.instructor_name, 
                CASE 
                    WHEN bookmarks.quiz_id IS NOT NULL THEN 1 
                    ELSE 0 
                END AS is_bookmarked
            FROM bookmarks
            INNER JOIN quizzes ON bookmarks.quiz_id = quizzes.quiz_id 
            INNER JOIN instructors ON instructors.instructor_id = quizzes.instructor_id 
            WHERE student_id = '$studentId' AND bookmarks.material_id IS NULL";
$quizzes = mysqli_query($conn, $quizzesSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookmark</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>
    body {
        background-color: var(--grey);
        font-family: "inder";
    }

    .nav-link {
        color: var(--bs-green);
    }

    .form-control {
        background: lightgrey;
    }

    .nav-tabs .nav-link.active {
        background-color: var(--light-green);
        color: black;
    }

    .nav-tabs .nav-link:hover:not(.active) {
        background-color: #e8f5e9;
    }

    .nav-tabs {
        border-bottom-color: #28a745;
        flex-wrap: wrap;
        font-size: larger;
    }

    @media (max-width: 576px) {
        .nav-tabs .nav-item {
            width: 100%;
            margin-bottom: 0.25rem;
        }

        .nav-tabs .nav-link {
            border-radius: 0.25rem;
            text-align: center;
        }
    }

    .card {
        background: var(--dark-grey);
        color: white;
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    .card-body {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: white;
        margin-bottom: 0.75rem;
    }

    .card-text {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 0.75rem;
    }

    .bookmarkBtn {
        margin-top: 1rem;
        display: inline-block;
        color: var(--light-green);
        font-weight: bold;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .bookmarkBtn:hover {
        color: var(--green);
    }
</style>

<body>
    <div class="container mt-4">
        <h1 class="text-center" style="color: #005c0a;">Bookmarks</h1>
        <ul class="nav nav-tabs mt-5" id="bookmarkTabs" role="tablist">
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
        <div class="tab-content mt-3" id="bookmarkTabsContent">
            <!--all tab -->
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                <input type="text" class="form-control mb-5 mt-4" id="searchAll" placeholder="Search All">
                <div class="row" id="allTab">
                    <?php while ($row = mysqli_fetch_assoc($allBookmarks)) {
                        //only show card if material_title or quiz_title is not empty
                        if (
                            (!empty($row['material_title']) && $row['material_title'] !== null) ||
                            (!empty($row['quiz_title']) && $row['quiz_title'] !== null)
                        ) {
                            ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <?php echo $row['material_title'] ?? $row['quiz_title']; ?>
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
                                            <?php echo $row['material_subject'] ?? $row['quiz_subject']; ?>
                                        </p>
                                        <p class="card-text">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                class="bi bi-tag" viewBox="0 0 16 16">
                                                <path
                                                    d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0" />
                                                <path
                                                    d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1m0 5.586 7 7L13.586 9l-7-7H2z" />
                                            </svg>
                                            <?php echo $row['material_id'] ? 'Material' : 'Quiz'; ?>
                                        </p>
                                        <a class="bookmarkBtn <?php echo $row['is_bookmarked'] ? 'bookmarked' : ''; ?>"
                                            href="#;" onclick="toggleBookmark(
                                            this, 
                                            '<?php echo $row['material_id'] ?? $row['quiz_id']; ?>', 
                                            '<?php echo $studentId; ?>', 
                                            '<?php echo $row['material_id'] ? 'material' : 'quiz'; ?>'
                                        )">
                                            <?php if ($row['is_bookmarked']): ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                                    class="bi bi-bookmark-fill" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2" />
                                                </svg> Unmark
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } // end if
                    } // end while
                    ?>
                </div>
            </div>
            <!--materials tab-->
            <div class="tab-pane fade" id="materials" role="tabpanel" aria-labelledby="materials-tab">
                <input type="text" class="form-control mb-3" id="searchMaterials" placeholder="Search Materials">
                <div class="row" id="materialTab">
                    <?php while ($row = mysqli_fetch_assoc($materials)) { ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
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
                                    <a class="bookmarkBtn <?php echo $row['is_bookmarked'] ? 'bookmarked' : ''; ?>"
                                        href="#;"
                                        onclick="toggleBookmark(this, '<?php echo $row['material_id']; ?>', '<?php echo $studentId; ?>')">
                                        <?php if ($row['is_bookmarked']): ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                                class="bi bi-bookmark-fill" viewBox="0 0 16 16">
                                                <path
                                                    d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2" />
                                            </svg> Unmark

                                        <?php endif; ?>
                                    </a>
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
                            <div class="card">
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
                                    <a class="bookmarkBtn <?php echo $row['is_bookmarked'] ? 'bookmarked' : ''; ?>"
                                        href="#;"
                                        onclick="toggleBookmark(this, '<?php echo $row['quiz_id']; ?>', '<?php echo $studentId; ?>')">
                                        <?php if ($row['is_bookmarked']): ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                                class="bi bi-bookmark-fill" viewBox="0 0 16 16">
                                                <path
                                                    d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2" />
                                            </svg> Unmark

                                        <?php endif; ?>
                                    </a>
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
                const subject = card.querySelector('.card-text:nth-of-type(1)').textContent.toLowerCase();
                const author = card.querySelector('.card-text:nth-of-type(2)').textContent.toLowerCase();

                const matches = title.includes(input) ||
                    subject.includes(input) ||
                    author.includes(input) ||
                    input === '';

                cardCol.style.display = matches ? '' : 'none';
            });
        }

        //bookmark btn
        function toggleBookmark(element, contentId, studentId, type) {
            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', 'bookmark.php', true);
            xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            const isCurrentlyBookmarked = element.classList.contains('bookmarked');
            const action = isCurrentlyBookmarked ? 'remove' : 'add';

            let data;
            if (type === 'material') {
                data = "action=" + action + "&material_id=" + contentId + "&student_id=" + studentId;
            } else {
                data = "action=" + action + "&quiz_id=" + contentId + "&student_id=" + studentId;
            }

            xhttp.onload = function () {
                if (xhttp.status === 200) {
                    if (xhttp.responseText.includes('Success')) {
                        location.reload();
                    }
                }
            };

            xhttp.onerror = function () {
                alert('Error occurs. Please try again :D');
            };

            xhttp.send(data);
        }

        //redirect to page
        document.addEventListener('DOMContentLoaded', function () {
            function handleCardClick(event) {
                const card = event.target.closest('.card');
                if (!card) return;

                if (event.target.closest('.bookmarkBtn')) {
                    return;
                }

                const bookmarkBtn = card.querySelector('.bookmarkBtn');
                if (!bookmarkBtn) return;

                const onclickAttr = bookmarkBtn.getAttribute('onclick');
                if (!onclickAttr) return;

                const contentIdMatch = onclickAttr.match(/'([^']+)'/);
                if (!contentIdMatch) return;

                const contentId = contentIdMatch[1];

                let cardType = 'material';
                if (card.closest('#all')) {
                    const cardTexts = card.querySelectorAll('.card-text');
                    if (cardTexts.length > 0) {
                        const typeText = cardTexts[cardTexts.length - 1].textContent;
                        cardType = typeText.includes('Material') ? 'material' : 'quiz';
                    }
                } else if (card.closest('#materials')) {
                    cardType = 'material';
                } else if (card.closest('#quizzes')) {
                    cardType = 'quiz';
                }

                if (cardType === 'material') {
                    window.location.href = `/capstone/STUDENT ( LING )/stuLearningMaterial.php?material_id=${contentId}`;
                } else {
                    window.location.href = `/capstone/STUDENT ( LING )/stuQuiz.php?quiz_id=${contentId}`;
                }
            }
            document.getElementById('all').addEventListener('click', handleCardClick);
            document.getElementById('materials').addEventListener('click', handleCardClick);
            document.getElementById('quizzes').addEventListener('click', handleCardClick);
        });

    </script>
</body>

</html>