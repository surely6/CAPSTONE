<?php
    include "connect.php";
    if(isset($_POST)){
        $data = file_get_contents("php://input");

        $feedbackInfo = json_decode($data, true);
        $feedbackID = $feedbackInfo['feedbackID'];

        $delete = "DELETE FROM system_feedbacks WHERE system_feedback_id = '$feedbackID';";
        mysqli_query($conn, $delete);
    }
?>