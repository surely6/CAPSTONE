<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $studentId = $_SESSION['user_id'];

    if ($action === "fetchMaterials") {
        $sql = "SELECT * FROM learning_materials 
                WHERE material_id NOT IN (
                    SELECT material_id 
                    FROM sequences 
                    WHERE pathway_id = (SELECT pathway_id FROM learning_pathways WHERE student_id = '$studentId')
                )
                AND material_learning_type = (
                    SELECT student_learning_style 
                    FROM students 
                    WHERE student_id = '$studentId'
                )
                AND completion_status = 1"; // Add this condition
    } elseif ($action === "fetchBookmarks") {
        $sql = "SELECT lm.* 
                FROM bookmarks b
                INNER JOIN learning_materials lm ON b.material_id = lm.material_id
                WHERE b.student_id = '$studentId'
                AND b.quiz_id IS NULL
                AND b.material_id NOT IN (
                    SELECT material_id 
                    FROM sequences 
                    WHERE pathway_id = (SELECT pathway_id FROM learning_pathways WHERE student_id = '$studentId')
                )
                AND lm.completion_status = 1"; // Add this condition
    }

    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="col-md-6 mb-3">';
            echo '<div class="card">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlspecialchars($row['material_title']) . '</h5>';
            echo '<p class="card-text">' . htmlspecialchars($row['material_subject']) . '</p>';
            echo '<button class="btn btn-primary" onclick="addToPath(\'' . $row['material_id'] . '\', \'' . htmlspecialchars($row['material_title']) . '\')">Add to Path</button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p class="p-3 bg-success bg-opacity-10 border border-success border-start-0 rounded-end text-center">No materials found.</p>';
    }
}
?>