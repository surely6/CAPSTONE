<?php
include('connect.php');
include('font.php');

$unfinished;
$finished;

$unfinsihedQUERY = "SELECT `quiz_id`,`quiz_title`, `quiz_total_questions`, `quiz_subject`, `quiz_level` 
FROM quizzes WHERE `instructor_id` = 'L001' AND `completion_status` = '0'";
$unfinishedSQL = mysqli_query($condb, $unfinsihedQUERY);

if ( mysqli_num_rows($unfinishedSQL) > 0){
    $unfinished = mysqli_fetch_all($unfinishedSQL, MYSQLI_ASSOC);
}

$finsihedQUERY = "SELECT `quiz_id`,`quiz_title`, `quiz_total_questions`, `quiz_subject`, `quiz_level` 
FROM quizzes WHERE `instructor_id` = 'L001' AND `completion_status` = '1'";
$finishedSQL = mysqli_query($condb, $finsihedQUERY);

if ( mysqli_num_rows($finishedSQL) > 0){
    $finished = mysqli_fetch_all($finishedSQL, MYSQLI_ASSOC);
}

var_dump($unfinished);
echo"<br>";
echo"<br>";
var_dump($finished);
?>