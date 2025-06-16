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
    "chapter" => "",
    "learning_style" => "",
    "title" => "",
    "description" => "",
    "delete" => false,
    "saved" => false,
    "content" => []
);

$MaterialQuery = "SELECT * FROM learning_materials WHERE material_id = $ID";
$MaterialSQL = mysqli_query($conn, $MaterialQuery);

$array = mysqli_fetch_assoc($MaterialSQL);

$PartsQuery = "SELECT material_content FROM learning_material_parts WHERE material_id = $ID ORDER BY part ASC";
$PartsSQL = mysqli_query($conn, $PartsQuery);


if (mysqli_num_rows($PartsSQL) > 0) {
    while ($row = mysqli_fetch_row($PartsSQL)) {
        array_push($data['content'], $row[0]);
    }
}

$data['form'] = $array['material_level'];
$data['subject'] = $array['material_subject'];
$data['chapter'] = $array['material_chapter'];
$data['learning_style'] = $array['material_learning_type'];
$data['title'] = $array['material_title'];
$data['description'] = $array['material_description'];

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
                    <label>LEARNING STYLE: </label>
                    <select name="learning-style" id="learning-style" required>
                        <option value="read_write">Read & Write</option>
                        <option value="visual">Visual</option>
                        <option value="audio">Audio</option>
                    </select>
                </li>
                <li id="select-detail">
                    <label for="chapter">CHAPTER: </label>
                    <input type="text" placeholder="Insert chapter" name="chapter" required id="chapter"></input>
                </li>
                <li id="select-detail">
                    <label for="subjects">SUBJECT: </label>
                    <select name="subjects" id="subjects">
                    </select>
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
            <input type="text" id="title" name="title" required>
            <br>
            <label for="description">DESCRIPTION:</label>
            <textarea name="description" id="autoresizing" required></textarea>
        </div>
        <div id="part">
        </div>
    </main>
    <script>
        let data = <?= json_encode($data) ?>;
        console.log(data);
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="importmap">
        {
            "imports": {
                "ckeditor5": "./ckeditor5/ckeditor5.js",
                "ckeditor5/": "./ckeditor5/"
            }
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="module" src="RESOURCES/JAVA/Edit Material.js">
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