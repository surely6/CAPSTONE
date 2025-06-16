<?php
session_start();
include("connection.php");
include("header.php");
include("bootstrapFile.html");
include("../../block.php");



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


$studentId = $_SESSION['user_id'];

$goalSql = "SELECT * FROM goals WHERE student_id = '$studentId'";
$goalResult = fetchData($goalSql);

$currentTime = date("Y-m-d H:i:s");
$currentDate = date("Y-m-d");
$currentWeek = date("W", strtotime($currentDate));


$logSql = "SELECT * FROM user_logs WHERE student_id = '$studentId' ORDER BY datetime_of_log ASC";
$logResult = fetchData($logSql);


$progressSql = "SELECT 
                    lm.material_subject, 
                    lm.material_title, 
                    lm.material_id, 
                    i.instructor_name, 
                    (SELECT COUNT(*) 
                     FROM learning_material_parts 
                     WHERE learning_material_parts.material_id = lm.material_id) AS total_parts,
                    FLOOR(((LENGTH(p.progress) - LENGTH(REPLACE(p.progress, ',', '')) + 1) / 
                          (SELECT COUNT(*) 
                           FROM learning_material_parts 
                           WHERE learning_material_parts.material_id = lm.material_id)) * 100) AS progress_percentage,
                    CASE    
                        WHEN b.material_id IS NOT NULL THEN 1 
                        ELSE 0 
                    END AS is_bookmarked
                FROM progress p 
                INNER JOIN learning_materials lm ON lm.material_id = p.material_id 
                INNER JOIN instructors i ON i.instructor_id = lm.instructor_id 
                LEFT JOIN bookmarks b ON b.material_id = lm.material_id AND b.student_id = '$studentId' 
                WHERE p.student_id = '$studentId' 
                HAVING progress_percentage < 100 
                ORDER BY progress_percentage DESC 
                LIMIT 8";

$progressResult = fetchData($progressSql);


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
                AND lm.material_learning_type = (
                SELECT student_learning_style FROM students WHERE student_id = '$studentId'
                )                
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

    $minSequenceRow = array_shift($yourPathResult); // first sequence is taken out for the hero section of your path
    $remainingSequences = $yourPathResult;
} else {
    $minSequenceRow = null;
    $remainingSequences = [];
}

//suggestion
$suggestSql = "SELECT COUNT(DISTINCT a.attempt_id) AS quiz_count 
               FROM attempts a 
               WHERE a.student_id = '$studentId'";
$suggestResult = fetchData($suggestSql);

$suggestionMessage = "";

if (!empty($suggestResult) && $suggestResult[0]['quiz_count'] > 2) {//only attempts from specific student>10 only give suggestions //2 for testing
    $detailedSuggestSql = "SELECT q.quiz_subject AS subject, 
                                  AVG((a.score / q.quiz_total_questions) * 100) AS average_percentage 
                           FROM attempts a 
                           INNER JOIN quizzes q ON a.quiz_id = q.quiz_id 
                           WHERE a.student_id = '$studentId' 
                           GROUP BY q.quiz_subject 
                           ORDER BY average_percentage ASC 
                           LIMIT 1";
    $detailedSuggestResult = fetchData($detailedSuggestSql);

    if (!empty($detailedSuggestResult)) {
        $topSuggestion = $detailedSuggestResult[0];
        $suggestionMessage = "You're doing great! To reach even higher, keep practicing <b>" . $topSuggestion["subject"] . "</b> amongst other subjects! <br>
        Right now, your average score is <i>" . round($topSuggestion["average_percentage"], 2) . "%</i>— let's push it even higher!";
    }
} else {
    $suggestionMessage = "Explore more quizzes to get personal suggestions!";
}

//check for daily & weekly progress
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


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- chart.js for progress chart -->
    <title>Dashboard</title>
</head>
<style>
    body {
        background-color: var(--grey);
        font-family: "inder";
    }

    .goalContainer {
        background: var(--dark-grey);
        border-radius: 20px;
    }

    .goalProgressContainer {
        width: 140px;
        height: 140px;
        position: relative;
    }

    .percentageContainer {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-weight: bold;
        font-size: 20px;
    }

    .chartContainer {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .dropdown-toggle::after {
        /* removing default dropdown arrow from bootstrap */
        display: none
    }

    .scrollContainer {
        margin-bottom: 35px;
        background: var(--dark-grey);
        border-radius: 10px;
        position: relative;
        transition: all 0.3s ease-in-out;
    }

    .scrollContainerTitle {
        background: var(--light-green);
        border-radius: 10px 10px 0px 0px;
        padding: 10px;
        margin: 0;
        border: solid black;
        border-radius: 10px;
    }

    @media (max-width: 1000px) {
        .scrollContainer {
            height: auto;
        }
    }

    .col-12 h5 {
        padding-top: 10px;
        margin-bottom: 0px;
        color: #f8f9fa;
    }

    .progress {
        --bs-progress-bg: #ffffff;
        margin-top: 30px;
    }

    .container {
        padding: 24px;
    }


    .scrollContainer:hover {
        transform: scale(1.05);
        cursor: pointer;
    }

    .bookmarked svg path {
        fill: black;
    }

    .modal-body {
        display: flex;
        flex-direction: column;
        width: 100%;
        padding: 20px;
    }

    .modal-content .row {
        margin: 0;
        padding: 0 15px;
        width: 100%;
        background: #88bfa3;
        border-radius: 10px 10px 0 0;
    }

    .modal-body p {
        margin: 0;
    }

    #dueDate {
        padding: 10px;
        margin: 20px;
        background: var(--bs-light-bg-subtle);
        border: none;
        border-radius: 10px;
    }

    .modal-footer,
    .modal-body {
        background: var(--light-grey);
    }

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

    .firstSequenceContainer:hover {
        opacity: 0.8;
        transition: opacity 0.3s ease-in-out;
        cursor: pointer;
    }

    .firstSequenceContainer {
        margin-bottom: 30px;
        border-radius: 20px;
        color: white;
    }

    @media only screen and (max width: 767px) {
        .col-4:hover {
            transform: none
        }
    }

    button {
        background: var(--light-green);
        color: black;
        border-color: aquamarine;
        border-radius: 10px;
        padding: 5px 10px;
    }

    button:hover {
        transition: 0.3s;
        background: var(--green);
    }

    .weeklyHourContainer,
    .dailyProgressContainer {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        font-size: larger;
    }

    @media (max-width: 768px) {

        .dailyProgressContainer,
        .chartContainer {
            margin-bottom: 20px;
        }
    }

    .percentageContainer,
    .goalContainer h2,
    .goalContainer h3,
    .firstSequenceContainer h3 {
        color: var(--light-green);
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

    .scrollContent {
        color: white;
        font-size: 1.2rem;
        border: solid black;
        border-radius: 10px;
        border-top: none;
    }

    .bg-success {
        --bs-bg-opacity: 1;
        background-color: var(--green) !important;
    }

    .keepLearningContainer {
        background: var(--light-green);
        border-radius: 10px;
        border: solid black;
    }
</style>

<body>
    <div class="container mt-3">
        <div class="row">
            <div class="col-9">
                <h2>Goal</h2>
            </div>
            <div class="col text-end">
                <button data-bs-toggle="modal" data-bs-target="#setGoalModal">Change</button>
            </div>
        </div>
        <hr>
    </div>
    <div class="goalContainer container text-light p-4 mb-4">
        <?php if (!$isGoal): ?>
            <div class="row">
                <div class="col text-center">
                    <p class="p-3 bg-success bg-opacity-10 border border-success border-start-0 rounded-end">Setting goals
                        is
                        the
                        first step in turning the invisible into the visible.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col chartContainer">
                    <div class="message">
                        <?php if ($isGoalDaily): ?>
                            <p><?php echo "<h3>Daily Goal</h3>"; ?></p>
                        <?php elseif ($isGoalWeekly): ?>
                            <p><?php echo "<h3>Weekly Goal</h3>"; ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="goalProgressContainer">
                        <canvas id="progressChart" width="140" height="140"></canvas>
                        <div class="percentageContainer">
                            <?php echo $progressPercentage . "%"; ?>
                        </div>
                    </div>
                </div>

                <div class="col" style="display:flex;">
                    <div class="dailyProgressContainer container text-center align-items-stretch">
                        <h3>Daily Progress</h3>
                        <hr>
                        <?php if (!($isDailyProgress)): ?>
                            <p>No daily progress today. Keep it up!</p>
                        <?php else: ?>
                            <?php echo "{$dailyHours} hours {$dailyMin} minutes"; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col" style="display:flex;">
                    <div class="weeklyHourContainer container text-center align-items-stretch">
                        <h3>Total This Week</h3>
                        <hr>
                        <?php if (!($isWeeklyProgress)): ?>
                            <p>No weekly progress today. Keep it up!</p>
                        <?php else: ?>
                            <?php echo "{$weeklyHours} hours {$weeklyMin} minutes"; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

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


    <div class="pathContainer container mt-5">
        <div class="row">
            <div class="col">
                <h2>Learning Path</h2>
            </div>

            <div class="col text-end">
                <button type="button" onclick="window.location.href='managePath.php'">Change/View</button>
            </div>
        </div>
        <hr>
        <div class="row justify-content-center"
            onclick="redirectToMaterial('<?php echo $minSequenceRow['material_id']; ?>')">
            <?php if (!empty($minSequenceRow) || !empty($remainingSequences)): ?>
                <?php if (!empty($minSequenceRow)): ?>
                    <div class="col p-4 firstSequenceContainer">
                        <div class="row align-items-center keepLearningContainer">
                            <div class="col-4 text-center">
                                <h3 style="color: black;">KEEP LEARNING!</h3>
                            </div>
                            <div class="col-8 ps-5 py-3 border-start"
                                style="font-size: large; background: var(--dark-grey); border-radius: 0 10px 10px 0;">
                                <h4 style="margin-bottom:20px;"><?php echo strtoupper($minSequenceRow['material_title']); ?>
                                </h4>
                                <?php echo !empty($minSequenceRow['material_subject']) ? $minSequenceRow['material_subject'] : "Undefined Subject"; ?>
                                <br>
                                <i class="pb-3"><?php echo $minSequenceRow['instructor_name']; ?></i>
                                <p
                                    style="margin-bottom: 10px; margin-top: 10px; font-size: small; color: <?php echo ($minSequenceRow['days_left'] > 0) ? '#bcffa9' : '#ff5f5f'; ?>;">
                                    <?php echo $minSequenceRow['days_left'] > 0 ? $minSequenceRow['days_left'] . " days left" : "Overdue. Catch Up!"; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row flex-nowrap overflow-auto">
                <?php if (!empty($remainingSequences)): ?>
                    <?php foreach ($remainingSequences as $sequence): ?>
                        <div class="col-12 col-md-4 col-sm-6 scale"
                            onclick="redirectToMaterial('<?php echo $sequence['material_id']; ?>')">
                            <div class="scrollContainer mt-2">
                                <div class="p-3 scrollContainerTitle">
                                    <h4><?php echo strtoupper($sequence['material_title']); ?></h4>
                                </div>
                                <div class="p-3 scrollContent">
                                    <h5><?php echo !empty($sequence['material_subject']) ? $sequence['material_subject'] : "Undefined Subject"; ?>
                                    </h5>
                                    <i style="color: #f8f9fa;"><?php echo $sequence['instructor_name']; ?></i>
                                    <p
                                        style="margin-bottom: 10px; margin-top: 10px; font-size: small; color: <?php echo ($sequence['days_left'] > 0) ? '#bcffa9' : '#ff5f5f'; ?>;">
                                        <?php echo $sequence['days_left'] > 0 ? $sequence['days_left'] . " days left" : "Overdue. Catch Up!"; ?>
                                    </p>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="p-3 bg-success bg-opacity-10 border border-success border-start-0 rounded-end text-center">Explore
                        more materials and add them into path!</p>
                <?php endif; ?>

            <?php else: ?>
                <p class="p-3 bg-success bg-opacity-10 border border-success border-start-0 rounded-end text-center"><a
                        href="managePath.php">Create your own path</a> now!</p>
            <?php endif; ?>
        </div>



    </div>

    <div class="progressContainer container mt-5">
        <div class="row">
            <div class="col-11">
                <h2>Unfinished Materials</h2>
            </div>
            <div class="col text-end">
                <button
                    onclick="window.location.href='/capstone/PROFILE/INSTRUCTOR ( SURELY )/studentHistory.php?tab=materials'">More</button>
            </div>
        </div>
        <hr>
        <div class="row flex-nowrap overflow-auto">
            <?php if (!empty($progressResult)): ?>
                <?php foreach ($progressResult as $progress): ?>
                    <?php
                    $sequenceSql = "SELECT COUNT(*) AS count FROM sequences WHERE material_id = '{$progress['material_id']}' AND pathway_id = '{$pathwayId}'";
                    $sequenceResult = fetchData($sequenceSql);

                    $isAddToPath = !empty($sequenceResult) && $sequenceResult[0]['count'] > 0;
                    ?>

                    <div class="col-12 col-md-4 col-sm-6 scale">
                        <div class="scrollContainer mt-2">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row scrollContainerTitle p-3" style="padding-left: 3px;">
                                        <div class="col-10">
                                            <h4><?php echo strtoupper($progress['material_title']); ?></h4>
                                        </div>
                                        <div class="col-2">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false"
                                                    style="border: none; background: transparent; padding: 0; display: inline-block; cursor: pointer;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="30"
                                                        height="30" viewBox="0 0 100 100" id="more">
                                                        <g id="_x37_7_Essential_Icons">
                                                            <path id="More_Details__x28_3_x29_"
                                                                d="M50 12c-21 0-38 17-38 38s17 38 38 38 38-17 38-38-17-38-38-38zm0 72c-18.8 0-34-15.2-34-34s15.2-34 34-34 34 15.2 34 34-15.2 34-34 34zm0-41c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zm20-10c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zM30 43c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3z">
                                                            </path>
                                                        </g>
                                                    </svg>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item <?php echo $progress['is_bookmarked'] ? 'bookmarked' : ''; ?>"
                                                        href="#;"
                                                        onclick="toggleBookmark(this, '<?php echo $progress['material_id']; ?>', '<?php echo $studentId; ?> ')">
                                                        <?php if ($progress['is_bookmarked']): ?>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24S"
                                                                fill="currentColor" class="bi bi-bookmark-fill" viewBox="0 0 16 16">
                                                                <path
                                                                    d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2" />
                                                            </svg> Unmark
                                                        <?php else: ?>
                                                            <svg id="bookmarkIcon" xmlns="http://www.w3.org/2000/svg" x="0px"
                                                                y="0px" width="24" height="24" viewBox="0 0 24 24">
                                                                <path
                                                                    d="M 6 2 C 4.8444444 2 4 2.9666667 4 4 L 4 22.039062 L 12 19.066406 L 20 22.039062 L 20 20.599609 L 20 4 C 20 3.4777778 19.808671 2.9453899 19.431641 2.5683594 C 19.05461 2.1913289 18.522222 2 18 2 L 6 2 z M 6 4 L 18 4 L 18 19.162109 L 12 16.933594 L 6 19.162109 L 6 4 z">
                                                                </path>
                                                            </svg> Bookmark
                                                        <?php endif; ?>
                                                    </a>

                                                    <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                        data-material-id="<?php echo $progress['material_id']; ?>"
                                                        data-material-title="<?php echo $progress['material_title'] ?>"
                                                        onclick="toggleLearningPath('<?php echo $progress['material_id']; ?>', '<?php echo $progress['material_title'] ?>', '<?php echo $isAddToPath ? 'remove' : 'add'; ?>')">
                                                        <?php if ($isAddToPath): ?>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                                <path
                                                                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                                            </svg> Remove from Learning Path
                                                        <?php else: ?>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                                                <path
                                                                    d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16zM8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                                                            </svg> Add to Learning Path
                                                        <?php endif; ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="p-4 scrollContent"
                                onclick="redirectToMaterial('<?php echo $sequence['material_id']; ?>')">
                                <div class="row">
                                    <div class="col-9">
                                        PROGRESS
                                    </div>
                                    <div class="col text-end">
                                        <?php echo $progress['progress_percentage'] . "%" ?>
                                    </div>
                                </div>

                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width:<?php echo $progress['progress_percentage'] . "%" ?> ;"
                                        aria-valuenow="<?php echo $progress['progress_percentage'] . "%" ?>" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <!-- add learning path modal -->
                <div class="modal fade" id="learningPathModal" tabindex="-1" aria-labelledby="learningPathModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                        <form id="addLearningPathForm" method="POST">
                            <div class="modal-content">
                                <div class="row text-end">
                                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"
                                            style="font-weight: bold; font-size: larger;">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <input type="hidden" id="addMaterialIdInput" name="material_id" value="">
                                    <input type="hidden" id="addStudentIdInput" name="student_id"
                                        value=" <?php echo $studentId; ?> ">
                                    <input type="hidden" id="addActionInput" name="action" value="add">
                                    <input type="hidden" id="addMaterialTitleInput" name="material_title" value="">
                                    <strong><label for="dueDate" style="font-size:larger;">Select a Due
                                            Date</label></strong>
                                    <input type="date" id="dueDate" name="dueDate" min="" required>
                                    <i>Adding <strong><span id="materialTitle"></span></strong> to your learning
                                        path.</i>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn" style="background: var(--green); color:white;">Update
                                        Path</button>
                                    <button type="button" class="btn" style="background: lightgray"
                                        data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- remove learning modal -->
                <div class="modal fade" id="removePathModal" tabindex="-1" aria-labelledby="removePathModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form id="removeLearningPathForm" method="POST">
                            <div class="modal-content">
                                <div class="row text-end">
                                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"
                                            style="font-weight: bold; font-size: larger;">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="removeMaterialIdInput" name="material_id" value="">
                                    <input type="hidden" id="removeStudentIdInput" name="student_id"
                                        value=" <?php echo $studentId; ?> ">
                                    <input type="hidden" id="removeActionInput" name="action" value="remove">
                                    <input type="hidden" id="removeMaterialTitleHidden" name="material_title" value="">
                                    <p>Are you sure you want to remove <strong><span
                                                id="removeMaterialTitle"></span></strong> from your learning path?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger" id="removeButton">Remove</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                <p class="p-3 bg-success bg-opacity-10 border border-success border-start-0 rounded-end text-center">No
                    unfinished
                    materials found.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="suggestionContainer container mt-5">
        <h2>Suggestion</h2>
        <hr>
        <div class="suggestContent">
            <p style="background: rgba(74, 183, 136, 0.2); border-left: 4px solid var(--green); font-size: larger; border-radius: 10px;"
                class="p-4">
                <?php echo $suggestionMessage; ?>
            </p>
        </div>
    </div>

    <?php include("../../footer.php"); ?>



    <script>
        Chart.register({
            id: 'shadowPlugin',
            beforeDraw: (chart) => {
                const ctx = chart.ctx;
                ctx.save();
                ctx.shadowColor = 'rgba(0, 0, 0, 0.3)';
                ctx.shadowBlur = 15;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 5;
            },
            afterDraw: (chart) => {
                chart.ctx.restore();
            }
        });

        const progressValue = <?php echo $progressPercentage; ?>;

        const ctx = document.getElementById('progressChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, '#4CAF50');
        gradient.addColorStop(1, '#88bfa3');

        const progressChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [progressValue, 100 - progressValue],
                    backgroundColor: [gradient, '#e0e0e0'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '80%',
                responsive: false,
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                plugins: {
                    tooltip: { enabled: false },
                    legend: { display: false },
                    title: { display: false }
                }
            },
            plugins: ['shadowPlugin']
        });

        function toggleBookmark(element, materialId, studentId) {
            const isBookmarked = element.classList.contains('bookmarked');

            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', 'bookmark.php', true);
            xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            const action = isBookmarked ? 'remove' : 'add';
            const data = "action=" + action + "&material_id=" + materialId + "&student_id=" + studentId;
            xhttp.onload = function () {
                if (xhttp.status === 200) {
                    if (isBookmarked) {
                        element.classList.remove('bookmarked');
                        element.innerHTML = `
                    <svg id="bookmarkIcon" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M 6 2 C 4.8444444 2 4 2.9666667 4 4 L 4 22.039062 L 12 19.066406 L 20 22.039062 L 20 20.599609 L 20 4 C 20 3.4777778 19.808671 2.9453899 19.431641 2.5683594 C 19.05461 2.1913289 18.522222 2 18 2 L 6 2 z M 6 4 L 18 4 L 18 19.162109 L 12 16.933594 L 6 19.162109 L 6 4 z"></path>
                    </svg> Bookmark`;
                    } else {
                        element.classList.add('bookmarked');
                        element.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bookmark-fill" viewBox="0 0 16 16">
                    <path d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2"/>
                    </svg> Unmark`;
                    }
                } else {
                    console.error('Failed to toggle bookmark:', xhttp.responseText);
                }
            };

            xhttp.send(data);
        }

        function toggleLearningPath(id, title, action) {
            if (action === 'add') {
                document.getElementById('addMaterialIdInput').value = id;
                document.getElementById('addMaterialTitleInput').value = title;
                document.getElementById('materialTitle').textContent = title;
                document.getElementById('addActionInput').value = action;
                const addModal = new bootstrap.Modal(document.getElementById('learningPathModal'));
                addModal.show();
            } else if (action === 'remove') {
                document.getElementById('removeMaterialIdInput').value = id;
                document.getElementById('removeMaterialTitleHidden').value = title;
                document.getElementById('removeMaterialTitle').textContent = title;
                document.getElementById('removeActionInput').value = action;
                const removeModal = new bootstrap.Modal(document.getElementById('removePathModal'));
                removeModal.show();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const dueDateInput = document.getElementById('dueDate');
            const today = new Date().toISOString().split('T')[0]; // YYYY-MM-DD
            dueDateInput.setAttribute('min', today); //calendar only can choose present datae

            const addLearningPathForm = document.getElementById('addLearningPathForm');
            const removeLearningPathForm = document.getElementById('removeLearningPathForm');

            function sendLearningPathRequest(form, action) {
                const formData = new FormData(form);
                formData.set('action', action);

                const materialId = formData.get('material_id');
                const materialTitle = formData.get('material_title');

                const xhttp = new XMLHttpRequest();
                xhttp.open('POST', 'addLearningPath.php', true);

                xhttp.onload = function () {
                    if (xhttp.status === 200) {
                        const dropdownButton = document.querySelector(`[data-material-id="${materialId}"]`);

                        if (action === 'add') {
                            const addModal = bootstrap.Modal.getInstance(document.getElementById('learningPathModal'));
                            addModal.hide();

                            dropdownButton.setAttribute('onclick', `toggleLearningPath('${materialId}', '${materialTitle}', 'remove')`);
                            dropdownButton.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                            </svg> Remove from Learning Path
                        `;
                        } else if (action === 'remove') {
                            const removeModal = bootstrap.Modal.getInstance(document.getElementById('removePathModal'));
                            removeModal.hide();

                            dropdownButton.setAttribute('onclick', `toggleLearningPath('${materialId}', '${materialTitle}', 'add')`);
                            dropdownButton.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16zM8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                            </svg> Add to Learning Path
                        `;
                        }
                    } else {
                        console.error('Request failed:', xhttp.responseText);
                    }
                };

                xhttp.send(formData);
            }

            if (addLearningPathForm) {
                addLearningPathForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    sendLearningPathRequest(addLearningPathForm, 'add');
                });
            }

            if (removeLearningPathForm) {
                removeLearningPathForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    sendLearningPathRequest(removeLearningPathForm, 'remove');
                });
            }
        });

        //your path
        function redirectToMaterial(materialId) {
            window.location.href = `/capstone/STUDENT ( LING )/stuLearningMaterial.php?material_id=${materialId}`;
        }

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