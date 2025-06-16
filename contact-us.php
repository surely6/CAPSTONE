<?php
session_start();
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';

$isInstructor = isset($_SESSION['is_instructor']) && $_SESSION['is_instructor'] === true;
$isStudent = isset($_SESSION['is_student']) && $_SESSION['is_student'] === true;
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Initialize variables
$name = $email = $subject = $message = "";
$nameErr = $emailErr = $subjectErr = $messageErr = "";
$formSubmitted = false;
$formSuccess = false;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formSubmitted = true;

    // Validate name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = test_input($_POST["name"]);
        // Check if name only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            $nameErr = "Only letters and white space allowed";
        }
    }

    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        // Check if email address is well formed
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // Validate subject
    if (empty($_POST["subject"])) {
        $subjectErr = "Subject is required";
    } else {
        $subject = test_input($_POST["subject"]);
    }

    // Validate message
    if (empty($_POST["message"])) {
        $messageErr = "Message is required";
    } else {
        $message = test_input($_POST["message"]);
    }

    // If no errors, process the form
    if (empty($nameErr) && empty($emailErr) && empty($subjectErr) && empty($messageErr)) {
        // In a real application, you would send an email here
        // For now, we'll just simulate success
        $formSuccess = true;

        // Reset form fields
        $name = $email = $subject = $message = "";
    }
}

// Function to sanitize input data
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Assestify</title>
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
            padding: 3rem 0;
        }

        .contact-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .contact-title {
            font-size: 3rem;
            color: #3fd0a4;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .contact-subtitle {
            font-size: 1.2rem;
            color: #ccc;
            max-width: 700px;
            margin: 0 auto;
        }

        .contact-form-container {
            background-color: #444;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #3fd0a4;
        }

        .form-control {

            width: 100%;
            padding: 0.8rem;
            border: 1px solid #555;
            background-color: #333;
            color: #fff;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #3fd0a4;
            box-shadow: 0 0 0 2px rgba(63, 208, 164, 0.2);
        }

        input[type="name"]::placeholder {
            color: #3fd0a4;
        }

        .text-danger {
            color: #ff6b6b !important;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .submit-btn {
            background-color: #3fd0a4;
            color: #333;
            border: none;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            width: 100%;
        }

        .submit-btn:hover {
            background-color: #2fb890;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .contact-info-container {
            background-color: #333;
            border-radius: 10px;
            padding: 2rem;
            height: 100%;
        }

        .contact-info-title {
            font-size: 1.5rem;
            color: #3fd0a4;
            margin-bottom: 1.5rem;
            font-weight: 600;
            position: relative;
            display: inline-block;
        }

        .contact-info-title::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 45px;
            height: 2px;
            background-color: #3fd0a4;
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .contact-icon {
            color: #3fd0a4;
            font-size: 1.2rem;
            margin-right: 1rem;
            margin-top: 0.2rem;
        }

        .contact-text {
            color: #ccc;
        }

        .contact-text a {
            color: #3fd0a4;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .contact-text a:hover {
            color: #2fb890;
            text-decoration: underline;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;

        }

        .social-links a {
            text-decoration: none;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #3fd0a4;
            color: #333;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background-color: #2fb890;
            transform: translateY(-3px);
        }

        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: rgba(63, 208, 164, 0.2);
            border: 1px solid #3fd0a4;
            color: #3fd0a4;
        }

        .heart {
            color: #ff4d4d;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                padding: 0.8rem 1rem;
            }

            .contact-title {
                font-size: 2.5rem;
            }

            .contact-info-container {
                margin-top: 2rem;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .contact-title {
                font-size: 2rem;
            }

            .contact-subtitle {
                font-size: 1rem;
            }

            .contact-form-container,
            .contact-info-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">ASSESTIFY</div>
        <nav>
            <a href="#" class="nav-link active">HOME</a>
            <?php if ($isLoggedIn): ?>
                <?php if ($isInstructor): ?>
                    <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/profile.php" class="nav-link">PROFILE</a>
                    <a href="/capstone/INSTRUCTOR ( CHOW )/Quiz View.php" class="nav-link">DASHBOARD</a>
                <?php elseif ($isStudent): ?>
                    <a href="/capstone/PROFILE/STUDENT ( PIKER )/studentProfile.php" class="nav-link">PROFILE</a>
                    <a href="/capstone/PROFILE/STUDENT ( PIKER )/studentDashboard.php" class="nav-link">DASHBOARD</a>
                <?php elseif ($isAdmin): ?>
                    <!-- <a href="/capstone/PROFILE/ADMIN ( OSCAR )/adminProfile.php" class="nav-link">PROFILE</a> -->
                    <a href="/capstone/ADMIN ( OSCAR )/adminDashboard.php" class="nav-link">DASHBOARD</a>
                <?php endif; ?>
                <a href="logout.php" class="nav-link">LOGOUT</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">LOGIN</a>
                <a href="signup.php" class="nav-link">SIGN UP</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <div class="contact-section">
            <div class="contact-header">
                <h1 class="contact-title">Contact Us</h1>
                <p class="contact-subtitle">Have questions? We'd love to hear from you. Fill out the form below or reach
                    out to us directly.</p>
            </div>

            <div class="row">
                <div class="col-lg-7">
                    <div class="contact-form-container">
                        <?php if ($formSubmitted && $formSuccess): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i> Thank you for your message! We'll get back to you
                                as soon as possible.
                            </div>
                        <?php endif; ?>

                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?php echo $name; ?>" placeholder="Enter your name">
                                <?php if (!empty($nameErr)): ?>
                                    <span class="text-danger"><?php echo $nameErr; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo $email; ?>" placeholder="Enter your email">
                                <?php if (!empty($emailErr)): ?>
                                    <span class="text-danger"><?php echo $emailErr; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject"
                                    value="<?php echo $subject; ?>" placeholder="Enter subject">
                                <?php if (!empty($subjectErr)): ?>
                                    <span class="text-danger"><?php echo $subjectErr; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5"
                                    placeholder="Enter your message"><?php echo $message; ?></textarea>
                                <?php if (!empty($messageErr)): ?>
                                    <span class="text-danger"><?php echo $messageErr; ?></span>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="submit-btn">Send Message</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="contact-info-container">
                        <h2 class="contact-info-title">Get in Touch</h2>

                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Email</h3>
                                <p><a href="mailto:assestifyofficial@gmail.com">assestifyofficial@gmail.com</a></p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Location</h3>
                                <p>Kuala Lumpur, Malaysia</p>
                                <p>Remote-first company</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Hours</h3>
                                <p>Monday - Friday: 8:30 AM - 6:00 PM</p>
                                <p>Saturday - Sunday: Closed</p>
                            </div>
                        </div>

                        <div class="social-links">
                            <a href="#" class="social-link">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-github"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>