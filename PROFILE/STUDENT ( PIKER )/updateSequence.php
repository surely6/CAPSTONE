<?php
session_start();
include("connection.php");

$studentId = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $materialId = $_POST['material_id'];
    $newSequence = (int) $_POST['new_sequence'];

    $sql = "SELECT sequence FROM sequences WHERE material_id = '$materialId'";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentSequence = $row['sequence'];

        if ($currentSequence == $newSequence) {
            echo "No changes made.";
            exit;
        }

        if ($currentSequence < $newSequence) {
            $adjustSql = "UPDATE sequences 
                          SET sequence = sequence - 1 
                          WHERE sequence > $currentSequence AND sequence <= $newSequence";
        } else {
            $adjustSql = "UPDATE sequences 
                          SET sequence = sequence + 1 
                          WHERE sequence >= $newSequence AND sequence < $currentSequence";
        }
        mysqli_query($conn, $adjustSql);

        $updateSql = "UPDATE sequences SET sequence = $newSequence WHERE material_id = '$materialId'";
        mysqli_query($conn, $updateSql);
    } else {
        echo "Material not found.";
    }
}
?>