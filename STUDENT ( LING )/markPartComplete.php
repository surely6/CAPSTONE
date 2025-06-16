<?php
session_start();
include "connect.php";

$input = json_decode(file_get_contents("php://input"), true);

$stu_id = $_SESSION['user_id'];
$material_id = $input['material_id'];
$part_id = $input['part_id'];
$current_time = date('Y-m-d H:i:s');

$material_parts_query = "SELECT part FROM learning_material_parts WHERE part_id = '$part_id'";
$material_parts_sql = mysqli_query($conn, $material_parts_query);
$material_part = "";
if (mysqli_num_rows($material_parts_sql) > 0) {
    $row = mysqli_fetch_assoc($material_parts_sql);
    $material_part = $row['part'];
}

$stu_progress_query = "SELECT * FROM progress WHERE student_id = '$stu_id' AND material_id = '$material_id'";
$stu_progress_sql = mysqli_query($conn, $stu_progress_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("POST received: stu_id=$stu_id, material_id=$material_id, part_id=$part_id, material_part=$material_part");

    if (empty($stu_id) || empty($material_id) || empty($part_id) || empty($material_part)) {
        error_log("Missing required data");
        echo json_encode(['success' => false, 'error' => 'Missing data']);
        exit;
    }
    if (mysqli_num_rows($stu_progress_sql) > 0) {
        $row = mysqli_fetch_assoc($stu_progress_sql);
        $exist_part = explode(",", $row['progress']);

        if (!in_array($material_part, $exist_part)) {
            $exist_part[] = $material_part;
            $add_part = implode(", ", $exist_part);

            $update_progress = "UPDATE progress 
                                    SET progress = '$add_part', last_datetime = '$current_time' 
                                    WHERE student_id = '$stu_id' AND material_id = '$material_id'";
            mysqli_query($conn, $update_progress);
        }
    } else {
        $insert_progress = "INSERT INTO progress 
                                (attempt_id, material_id, student_id, progress, last_datetime)
                                VALUES 
                                ('', '$material_id', '$stu_id', '$material_part', '$current_time')";
        mysqli_query($conn, $insert_progress);
    }
    echo json_encode(['success' => true]);
    exit;
}
?>