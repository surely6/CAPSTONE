<?php
session_start();
include('connect.php');
include('font.php');
include("../block.php");
?>
<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <link href="./RESOURCES/CSS/header-format.css" rel="stylesheet">
    <link href="./RESOURCES/CSS/colors.css" rel="stylesheet">
    <link href="./RESOURCES/CSS/Learning Material Create.css" rel="stylesheet">
    <link rel="stylesheet" href="./ckeditor5/ckeditor5.css">
    <title>creating quiz</title>
</head>

<body>
    <header>
        <div id="main-header">
            <ul id="material-alter">
                <li id="logo"><a href="Quiz View.php">RETURN</a></li>
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
                    <input type="text" placeholder="Insert chapter" name="chapter" required id="chapter"></input>

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
        <button id="add">ADD QUESTIONS</button>
        <div class="section">
            <label for="title">TITLE:</label>
            <input type="text" id="title" name="title" required>
            <br>
            <label for="description">DESCRIPTION:</label>
            <textarea name="description" id="autoresizing" required></textarea>
        </div>
        <div id="question">
            <div class="section">
                <label for="question">QUESTION 1:</label>
                <button id="remove" onclick="RemoveEditors(`+(i)+`)"
                    style="display: block; margin-top: 0;">DELETE</button>
                <select id="question-type">
                    <option value="Q01">SINGLE ANSWER</option>
                    <option value="Q02">MULTIPLE ANSWER</option>
                </select>
                <textarea name="part `+(i+1)+`" id="editor" required></textarea>
                <label>ANSWERS:</label>
                <button id="remove" onclick="RemoveEditors(`+(i)+`)" style="display: block;">REMOVE ANSWER</button>
                <button id="remove" onclick="RemoveEditors(`+(i)+`)" style="display: block;">ADD ANSWER</button>
                <div id="answer-section">
                    <div class="answer">
                        <div class="round">
                            <input type="checkbox" id="correct-answer 1" value="0" /><label
                                for="correct-answer 1"></label>
                        </div>
                        <input type="text" placeholder="answer" id="ans">
                    </div>
                    <div class="answer">
                        <div class="round">
                            <input type="checkbox" id="correct-answer 2" value="1" /><label
                                for="correct-answer 2"></label>
                        </div>
                        <input type="text" placeholder="answer" id="ans">
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script type="importmap">
        {
            "imports": {
                "ckeditor5": "./ckeditor5/ckeditor5.js",
                "ckeditor5/": "./ckeditor5/"
            }
        }
        </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="module" src="RESOURCES/JAVA/Create Quiz.js">
    </script>
    <script type="text/javascript">
        textarea = document.querySelector("#autoresizing");
        textarea.addEventListener('input', autoResize, false);

        function autoResize() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        }
    </script>
</body>