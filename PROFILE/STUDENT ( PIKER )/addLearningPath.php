<?php
session_start();
include("connection.php");

$studentId = $_SESSION['user_id'];

$learningPathSql = "SELECT * FROM learning_pathways WHERE student_id = '$studentId'";
$result = mysqli_query($conn, $learningPathSql);
$row = mysqli_fetch_assoc($result);

if ($row) {
    $pathwayId = $row['pathway_id'];

} else {
    $currentDateTime = date('Y-m-d H:i:s');
    $createPathwaySql = "INSERT INTO learning_pathways (student_id, last_datetime_altered) VALUES ('$studentId', '$currentDateTime')";

    if (mysqli_query($conn, $createPathwaySql)) {
        $pathwayId = mysqli_insert_id($conn);
    } else {
        die("Error creating pathway: " . mysqli_error($conn));
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $materialId = $_POST['material_id'];
    $studentId = $_POST['student_id'];
    $dueDate = $_POST['dueDate'] ?? null;

    if ($action === 'add') {
        if (!empty($materialId) && !empty($studentId) && !empty($dueDate)) {

            $sequenceSql = "SELECT MAX(sequence) AS maxSequence FROM sequences WHERE pathway_id = '$pathwayId'";
            $sequenceResult = mysqli_query($conn, $sequenceSql);
            $sequenceRow = mysqli_fetch_assoc($sequenceResult);

            if ($sequenceRow && $sequenceRow['maxSequence'] !== null) {
                $sequence = $sequenceRow['maxSequence'] + 1;
            } else {
                $sequence = 1;
            }

            $insertSql = "INSERT INTO sequences (pathway_id, material_id, due_date, sequence) VALUES ('$pathwayId', '$materialId',  '$dueDate', '$sequence')";

            if (!mysqli_query($conn, $insertSql)) {
                die("Error add path: " . mysqli_error($conn));
            }
        }
    } elseif ($action === 'remove') {
        if (!empty($materialId) && !empty($studentId)) {
            $sql = "DELETE FROM sequences WHERE pathway_id = '$pathwayId' AND material_id = '$materialId'";

            if (!mysqli_query($conn, $sql)) {
                die("Error remove path: " . mysqli_error($conn));
            }
        }
    }
}
?>