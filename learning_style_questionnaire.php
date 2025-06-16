<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

// Initialize variables
$visualScore = 0;
$auditoryScore = 0;
$readWriteScore = 0;
$learningStyle = '';
$message = '';
$formSubmitted = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $formSubmitted = true;

    // Initialize scores
    $visualScore = 0;
    $auditoryScore = 0;
    $readWriteScore = 0;

    // Process each question based on its type
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'question_') === 0) {
            $parts = explode('_', $key);
            if (count($parts) >= 3) {
                $type = $parts[1];
                $value = intval($value);

                // Add to the appropriate score based on question type
                switch ($type) {
                    case 'visual':
                        $visualScore += $value;
                        break;
                    case 'auditory':
                        $auditoryScore += $value;
                        break;
                    case 'readwrite':
                        $readWriteScore += $value;
                        break;
                }
            }
        }
    }

    // Determine the dominant learning style
    if ($visualScore > $auditoryScore && $visualScore > $readWriteScore) {
        $learningStyle = 'visual';
    } elseif ($auditoryScore > $visualScore && $auditoryScore > $readWriteScore) {
        $learningStyle = 'audio';
    } else {
        $learningStyle = 'read_write';
    }

    // Update the user's learning style in the database
    $stmt = $conn->prepare("UPDATE students SET student_learning_style = ? WHERE student_id = ?");
    $stmt->bind_param("ss", $learningStyle, $userId);

    if ($stmt->execute()) {
        // Update session variable
        $_SESSION['user_learning_style'] = $learningStyle;

        $message = "Your learning style has been updated to: " . ucfirst($learningStyle);

        // Redirect to learning dashboard page after a delay
        header("Refresh: 30; URL=/capstone/PROFILE/STUDENT ( PIKER )/studentDashboard.php");
    } else {
        $message = "Error updating your learning style: " . $conn->error;
    }
} else {
    // Get questions for each learning style
    $questionsByType = [];

    // Function to get questions of a specific type
    function getQuestionsByType($conn, $type)
    {
        $stmt = $conn->prepare("SELECT questionaire_id, questionaire FROM questionaires WHERE questionaire_options = ?");
        $stmt->bind_param("s", $type);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get all questions by type
    $visualQuestions = getQuestionsByType($conn, 'Visual');
    $auditoryQuestions = getQuestionsByType($conn, 'Auditory');
    $readWriteQuestions = getQuestionsByType($conn, 'read_write');

    // Randomly select 3 questions from each type
    function getRandomQuestions($questions, $count)
    {
        shuffle($questions);
        return array_slice($questions, 0, $count);
    }

    // Get 3 random questions for each type
    $selectedVisual = getRandomQuestions($visualQuestions, 3);
    $selectedAuditory = getRandomQuestions($auditoryQuestions, 3);
    $selectedReadWrite = getRandomQuestions($readWriteQuestions, 3);

    // Create a nested array with question and its type
    $questionsByType = [
        'visual' => $selectedVisual,
        'auditory' => $selectedAuditory,
        'readwrite' => $selectedReadWrite
    ];

    // Combine all questions into a single array for display
    $allQuestions = [];
    foreach ($selectedVisual as $q) {
        $allQuestions[] = ['question' => $q, 'type' => 'visual'];
    }
    foreach ($selectedAuditory as $q) {
        $allQuestions[] = ['question' => $q, 'type' => 'auditory'];
    }
    foreach ($selectedReadWrite as $q) {
        $allQuestions[] = ['question' => $q, 'type' => 'readwrite'];
    }

    // Shuffle the questions to mix them up while preserving type associations
    shuffle($allQuestions);

    // Store questions in session to maintain consistency if form is reloaded
    $_SESSION['questionnaire_questions'] = $allQuestions;
}

// Use questions from session if available and not submitted
if (!$formSubmitted && isset($_SESSION['questionnaire_questions'])) {
    $allQuestions = $_SESSION['questionnaire_questions'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Style Questionnaire - Educational Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #1e1e1e;
            color: #fff;
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            padding: 2rem 1rem;
        }

        .header {
            background-color: #333;
            padding: 1rem;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            color: #3fd0a4;
            font-size: 1.5rem;
            margin: 0;
            margin-left: 1rem;
        }

        .back-button {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .question-container {
            background-color: #333;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .question-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            background-color: #3fd0a4;
            color: #fff;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            margin-bottom: 1rem;
        }

        .question-text {
            color: #fff;
            font-size: 1rem;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background-color: #444;
            border-radius: 4px;
        }

        .answer-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .answer-option {
            background-color: #3fd0a4;
            color: #fff;
            border: none;
            padding: 0.8rem;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .answer-option:hover,
        .answer-option.selected {
            background-color: #2fb890;
        }

        .answer-option:last-child {
            grid-column: span 2;
            width: 50%;
            margin: 0 auto;
        }

        .submit-btn {
            background-color: #3fd0a4;
            color: #fff;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            float: right;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            background-color: #2fb890;
            transition: all 0.3s ease;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(63, 208, 164, 0.4);
        }

        .result-container {
            background-color: #333;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            margin-top: 2rem;
        }

        .result-title {
            color: #3fd0a4;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .result-description {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .learning-style-icon {
            font-size: 3rem;
            color: #3fd0a4;
            margin-bottom: 1rem;
        }

        .radio-option {
            display: none;
        }

        .radio-label {
            display: block;
            background-color: #3fd0a4;
            color: #fff;
            padding: 0.8rem;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .radio-label:hover {
            background-color: #2fb890;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(63, 208, 164, 0.4);
        }

        .radio-option:checked+.radio-label {
            background-color: #19715C;
            box-shadow: 0 0 0 2px #fff;
        }

        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .score-breakdown {
            display: flex;
            justify-content: space-around;
            margin-bottom: 2rem;
        }

        .score-item {
            text-align: center;
            padding: 1rem;
            background-color: #444;
            border-radius: 8px;
            min-width: 100px;
        }

        .score-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #3fd0a4;
        }

        .score-label {
            font-size: 0.9rem;
            color: #ccc;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="student_preferences.php" class="back-button">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>Learning Style Questionnaire</h1>
        </div>

        <?php if ($formSubmitted): ?>
            <div class="result-container">
                <div class="learning-style-icon">
                    <?php if ($learningStyle === 'visual'): ?>
                        <i class="fas fa-eye"></i>
                    <?php elseif ($learningStyle === 'audio'): ?>
                        <i class="fas fa-headphones"></i>
                    <?php else: ?>
                        <i class="fas fa-book-reader"></i>
                    <?php endif; ?>
                </div>
                <h2 class="result-title">Your Learning Style: <?php echo ucfirst($learningStyle); ?></h2>
                <p class="result-description"><?php echo $message; ?></p>

                <div class="score-breakdown">
                    <div class="score-item">
                        <div class="score-value"><?php echo $visualScore; ?></div>
                        <div class="score-label">Visual</div>
                    </div>
                    <div class="score-item">
                        <div class="score-value"><?php echo $auditoryScore; ?></div>
                        <div class="score-label">Auditory</div>
                    </div>
                    <div class="score-item">
                        <div class="score-value"><?php echo $readWriteScore; ?></div>
                        <div class="score-label">Read/Write</div>
                    </div>
                </div>

                <p>You will be redirected to the student dashboard page shortly...</p>
                <a href="/capstone/PROFILE/STUDENT ( PIKER )/studentDashboard.php" class="btn submit-btn">Continue</a>
            </div>
        <?php else: ?>
            <?php if (!empty($message)): ?>
                <div class="alert"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="questionnaireForm">
                <?php foreach ($allQuestions as $index => $questionData): ?>
                    <?php
                    $question = $questionData['question'];
                    $type = $questionData['type'];
                    $questionId = $question['questionaire_id'];
                    ?>
                    <div class="question-container" data-type="<?php echo $type; ?>">
                        <div class="question-number"><?php echo $index + 1; ?></div>
                        <div class="question-text"><?php echo htmlspecialchars($question['questionaire']); ?></div>

                        <div class="answer-options">
                            <div>
                                <input type="radio" id="q<?php echo $questionId; ?>_1"
                                    name="question_<?php echo $type; ?>_<?php echo $questionId; ?>" value="1"
                                    class="radio-option" required>
                                <label for="q<?php echo $questionId; ?>_1" class="radio-label">Never applies to me</label>
                            </div>

                            <div>
                                <input type="radio" id="q<?php echo $questionId; ?>_2"
                                    name="question_<?php echo $type; ?>_<?php echo $questionId; ?>" value="2"
                                    class="radio-option">
                                <label for="q<?php echo $questionId; ?>_2" class="radio-label">Sometimes applies to me</label>
                            </div>

                            <div>
                                <input type="radio" id="q<?php echo $questionId; ?>_3"
                                    name="question_<?php echo $type; ?>_<?php echo $questionId; ?>" value="3"
                                    class="radio-option">
                                <label for="q<?php echo $questionId; ?>_3" class="radio-label">Often applies to me</label>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <button type="submit" name="submit" class="submit-btn">Submit</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation to ensure all questions are answered
        document.getElementById('questionnaireForm').addEventListener('submit', function (e) {
            const questionContainers = document.querySelectorAll('.question-container');
            let allAnswered = true;

            // Check if all questions are answered
            questionContainers.forEach(function (container) {
                const radios = container.querySelectorAll('input[type="radio"]');
                let answered = false;

                radios.forEach(function (radio) {
                    if (radio.checked) {
                        answered = true;
                    }
                });

                if (!answered) {
                    allAnswered = false;
                    container.style.border = '2px solid #f8d7da';
                } else {
                    container.style.border = 'none';
                }
            });

            if (!allAnswered) {
                e.preventDefault();
                alert('Please answer all questions before submitting.');
                window.scrollTo(0, 0);
            }

            // Verify we have exactly 3 questions answered for each type
            const visualContainers = document.querySelectorAll('.question-container[data-type="visual"]');
            const auditoryContainers = document.querySelectorAll('.question-container[data-type="auditory"]');
            const readwriteContainers = document.querySelectorAll('.question-container[data-type="readwrite"]');

            if (visualContainers.length !== 3 || auditoryContainers.length !== 3 || readwriteContainers.length !== 3) {
                console.warn('Warning: Expected exactly 3 questions of each type.');
            }
        });
    </script>
</body>

</html>