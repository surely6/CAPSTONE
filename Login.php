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
        $_SESSION['login_error'] = "Database connection failed: " . $e->getMessage();
        header("Location: Login.php");
        exit();
    }

    // Get form data
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = "Invalid email format";
        header("Location: Login.php");
        exit();
    }

    // Check if email exists and verify instructor password
    $stmt = $pdo->prepare("SELECT instructor_id, instructor_email, instructor_password, approval_status FROM instructors WHERE instructor_email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the instructor is approved
        if ($user['approval_status'] == 0) {
            $_SESSION['login_error'] = "Kindly wait for approval";
            header("Location: Login.php");
            exit();
        }

        // Verify password
        if (password_verify($password, $user['instructor_password'])) {
            // Password is correct, start a new session
            session_regenerate_id();
            $_SESSION['user_id'] = $user['instructor_id'];
            $_SESSION['user_email'] = $user['instructor_email'];
            $_SESSION['logged_in'] = true;

            // User identifier
            $_SESSION['is_student'] = false;
            $_SESSION['is_instructor'] = true;
            $_SESSION['is_admin'] = false;

            // Log the login in user_logs table
            $logStmt = $pdo->prepare("INSERT INTO user_logs (instructor_id, datetime_of_log, isLogin) VALUES (?, NOW(), 1)");
            $logStmt->execute([$user['instructor_id']]);

            // Redirect to dashboard or home page
            header("Location: ./INSTRUCTOR ( CHOW )/Quiz View.php");
            exit();
        } else {
            // Password is incorrect
            $_SESSION['login_error'] = "Incorrect password";
            header("Location: Login.php");
            exit();
        }
    }

    // Check if email exists and verify student password
    $stmt = $pdo->prepare("SELECT student_id, student_email, student_password FROM students WHERE student_email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if (password_verify($password, $user['student_password'])) {
            // Password is correct, start a new session
            session_regenerate_id();
            $_SESSION['user_id'] = $user['student_id'];
            $_SESSION['user_email'] = $user['student_email'];
            $_SESSION['logged_in'] = true;
            // User identifier
            $_SESSION['is_student'] = true;
            $_SESSION['is_instructor'] = false;
            $_SESSION['is_admin'] = false;
            // Start timeout.php
            $_SESSION['LOGIN_TIME'] = time();

            // Log the login in user_logs table
            $logStmt = $pdo->prepare("INSERT INTO user_logs (student_id, datetime_of_log, isLogin) VALUES (?, NOW(), 1)");
            $logStmt->execute([$user['student_id']]);

            // Redirect to dashboard or home page
            header("Location: /capstone/PROFILE/STUDENT ( PIKER )/studentDashboard.php");
            exit();
        } else {
            // Password is incorrect
            $_SESSION['login_error'] = "Incorrect password";
            header("Location: Login.php");
            exit();
        }
    }

    // Check if email exists and verify admin password
    $stmt = $pdo->prepare("SELECT admin_id, admin_email, admin_password FROM administrators WHERE admin_email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if (password_verify($password, $user['admin_password'])) {
            // Password is correct, start a new session
            session_regenerate_id();
            $_SESSION['user_id'] = $user['admin_id'];
            $_SESSION['user_email'] = $user['admin_email'];
            $_SESSION['logged_in'] = true;
            // User identifier
            $_SESSION['is_student'] = false;
            $_SESSION['is_instructor'] = false;
            $_SESSION['is_admin'] = true;

            // Log the login in user_logs table
            $logStmt = $pdo->prepare("INSERT INTO user_logs (admin_id, datetime_of_log, isLogin) VALUES (?, NOW(), 1)");
            $logStmt->execute([$user['admin_id']]);

            // Redirect to dashboard or home page
            header("Location: ./ADMIN ( OSCAR )/adminDashboard.php");
            exit();
        } else {
            // Password is incorrect
            $_SESSION['login_error'] = "Incorrect password";
            header("Location: Login.php");
            exit();
        }
    } else {
        // Email not found
        $_SESSION['login_error'] = "Email not registered";
        header("Location: Login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Educational Platform</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="footer.css">
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
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
        }

        /* Login Form */
        .login-container {
            background-color: #f0f0f0;
            border-radius: 10px;
            padding: 2rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .login-title {
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

        .login-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
        }

        .signup-text {
            color: #333;
            font-size: 0.9rem;
        }

        .signup-link,
        .forgot-password {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .signup-link:hover,
        .forgot-password:hover {
            text-decoration: underline;
        }

        .error-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.5rem;
        }

        .error-message {
            color: #e53e3e;
            font-size: 0.85rem;
        }

        .login-btn {
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

        .login-btn:hover {
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

            .login-container {
                padding: 1.5rem;
            }

            .login-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .login-btn {
                width: 100%;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            .error-container {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .login-title {
                font-size: 1.5rem;
            }

            .form-control {
                padding: 0.7rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">ASSESTIFY</div>
        <nav>
            <a href="index.php" class="nav-link">HOME</a>
            <a href="Login.php" class="nav-link active">LOGIN</a>
            <a href="signup.php" class="nav-link">SIGN UP</a>
        </nav>
    </header>

    <main>
        <div class="login-container">
            <h1 class="login-title">LOG IN TO WEBSITE</h1>

            <?php
            // Display error or success messages
            if (isset($_SESSION['login_error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['login_error'] . '</div>';
                unset($_SESSION['login_error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            ?>

            <form id="loginForm" action="Login.php" method="post">
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

                <div class="login-footer">
                    <p class="signup-text">NOT A MEMBER? <a href="signup.php" class="signup-link">SIGN UP FOR FREE</a>
                    </p>
                    <button type="submit" class="login-btn">LOG IN</button>
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