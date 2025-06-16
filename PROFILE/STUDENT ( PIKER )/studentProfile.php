<?php
session_start();

include("connection.php");
include("header.php");
include("bootstrapFile.html");
include("../../block.php");



$studentId = $_SESSION['user_id'];

function fetchData($sql): array|null
{
    global $conn;
    $result = mysqli_query($conn, $sql);
    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    return null; //kalau null nanti print specific like no goal set....
}

$studentInfoSql = "SELECT * FROM students WHERE student_id = '$studentId'";
$studentInfoResult = fetchData($studentInfoSql);

$logSql = "SELECT * FROM user_logs WHERE student_id = '$studentId' ORDER BY datetime_of_log ASC";
$logResult = fetchData($logSql);

$goalSql = "SELECT * FROM goals WHERE student_id = '$studentId'";
$goalResult = fetchData($goalSql);

$currentTime = date("Y-m-d H:i:s");
$currentDate = date("Y-m-d");
$currentWeek = date("W", strtotime($currentDate));

$dailyTime = $weeklyTime = 0;
$isDailyProgress = false;
$isWeeklyProgress = false;
$loginArray = $logoutArray = []; //daily array
$weeklyLoginArray = $weeklyLogoutArray = [];
$loginCounter = $logoutCounter = 0;
$wloginCounter = $wlogoutCounter = 0; //weekly                                                                                                                                                                                                                           
if (!empty($logResult)) {

    foreach ($logResult as $log) {

        $logDate = date("Y-m-d", strtotime($log["datetime_of_log"]));
        $logWeek = date("W", strtotime($log["datetime_of_log"])); //week number of the date


        if ($logWeek == $currentWeek) {
            $isWeeklyProgress = true;
            if ($log["isLogin"] == true) {
                $weeklyLoginArray[$wloginCounter] = strtotime($log["datetime_of_log"]);
                $wloginCounter++;
            } else {
                $weeklyLogoutArray[$wlogoutCounter] = strtotime($log["datetime_of_log"]);
                $wlogoutCounter++;
            }
        }

        if ($logDate == $currentDate) {
            $isDailyProgress = true;
            if ($log["isLogin"] == true) {
                $loginArray[$loginCounter] = strtotime($log["datetime_of_log"]);
                $loginCounter++;
            } else { //logout
                $logoutArray[$logoutCounter] = strtotime($log["datetime_of_log"]);
                $logoutCounter++;

            }
        }
    }

    $count = min(count($loginArray), count($logoutArray)); //prevent last login attempt but hvnt logout, thats why when retrieve need to be in ASC order
    for ($i = 0; $i < $count; $i++) {
        if ($logoutArray[$i] > $loginArray[$i]) {
            $timestamp = $logoutArray[$i] - $loginArray[$i];
            $dailyTime += $timestamp;
        } else {
            $dailyTime += 0; //if login>logout = invalid
        }
    }

    $weeklyCount = min(count($weeklyLoginArray), count($weeklyLogoutArray));
    for ($i = 0; $i < $weeklyCount; $i++) {
        if ($weeklyLogoutArray[$i] > $weeklyLoginArray[$i]) {
            $weeklyTime += $weeklyLogoutArray[$i] - $weeklyLoginArray[$i];
        }
    }

    $dailyHours = floor($dailyTime / 3600);
    $dailyMin = floor(($dailyTime % 3600) / 60);

    $weeklyHours = floor($weeklyTime / 3600);
    $weeklyMin = floor(($weeklyTime % 3600) / 60);

} else {
    $isDailyProgress = false;
    $isWeeklyProgress = false;
}

//check for goal progress
$progressPercentage = 0;
$isGoalDaily = $isGoalWeekly = false;

if (!empty($goalResult)) {
    $isGoal = true;
    $goal = $goalResult[0]; //only one result per result since student id is set to unique row
    $timeSet = ($goal["time_set"]) * 3600; // result in seconds unit
    if ($goal["daily"] == true) {
        $progressPercentage = floor(($dailyTime / $timeSet) * 100);
        $isGoalDaily = true;
    } else if ($goal["weekly"] == true) {
        $progressPercentage = floor(($weeklyTime / $timeSet) * 100);
        $isGoalWeekly = true;
    }
} else {
    $isGoal = false;
}

if ($progressPercentage > 100) {
    $progressPercentage = 100;
}


$pathwaySql = "SELECT pathway_id FROM learning_pathways WHERE student_id = '$studentId'";
$pathwayResult = fetchData($pathwaySql);

if (!empty($pathwayResult)) {
    $pathwayId = $pathwayResult[0]['pathway_id'];

} else {
    $pathwayId = null;
}

$yourPathSql = "SELECT s.*, lm.*, i.instructor_name 
                FROM sequences s 
                INNER JOIN learning_materials lm ON s.material_id = lm.material_id 
                INNER JOIN instructors i ON lm.instructor_id = i.instructor_id 
                WHERE s.pathway_id = '$pathwayId' 
                ORDER BY s.sequence ASC 
                LIMIT 6";
$yourPathResult = fetchData($yourPathSql);

if (!empty($yourPathResult)) {
    foreach ($yourPathResult as $key => $sequence) {
        $dueDate = new DateTime($sequence['due_date']);
        $currentDateObj = new DateTime($currentDate);
        $interval = $currentDateObj->diff($dueDate);
        $daysLeft = (int) $interval->format('%r%a'); // %r = - for past date

        if ($daysLeft >= 0) {
            $yourPathResult[$key]['days_left'] = $daysLeft + 1; // include current day
        } else {
            $yourPathResult[$key]['days_left'] = $daysLeft;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- chart.js for progress chart -->
    <title>Profile</title>
</head>
<style>
    body {
        font-family: "inder";
    }

    .profile-section {
        background: linear-gradient(to right, #6a11cb, #2575fc);
        color: white;
        padding: 20px;
        border-radius: 10px;
    }

    .profile-pic {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
    }

    .card {
        background-color: #2c2f33;
        color: white;
        border-radius: 0 0 10px 10px;
        width: 100%;
        max-width: 100%;
    }

    .partContainer {
        background-color: #2c2f33;
        border-radius: 10px;
        color: white;
        margin-left: 0;
        margin-right: 0;
    }

    .partContainer h3 {
        font-weight: bold;
    }


    .profile-pic:hover {
        transform: scale(1.1);
        transition: transform 0.3s ease;
    }


    .chartContainer {
        width: 140px;
        height: 140px;
        margin: 0 auto;
        position: relative;
    }


    .percentageContainer {
        font-size: 1.5rem;
        color: #4CAF50;
    }

    canvas {
        display: block;
        margin: 0 auto;
    }

    .scrollContainer {
        margin-bottom: 35px;
        background: var(--dark-grey);
        border-radius: 10px;
        position: relative;
        transition: all 0.3s ease-in-out;
        width: 100%;
    }

    .scrollContainerTitle {
        background: var(--light-green);
        color: black;
        border-radius: 10px 10px 0px 0px;
        padding: 10px;
        margin: 0;
    }

    .scrollContainer:hover {
        transform: scale(1.03);
        cursor: pointer;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: #4ab788;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #3a9b6e;
    }

    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
        width: 100%;
        max-width: 100%;
    }

    .learning-path-scroll {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 20px;
        margin-right: 0;
        margin-left: 0;
        width: 100%;
    }

    .path-item {
        flex: 0 0 auto;
        width: 300px;
        margin-right: 15px;
    }

    .path-item:last-child {
        margin-right: 0;
    }

    .text-primary {
        color: rgb(172 255 168) !important;
    }


    .learning-style-option {
        border: 2px solid var(--green);
        border-radius: 10px;
        transition: all 0.3s ease-in-out;
    }

    /*all modal css */
    .modal-header {
        background-color: var(--green);
        color: white;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .modal-title {
        font-weight: bold;
        font-size: 1.5rem;
    }

    .modal-content {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .modal-body,
    .modal-footer {
        background: var(--dark-grey);
        color: white;
    }

    .btn-outline-primary {
        border-color: var(--light-green);
        color: var(--light-green);
    }

    .btn-outline-primary:hover,
    .btn-outline-primary:focus,
    .btn-outline-primary:active {
        border-color: var(--green);
        background-color: var(--green);
        color: white;
        box-shadow: none;
    }

    .btn-outline-primary:focus {
        outline: none;
    }

    .btn-primary {
        background-color: var(--green);
        border-color: var(--green);
    }

    .btn-primary:hover {
        background-color: #74c078;
        border-color: #74c078;
    }

    .btn-secondary {
        background-color: #f8f9fa;
        color: #333;
    }

    .btn-secondary:hover {
        background-color: #e2e6ea;
    }

    .modal.fade .modal-dialog {
        transform: translateY(-50px);
        transition: transform 0.3s ease-out;
    }

    .modal.show .modal-dialog {
        transform: translateY(0);
    }

    /*select learning style part */
    .btn-check:checked+.btn-outline-primary,
    .btn-check:not(:checked)+.btn-outline-primary:hover {
        background-color: var(--green);
        color: white;
        border-color: var(--green);
    }

    .btn-check:not(:checked)+.btn-outline-primary {
        border-color: var(--light-green);
        color: var(--light-green);
    }

    /*goal - range */
    input[type="range"] {
        -webkit-appearance: none;
        width: 100%;
        height: 8px;
        background: var(--light-green);
        border-radius: 5px;
        outline: none;
        transition: background 0.3s ease-in-out;
    }

    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        background: var(--green);
        border-radius: 50%;
        cursor: pointer;
        transition: background 0.3s ease-in-out;
    }

    input[type="range"]:hover::-webkit-slider-thumb {
        background: #74c078;
    }

    /*edit form radio btn */
    #editProfileModal .form-check-input:checked {
        background-color: var(--green);
        border-color: var(--green);
    }

    #editProfileModal .form-check-input:focus {
        border-color: var(--light-green);
        box-shadow: 0 0 0 0.25rem rgba(74, 183, 136, 0.25);
    }

    /* learning path*/
    @media (max-width: 768px) {
        .partContainer {
            margin-bottom: 20px;
        }

        .path-item {
            width: 260px;
        }
    }
</style>


</style>

<body style="background-color: var(--light-grey);">

    <!-- profile -->
    <div class="container-fluid p-0">
        <div class="card text-center shadow-lg">
            <div class="card-body">
                <?php if (!empty($studentInfoResult)): ?>
                    <?php
                    $studentInfo = $studentInfoResult[0];
                    ?>
                    <img src="<?php echo $studentInfo['profile_pic_url'] ?: 'profileIcon/profileIcon.png'; ?>"
                        alt="Profile Picture" class="profile-pic mx-auto d-block mb-3">
                    <h3 class="card-title">Welcome, <?php echo $studentInfo['student_name']; ?>!</h3>
                    <p class="card-text">
                        <strong>Email:</strong> <?php echo $studentInfo['student_email']; ?><br>
                        <strong>Form:</strong> <?php echo $studentInfo['student_level']; ?>
                    </p>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#editProfileModal">Edit Profile</button>
                <?php else: ?>
                    <?php echo "No student found" ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!--edit profile modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editProfileForm" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="studentName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="studentName" name="student_name"
                                value="<?php echo $studentInfo['student_name']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="studentEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="studentEmail" name="student_email"
                                value="<?php echo $studentInfo['student_email']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Form</label>
                            <div>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="student_level"
                                            id="form<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $studentInfo['student_level'] == $i ? 'checked' : ''; ?> required>
                                        <label class="form-check-label" for="form<?php echo $i; ?>">Form
                                            <?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="profilePic" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profilePic" name="profile_pic" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label for="oldPassword" class="form-label">Old Password</label>
                            <input type="password" class="form-control" id="oldPassword" name="old_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="updateProfileBtn">
                            <i class="bi bi-save"></i> Update Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--learning style -->
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="partContainer p-4 shadow-lg h-100">
                    <div class="row align-items-center mb-3">
                        <div class="col">
                            <h3 class="text-primary">Learning Style</h3>
                        </div>
                        <div class="col text-end">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#changeStyleModal">Change?</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-center">
                            <h4 class="text-light">Your current style is:</h4>
                            <br>
                            <?php if ($studentInfo['student_learning_style'] == 'visual'): ?>
                                <i class="bi bi-eye display-4 text-info"></i>
                                <p class="mt-2 text-info fw-bold">Visual</p>
                            <?php elseif ($studentInfo['student_learning_style'] == 'read_write'): ?>
                                <i class="bi bi-book-half display-4 text-warning"></i>
                                <p class="mt-2 text-warning fw-bold">Read/Write</p>
                            <?php elseif ($studentInfo['student_learning_style'] == 'audio'): ?>
                                <i class="bi bi-speaker display-4 text-success"></i>
                                <p class="mt-2 text-success fw-bold">Audio</p>
                            <?php else: ?>
                                <p class="text-danger">No learning type found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>


            <!--change style modal -->
            <div class="modal fade" id="changeStyleModal" tabindex="-1" aria-labelledby="changeStyleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <form id="changeStyleForm" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="changeStyleModalLabel">Change Learning Style</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row text-center">

                                    <div class="col-4">
                                        <input type="radio" class="btn-check" name="learning_style" id="visual"
                                            value="visual" <?php echo $studentInfo['student_learning_style'] == 'visual' ? 'checked' : ''; ?>>
                                        <label class="btn btn-outline-primary w-100 p-4 learning-style-option"
                                            for="visual">
                                            <i class="bi bi-eye display-4"></i>
                                            <p class="mt-2 fw-bold">Visual</p>
                                        </label>
                                    </div>

                                    <div class="col-4">
                                        <input type="radio" class="btn-check" name="learning_style" id="read_write"
                                            value="read_write" <?php echo $studentInfo['student_learning_style'] == 'read_write' ? 'checked' : ''; ?>>
                                        <label class="btn btn-outline-primary w-100 p-4 learning-style-option"
                                            for="read_write">
                                            <i class="bi bi-book-half display-4"></i>
                                            <p class="mt-2 fw-bold">Read/Write</p>
                                        </label>
                                    </div>

                                    <div class="col-4">
                                        <input type="radio" class="btn-check" name="learning_style" id="audio"
                                            value="audio" <?php echo $studentInfo['student_learning_style'] == 'audio' ? 'checked' : ''; ?>>
                                        <label class="btn btn-outline-primary w-100 p-4 learning-style-option"
                                            for="audio">
                                            <i class="bi bi-speaker display-4"></i>
                                            <p class="mt-2 fw-bold">Audio</p>
                                        </label>
                                    </div>
                                </div>
                                <div class="row text-center mt-3">
                                    <p class="mb-0">Dont know? Try this<a href="../../learning_style_questionnaire.php">
                                            questionnaire</a>
                                    </p>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="updateStyleBtn">Update
                                    Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <!--goal-->
            <div class="col-md-6 mb-4">
                <div class="partContainer p-4 shadow-lg h-100">
                    <div class="row align-items-center mb-3">
                        <div class="col">
                            <h3 class="text-primary">Goal</h3>
                        </div>
                        <div class="col text-end">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#setGoalModal">Change?</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col text-center">
                            <?php if (!$isGoal): ?>
                                <h4 class="text-light">Set a Goal Now!</h4>
                            <?php else: ?>
                                <?php if ($isGoalDaily): ?>
                                    <h4 class="text-light">Daily Goal:</h4>
                                <?php elseif ($isGoalWeekly): ?>
                                    <h4 class="text-light">Weekly Goal:</h4>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col d-flex justify-content-center align-items-center position-relative">
                            <canvas id="progressChart" width="140" height="140"></canvas>
                            <div
                                class="percentageContainer position-absolute top-50 start-50 translate-middle text-center">
                                <h4 class="fw-bold text-primary">
                                    <?php echo $progressPercentage . "%"; ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--goal modal-->
    <div class="modal fade" id="setGoalModal" tabindex="-1" aria-labelledby="setGoalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="setGoalForm" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="setGoalModalLabel">Set Your Goal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Goal Type</label>
                            <div class="d-flex justify-content-around">
                                <div>
                                    <input type="radio" class="btn-check" name="goal_type" id="dailyGoal" value="daily"
                                        checked>
                                    <label class="btn btn-outline-primary" for="dailyGoal">Daily Goal</label>
                                </div>
                                <div>
                                    <input type="radio" class="btn-check" name="goal_type" id="weeklyGoal"
                                        value="weekly">
                                    <label class="btn btn-outline-primary" for="weeklyGoal">Weekly Goal</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="goalRange" class="form-label">Set Time</label>
                            <input type="range" class="form-range" id="goalRange" name="time_set" min="1" max="168"
                                step="1" value="1">
                            <p class="text-center mt-2">Selected Time: <span id="selectedTime">1</span> hours</p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveGoalBtn">Save Goal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--learning path-->
    <div class="container-fluid mb-4">
        <div class="partContainer p-4 shadow-lg">
            <div class="row mb-4">
                <div class="col-9">
                    <h3 class="text-primary">Current Learning Path</h3>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-outline-primary"
                        onclick="window.location.href='managePath.php'">Edit/View</button>
                </div>
            </div>

            <!--scrollable materials-->
            <div class="learning-path-scroll">
                <?php if (!empty($yourPathResult)): ?>
                    <?php foreach ($yourPathResult as $result): ?>
                        <div class="path-item" onclick="redirectToMaterial('<?php echo $result['material_id']; ?>')">
                            <div class="scrollContainer">
                                <div class="p-3 scrollContainerTitle">
                                    <h5 class="text-truncate"><?php echo strtoupper($result['material_title']); ?></h5>
                                </div>
                                <div class="p-3">
                                    <h5 class="text-truncate">
                                        <?php echo !empty($result['material_subject']) ? $result['material_subject'] : "Undefined Subject"; ?>
                                    </h5>
                                    <i style="color: #f8f9fa;"><?php echo $result['instructor_name']; ?></i>
                                    <p
                                        style="margin-bottom: 10px; margin-top: 10px; font-size: small; color: <?php echo ($result['days_left'] > 0) ? '#bcffa9' : '#ff5f5f'; ?>;">
                                        <?php echo $result['days_left'] > 0 ? $result['days_left'] . " days left" : "Overdue. Catch Up!"; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p>Explore more materials and add them into path!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php
    include("../../footer.php"); ?>

    <script>
        const progressValue = <?php echo $progressPercentage; ?>;
        const ctx = document.getElementById('progressChart').getContext('2d');

        const progressChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [progressValue, 100 - progressValue],
                    backgroundColor: ['#4CAF50', '#e0e0e0'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '80%',
                responsive: true,
                plugins: {
                    tooltip: { enabled: false },
                    legend: { display: false },
                    title: { display: false }
                }
            }
        });


        function redirectToMaterial(materialId) {
            window.location.href = `materialPage.php?material_id=${materialId}`;
        }

        //profile
        document.getElementById('updateProfileBtn').addEventListener('click', function () {
            const form = document.getElementById('editProfileForm');
            const formData = new FormData(form);

            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', 'studentUpdateProfile.php', true);

            xhttp.onload = function () {
                if (xhttp.status === 200) {
                    try {
                        const data = JSON.parse(xhttp.responseText);
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
                            modal.hide();

                            //modal close delay time before page reload
                            setTimeout(function () {
                                window.location.reload();
                            }, 300);
                        } else {
                            alert(data.message);
                        }
                    } catch (e) {
                        alert('An error occurred. Please try again.');
                    }
                }
            };

            xhttp.onerror = function () {
                alert('An error occurred. Please try again.');
            };

            xhttp.send(formData);
        });

        //style
        document.getElementById('updateStyleBtn').addEventListener('click', function () {
            const form = document.getElementById('changeStyleForm');
            const formData = new FormData(form);

            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', 'studentUpdateStyle.php', true);

            xhttp.onload = function () {
                if (xhttp.status === 200) {
                    const data = JSON.parse(xhttp.responseText);
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('changeStyleModal'));
                        modal.hide();
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                }
            };

            xhttp.onerror = function () {
                alert('An error occurred. Please try again.');
            };

            xhttp.send(formData);
        });

        //goal
        document.addEventListener('DOMContentLoaded', function () {
            const dailyGoal = document.getElementById('dailyGoal');
            const weeklyGoal = document.getElementById('weeklyGoal');
            const goalRange = document.getElementById('goalRange');
            const selectedTime = document.getElementById('selectedTime');

            function updateRangeLimits() {
                if (dailyGoal.checked) {
                    goalRange.min = 1;
                    goalRange.max = 24;
                    goalRange.value = 1;
                } else if (weeklyGoal.checked) {
                    goalRange.min = 1;
                    goalRange.max = 168;
                    goalRange.value = 1;
                }
                selectedTime.textContent = goalRange.value;
            }

            dailyGoal.addEventListener('change', updateRangeLimits);
            weeklyGoal.addEventListener('change', updateRangeLimits);

            goalRange.addEventListener('input', function () {
                selectedTime.textContent = goalRange.value;
            });

            document.getElementById('saveGoalBtn').addEventListener('click', function () {
                const form = document.getElementById('setGoalForm');
                const formData = new FormData(form);

                const xhttp = new XMLHttpRequest();
                xhttp.open('POST', 'studentUpdateGoal.php', true);

                xhttp.onload = function () {
                    if (xhttp.status === 200) {
                        const data = JSON.parse(xhttp.responseText);
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('setGoalModal'));
                            modal.hide();
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    }
                };

                xhttp.onerror = function () {
                    alert('An error occurred. Please try again.');
                };

                xhttp.send(formData);
            });

            updateRangeLimits(); //reload range limits when page reload
        });

    </script>

</body>

</html>