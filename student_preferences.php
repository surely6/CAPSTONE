<?php
session_start();

// Check if user is logged in and is a student
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'capstone';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['error'] = "Database connection failed: " . $e->getMessage();
    header("Location: student_preferences.php");
    exit();
}

// Initialize selection step
$selectionStep = isset($_SESSION['selection_step']) ? $_SESSION['selection_step'] : 'level';

// Process level selection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['level'])) {
    // Get form data
    $level = $_POST['level'];
    $userId = $_SESSION['user_id'];

    // Validate level
    if (!in_array($level, ['1', '2', '3', '4', '5'])) {
        $_SESSION['error'] = "Invalid level selection";
        header("Location: student_preferences.php");
        exit();
    }

    // Update database
    try {
        $stmt = $pdo->prepare("UPDATE students SET student_level = ? WHERE student_id = ?");
        $stmt->execute([$level, $userId]);

        $_SESSION['user_level'] = $level;
        $_SESSION['selection_step'] = 'learning_style';
        $selectionStep = 'learning_style';

    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to update level: " . $e->getMessage();
        header("Location: student_preferences.php");
        exit();
    }
}

// Process learning style selection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['learning_style'])) {
    // Get form data
    $learningStyle = $_POST['learning_style'];
    $userId = $_SESSION['user_id'];

    // Check if user wants to take the questionnaire
    if ($learningStyle === 'questionnaire') {
        // Redirect to questionnaire page
        header("Location: learning_style_questionnaire.php");
        exit();
    }

    // Validate learning style
    if (!in_array($learningStyle, ['visual', 'read_write', 'audio'])) {
        $_SESSION['error'] = "Invalid learning style selection";
        header("Location: student_preferences.php");
        exit();
    }

    // Update database
    try {
        $stmt = $pdo->prepare("UPDATE students SET student_learning_style = ? WHERE student_id = ?");
        $stmt->execute([$learningStyle, $userId]);

        $_SESSION['user_learning_style'] = $learningStyle;
        $_SESSION['success'] = "Preferences updated successfully!";

        // Reset selection step for future visits
        $_SESSION['selection_step'] = 'complete';

        // Redirect to dashboard
        header("Location: /capstone/PROFILE/STUDENT ( PIKER )/studentDashboard.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to update learning style: " . $e->getMessage();
        header("Location: student_preferences.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Preferences - Educational Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #1e1e1e;
            color: #fff;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .student-header {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Selection Container */
        .selection-container {
            background-color: #333;
            border-radius: 10px;
            padding: 2rem;
            width: 100%;
            max-width: 800px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .welcome-title {
            color: #3fd0a4;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .welcome-subtitle {
            color: #3fd0a4;
            font-size: 1rem;
            margin-bottom: 2rem;
            text-transform: uppercase;
        }

        /* Selection Options */
        .selection-options {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .selection-option {
            background-color: #e0e0e0;
            border-radius: 10px;
            padding: 1.5rem 1rem;
            width: 120px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .selection-option:hover {
            background-color: #3fd0a4;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(63, 208, 164, 0.4);
        }

        .selection-option:hover .selection-icon {
            color: #fff;
        }

        .selection-option:hover .selection-text {
            color: #fff;
        }

        .selection-icon {
            font-size: 2rem;
            color: #3fd0a4;
            transition: all 0.3s ease;
        }

        .selection-text {
            color: #333;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            text-align: center;
        }

        /* Alert messages */
        .alert {
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            width: 100%;
            max-width: 800px;
        }

        /* Hidden form for submission */
        #selectionForm {
            display: none;
        }

        /* Questionnaire option */
        .questionnaire-options {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .questionnaire-option {
            background-color: #444;
            border-radius: 10px;
            padding: 1.5rem 1rem;
            width: 180px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .questionnaire-option:hover {
            background-color: #3fd0a4;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(63, 208, 164, 0.4);
        }

        .questionnaire-option:hover .selection-icon {
            color: #fff;
        }

        .questionnaire-option:hover .selection-text {
            color: #fff;
        }

        .questionnaire-option .selection-icon {
            color: #3fd0a4;
        }

        .questionnaire-option .selection-text {
            color: #fff;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .selection-container {
                padding: 1.5rem;
                margin: 0 1rem;
            }

            .selection-options {
                gap: 0.8rem;
            }

            .selection-option {
                width: 100px;
                padding: 1rem 0.8rem;
            }

            .welcome-title {
                font-size: 1.5rem;
            }

            .questionnaire-option {
                width: 160px;
            }
        }

        @media (max-width: 480px) {
            .selection-options {
                flex-direction: column;
                align-items: center;
            }

            .selection-option {
                width: 80%;
            }

            .questionnaire-option {
                width: 80%;
            }
        }
    </style>
</head>

<body>
    <?php
    // Display error or success messages
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    ?>

    <div class="student-header">STUDENT</div>

    <div class="selection-container">
        <h1 class="welcome-title">WELCOME TO <span>WEBSITE</span></h1>
        <h2 class="welcome-title">
            <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>
        </h2>

        <?php if ($selectionStep === 'level'): ?>
            <!-- Level Selection -->
            <p class="welcome-subtitle">SELECT YOUR CURRENT LEVEL OF STUDY</p>

            <div class="selection-options">
                <div class="selection-option" onclick="selectLevel('1')">
                    <i class="selection-icon fas fa-child"></i>
                    <span class="selection-text">FORM 1</span>
                </div>

                <div class="selection-option" onclick="selectLevel('2')">
                    <i class="selection-icon fas fa-book-reader"></i>
                    <span class="selection-text">FORM 2</span>
                </div>

                <div class="selection-option" onclick="selectLevel('3')">
                    <i class="selection-icon fas fa-university"></i>
                    <span class="selection-text">FORM 3</span>
                </div>

                <div class="selection-option" onclick="selectLevel('4')">
                    <i class="selection-icon fas fa-laptop"></i>
                    <span class="selection-text">FORM 4</span>
                </div>

                <div class="selection-option" onclick="selectLevel('5')">
                    <i class="selection-icon fas fa-graduation-cap"></i>
                    <span class="selection-text">FORM 5</span>
                </div>
            </div>

            <!-- Hidden form for level submission -->
            <form id="selectionForm" action="student_preferences.php" method="post">
                <input type="hidden" id="levelInput" name="level" value="">
            </form>

            <script>
                function selectLevel(level) {
                    document.getElementById('levelInput').value = level;
                    document.getElementById('selectionForm').submit();
                }
            </script>

        <?php elseif ($selectionStep === 'learning_style'): ?>
            <!-- Learning Style Selection -->
            <p class="welcome-subtitle">SELECT YOUR LEARNING STYLE</p>

            <div class="selection-options">
                <div class="selection-option" onclick="selectLearningStyle('visual')">
                    <i class="selection-icon fas fa-eye"></i>
                    <span class="selection-text">VISUAL</span>
                </div>

                <div class="selection-option" onclick="selectLearningStyle('read_write')">
                    <i class="selection-icon fas fa-book-open"></i>
                    <span class="selection-text">READ & WRITE</span>
                </div>

                <div class="selection-option" onclick="selectLearningStyle('audio')">
                    <i class="selection-icon fas fa-headphones"></i>
                    <span class="selection-text">AUDIO</span>
                </div>
            </div>

            <!-- Questionnaire Option -->
            <div class="questionnaire-options">
                <div class="questionnaire-option" onclick="selectLearningStyle('questionnaire')">
                    <i class="selection-icon fas fa-question-circle"></i>
                    <span class="selection-text">DON'T KNOW? TRY QUESTIONNAIRE</span>
                </div>
            </div>

            <!-- Hidden form for learning style submission -->
            <form id="selectionForm" action="student_preferences.php" method="post">
                <input type="hidden" id="learningStyleInput" name="learning_style" value="">
            </form>

            <script>
                function selectLearningStyle(style) {
                    document.getElementById('learningStyleInput').value = style;
                    document.getElementById('selectionForm').submit();
                }
            </script>
        <?php endif; ?>
    </div>
</body>

</html>