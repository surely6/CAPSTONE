<?php
session_start();
include "connect.php";
include "dateTimeProcesses.php";
include("../block.php");

if (isset($_GET['arrangementType'])) {
    $arrangementType = $_GET['arrangementType'];
}
$Feedbacks = [];
// using sql to filter these would be easier due to having two types of filters
// cause like thejs filter is used for after display and this is for display process
switch ($arrangementType) {
    //filtering by recent
    case 'recent':
        $sql = "SELECT * FROM system_feedbacks ORDER BY datetime_of_feedback DESC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($resultRow = mysqli_fetch_array($result)) {
                $dateOfFeedback = formatDateTime($resultRow['datetime_of_feedback']);
                $userID = $resultRow['student_id'] ?? $resultRow['instructor_id'];
                $userName = "";

                if ($resultRow['student_id'] != null) {
                    $id = $resultRow['student_id'];
                    $sql2 = "SELECT * FROM students WHERE student_id = '$id'";
                    $result2 = mysqli_query($conn, $sql2);
                    $studentDetail = mysqli_fetch_array($result2);
                    $userName = $studentDetail['student_name'];
                    $profilePic = !empty($studentDetail['profile_pic_url'])
                        ? $studentDetail['profile_pic_url']
                        : 'profileIcon/profileIcon.png';
                    $userType = "student";
                } else if ($resultRow['instructor_id'] != null) {
                    $id = $resultRow['instructor_id'];
                    $sql2 = "SELECT * FROM instructors WHERE instructor_id = '$id'";
                    $result2 = mysqli_query($conn, $sql2);
                    $instructorDetail = mysqli_fetch_array($result2);
                    $userName = $instructorDetail['instructor_name'];
                    $profilePic = !empty($instructorDetail['profile_pic_url'])
                        ? $instructorDetail['profile_pic_url']
                        : 'uploads/profileIcon.png';
                    $userType = "instructor";
                }

                $data = [
                    "FeedbackID" => $resultRow['system_feedback_id'],
                    "FeedbackText" => $resultRow['feedback'],
                    "DateOfFeedback" => $dateOfFeedback,
                    "UserName" => $userName,
                    "UserType" => $userType,
                    "ProfilePic" => $profilePic,
                ];

                array_push($Feedbacks, $data);
            }
            ;
        }
        ;

        echo json_encode($Feedbacks);
        break;

    //filtering by oldest
    case 'oldest':
        $sql = "SELECT * FROM system_feedbacks ORDER BY datetime_of_feedback ASC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($resultRow = mysqli_fetch_array($result)) {
                $dateOfFeedback = formatDateTime($resultRow['datetime_of_feedback']);
                $userID = $resultRow['student_id'] ?? $resultRow['instructor_id'];
                $userName = "";

                if ($resultRow['student_id'] != null) {
                    $id = $resultRow['student_id'];
                    $sql2 = "SELECT * FROM students WHERE student_id = '$id'";
                    $result2 = mysqli_query($conn, $sql2);
                    $studentDetail = mysqli_fetch_array($result2);
                    $userName = $studentDetail['student_name'];
                    $profilePic = $studentDetail['profile_pic_url'];
                    $userType = "student";
                } else if ($resultRow['instructor_id'] != null) {
                    $id = $resultRow['instructor_id'];
                    $sql2 = "SELECT * FROM instructors WHERE instructor_id = '$id'";
                    $result2 = mysqli_query($conn, $sql2);
                    $instructorDetail = mysqli_fetch_array($result2);
                    $userName = $instructorDetail['instructor_name'];
                    $profilePic = $instructorDetail['profile_pic_url'];
                    $userType = "instructor";
                }

                $data = [
                    "FeedbackID" => $resultRow['system_feedback_id'],
                    "FeedbackText" => $resultRow['feedback'],
                    "DateOfFeedback" => $dateOfFeedback,
                    "UserName" => $userName,
                    "UserType" => $userType,
                    "ProfilePic" => $profilePic,
                ];

                array_push($Feedbacks, $data);
            }
            ;
        }
        ;

        echo json_encode($Feedbacks);
        break;
}



?>