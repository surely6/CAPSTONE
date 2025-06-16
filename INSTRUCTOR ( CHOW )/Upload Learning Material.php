<?php

session_start();
if (!isset($_SESSION)) {
    die("Instructor ID is not set in the session.");
}

$instructorID = $_SESSION['user_id'];

include('connect.php');



if (isset($_POST)) {
    var_dump($_POST);

    $id = isset($_POST["ID"]) ? $_POST["ID"] : null;
    $form = $_POST['form'];
    $subject = $_POST['subject'];
    $chapter = $_POST['chapter'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $learning_style = $_POST['learning_style'];
    $delete = isset($_POST['delete']) && $_POST['delete'] == "true" ? true : false;
    $saved = isset($_POST['saved']) && $_POST['saved'] == "true" ? true : false;
    $completion = isset($_POST['completion']) && $_POST['completion'] == "true" ? true : false;
    $content = $_POST['content'];

    $date = date('Y-m-d');

    $Amount = 0;

    echo $id;
    var_dump($saved);
    if ($saved) {

        $query = "SELECT MAX(material_id) FROM learning_materials";
        $sql = mysqli_query($conn, $query);

        $row = mysqli_fetch_row($sql);
        $id = $row[0];

        if ($delete) {

            mysqli_query($conn, "DELETE FROM sequences WHERE material_id = $id");
            mysqli_query($conn, "DELETE FROM bookmarks WHERE material_id = $id");
            mysqli_query($conn, "DELETE FROM progress WHERE material_id = $id");

            $deleteQUERY = "DELETE FROM learning_material_parts WHERE material_id = $id";
            $deleteSQL = mysqli_query($conn, $deleteQUERY);

            $deleteQUERY = "DELETE FROM learning_material_feedback WHERE material_id = $id";
            $deleteSQL = mysqli_query($conn, $deleteQUERY);

            $deleteQUERY = "DELETE FROM learning_materials WHERE material_id = $id";
            $deleteSQL = mysqli_query($conn, $deleteQUERY);
        } else {
            $materialQUERY = "UPDATE learning_materials SET 
            material_title = '$title', material_level = '$form', material_subject = '$subject', material_chapter = '$chapter', 
            material_learning_type = '$learning_style', completion_status = '$completion', material_description = '$description',
            date_made = '$date'
            WHERE material_id = '$id'";
            $materialSQL = mysqli_query($conn, $materialQUERY);

            $partAmountQUERY = "SELECT COUNT(*) FROM learning_material_parts WHERE material_id = $id";
            $partAmountSQL = mysqli_query($conn, $partAmountQUERY);

            $row = mysqli_fetch_row($partAmountSQL);
            $Amount = $row[0];
            echo $id;
            echo $Amount;
            echo "<br>";

            $length = max($Amount, count($content));

            for ($i = 0; $i < $length; $i++) {
                var_dump($i);
                $part = $i + 1;
                if ($i >= $Amount && $i < count($content)) {
                    // Insert new part
                    $partContent = $content[$i];
                    $partsQUERY = "INSERT INTO learning_material_parts (material_id, part, material_content) VALUES 
                    ('$id', '$part', '$partContent')";
                    $partsSQL = mysqli_query($conn, $partsQUERY);
                } else if ($i >= count($content)) {
                    // Delete extra part
                    $partsQUERY = "DELETE FROM learning_material_parts 
                    WHERE material_id = $id AND part = $part";
                    $partsSQL = mysqli_query($conn, $partsQUERY);
                } else {
                    // Update existing part
                    $partContent = $content[$i];
                    $partsQUERY = "UPDATE learning_material_parts 
                    SET material_content = '$partContent' 
                    WHERE material_id = $id AND part = $part";
                    $partsSQL = mysqli_query($conn, $partsQUERY);
                }
            }
        }
    } else {
        $materialQUERY = "INSERT INTO learning_materials (instructor_id, material_title, material_level, material_subject, material_chapter, material_learning_type, completion_status, material_description, date_made) VALUES 
        ('$instructorID','$title','$form','$subject','$chapter','$learning_style','$completion','$description','$date')";
        $materialSQL = mysqli_query($conn, $materialQUERY);

        // Get the auto-incremented material_id
        $id = mysqli_insert_id($conn);

        for ($i = 0; $i < count($content); $i++) {
            $part = $i + 1;
            $partContent = $content[$i];
            $partsQUERY = "INSERT INTO learning_material_parts (material_id, part, material_content) VALUES 
            ('$id', '$part', '$partContent')";
            $partsSQL = mysqli_query($conn, $partsQUERY);
        }
    }
    mysqli_close($conn);
}

?>