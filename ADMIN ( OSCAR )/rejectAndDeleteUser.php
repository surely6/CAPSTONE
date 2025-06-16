<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include "connect.php";

if (isset($_POST)) {
    $data = file_get_contents("php://input");

    $userInfo = json_decode($data, true);
    switch ($userInfo['userType']) {
        case 'pend_instructor':
            $id = $userInfo['insID'];

            $sqlQ1 = "SELECT * FROM instructors WHERE instructor_id = '$id';";
            $result1 = mysqli_query($conn, $sqlQ1);
            $rawData = mysqli_fetch_array($result1);
            $instructorDetails = [
                "InstructorName" => $rawData['instructor_name'],
                "InstructorEmail" => $rawData['instructor_email']
            ];


            $sqlQ2 = "DELETE FROM instructors WHERE instructor_id = '$id';";
            mysqli_query($conn, $sqlQ2);

            if (mysqli_affected_rows($conn) > 0) {
                $emailBody = "Dear " . $instructorDetails["InstructorName"] . ",<br><br>
                    We are sorry but your request for signing up as an instructor for Assestify has been rejected.<br><br>
                    Regards,<br>
                    Assestify Team";

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'assestifyofficial@gmail.com'; //  email
                $mail->Password = 'crrcfhifiqqplmpt'; //  app password
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('assestifyofficial@gmail.com', 'Assestify');
                $mail->addAddress($instructorDetails["InstructorEmail"]);

                $mail->isHTML(true);
                $mail->Subject = "ASSESTIFY SIGN UP REQUEST REJECTED"; // subject
                $mail->Body = $emailBody; // body

                $mail->send();
            }
            break;

        case 'instructor':
            $id = $userInfo['insID'];
            $data1storage = [];
            $data2storage = [];
            $data3storage = [];
            $data4storage = [];

            $sqlQ1 = "SELECT * FROM instructors WHERE instructor_id = '$id';";
            $result1 = mysqli_query($conn, $sqlQ1);
            $rawData = mysqli_fetch_array($result1);
            $instructorDetails = [
                "InstructorName" => $rawData['instructor_name'],
                "InstructorEmail" => $rawData['instructor_email']
            ];


            // start of obtaining other details for clean deletion
            $obtain1 = "SELECT * FROM learning_materials WHERE instructor_id = '$id';";
            $dataResult1 = mysqli_query($conn, $obtain1);
            while ($data1 = mysqli_fetch_array($dataResult1)) {
                $data1storage[] = $data1['material_id'];
            }

            // learning materials area
            foreach ($data1storage as $matID) {
                $data2storage = [];

                $obtain2 = "SELECT * FROM sequences WHERE material_id = '$matID';";
                $dataResult2 = mysqli_query($conn, $obtain2);
                while ($data2 = mysqli_fetch_array($dataResult2)) {
                    $data2storage[] = $data2['pathway_id'];
                }

                foreach ($data2storage as $pathID) {
                    $delete1 = "DELETE FROM learning_pathways WHERE pathway_id = '$pathID';";
                    mysqli_query($conn, $delete1);
                }
                $delete2 = "DELETE FROM sequences WHERE material_id = '$matID';";
                mysqli_query($conn, $delete2);

                $delete3 = "DELETE FROM learning_materials_parts WHERE material_id = '$matID';";
                $delete4 = "DELETE FROM learning_material_feedbacks WHERE material_id = '$matID';";
                $delete5 = "DELETE FROM progress WHERE material_id = '$matID';";
                $delete6 = "DELETE FROM bookmarks WHERE material_id = '$matID';";
                $delete7 = "DELETE FROM learning_materials WHERE material_id = '$matID';";
                mysqli_query($conn, $delete3);
                mysqli_query($conn, $delete4);
                mysqli_query($conn, $delete5);
                mysqli_query($conn, $delete6);
                mysqli_query($conn, $delete7);
            }

            // quiz area
            $obtain2 = "SELECT * FROM quizzes WHERE instructor_id = '$id';";
            $dataResult3 = mysqli_query($conn, $obtain2);
            while ($data2 = mysqli_fetch_array($dataResult3)) {
                $data3storage[] = $data2['quiz_id'];
            }

            foreach ($data3storage as $quizID) {
                $data4storage = [];

                $obtain3 = "SELECT * FROM questions WHERE quiz_id = '$quizID';";
                $dataResult4 = mysqli_query($conn, $obtain3);
                while ($data3 = mysqli_fetch_array($dataResult4)) {
                    $data4storage[] = $data3['question_id'];
                }

                foreach ($data4storage as $questionID) {
                    $delete8 = "DELETE FROM question_answers WHERE question_id = '$questionID';";
                    mysqli_query($conn, $delete8);
                }
                $delete9 = "DELETE FROM questions WHERE quiz_id = '$quizID';";
                mysqli_query($conn, $delete9);

                $delete10 = "DELETE FROM attempts WHERE quiz_id = '$quizID';";
                $delete11 = "DELETE FROM questions WHERE quiz_id = '$quizID';";
                $delete12 = "DELETE FROM quiz_feedbacks WHERE quiz_id = '$quizID';";
                $delete13 = "DELETE FROM quizzes WHERE quiz_id = '$quizID';";
                mysqli_query($conn, $delete10);
                mysqli_query($conn, $delete11);
                mysqli_query($conn, $delete12);
                mysqli_query($conn, $delete13);
            }

            // end of obtainment and deletion

            $delete14 = "DELETE FROM system_feedbacks WHERE instructor_id = '$id';";
            $delete15 = "DELETE FROM instructors WHERE instructor_id = '$id';";
            mysqli_query($conn, $delete14);
            mysqli_query($conn, $delete15);
            if (mysqli_affected_rows($conn) > 0) {
                $emailBody = "Dear " . $instructorDetails["InstructorName"] . ",<br><br>
                    Your instructor account for Assestify has been deleted, if this has been a mistake reply to this email.<br><br>
                    Regards,<br>
                    Assestify Team";

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'assestifyofficial@gmail.com'; //  email
                $mail->Password = 'crrcfhifiqqplmpt'; //  app password
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('assestifyofficial@gmail.com', 'Assestify');
                $mail->addAddress($instructorDetails["InstructorEmail"]);

                $mail->isHTML(true);
                $mail->Subject = "ASSESTIFY INSTRUCTOR ACCOUNT DELETED"; // subject
                $mail->Body = $emailBody; // body

                $mail->send();
            }
            break;

        case 'student':
            $id = $userInfo['stuID'];
            $data1storage = [];
            $data2storage = [];

            $sqlQ1 = "SELECT * FROM students WHERE student_id = '$id';";
            $result1 = mysqli_query($conn, $sqlQ1);
            $rawData = mysqli_fetch_array($result1);
            $studentDetails = [
                "StudentName" => $rawData['student_name'],
                "StudentEmail" => $rawData['student_email']
            ];

            // start of obtaining other details for clean deletion
            $obtain1 = "SELECT * FROM attempts WHERE student_id = '$id';";
            $dataResult1 = mysqli_query($conn, $obtain1);
            while ($data1 = mysqli_fetch_array($dataResult1)) {
                $data1storage[] = $data1['attempt_id'];
            }

            foreach ($data1storage as $attemptID) {
                $delete1 = "DELETE FROM student_answers WHERE attempt_id = '$attemptID';";
                $delete2 = "DELETE FROM progress WHERE attempt_id = '$attemptID';";
                $delete3 = "DELETE FROM attempts WHERE attempt_id = '$attemptID';";
                mysqli_query($conn, $delete1);
                mysqli_query($conn, $delete2);
                mysqli_query($conn, $delete3);
            }

            $obtain2 = "SELECT * FROM learning_pathways WHERE student_id = '$id';";
            $dataResult2 = mysqli_query($conn, $obtain2);
            while ($data2 = mysqli_fetch_array($dataResult2)) {
                $data2storage[] = $data2['pathway_id'];
            }

            foreach ($data2storage as $pathID) {
                $delete4 = "DELETE FROM sequences WHERE pathway_id = '$pathID';";
                $delete5 = "DELETE FROM learning_pathways WHERE pathway_id = '$pathID';";
                mysqli_query($conn, $delete4);
                mysqli_query($conn, $delete5);
            }
            // end of obtainment and deletion

            $delete6 = "DELETE FROM bookmarks WHERE student_id = '$id';";
            $delete7 = "DELETE FROM goals WHERE student_id = '$id';";
            $delete8 = "DELETE FROM learning_material_feedbacks WHERE student_id = '$id';";
            $delete9 = "DELETE FROM quiz_feedback WHERE student_id = '$id';";
            $delete10 = "DELETE FROM system_feedback WHERE student_id = '$id';";
            $delete11 = "DELETE FROM responses WHERE student_id = '$id';";
            $delete12 = "DELETE FROM students WHERE student_id = '$id';";
            mysqli_query($conn, $delete6);
            mysqli_query($conn, $delete7);
            mysqli_query($conn, $delete8);
            mysqli_query($conn, $delete9);
            mysqli_query($conn, $delete10);
            mysqli_query($conn, $delete11);
            mysqli_query($conn, $delete12);
            if (mysqli_affected_rows($conn) > 0) {
                $emailBody = "Dear " . $studentDetails["StudentName"] . ",<br><br>
                    Your student account for Assestify has been deleted, if this has been a mistake reply to this email.<br><br>
                    Regards,<br>
                    Assestify Team";

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'assestifyofficial@gmail.com'; //  email
                $mail->Password = 'crrcfhifiqqplmpt'; //  app password
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('assestifyofficial@gmail.com', 'Assestify');
                $mail->addAddress($studentDetails["StudentEmail"]);

                $mail->isHTML(true);
                $mail->Subject = "ASSESTIFY STUDENT ACCOUNT DELETED"; // subject
                $mail->Body = $emailBody; // body

                $mail->send();
            }
            break;
    }
}

?>