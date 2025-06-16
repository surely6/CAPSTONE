<?php
session_start();
include "connect.php";

$stu_id = $_SESSION['user_id'];
$material_id = isset($_COOKIE['Material_ID']) ? $_COOKIE['Material_ID'] : null;
$quiz_id = isset($_COOKIE['Quiz_ID']) ? $_COOKIE['Quiz_ID'] : null;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["material_feedback"]) && !empty($material_id)) {
        $feedback = trim($_POST["material_feedback"]);
        $sql = "INSERT INTO learning_material_feedback (material_feedback_id, material_id, student_id, feedback) 
            VALUES ('', '$material_id', '$stu_id', '$feedback');";

        mysqli_query($conn, $sql);
        echo "<script>window.location.href='stuLearningMaterial.php';</script>";
    } else if (isset($_POST["quiz_feedback"]) && !empty($quiz_id)) {
        $feedback = trim($_POST["quiz_feedback"]);
        $sql = "INSERT INTO quiz_feedbacks (quiz_feedback_id, quiz_id, student_id, feedback) 
            VALUES ('', '$quiz_id', '$stu_id', '$feedback');";

        mysqli_query($conn, $sql);
        echo "<script>window.location.href='stuQuiz.php';</script>";
    } else {
        echo "<script>alert('Error: No material or quiz ID found.');</script>";
    }

}
?>