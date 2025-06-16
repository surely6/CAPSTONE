<?php
session_start();

include('connect.php');
include('font.php');
include("../block.php");


$ID = $_COOKIE['ID'];
$origin = $_COOKIE['ORIGIN'];


$data = array(
    "ID" => $ID,
    "form" => "",
    "subject" => "",
    "chapter" => $quiz_chapter,
    "title" => "",
    "description" => "",
    "question" => [],
    "type" => [],
    "answer" => [],
    "correct" => []
);
//obtain quiz details
$QuizQuery = "SELECT * FROM quizzes WHERE quiz_id = $ID";
$QuizSQL = mysqli_query($conn, $QuizQuery);

$array = mysqli_fetch_assoc($QuizSQL);

//obtain questions & answer & correct
$QuestionQuery =
    "SELECT question_id, question, question_style_id FROM questions 
WHERE quiz_id = $ID 
ORDER BY question_id ASC";
$QuestionSQL = mysqli_query($conn, $QuestionQuery);

$QuestionID = [];

if (mysqli_num_rows($QuestionSQL) > 0) {
    while ($row = mysqli_fetch_assoc($QuestionSQL)) {
        array_push($data['question'], $row['question']);
        array_push($data['type'], $row['question_style_id']);
        array_push($QuestionID, $row['question_id']);
    }
}

foreach ($QuestionID as $id) {
    $AnswerQuery = "SELECT answer_list, correct_answer FROM question_answers 
    WHERE question_id = $id ORDER BY answer_id ASC";
    $AnswerSQL = mysqli_query($conn, $AnswerQuery);
    if (mysqli_num_rows($AnswerSQL) > 0) {
        while ($row = mysqli_fetch_assoc($AnswerSQL)) {
            array_push($data['answer'], explode(",", $row['answer_list']));
            array_push($data['correct'], explode(",", $row['correct_answer']));
        }
    }
}

$data['form'] = $array['quiz_level'];
$data['subject'] = $array['quiz_subject'];
$data['title'] = $array['quiz_title'];
$data['description'] = $array['quiz_description'];
$data['chapter'] = $array['quiz_chapter'];


?>
<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <link href="./RESOURCES/CSS/header-format.css" rel="stylesheet">
    <link href="./RESOURCES/CSS/colors.css" rel="stylesheet">
    <link href="./RESOURCES/CSS/Learning Material Create.css" rel="stylesheet">
    <link rel="stylesheet" href="./ckeditor5/ckeditor5.css">
    <title>edit quiz</title>
</head>

<body>
    <div id="id" style="display: none;"><?php echo $ID ?></div>
    <header>
        <div id="main-header">
            <ul id="material-alter">
                <li id="logo"><a href="<?php echo $origin ?>">RETURN</a></li>
                <li><button style="background-color: var(--blue);" id="publish">PUBLISH</button></li>
                <li><button style="background-color: var(--blue);" id="draft">SAVE</button></li>
                <li><button style="background-color: var(--red);" id="delete">DELETE</button></li>
            </ul>
        </div>
        <div id="secondary-header">
            <ul>
                <li id="select-detail">
                    <label for="subjects">SUBJECT: </label>
                    <select name="subjects" id="subjects">
                    </select>
                </li>
                <li id="select-detail">

                    <label for="chapter">CHAPTER: </label>
                    <input type="text" placeholder="Insert chapter" name="chapter" id="chapter" required></input>

                </li>
                <li id="select-detail">
                    <label for="forms">FORM: </label>
                    <select name="forms" id="forms" onchange="ChangeSubject()">
                        <option value="1" selected>Form 1</option>
                        <option value="2">Form 2</option>
                        <option value="3">Form 3</option>
                        <option value="4">Form 4</option>
                        <option value="5">Form 5</option>
                    </select>
                </li>
            </ul>
        </div>
    </header>
    <main>
        <button id="add">ADD PARTS</button>
        <div class="section">
            <label for="title">TITLE:</label>
            <input type="text" id="title" name="title" value="<?php echo $data['title'] ?>">
            <br>
            <label for="description">DESCRIPTION:</label>
            <textarea name="description" id="autoresizing" required><?php echo $data['description'] ?></textarea>
        </div>
        <div id="question">
        </div>
    </main>
    <script>
        let data = <?= json_encode($data) ?>;
        console.log(data);

    </script>
    <script type="importmap">
        {
            "imports": {
                "ckeditor5": "./ckeditor5/ckeditor5.js",
                "ckeditor5/": "./ckeditor5/"
            }
        }
        </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="module" src="RESOURCES/JAVA/Edit Quiz.js">
    </script>
    <script type="text/javascript">
        textarea = document.querySelector("#autoresizing");
        textarea.addEventListener('input', autoResize, false);

        function autoResize() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        }
    </script>
    <script type="module" src="RESOURCES/JAVA/Upload Learning Material.js"></script>
</body>