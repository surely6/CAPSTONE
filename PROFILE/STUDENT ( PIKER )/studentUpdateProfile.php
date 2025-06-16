<?php
session_start();
include("connection.php");


header('Content-Type: application/json');

$studentId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['student_name'] ?? null;
    $email = $_POST['student_email'] ?? null;
    $level = $_POST['student_level'] ?? null;
    $oldPassword = $_POST['old_password'] ?? null;
    $newPassword = $_POST['new_password'] ?? null;

    if (!$name || !$email || !$level || !$oldPassword) {
        echo json_encode(['success' => false, 'message' => 'Ensured all required field is key in. Old password is required to update your profile']);
        exit;
    }

    $sql = "SELECT student_password FROM students WHERE student_id = '$studentId'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if (!$row || !password_verify($oldPassword, $row['student_password'])) {
        echo json_encode(['success' => false, 'message' => 'Incorrect old password.']);
        exit;
    }

    $uploadDir = 'profileIcon/';
    $profilePicUrl = null;

    if (!empty($_FILES['profile_pic']['name'])) {
        $picName = basename($_FILES['profile_pic']['name']);
        $profilePath = $uploadDir . $picName;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profilePath)) {
            $profilePicUrl = $profilePath;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload the profile picture.']);
            exit;
        }
    }

    $updateSql = "UPDATE students SET student_name = '$name', student_email = '$email', student_level = '$level'";

    if ($profilePicUrl) {
        $updateSql .= ", profile_pic_url = '$profilePicUrl'";
    }

    if (!empty($newPassword)) {
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateSql .= ", student_password = '$newPasswordHash'";
    }

    $updateSql .= " WHERE student_id = '$studentId'";

    if (mysqli_query($conn, $updateSql)) {
        $sql = "SELECT profile_pic_url FROM students WHERE student_id = '$studentId'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        $response = [
            'success' => true,
            'message' => 'Profile updated successfully.',
            'profile_pic_url' => $row['profile_pic_url']
        ];

        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating profile: ' . mysqli_error($conn)]);
    }
}
?>