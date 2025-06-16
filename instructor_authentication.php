<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in and is an instructor
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
$showConfirmation = false;

// Check if instructor already has a certificate uploaded
$stmt = $conn->prepare("SELECT instructor_certificate, approval_status FROM instructors WHERE instructor_id = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $instructor = $result->fetch_assoc();

    // If certificate is already uploaded and pending/approved, redirect to dashboard
    if ($instructor['instructor_certificate'] !== null) {
        if ($instructor['approval_status'] === 0) {
            $_SESSION['info'] = "Your certification is pending approval.";
            // header("Location: index.php");
        } else if ($instructor['approval_status'] === 1) {
            $_SESSION['success'] = "Your certification has been approved.";
            header("Location: dashboard.php");
        }

        // exit();
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['certificate'])) {
    $file = $_FILES['certificate'];

    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
            UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.",
            UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
            UPLOAD_ERR_NO_FILE => "No file was uploaded.",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
            UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload."
        ];

        $_SESSION['error'] = "Upload failed: " . ($errorMessages[$file['error']] ?? "Unknown error");
    } else {
        // Check file type
        $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $imageTypes)) {
            $_SESSION['error'] = "Only IMAGE files are allowed.";
        }
        // Check file size (limit to 5MB)
        else if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "File size exceeds the 5MB limit.";
        } else {
            // Read file content
            $fileContent = file_get_contents($file['tmp_name']);

            // Update database
            $stmt = $conn->prepare("UPDATE instructors SET instructor_certificate = ?, approval_status = 0 WHERE instructor_id = ?");
            $stmt->bind_param("ss", $fileContent, $userId);

            if ($stmt->execute()) {
                // Show confirmation message
                $showConfirmation = true;
            } else {
                $_SESSION['error'] = "Failed to upload certificate: " . $conn->error;
            }
        }
    }
    $_SESSION['logged_in'] = false;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $showConfirmation ? 'Submission Confirmation' : 'Instructor Authentication'; ?> - Educational
        Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <style>
        /* Base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #3a3a3a;
            color: #fff;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #3fd0a4;
            padding: 1rem 2rem;
            color: #333;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 1px;
        }

        nav {
            display: flex;
            gap: 1.5rem;
        }

        .nav-link {
            color: #333;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.3rem 0.8rem;
            border-bottom: 2px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            border-bottom: 2px solid #333;
        }

        /* Main content */
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
        }

        /* Container styles */
        .container-box {
            background-color: #e0e0e0;
            border-radius: 10px;
            padding: 2rem;
            width: 100%;
            max-width: 530px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        .auth-title {
            color: #3fd0a4;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 600;
        }

        .auth-description {
            color: #333;
            margin-bottom: 2rem;
            text-align: left;
        }

        .file-input-container {
            margin-bottom: 2rem;
            text-align: left;
        }

        .file-input {
            display: none;
        }

        .file-input-label {
            display: inline-block;
            background-color: #444;
            color: #fff;
            padding: 0.6rem 1.2rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            background-color: #555;
        }

        .file-name {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #555;
        }

        .submit-btn {
            background-color: #3fd0a4;
            color: #fff;
            border: none;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            float: right;
        }

        .submit-btn:hover {
            background-color: #2fb890;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Confirmation styles */
        .confirmation-title {
            color: #3fd0a4;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            text-align: center;
            font-weight: 600;
        }

        .confirmation-subtitle {
            color: #3fd0a4;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 600;
        }

        .confirmation-message {
            color: #333;
            margin-bottom: 2rem;
            text-align: center;
        }

        .home-btn {
            background-color: #3fd0a4;
            color: #fff;
            border: none;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .home-btn:hover {
            background-color: #2fb890;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: #fff;
        }

        /* Alert messages */
        .alert {
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        /* Footer */
        footer {
            background-color: #3fd0a4;
            color: #333;
            padding: 1rem 2rem;
            margin-top: auto;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-logo {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .footer-logo div {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .footer-logo small {
            font-size: 0.7rem;
        }

        .heart {
            color: #ff4d4d;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                padding: 0.8rem 1rem;
            }

            .container-box {
                padding: 1.5rem;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">LOGO</div>
        <nav>
            <a href="index.php" class="nav-link">HOME</a>
        </nav>
    </header>

    <main>
        <?php if ($showConfirmation): ?>
            <!-- Confirmation Message -->
            <div class="container-box">
                <h1 class="confirmation-title">THANK YOU</h1>
                <h2 class="confirmation-subtitle">FOR YOUR SUBMISSION</h2>

                <p class="confirmation-message">
                    Your certification has been submitted.<br>
                    You will receive an e-mail of approval within<br>
                    3-4 business days.<br>
                    Your approval email may appear in your spam inbox.<br>
                </p>

                <a href="index.php" class="home-btn">HOME</a>
            </div>
        <?php else: ?>
            <!-- Authentication Form -->
            <div class="container-box">
                <h1 class="auth-title">AUTHENTICATION</h1>

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

                <p class="auth-description">Please submit a digital copy of your teaching certification.</p>

                <form action="instructor_authentication.php" method="post" enctype="multipart/form-data">
                    <!-- Hidden field to limit file size to 5MB -->
                    <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />

                    <div class="file-input-container">
                        <input type="file" id="certificate" name="certificate" class="file-input" accept="image/*" required>
                        <label for="certificate" class="file-input-label">select a IMAGE file</label>
                        <div id="file-name" class="file-name"></div>
                    </div>

                    <button type="submit" class="submit-btn">TURN IN</button>
                </form>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-logo">
                <div>LOGO</div>
                <small>CREATED BY 6 PEOPLE</small>
            </div>
            <div class="footer-credit">
                made with <span class="heart">❤</span> in MALAYSIA
            </div>
        </div>
    </footer>

    <script>
        // Display selected file name
        document.getElementById('certificate') && document.getElementById('certificate').addEventListener('change', function () {
            const fileName = this.files[0] ? this.files[0].name : '';
            document.getElementById('file-name').textContent = fileName;
        });
    </script>
</body>

</html>