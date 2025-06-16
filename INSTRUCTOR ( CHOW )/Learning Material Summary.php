<?php
session_start();
include('connect.php');
include('font.php');
include("../block.php");

// echo "<script>console.log(".$id.")</script>";

$ID = $_COOKIE['ID'];

$data = array(
    "ID" => $ID,
    "form" => "",
    "subject" => "",
    "chapter" => "",
    "learning_style" => "",
    "title" => "",
    "description" => "",
    "content" => []
);

$MaterialQuery = "SELECT * FROM learning_materials WHERE material_id = $ID";
$MaterialSQL = mysqli_query($conn, $MaterialQuery);

$array = mysqli_fetch_assoc($MaterialSQL);

$PartsQuery = "SELECT material_content FROM learning_material_parts WHERE material_id = $ID ORDER BY part ASC";
$PartsSQL = mysqli_query($conn, $PartsQuery);

// $totalAttempt = mysqli_num_rows($PartsSQL);

if (mysqli_num_rows($PartsSQL) > 0) {
    while ($row = mysqli_fetch_row($PartsSQL)) {
        array_push($data['content'], $row[0]);
    }
}

$data['form'] = $array['material_level'];
$data['subject'] = $array['material_subject'];
$data['chapter'] = $array['material_chapter'];
$data['learning_style'] = $array['material_learning_type'];
$data['description'] = $array['material_description'];
$data['title'] = $array['material_title'];

if ($data['title'] == "") {
    $data['title'] = "NO TITLE";
}

$StudentsQuery =
    "SELECT progress, last_datetime, student_name FROM progress 
INNER JOIN students ON progress.student_id = students.student_id AND material_id = $ID
ORDER BY last_datetime DESC";
$StudentsSQL = mysqli_query($conn, $StudentsQuery);

$StudentCompletedData = [];
$StudentInProgressData = [];

$completedAttempt = 0;
$inProgressAttempt = 0;
$totalAttempt = mysqli_num_rows($StudentsSQL);


if (mysqli_num_rows($StudentsSQL) > 0) {
    while ($row = mysqli_fetch_assoc($StudentsSQL)) {
        $info = array(
            "name" => $row['student_name'],
            "progress" => "",
            "date" => ""
        );
        if ($row['progress'] == NULL) {
            $progress = 0;
        } else {
            $current = explode(",", $row['progress']);
            $progress = count($current);
        }

        $percentage = round(($progress / count($data['content'])) * 100, 0);
        $info['progress'] = $percentage;

        $originalDate = $row['last_datetime'];
        $newDate = date("d/m/Y", strtotime($originalDate));
        $info['date'] = $newDate;

        if ($percentage == 100) {
            $completedAttempt++;
            array_push($StudentCompletedData, $info);
        } else {
            $inProgressAttempt++;
            array_push($StudentInProgressData, $info);
        }

    }
}

$FeedbackQuery =
    "SELECT feedback, date_made, student_name FROM learning_material_feedback
INNER JOIN students ON learning_material_feedback.student_id = students.student_id AND material_id = $ID
ORDER BY date_made DESC";
$FeedbackSQL = mysqli_query($conn, $FeedbackQuery);

$FeedbackData = [];

if (mysqli_num_rows($FeedbackSQL) > 0) {
    while ($row = mysqli_fetch_assoc($FeedbackSQL)) {
        $info = array(
            "name" => $row['student_name'],
            "feedback" => $row['feedback'],
            "date" => ""
        );

        $originalDate = $row['date_made'];
        $newDate = date("d/m/Y", strtotime($originalDate));
        $info['date'] = $newDate;

        array_push($FeedbackData, $info);
    }
}
?>
<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <link href="./RESOURCES/CSS/header-format.css" rel="stylesheet">
    <link href="./RESOURCES/CSS/colors.css" rel="stylesheet">
    <link href="./RESOURCES/CSS/summary material.css" rel="stylesheet">
    <title>material summary</title>
</head>

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
                <li class="options"><a href="Learning Material View.php">LEARNING MATERIAL</a></li>
                <li class="options"><a href="Quiz View.php">QUIZ</a></li>
                <li id="logo"><a href="Learning Material View.php">ASSESTIFY</a></li>
            </ul>
        </div>
    </header>
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
    </script>
    <main>
        <!-- main selection area -->
        <div class="title">
            <h1>
                <?php echo $data['title'] ?>
                <div>
                    <button style="background-color: var(--red);" onclick="DeleteMaterial(<?php echo $ID ?>)">
                        DELETE</button>
                    <button style="background-color: var(--blue);" onclick="EditMaterial(<?php echo $ID ?>)">
                        EDIT</button>
                    <button style="background-color: var(--blue);" onclick="window.print()"> PRINT</button>
                </div>
            </h1>
        </div>

        <div id="summary">
            <div class="title">
                <h1>
                    COMPLETION RATING
                </h1>
            </div>
            <div id="detail">
                <div id="chart">
                    <canvas id="myChart"></canvas>
                </div>
                <div id="chart-detail">
                    <div class="category" style="background-color: var(--light-green);">
                        <div class="cat-title">
                            <h1>
                                COMPLETED
                            </h1>
                        </div>
                        <p><?php echo $completedAttempt ?></p>
                        <p style="font-size: 2vw;">STUDENTS</p>
                    </div>
                    <div class="category" style="background-color: var(--light-orange);">
                        <div class="cat-title">
                            <h1>
                                IN PROGRESS
                            </h1>
                        </div>
                        <p><?php echo $inProgressAttempt ?></p>
                        <p style="font-size: 2vw;">STUDENTS</p>
                    </div>
                    <div class="category">
                        <div class="cat-title">
                            <h1>
                                TOTAL
                            </h1>
                        </div>
                        <p><?php echo $totalAttempt ?></p>
                        <p style="font-size: 2vw;">STUDENTS</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="header">
            <div class="button" onclick="SetContent()"><span>CONTENT</span></div>
            <div class="button" onclick="SetUser()"><span>USER LIST</span></div>
            <div class="button" onclick="SetFeedback()"><span>FEEDBACK</span></div>
            <div id="filter">
                <select name="status" id="status" style="display: block;">
                    <option value="all" selected>ALL</option>
                    <option value="completed">COMPLETED</option>
                    <option value="in-progress">IN PROGRESS</option>
                </select>
                <select name="time" id="time" style="display: block;">
                    <option value="latest" selected>LATEST</option>
                    <option value="oldest">OLDEST</option>
                </select>
            </div>
        </div>
        <div id="information">
            <div id="feedback">
                <div id="info">
                    <h1>PART 1</h1>
                    <span>Created On 11/09/2050</span>
                    <p>suck mah dick</p>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4/dist/chart.umd.min.js"></script>
    <script>
        let Material = <?php echo json_encode($data) ?>;
        let StudentCompleted = <?php echo json_encode($StudentCompletedData) ?>;
        let StudentInProgress = <?php echo json_encode($StudentInProgressData) ?>;
        let Completed = <?php echo $completedAttempt ?>;
        let InProgress = <?php echo $inProgressAttempt ?>;
        let Feedback = <?php echo json_encode($FeedbackData) ?>;
    </script>
    <script src="RESOURCES/JAVA/Summarize Material.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function DeleteMaterial(id) {
            if (confirm('Are you sure? All info related to this material will be deleted')) {
                $.ajax({
                    method: "POST",
                    url: "Upload Learning Material.php",
                    data: {
                        ID: id,
                        form: "",
                        subject: "",
                        chapter: "",
                        learning_style: "",
                        title: "",
                        description: "",
                        delete: true,
                        saved: true,
                        completion: false,
                        content: []
                    }
                })

                    .done(function (response) {
                        console.log(response);
                    });
                window.location.href = "Learning Material View.php";
            }
        }

        function EditMaterial(id) {
            document.cookie = "ID = " + id + ";"
            document.cookie = "ORIGIN = Learning Material Summary.php;"
            window.location.href = "Learning Material Edit.php";
        }

        const ctx = document.getElementById('myChart');

        console.log(Completed);

        const data = {
            labels: [
                'Completed',
                'In Progress'
            ],
            datasets: [{
                data: [Completed, InProgress],
                backgroundColor: [
                    'rgb(74, 183, 136)',
                    'rgb(242, 144, 16)'
                ],
                hoverOffset: 7
            }]
        };

        const config = {
            type: 'doughnut',
            data: data,
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        };

        new Chart(ctx, config);
    </script>
</body>