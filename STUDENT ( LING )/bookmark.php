<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $materialId = $_POST['material_id'] ?? null;
    $quizId = $_POST['quiz_id'] ?? null;
    $studentId = $_POST['student_id'];
    $current_time = date('Y-m-d H:i:s');


    if (!empty($action) && !empty($studentId)) {
        if ($action === 'add') {
            if (!empty($materialId)) {
                // Add bookmark for material
                $sql = "INSERT INTO bookmarks (material_id, student_id, date_added) VALUES ('$materialId', '$studentId', '$current_time')";
            } else if (!empty($quizId)) {
                // Add bookmark for quiz
                $sql = "INSERT INTO bookmarks (quiz_id, student_id, date_added) VALUES ('$quizId', '$studentId', '$current_time')";
            }
        } else if ($action === 'remove') {
            if (!empty($materialId)) {
                // Remove bookmark for material
                $sql = "DELETE FROM bookmarks WHERE material_id = '$materialId' AND student_id = '$studentId'";
            } else if (!empty($quizId)) {
                // Remove bookmark for quiz
                $sql = "DELETE FROM bookmarks WHERE quiz_id = '$quizId' AND student_id = '$studentId'";
            }
        }
        mysqli_query($conn, $sql);
    }
}
?>