<?php
    // learning material details
    $sqlQ1 = "SELECT material_id, material_title, material_level, material_subject, material_chapter, material_learning_type, instructor_id FROM learning_materials WHERE completion_status = '1';";
    $result1 = mysqli_query($conn, $sqlQ1);
    $learningMaterialDetails = [];


    while($tempData = mysqli_fetch_array($result1)){
        $learningMaterialDetails[] = [
                                    "id" => $tempData['material_id'],
                                    "title" => $tempData['material_title'],
                                    "level" => $tempData['material_level'],
                                    "subject" => $tempData['material_subject'],
                                    "chapter" => $tempData['material_chapter'],
                                    "learning_type" => $tempData['material_learning_type'],
                                    "instructor_id" => $tempData['instructor_id'],
        ];
    }

    // just getting name
    if($learningMaterialDetails != null){
        foreach($learningMaterialDetails as $index => $rows){
            $instructorID = $rows['instructor_id'];
            $sqlQ2 = "SELECT instructor_name FROM instructors WHERE instructor_id = '$instructorID';";
            $result2 = mysqli_query($conn, $sqlQ2);
            $instructorName = mysqli_fetch_array($result2);
    
            $learningMaterialDetails[$index]['instructor_name'] = $instructorName['instructor_name']; 
        }
    };

    // quiz details
    $sqlQ3 = "SELECT quiz_id, quiz_title, quiz_subject, quiz_level, instructor_id FROM quizzes;";
    $result3 = mysqli_query($conn, $sqlQ3);
    $quizDetails = [];

    while($tempData2 = mysqli_fetch_array($result3)){
        $quizDetails[] = [
                        "id" => $tempData2['quiz_id'],
                        "title" => $tempData2['quiz_title'],
                        "level" => $tempData2['quiz_level'],
                        "subject" => $tempData2['quiz_subject'],
                        "instructor_id" => $tempData2['instructor_id'],
        ];
    }

    // just getting name
    if($quizDetails != null){
        foreach($quizDetails as $index => $rows){
            $instructorID = $rows['instructor_id'];
            $sqlQ4 = "SELECT instructor_name FROM instructors WHERE instructor_id = '$instructorID';";
            $result4 = mysqli_query($conn, $sqlQ4);
            $instructorName = mysqli_fetch_array($result4);
    
            $quizDetails[$index]['instructor_name'] = $instructorName['instructor_name']; 
        }
    }
?>