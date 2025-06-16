<?php
include("connection.php");
session_start();



header('Content-Type: application/json');

$studentId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $learningStyle = $_POST['learning_style'];

    $sql = "UPDATE students SET student_learning_style = '$learningStyle' WHERE student_id = '$studentId'";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Learning style updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating learning style: ' . mysqli_error($conn)]);
    }
}
?>