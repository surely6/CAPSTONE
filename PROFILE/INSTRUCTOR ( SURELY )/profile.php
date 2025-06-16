<?php
session_start();
include('connect.php');
include('font.php');
include("../../block.php");

$message = "";
//if (!isset($_SESSION['instructor_id'])) {
//    header("Location: login.php"); //if xde login 
//    exit();
//}
//check onli
if (!isset($_SESSION['user_id'])) {
    die("Instructor ID is not set in the session.");
}

$instructorID = $_SESSION['user_id'];
$query = "SELECT * FROM instructors WHERE instructor_id = '$instructorID'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching instructor data: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    $instructor = mysqli_fetch_assoc($result);
    $instructorEmail = $instructor['instructor_email'] ?? 'Email not found';
    $instructorName = $instructor['instructor_name'] ?? 'Name not found';
    $ProfileImage = $instructor['profile_pic_url'] ?? 'default_profile.png';
} else {
    $instructorEmail = 'Email not found';
    $instructorName = 'Name not found';
}

// upload profile picture
if (isset($_POST['upload_profile_pic'])) {
    $profilePicURL = NULL;//pic null dulu

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {//check file uploaded via form profile_image n makesure upload success
        $targetDir = "uploads/";//save the picture to uploads folder
        $fileName = uniqid() . "_" . basename($_FILES["profile_image"]["name"]);//save image unique name
        $targetFilePath = $targetDir . $fileName;//combine $targetDir n $fileName to create full path where file be saved(url)
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));//extracts file extension from full file path. exp: uploads/123.jpg will return to jpg then JPG converted to jpg

        // Create uploads directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);//0755 = set directory permissions. owner allow read/write/execute,user allow
        }

        $allowedTypes = array("jpg", "jpeg", "png", "gif");
        if (in_array($fileType, $allowedTypes)) {//check the picture is $allowedtype or not
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFilePath)) {//move picture to uploads/ folder
                $profilePicURL = $targetFilePath;  //if success, save to profilePicURL
            } else {
                $message = "<script>alert('Sorry, there was an error uploading your file. Check permissions for the `uploads/` directory.');</script>";
            }
        } else {
            $message = "<script>alert('Only JPG, JPEG, PNG & GIF files are allowed.');</script>";
        }
    }

    // save picture to database
    if ($profilePicURL) {
        $updateQuery = "UPDATE instructors SET profile_pic_url = '$profilePicURL' WHERE instructor_id = '$instructorID'";
        if (mysqli_query($conn, $updateQuery)) {
            $message = "<script>alert('Profile picture updated successfully.');</script>";
            header("Location: profile.php");
            exit();
        } else {
            $message = "<script>alert('Error updating profile picture: " . mysqli_error($conn) . "');</script>";
        }
    }
}


// kira total learning materials&quizzes the instructor created
$materialsQuery = "SELECT COUNT(*) as material_count FROM learning_materials WHERE instructor_id = '$instructorID'";
$materialsResult = mysqli_query($conn, $materialsQuery);
$materialsCount = mysqli_fetch_assoc($materialsResult)['material_count'];

$quizzesQuery = "SELECT COUNT(*) as quiz_count FROM quizzes WHERE instructor_id = '$instructorID'";
$quizzesResult = mysqli_query($conn, $quizzesQuery);
$quizzesCount = mysqli_fetch_assoc($quizzesResult)['quiz_count'];
?>

<!DOCTYPE html>

<head>
    <link href="style.css" rel="stylesheet">
    <link href="header-format.css" rel="stylesheet">
    <title>INSTRUCTOR PROFILE PAGE</title>
</head>
<style>
    .profile-container {
        margin: 20px auto;
        width: 90%;
        max-width: 1200px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 50px;
        padding: 20px;
        background-color: var(--dark-grey);
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        position: relative;
        color: var(--grey);
        width: 75%;
        margin: 0 auto;
        height: 180px;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 30px;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #6c757d;
        overflow: hidden;
        position: relative;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .edit-profile-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: var(--blue);
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
    }

    .edit-profile-btn:hover {
        background-color: #0a6ad1;
    }

    .profile-info h1 {
        margin: 0 0 10px 0;
        color: var(--grey);
        position: relative;
        display: inline-block;
    }

    .profile-info h1::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -5px;
        width: 100%;
        height: 2px;
        background-color: var(--gray);
    }

    .profile-info p {
        margin: 5px 0;
        color: var(--grey);
    }

    .stats-container {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        margin-top: 30px;
        width: 80%;
        max-width: 1200px;
    }

    .stat-card {
        flex: 1;
        background-color: var(--dark-grey);
        border-radius: 10px;
        padding: 10px;
        margin: 0 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        text-align: center;
        color: var(--grey);
        height: 180px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .stat-card h3 {
        font-size: 18px;
        color: var(--grey);
        margin-bottom: 15px;
    }

    .stat-card .stat-value {
        font-size: 36px;
        font-weight: bold;
        color: var(--grey);
    }

    .profile-form {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        display: none;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 5px;
    }

    .btn-primary {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #218838;
    }

    .form-tabs {
        display: flex;
        margin-bottom: 15px;
    }

    .form-tab {
        padding: 10px 15px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px 5px 0 0;
        margin-right: 5px;
        cursor: pointer;
    }

    .form-tab.active {
        background-color: #fff;
        border-bottom-color: #fff;
    }

    .tab-content>.tab-pane {
        display: none;
    }

    .tab-content>.active {
        display: block;
    }

    .modules-section {
        width: 65%;
        max-width: 1200px;
        margin: 30px auto 30px;
        background-color: #414443;
        border-radius: 10px;
        padding: 20px;
        color: #d9d9d9;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        overflow-x: auto;
    }

    .modules-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #d9d9d9;
        padding-bottom: 10px;
    }

    .modules-header h2 {
        margin: 0;
        color: #d9d9d9;
    }

    .module-tabs {
        display: flex;
        gap: 15px;
    }

    .tab-btn {
        background: none;
        border: none;
        color: #d9d9d9;
        padding: 8px 15px;
        cursor: pointer;
        font-size: 16px;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .tab-btn:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .tab-btn.active {
        background-color: #1081F2;
        color: white;
    }

    .module-content {
        display: none;
    }

    .module-content.active {
        display: block;
    }

    .module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .module-card {
        background-color: rgba(217, 217, 217, 0.1);
        border-radius: 8px;
        padding: 15px;
        position: relative;
        transition: transform 0.3s;
        overflow: hidden;
        word-wrap: break-word;
    }

    .module-card:hover {
        transform: translateY(-5px);
    }

    .module-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #d9d9d9;
        border-bottom: 1px solid #d9d9d9;
        padding-bottom: 8px;
    }

    .module-details {
        margin-bottom: 40px;
    }

    .module-details p {
        margin: 5px 0;
        font-size: 14px;
    }

    .view-btn {
        position: absolute;
        bottom: 15px;
        right: 15px;
        background-color: #1081F2;
        color: white;
        border: none;
        padding: 5px 12px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
    }

    .view-all-btn {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #1081F2;
        text-decoration: none;
        font-weight: bold;
    }

    .no-content {
        text-align: center;
        padding: 30px;
        font-style: italic;
        color: #d9d9d9;
    }

    @media screen and (max-width: 768px) {
        header ul li {
            margin: 0 0.5rem;
        }

        li.options a {
            font-size: 1rem;
        }

        .profile-header {
            width: 90%;
            flex-direction: column;
            height: auto;
            padding: 20px 10px;
            text-align: center;
        }

        .profile-avatar {
            margin-right: 0;
            margin-bottom: 15px;
        }

        .profile-info h1 {
            font-size: 1.5rem;
        }

        .edit-profile-btn {
            position: relative;
            top: auto;
            right: auto;
            margin-top: 15px;
        }

        .stats-container {
            flex-direction: column;
            width: 90%;
        }

        .stat-card {
            margin: 10px 0;
            height: 120px;
        }

        .modules-section {
            width: 90%;
            padding: 15px;
        }

        .modules-header {
            flex-direction: column;
            gap: 10px;
        }

        .module-tabs {
            width: 100%;
            justify-content: space-between;
        }

        .module-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
    }

    @media screen and (max-width: 480px) {
        header {
            height: auto;
        }

        header ul {
            display: flex;
            flex-direction: column-reverse;
            align-items: center;
        }

        header ul li {
            float: none;
            width: 100%;
            margin: 5px 0;
            text-align: center;
        }

        li#logo {
            float: none;
            order: -1;
        }

        li.options a {
            border-bottom: none;
            padding: 8px 0;
        }

        #profileDropdown {
            margin-bottom: 10px;
        }

        .dropdown-content {
            width: 100%;
            left: 0;
        }

        .profile-header {
            width: 95%;
            padding: 15px 5px;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
        }

        .profile-info h1 {
            font-size: 1.2rem;
        }

        .profile-info p {
            font-size: 0.9rem;
        }

        .stats-container {
            width: 95%;
        }

        .stat-card {
            height: 100px;
        }

        .stat-card h3 {
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .stat-card .stat-value {
            font-size: 1.8rem;
        }

        .modules-section {
            width: 95%;
            padding: 10px;
        }

        .module-tabs {
            flex-direction: column;
            gap: 5px;
        }

        .tab-btn {
            width: 100%;
            padding: 8px 0;
            text-align: center;
        }

        .module-grid {
            grid-template-columns: 1fr;
        }

        .module-card {
            padding: 10px;
        }

        .module-title {
            font-size: 1rem;
        }

        .module-details p {
            font-size: 0.85rem;
        }
    }
</style>
<style>
    li#profileDropdown {
        position: relative;
        width: fit-content;
    }

    #profileMenu {
        z-index: 3;
        position: absolute;
        top: 70%;
        right: 0;
    }

    #profileMenu a {
        z-index: 1;
        background-color: var(--light-grey);
        padding: .5vh 1vw;
    }
</style>

<body>
    <header>
        <ul>
            <li id="profileDropdown">
                <img src="icon/profile.png" alt="Profile Icon" id="profileIcon"
                    style="width: 50px; height: 50px; cursor: pointer; align-items: center; display: flex; justify-content: center; margin-left: 10px; margin-top: 10px;position:relaive;">
                <div id="profileMenu" class="dropdown-content" style="display: none;">
                    <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/profile.php">My Profile</a>
                    <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/system_feedback.php">Feedback</a>
                    <!-- <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/studentBookmark.php">History</a> -->
                    <a href="/capstone/logout.php">Logout</a>
                </div>
            </li>
            <li class="options"><a href="/capstone/INSTRUCTOR ( CHOW )/Learning Material View.php">LEARNING MATERIAL</a>
            </li>
            <li class="options"><a href="/capstone/INSTRUCTOR ( CHOW )/Quiz View.php">QUIZ</a></li>
            <li id="logo" style="margin-left: 65px;"><a
                    href="/capstone/INSTRUCTOR ( CHOW )/Learning Material View.php">ASSESTIFY</a></li>
        </ul>
    </header>
    <script>
        document.querySelector('#profileIcon').addEventListener("click", function () {
            let profile = document.querySelector('#profileMenu');
            if (profile.style.display == "none") {
                console.log('open')
                profile.style.display = "block";
            } else {
                console.log('close')
                profile.style.display = "none";
            }
        })
    </script>
    <main>
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php
                    if (!empty($ProfileImage) && file_exists($ProfileImage)) {//check profile_pic_url exists and not empty
                        echo '<img src="' . htmlspecialchars($ProfileImage) . '" alt="Profile Image">';
                    } else {
                        echo '<div class="profile-initial">' . substr($instructorName ?? 'U', 0, 1) . '</div>';//display default profile image
                    }
                    ?>
                </div>
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($instructorName); ?></h1>
                    <p><strong>ID: </strong><?php echo htmlspecialchars($instructorID); ?></p>
                    <p><strong>Email: </strong><?php echo htmlspecialchars($instructorEmail); ?></p>
                    <p><strong>Role: </strong>Instructor</p>
                </div>
                <a href="manage_profile.php" class="edit-profile-btn">Manage Profile</a>
            </div>

            <div class="stats-container">
                <div class="stat-card">
                    <h3>Learning Materials</h3>
                    <div class="stat-value"><?php echo $materialsCount; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Quizzes</h3>
                    <div class="stat-value"><?php echo $quizzesCount; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Content</h3>
                    <div class="stat-value"><?php echo $materialsCount + $quizzesCount; ?></div>
                </div>
            </div>
        </div>

        <div class="modules-section">
            <div class="modules-header">
                <h2>My Content</h2>
                <div class="module-tabs">
                    <button class="tab-btn active" data-tab="materials">Learning Materials</button>
                    <button class="tab-btn" data-tab="quizzes">Quizzes</button>
                </div>
            </div>

            <div class="module-content active" id="materials-content">
                <?php
                $materialsQuery = "SELECT * FROM learning_materials WHERE instructor_id = '$instructorID' ORDER BY material_id DESC LIMIT 6";
                $materialsResult = mysqli_query($conn, $materialsQuery);

                if (mysqli_num_rows($materialsResult) > 0) {
                    echo '<div class="module-grid">';
                    while ($material = mysqli_fetch_assoc($materialsResult)) {
                        echo '<div class="module-card">';
                        echo '<div class="module-title">' . $material['material_title'] . '</div>';
                        echo '<div class="module-details">';
                        echo '<p><strong>Subject:</strong> ' . $material['material_subject'] . '</p>';
                        echo '<p><strong>Chapter:</strong> ' . $material['material_chapter'] . '</p>';
                        echo '<p><strong>Level:</strong> ' . $material['material_level'] . '</p>';
                        echo '<p><strong>Type:</strong> ' . $material['material_learning_type'] . '</p>';
                        echo '</div>';
                        // echo '<a href="view_material.php?id=' . $material['material_id'] . '" class="view-btn">View</a>';
                        echo '</div>';
                    }
                    echo '</div>';

                    if (mysqli_num_rows($materialsResult) >= 6) {
                        echo '<a href="/capstone/INSTRUCTOR ( CHOW )/Learning Material View.php" class="view-all-btn">View All Materials</a>';
                    }
                } else {
                    echo '<div class="no-content">No learning materials created yet.</div>';
                }
                ?>
            </div>

            <div class="module-content" id="quizzes-content">
                <?php
                $quizzesQuery = "SELECT * FROM quizzes WHERE instructor_id = '$instructorID' ORDER BY quiz_id DESC LIMIT 6";
                $quizzesResult = mysqli_query($conn, $quizzesQuery);

                if (mysqli_num_rows($quizzesResult) > 0) {
                    echo '<div class="module-grid">';
                    while ($quiz = mysqli_fetch_assoc($quizzesResult)) {
                        echo '<div class="module-card">';
                        echo '<div class="module-title">' . $quiz['quiz_title'] . '</div>';
                        echo '<div class="module-details">';
                        echo '<p><strong>Subject:</strong> ' . $quiz['quiz_subject'] . '</p>';
                        echo '<p><strong>Chapter:</strong> ' . $quiz['quiz_chapter'] . '</p>';
                        echo '<p><strong>Level:</strong> ' . $quiz['quiz_level'] . '</p>';
                        echo '<p><strong>Questions:</strong> ' . $quiz['quiz_total_questions'] . '</p>';
                        echo '</div>';
                        // echo '<a href="view_quiz.php?id=' . $quiz['quiz_id'] . '" class="view-btn">View</a>';
                        echo '</div>';
                    }
                    echo '</div>';

                    if (mysqli_num_rows($quizzesResult) >= 6) {
                        echo '<a href="/capstone/INSTRUCTOR ( CHOW )/Quiz View.php" class="view-all-btn">View All Quizzes</a>';
                    }
                } else {
                    echo '<div class="no-content">No quizzes created yet.</div>';
                }
                ?>
            </div>
    </main>

    <?php include("../../footer.php"); ?>

    <script>
        // Profile dropdown menu
        document.getElementById('profileIcon').addEventListener('click', function (event) {
            event.stopPropagation();
            document.getElementById('profileMenu').classList.toggle('active');
        });

        // Close dropdown when clicking elsewhere
        window.addEventListener('click', function (event) {
            const profileMenu = document.getElementById('profileMenu');
            if (!document.getElementById('profileDropdown').contains(event.target)) {
                profileMenu.classList.remove('active');
            }
        });

        // Tab functionality for modules
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.module-content').forEach(content => content.classList.remove('active'));
                button.classList.add('active');
                const tabId = button.getAttribute('data-tab');
                document.getElementById(tabId + '-content').classList.add('active');
            });
        });
    </script>
</body>