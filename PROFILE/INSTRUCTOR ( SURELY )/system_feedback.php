<?php
session_start();
include("connect.php");
include("font.php");
include("../../block.php");
include("bootstrapFile.html");


if (!empty($_SESSION['is_instructor'])) {
    include("../INSTRUCTOR ( SURELY )/header.php");
    $instructor_id = $_SESSION['user_id'];
    $student_id = null;
} else {
    include("../STUDENT ( PIKER )/header.php");
    $student_id = $_SESSION['user_id'];
    $instructor_id = null;

}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['feedback'])) {

        $feedback = htmlspecialchars($_POST['feedback']);

        // $instructor_id = 'I01';
        if (empty($feedback)) {
            echo "<script>alert('Please enter your feedback before clicking the button');</script>";
        } else {
            if (($student_id && !$instructor_id) || (!$student_id && $instructor_id)) {
                $stmt = $conn->prepare("INSERT INTO system_feedbacks (student_id, instructor_id, feedback, datetime_of_feedback) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("sss", $student_id, $instructor_id, $feedback);

                if ($stmt->execute()) {
                    $message = "<script>alert('Feedback submitted successfully!');</script>";
                } else {
                    $message = "<script>alert('Error: Your feedback submission failed.');</script>";
                }

                $stmt->close();
            }
        } //else {
        //echo "Error: Either student_id or instructor_id must be provided, but not both.";
        //}
//}
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="style.css" rel="stylesheet">
    <link href="header-format.css" rel="stylesheet">
    <title>Feedback Page</title>
</head>
<style>
    .feedback-header {
        display: flex;
        align-items: center;
        padding: 20px;
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin: 0 auto 20px auto;
        width: 90%;
        border-bottom: 1px solid #414443;
    }

    .feedback-header span {
        margin-left: 10px;
    }

    .feedback-container {
        background-color: #414443;
        width: 90%;
        height: 60%;
        margin: 20px auto;
        padding: 20px;
        border-radius: 5px;
        color: white;
        position: relative
    }

    .feedback-container label {
        font-size: 20px;
        font-weight: bold;
        display: block;
        margin-bottom: 10px;
        text-align: left;
        margin-left: 20px;
    }

    .feedback-container textarea {
        width: 95%;
        height: 300px;
        border: none;
        border-radius: 5px;
        padding: 10px;
        font-size: 16px;
        resize: none;
        display: block;
        margin: 0 auto 20px;
    }

    .buttons-container-submit {
        margin-top: 20px;
        margin-bottom: 20px;
        margin-right: 20px;
        text-align: right;
    }

    @media screen and (max-width: 768px) {
        .feedback-header {
            font-size: 20px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .feedback-container {
            width: 90%;
            height: auto;
            padding: 15px;
        }

        .feedback-container label {
            font-size: 18px;
        }

        .feedback-container textarea {
            width: 100%;
            height: 200px;
            font-size: 14px;
        }

        .buttons-container-submit {
            text-align: center;
            margin-right: 0;
        }

        .feedback-header span {
            font-size: 18px;
        }
    }


    @media screen and (max-width: 480px) {
        .feedback-header {
            font-size: 18px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .feedback-container {
            width: 100%;
            padding: 10px;
        }

        .feedback-container label {
            font-size: 16px;
        }

        .feedback-container textarea {
            width: 100%;
            height: 150px;
            font-size: 12px;
        }

        .buttons-container-submit {
            text-align: center;
            margin-right: 0;
        }

        .feedback-header span {
            font-size: 16px;
        }
    }
</style>

<body>
    <?php echo $message; ?>
    <!-- <header>
        <ul>
            <li id="profile"><a href="">PROFILE</a></li>
            <li class="options"><a href="">LEARNING MATERIAL</a></li>
            <li class="options"><a href="">QUIZ</a></li>
            <li class="options"><a href="">DASHBOARD</a></li>
            <li id="logo"><a href="">LOGO</a></li>
        </ul>
    </header> -->

    <?php
    if (!empty($_SESSION['is_instructor'])) {
        $backUrl = "/capstone/INSTRUCTOR ( CHOW )/Learning Material View.php";
    } else {
        $backUrl = "/capstone/PROFILE/STUDENT ( PIKER )/studentDashboard.php";
    }
    ?>
    <div class="feedback-header">
        <div class="back-arrow"><img src="icon/leftArrow.png" style="width: 50px; height: 50px"
                onclick="window.location.href='<?php echo $backUrl; ?>';">
        </div>
        <span>FEEDBACK</span>
    </div>

    <div class="feedback-container">
        <form method="POST" action="">
            <label for="feedback">INSERT FEEDBACK</label>
            <textarea id="feedback" name="feedback" placeholder="Type your feedback here..."></textarea>
            <div class="buttons-container-submit">
                <button type="submit" class="btn btn-primary">SUBMIT</button>
            </div>
        </form>
    </div>
</body>

<?php include("../../footer.php"); ?>


</html>