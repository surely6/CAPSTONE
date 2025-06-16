<?php
include 'connect.php';
if (isset($_POST)) {
    $data = file_get_contents("php://input");

    $deletionDetails = json_decode($data, true);
    switch ($deletionDetails['deleteType']) {
        case 'learningMaterial':
            $id = $deletionDetails['matID'];

            $delete1 = "DELETE FROM sequences WHERE material_id = '$id';";
            $delete2 = "DELETE FROM bookmarks WHERE material_id = '$id';";
            $delete3 = "DELETE FROM learning_material_parts WHERE material_id = '$id';";
            $delete4 = "DELETE FROM learning_material_feedbacks WHERE material_id = '$id';";
            $delete5 = "DELETE FROM learning_materials WHERE material_id = '$id';";
            mysqli_query($conn, $delete1);
            mysqli_query($conn, $delete2);
            mysqli_query($conn, $delete3);
            mysqli_query($conn, $delete4);
            mysqli_query($conn, $delete5);
            break;

        case 'quiz':
            $id = $deletionDetails['quizID'];
            $data1storage = [];

            $obtain1 = "SELECT * FROM questions WHERE quiz_id = '$id';";
            $dataResult1 = mysqli_query($conn, $obtain1);
            while ($data1 = mysqli_fetch_array($dataResult1)) {
                $data1storage[] = $data1['question_id'];
            }

            foreach ($data1storage as $questionID) {
                $delete1 = "DELETE FROM question_answers WHERE question_id = '$questionID';";
                $delete2 = "DELETE FROM student_answers WHERE question_id = '$questionID';";
                $delete3 = "DELETE FROM questions WHERE question_id = '$questionID';";
                mysqli_query($conn, $delete1);
                mysqli_query($conn, $delete2);
                mysqli_query($conn, $delete3);
            }

            $delete4 = "DELETE FROM attempt WHERE quiz_id = '$id';";
            $delete5 = "DELETE FROM quiz_feedbacks WHERE quiz_id = '$id';";
            $delete6 = "DELETE FROM quizzes WHERE quiz_id = '$id';";
            mysqli_query($conn, $delete4);
            mysqli_query($conn, $delete5);
            mysqli_query($conn, $delete6);
            break;
    }
}
?>