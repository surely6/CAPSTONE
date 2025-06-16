<?php
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $studentId = $_POST['student_id'];
    $materialId = isset($_POST['material_id']) ? $_POST['material_id'] : null;
    $quizId = isset($_POST['quiz_id']) ? $_POST['quiz_id'] : null;

    if (!empty($action) && !empty($studentId) && ($materialId !== null || $quizId !== null)) {
        if ($action === 'add') {
            if ($materialId) {
                $sql = "INSERT INTO bookmarks (material_id, student_id) VALUES ('$materialId', '$studentId')";
            } else if ($quizId) {
                $sql = "INSERT INTO bookmarks (quiz_id, student_id) VALUES ('$quizId', '$studentId')";
            }
        } elseif ($action === 'remove') {
            if ($materialId) {
                $sql = "DELETE FROM bookmarks WHERE material_id = '$materialId' AND student_id = '$studentId'";
            } else if ($quizId) {
                $sql = "DELETE FROM bookmarks WHERE quiz_id = '$quizId' AND student_id = '$studentId'";
            }
        }

        $result = mysqli_query($conn, $sql);
        if ($result) {
            echo "Success";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Missing required parameters";
    }
} else {
    echo "Invalid request method";
}
?>