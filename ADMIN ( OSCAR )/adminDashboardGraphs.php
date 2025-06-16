<?php
session_start();
include "connect.php";
include("../block.php");

if (isset($_GET['graphType'])) {
    $graphType = $_GET['graphType'];
}
//obtaining graph type from the html

switch ($graphType) {
    // student section
    case 'studentLearningStyle':
        $sqlQ1 = "SELECT student_learning_style, COUNT(*) as count FROM students GROUP BY student_learning_style;";
        $result1 = mysqli_query($conn, $sqlQ1);

        $learnCount = [
            'VISUAL' => 0,
            'READ_WRITE' => 0,
            'AUDIO' => 0
        ];

        while ($resultRow = mysqli_fetch_array($result1)) {
            $learningStyle = strtoupper($resultRow['student_learning_style']);
            $learnCount[$learningStyle] = $resultRow['count'];
        }

        echo json_encode([
            'label' => 'Learning Styles',
            'labels' => ['VISUAL', 'READ & WRITE', 'AUDIO'],
            'values' => [$learnCount['VISUAL'], $learnCount['READ_WRITE'], $learnCount['AUDIO']]
        ]);
        break;

    case 'studentForm':
        $sqlQ1 = "SELECT student_level, COUNT(*) as count FROM students GROUP BY student_level;";
        $result1 = mysqli_query($conn, $sqlQ1);

        $levelCount = [
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0
        ];

        while ($resultRow = mysqli_fetch_array($result1)) {
            $level = $resultRow['student_level'];
            $levelCount[$level] = $resultRow['count'];
        }
        echo json_encode([
            'label' => 'Forms',
            'labels' => ['FORM 1', 'FORM 2', 'FORM 3', 'FORM 4', 'FORM 5'],
            'values' => [$levelCount['1'], $levelCount['2'], $levelCount['3'], $levelCount['4'], $levelCount['5']]
        ]);
        break;



    // module section
    case 'moduleLearningStyle':
        $sqlQ2 = "SELECT material_learning_type, COUNT(*) as count FROM learning_materials GROUP BY material_learning_type;";
        $result2 = mysqli_query($conn, $sqlQ2);

        $learnCount = [
            'VISUAL' => 0,
            'READ_WRITE' => 0,
            'AUDIO' => 0
        ];

        while ($resultRow = mysqli_fetch_array($result2)) {
            $learningStyle = strtoupper($resultRow['material_learning_type']);
            $learnCount[$learningStyle] = $resultRow['count'];
        }

        echo json_encode([
            'label' => 'Learning Styles',
            'labels' => ['VISUAL', 'READ & WRITE', 'AUDIO'],
            'values' => [$learnCount['VISUAL'], $learnCount['READ_WRITE'], $learnCount['AUDIO']]
        ]);
        break;

    case 'moduleForm':
        $sqlQ2 = "SELECT material_level, COUNT(*) as count FROM learning_materials GROUP BY material_level;";
        $result2 = mysqli_query($conn, $sqlQ2);

        $levelCount = [
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0
        ];

        while ($resultRow = mysqli_fetch_array($result2)) {
            $level = $resultRow['material_level'];
            $levelCount[$level] = $resultRow['count'];
        }
        echo json_encode([
            'label' => 'Forms',
            'labels' => ['FORM 1', 'FORM 2', 'FORM 3', 'FORM 4', 'FORM 5'],
            'values' => [$levelCount['1'], $levelCount['2'], $levelCount['3'], $levelCount['4'], $levelCount['5']]
        ]);
        break;

    case 'moduleSubject':
        $sqlQ2 = "SELECT material_subject, COUNT(*) as count FROM learning_materials GROUP BY material_subject;";
        $result2 = mysqli_query($conn, $sqlQ2);

        $subjectCount = [
            'ENGLISH' => 0,
            'MALAY' => 0,
            'HISTORY' => 0,
            'MATHEMATICS' => 0,
            'SCIENCE' => 0,
            'GEOGRAPHY' => 0,
            'PHYSICS' => 0,
            'ADDITIONAL MATHEMATICS' => 0,
            'CHEMISTRY' => 0,
            'BIOLOGY' => 0,
            'ACCOUNTING' => 0,
            'BUSINESS' => 0,
            'ECONOMY' => 0
        ];

        while ($resultRow = mysqli_fetch_array($result2)) {
            $subject = strtoupper($resultRow['material_subject']);
            $subjectCount[$subject] = $resultRow['count'];
        }

        echo json_encode([
            'label' => 'Subjects',
            'labels' => ['ENGLISH', 'MALAY', 'HISTORY', 'MATHEMATICS', 'SCIENCE', 'GEOGRAPHY', 'PHYSICS', 'ADDITIONAL MATHEMATICS', 'CHEMISTRY', 'BIOLOGY', 'ACCOUNTING', 'BUSINESS', 'ECONOMY'],
            'values' => [
                $subjectCount['ENGLISH'],
                $subjectCount['MALAY'],
                $subjectCount['HISTORY'],
                $subjectCount['MATHEMATICS'],
                $subjectCount['SCIENCE'],
                $subjectCount['GEOGRAPHY'],
                $subjectCount['PHYSICS'],
                $subjectCount['ADDITIONAL MATHEMATICS'],
                $subjectCount['CHEMISTRY'],
                $subjectCount['BIOLOGY'],
                $subjectCount['ACCOUNTING'],
                $subjectCount['BUSINESS'],
                $subjectCount['ECONOMY']
            ]
        ]);
        break;



    // quiz section
    case 'quizForm':
        $sqlQ3 = "SELECT quiz_level, COUNT(*) as count FROM quizzes GROUP BY quiz_level;";
        $result3 = mysqli_query($conn, $sqlQ3);

        $levelCount = [
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0
        ];

        while ($resultRow = mysqli_fetch_array($result3)) {
            $level = $resultRow['quiz_level'];
            $levelCount[$level] = $resultRow['count'];
        }
        echo json_encode([
            'label' => 'Forms',
            'labels' => ['FORM 1', 'FORM 2', 'FORM 3', 'FORM 4', 'FORM 5'],
            'values' => [$levelCount['1'], $levelCount['2'], $levelCount['3'], $levelCount['4'], $levelCount['5']]
        ]);
        break;

    case 'quizSubject':
        $sqlQ3 = "SELECT quiz_subject, COUNT(*) as count FROM quizzes GROUP BY quiz_subject;";
        $result3 = mysqli_query($conn, $sqlQ3);

        $subjectCount = [
            'ENGLISH' => 0,
            'MALAY' => 0,
            'HISTORY' => 0,
            'MATHEMATICS' => 0,
            'SCIENCE' => 0,
            'GEOGRAPHY' => 0,
            'PHYSICS' => 0,
            'ADDITIONAL MATHEMATICS' => 0,
            'CHEMISTRY' => 0,
            'BIOLOGY' => 0,
            'ACCOUNTING' => 0,
            'BUSINESS' => 0,
            'ECONOMY' => 0
        ];

        while ($resultRow = mysqli_fetch_array($result3)) {
            $subject = strtoupper($resultRow['quiz_subject']);
            $subjectCount[$subject] = $resultRow['count'];
        }

        echo json_encode([
            'label' => 'Subjects',
            'labels' => ['ENGLISH', 'MALAY', 'HISTORY', 'MATHEMATICS', 'SCIENCE', 'GEOGRAPHY', 'PHYSICS', 'ADDITIONAL MATHEMATICS', 'CHEMISTRY', 'BIOLOGY', 'ACCOUNTING', 'BUSINESS', 'ECONOMY'],
            'values' => [
                $subjectCount['ENGLISH'],
                $subjectCount['MALAY'],
                $subjectCount['HISTORY'],
                $subjectCount['MATHEMATICS'],
                $subjectCount['SCIENCE'],
                $subjectCount['GEOGRAPHY'],
                $subjectCount['PHYSICS'],
                $subjectCount['ADDITIONAL MATHEMATICS'],
                $subjectCount['CHEMISTRY'],
                $subjectCount['BIOLOGY'],
                $subjectCount['ACCOUNTING'],
                $subjectCount['BUSINESS'],
                $subjectCount['ECONOMY']
            ]
        ]);
        break;
}
?>