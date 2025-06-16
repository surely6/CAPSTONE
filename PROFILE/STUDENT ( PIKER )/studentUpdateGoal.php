<?php
session_start();
include("connection.php");


header('Content-Type: application/json');
$studentId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $goalType = $_POST['goal_type'] ?? null;
    $timeSet = $_POST['time_set'] ?? null;

    if (!$goalType || !$timeSet) {
        echo json_encode(['success' => false, 'message' => 'Invalid input: Goal type or time not provided.']);
        exit;
    }

    $daily = ($goalType === 'daily') ? 1 : 0;
    $weekly = ($goalType === 'weekly') ? 1 : 0;

    $checkSql = "SELECT * FROM goals WHERE student_id = '$studentId'";
    $result = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($result) > 0) {
        $sql = "UPDATE goals SET daily = $daily, weekly = $weekly, time_set = $timeSet WHERE student_id = '$studentId'";
    } else {
        $sql = "INSERT INTO goals (student_id, daily, weekly, time_set) VALUES ('$studentId', $daily, $weekly, $timeSet)";
    }

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Goal saved successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving goal: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>