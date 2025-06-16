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
    <title>creating material</title>
</head>

<body>
    <header>
        <div id="main-header">
            <ul id="material-alter">
                <li id="logo"><a href="Learning Material View.php">RETURN</a></li>
                <li><button style="background-color: var(--blue);" id="publish">PUBLISH</button></li>
                <li><button style="background-color: var(--blue);" id="draft">SAVE</button></li>
                <li><button style="background-color: var(--red);" id="delete">DELETE</button></li>
            </ul>
        </div>
        <div id="secondary-header">
            <div id="select-detail">
                <label for="forms">FORM: </label>
                <select name="forms" id="forms" onchange="ChangeSubject()">
                    <option value="1" selected>Form 1</option>
                    <option value="2">Form 2</option>
                    <option value="3">Form 3</option>
                    <option value="4">Form 4</option>
                    <option value="5">Form 5</option>
                </select>
            </div>
            <div id="select-detail">
                <label for="subjects">SUBJECT: </label>
                <select name="subjects" id="subjects">
                </select>
            </div>
            <div id="select-detail">
                <label for="chapter">CHAPTER: </label>
                <input type="text" placeholder="Insert chapter" name="chapter" required id="chapter"></input>
            </div>
            <div id="select-detail">
                <label>LEARNING STYLE: </label>
                <select name="learning-style" id="learning-style" required>
                    <option value="read_write">Read & Write</option>
                    <option value="visual">Visual</option>
                    <option value="audio">Audio</option>
                </select>
            </div>
        </div>
    </header>
    <main>
        <button id="add">ADD PARTS</button>
        <div class="section">
            <label for="title">TITLE:</label>
            <input type="text" id="title" name="title" required>
            <br>
            <label for="description">DESCRIPTION:</label>
            <textarea name="description" id="autoresizing" required></textarea>
        </div>
        <div id="part">
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
    <script type="module" src="RESOURCES/JAVA/Create Material.js">
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