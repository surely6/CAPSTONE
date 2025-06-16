<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Check if material_id is provided
if (!isset($_GET['material_id']) || !is_numeric($_GET['material_id'])) {
    $_SESSION['error'] = "Invalid module selection";
    header("Location: module_selection.php");
    exit();
}

$materialId = $_GET['material_id'];

// Verify the material exists
$stmt = $conn->prepare("SELECT * FROM learning_materials WHERE material_id = ?");
$stmt->bind_param("i", $materialId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Module not found";
    header("Location: module_selection.php");
    exit();
}

// Get the user's learning pathway ID
$stmt = $conn->prepare("SELECT pathway_id FROM learning_pathways WHERE student_id = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Create a new learning pathway for the user
    $currentDateTime = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO learning_pathways (student_id, last_datetime_altered) VALUES (?, ?)");
    $stmt->bind_param("ss", $userId, $currentDateTime);
    $stmt->execute();
    $pathwayId = $conn->insert_id;
} else {
    $pathway = $result->fetch_assoc();
    $pathwayId = $pathway['pathway_id'];
    
    // Update the last altered time
    $currentDateTime = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("UPDATE learning_pathways SET last_datetime_altered = ? WHERE pathway_id = ?");
    $stmt->bind_param("si", $currentDateTime, $pathwayId);
    $stmt->execute();
}

// Check if the material is already in the pathway
$stmt = $conn->prepare("SELECT * FROM sequences WHERE pathway_id = ? AND material_id = ?");
$stmt->bind_param("ii", $pathwayId, $materialId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['info'] = "This module is already in your learning path";
    header("Location: learning_path.php");
    exit();
}

// Get the next sequence number
$stmt = $conn->prepare("SELECT MAX(sequence) as max_seq FROM sequences WHERE pathway_id = ?");
$stmt->bind_param("i", $pathwayId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$sequence = ($row['max_seq'] !== null) ? $row['max_seq'] + 1 : 1;

// Add the material to the learning pathway
$stmt = $conn->prepare("INSERT INTO sequences (pathway_id, material_id, sequence) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $pathwayId, $materialId, $sequence);

if ($stmt->execute()) {
    $_SESSION['success'] = "Module added to your learning path";
} else {
    $_SESSION['error'] = "Failed to add module: " . $stmt->error;
}

// Redirect back to the learning path page
header("Location: learning_path.php");
exit();
?>