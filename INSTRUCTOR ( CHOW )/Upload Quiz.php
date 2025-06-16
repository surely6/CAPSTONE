<?php
include('connect.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Instructor ID is not set in the session.");
}

$instructorID = $_SESSION['user_id'];

if (isset($_POST)) {
    // Get POST data
    $id = isset($_POST["ID"]) ? $_POST["ID"] : null;
    $form = $_POST['form'];
    $subject = $_POST['subject'];
    $chapter = isset($_POST['chapter']) ? $_POST['chapter'] : '';
    $title = $_POST['title'];
    $description = $_POST['description'];
    $delete = $_POST['delete'] == "true" ? true : false;
    $saved = $_POST['saved'] == "true" ? true : false;
    $completion = $_POST['completion'] == "true" ? true : false;
    $question = $_POST['question'];
    $question_type = $_POST['question_type'];
    $answer = $_POST['answer'];
    $correct = $_POST['correct'];
    $date = date('Y-m-d');

    if ($saved) {

        $query = "SELECT MAX(quiz_id) FROM quizzes";
        $sql = mysqli_query($conn, $query);

        $row = mysqli_fetch_row($sql);
        $id = $row[0];

        if ($delete) {
            // Delete all related data
            $questionIDQUERY = "SELECT question_id FROM questions WHERE quiz_id = $id";
            $questionIDSQL = mysqli_query($conn, $questionIDQUERY);
            $rows = mysqli_fetch_all($questionIDSQL);

            foreach ($rows as $row) {
                $qid = $row[0];
                mysqli_query($conn, "DELETE FROM student_answers WHERE question_id = $qid");
                mysqli_query($conn, "DELETE FROM question_answers WHERE question_id = $qid");
            }

            mysqli_query($conn, "DELETE FROM questions WHERE quiz_id = $id");
            mysqli_query($conn, "DELETE FROM attempts WHERE quiz_id = $id");
            mysqli_query($conn, "DELETE FROM bookmarks WHERE quiz_id = $id");
            mysqli_query($conn, "DELETE FROM quiz_feedbacks WHERE quiz_id = $id");
            mysqli_query($conn, "DELETE FROM quizzes WHERE quiz_id = $id");
        } else {
            // Update quiz data
            $quizQUERY = "UPDATE quizzes SET 
                quiz_title = '$title', 
                quiz_level = '$form', 
                quiz_subject = '$subject',
                quiz_total_questions = '" . count($question) . "',
                quiz_description = '$description', 
                quiz_chapter = " . (is_numeric($chapter) && $chapter !== '' ? intval($chapter) : "NULL") . ",
                completion_status = '$completion', 
                date_made = '$date'
                WHERE quiz_id = $id";
            $quizSQL = mysqli_query($conn, $quizQUERY);

            // Fetch all existing question IDs for this quiz
            $questionIDQUERY = "SELECT question_id FROM questions WHERE quiz_id = $id";
            $questionIDSQL = mysqli_query($conn, $questionIDQUERY);
            $existingQuestions = mysqli_fetch_all($questionIDSQL, MYSQLI_NUM);

            $existingCount = count($existingQuestions);
            $newCount = count($question);

            // Update or insert questions/answers
            for ($i = 0; $i < $newCount; $i++) {
                if ($i < $existingCount) {
                    // Update existing question and answer
                    $qid = $existingQuestions[$i][0];
                    $questionQUERY = "UPDATE questions
                        SET question = '{$question[$i]}', question_style_id = '{$question_type[$i]}'
                        WHERE question_id = '$qid'";
                    mysqli_query($conn, $questionQUERY);

                    $answerQUERY = "UPDATE question_answers
                        SET answer_list = '" . implode(",", $answer[$i]) . "', correct_answer = '" . implode(",", $correct[$i]) . "'
                        WHERE question_id = '$qid'";
                    mysqli_query($conn, $answerQUERY);
                } else {
                    // Insert new question and answer
                    $questionQUERY = "INSERT INTO questions (quiz_id, question, question_style_id) VALUES 
                        ('$id', '{$question[$i]}', '{$question_type[$i]}')";
                    mysqli_query($conn, $questionQUERY);
                    $questionID = mysqli_insert_id($conn);

                    $answerQUERY = "INSERT INTO question_answers (question_id, answer_list, correct_answer) VALUES 
                        ('$questionID', '" . implode(",", $answer[$i]) . "', '" . implode(",", $correct[$i]) . "')
                        ON DUPLICATE KEY UPDATE 
                        answer_list = '" . implode(",", $answer[$i]) . "', 
                        correct_answer = '" . implode(",", $correct[$i]) . "'";
                    mysqli_query($conn, $answerQUERY);
                }
            }

            // Delete extra questions/answers if questions were removed
            if ($existingCount > $newCount) {
                for ($i = $newCount; $i < $existingCount; $i++) {
                    $qid = $existingQuestions[$i][0];
                    mysqli_query($conn, "DELETE FROM question_answers WHERE question_id = '$qid'");
                    mysqli_query($conn, "DELETE FROM questions WHERE question_id = '$qid'");
                }
            }
        }
    } else {
        // Insert new quiz
        $quizQUERY = "INSERT INTO quizzes (instructor_id, quiz_title, quiz_total_questions, quiz_subject, quiz_level, quiz_description, quiz_chapter, completion_status, date_made) VALUES 
    ('$instructorID', '$title', '" . count($question) . "', '$subject', '$form', '$description', '$chapter', '$completion', '$date')";
        $quizSQL = mysqli_query($conn, $quizQUERY);
        if (!$quizSQL) {
            die("Quiz insert failed: " . mysqli_error($conn));
        }
        $quizID = mysqli_insert_id($conn);

        // Insert questions and answers
        for ($i = 0; $i < count($question); $i++) {
            $questionQUERY = "INSERT INTO questions (quiz_id, question, question_style_id) VALUES 
                ('$quizID', '{$question[$i]}', '{$question_type[$i]}')";
            mysqli_query($conn, $questionQUERY);
            $questionID = mysqli_insert_id($conn);

            $answerQUERY = "INSERT INTO question_answers (question_id, answer_list, correct_answer) VALUES 
                ('$questionID', '" . implode(",", $answer[$i]) . "', '" . implode(",", $correct[$i]) . "')
                ON DUPLICATE KEY UPDATE 
                answer_list = '" . implode(",", $answer[$i]) . "', 
                correct_answer = '" . implode(",", $correct[$i]) . "'";
            mysqli_query($conn, $answerQUERY);
        }
    }
    mysqli_close($conn);
}
?>