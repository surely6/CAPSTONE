<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: Login.php");
    exit();
}

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
        header("Location: personalization.php");
        exit();
    }

    // Get form data
    $name = trim($_POST['username']);
    $userId = $_SESSION['user_id'];
    $role = $_SESSION['user_role'];

    // Validate name
    if (empty($name)) {
        $_SESSION['error'] = "Username is required";
        header("Location: personalization.php");
        exit();
    }

    // Update database based on role
    try {
        if ($role === 'student') {
            $stmt = $pdo->prepare("UPDATE students SET student_name = ? WHERE student_id = ?");
            $stmt->execute([$name, $userId]);

            $_SESSION['user_name'] = $name;
            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: student_preferences.php");
            exit();
        } else { // instructor
            $stmt = $pdo->prepare("UPDATE instructors SET instructor_name = ? WHERE instructor_id = ?");
            $stmt->execute([$name, $userId]);

            $_SESSION['user_name'] = $name;
            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: instructor_authentication.php");
            exit();
        }



        // Redirect to dashboard or home page

    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to update profile: " . $e->getMessage();
        header("Location: personalization.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalization - Educational Platform</title>
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

        html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
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

        /* Personalization Form */
        .personalization-container {
            background-color: #f0f0f0;
            border-radius: 10px;
            padding: 2rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .personalization-title {
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

        .role-display {
            color: #333;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        .role-value {
            font-weight: 600;
            color: #3fd0a4;
            text-transform: capitalize;
        }

        .proceed-btn {
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

        .proceed-btn:hover {
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

            .personalization-container {
                padding: 1.5rem;
            }

            .proceed-btn {
                width: 100%;
                float: none;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .personalization-title {
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
        </nav>
    </header>

    <main>
        <div class="personalization-container">
            <h1 class="personalization-title">PERSONALIZATION</h1>

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

            <form id="personalizationForm" action="personalization.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>

                <div class="role-display">
                    Role: <span
                        class="role-value"><?php echo isset($_SESSION['user_role']) ? htmlspecialchars($_SESSION['user_role']) : ''; ?></span>
                </div>

                <button type="submit" class="proceed-btn">PROCEED</button>
            </form>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-logo">
                <div>ASSESTIFY</div>
                <small>CREATED BY 6 PEOPLE</small>
            </div>
            <div class="footer-credit">
                made with <span class="heart">❤</span> in MALAYSIA
            </div>
        </div>
    </footer>
</body>

</html>