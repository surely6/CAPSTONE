<?php
session_start();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        header("Location: signup.php");
        exit();
    }

    // Get form data
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: signup.php");
        exit();
    }

    // Validate role
    if ($role != 'student' && $role != 'instructor') {
        $_SESSION['error'] = "Please select a valid role";
        header("Location: signup.php");
        exit();
    }

    // Check if email is already registered as an admin
    $stmt = $pdo->prepare("SELECT admin_id FROM administrators WHERE admin_email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "This email cannot be used for registration";
        header("Location: signup.php");
        exit();
    }

    // Check if email already exists
    if ($role == 'student') {
        $stmt = $pdo->prepare("SELECT student_id FROM students WHERE student_email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Email already registered";
            header("Location: signup.php");
            exit();
        }

        // Generate unique student ID (S0001, S0002, etc.)
        $stmt = $pdo->query("SELECT MAX(SUBSTRING(student_id, 2)) as max_id FROM students");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextId = $result['max_id'] ? intval($result['max_id']) + 1 : 1;
        $studentId = 'S' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into database
        try {
            $stmt = $pdo->prepare("INSERT INTO students (student_id, student_email, student_password) VALUES (?, ?, ?)");
            $stmt->execute([$studentId, $email, $hashedPassword]);

            // Store user info in session for personalization page
            $_SESSION['user_id'] = $studentId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;
            $_SESSION['logged_in'] = true;
            // User identifier
            $_SESSION['is_student'] = true;
            $_SESSION['is_instructor'] = false;
            $_SESSION['is_admin'] = false;

            // Log the login in user_logs table
            $logStmt = $pdo->prepare("INSERT INTO user_logs (student_id, datetime_of_log, isLogin) VALUES (?, NOW(), 1)");
            $logStmt->execute([$studentId]);

            // Redirect to personalization page
            header("Location: personalization.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Failed to create account: " . $e->getMessage();
            header("Location: signup.php");
            exit();
        }
    } elseif ($role == 'instructor') {
        $stmt = $pdo->prepare("SELECT instructor_id FROM instructors WHERE instructor_email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Email already registered";
            header("Location: signup.php");
            exit();
        }

        // Generate unique instructor ID (I0001, I0002, etc.)
        $stmt = $pdo->query("SELECT MAX(SUBSTRING(instructor_id, 2)) as max_id FROM instructors");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextId = $result['max_id'] ? intval($result['max_id']) + 1 : 1;
        $instructorId = 'I' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into database
        try {
            $stmt = $pdo->prepare("INSERT INTO instructors (instructor_id, instructor_email, instructor_password) VALUES (?, ?, ?)");
            $stmt->execute([$instructorId, $email, $hashedPassword]);

            // Store user info in session for personalization page
            $_SESSION['user_id'] = $instructorId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;
            $_SESSION['logged_in'] = true;
            // User identifier
            $_SESSION['is_student'] = false;
            $_SESSION['is_instructor'] = true;
            $_SESSION['is_admin'] = false;


            // Log the login in user_logs table
            // $logStmt = $pdo->prepare("INSERT INTO user_logs (student_id, datetime_of_log, isLogin) VALUES (?, NOW(), 1)");
            // $logStmt->execute([$user['student_id']]);

            // Redirect to personalization page
            header("Location: personalization.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Failed to create account: " . $e->getMessage();
            header("Location: signup.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Educational Platform</title>
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
            min-height: 150vh;
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
            /* flex: 1; */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
        }

        /* Sign Up Form */
        .signup-container {
            background-color: #f0f0f0;
            border-radius: 10px;
            padding: 2rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            position: relative;
        }

        .signup-title {
            color: #3fd0a4;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.2rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: none;
            background-color: #444;
            color: #fff;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(63, 208, 164, 0.5);
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 38px;
            background: none;
            border: none;
            color: #ccc;
            cursor: pointer;
            font-size: 1rem;
        }

        .password-toggle:focus {
            outline: none;
        }

        /* Role selection */
        .role-options {
            display: flex;
            gap: 1.5rem;
        }

        .role-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .role-option input[type="radio"] {
            margin: 0;
        }

        .role-option label {
            margin: 0;
            cursor: pointer;
        }

        .signup-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
        }

        .login-text {
            color: #333;
            font-size: 0.9rem;
        }

        .login-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        .signup-btn {
            background-color: #3fd0a4;
            color: #fff;
            border: none;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .signup-btn:hover {
            background-color: #2fb890;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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

        .heart {
            color: #ff4d4d;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                padding: 0.8rem 1rem;
            }

            .signup-container {
                padding: 1.5rem;
            }

            .signup-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .signup-btn {
                width: 100%;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            .footer-top {
                flex-direction: column;
                gap: 1.5rem;
            }

            .footer-links {
                gap: 1.5rem;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }

            .footer-bottom-links {
                justify-content: center;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 480px) {
            .signup-title {
                font-size: 1.5rem;
            }

            .form-control {
                padding: 0.7rem;
            }

            .role-options {
                flex-direction: column;
                gap: 0.5rem;
            }

            .footer-links-column {
                min-width: 100%;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">ASSESTIFY</div>
        <nav>
            <a href="index.php" class="nav-link">HOME</a>
            <a href="login.php" class="nav-link">LOGIN</a>
            <a href="signup.php" class="nav-link active">SIGN UP</a>
        </nav>
    </header>

    <main>
        <div class="signup-container">
            <h1 class="signup-title">GET STARTED</h1>

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

            <form id="signupForm" action="signup.php" method="post">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                        <span class="show-password">Show</span>
                        <span class="hide-password" style="display: none;">Hide</span>
                    </button>
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <div class="role-options">
                        <div class="role-option">
                            <input type="radio" id="student" name="role" value="student" required>
                            <label for="student">Student</label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="instructor" name="role" value="instructor">
                            <label for="instructor">Instructor</label>
                        </div>
                    </div>
                </div>

                <div class="signup-footer">
                    <p class="login-text">ALREADY A MEMBER? <a href="login.php" class="login-link">LOGIN NOW</a></p>
                    <button type="submit" class="signup-btn">SIGN UP</button>
                </div>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        // Password visibility toggle
        const passwordInput = document.getElementById('password');
        const toggleButton = document.querySelector('.password-toggle');
        const showPasswordText = document.querySelector('.show-password');
        const hidePasswordText = document.querySelector('.hide-password');

        toggleButton.addEventListener('click', function () {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                showPasswordText.style.display = 'none';
                hidePasswordText.style.display = 'inline';
            } else {
                passwordInput.type = 'password';
                showPasswordText.style.display = 'inline';
                hidePasswordText.style.display = 'none';
            }
        });
    </script>
</body>

</html>