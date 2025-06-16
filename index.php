<?php
session_start();

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';

$isInstructor = isset($_SESSION['is_instructor']) && $_SESSION['is_instructor'] === true;
$isStudent = isset($_SESSION['is_student']) && $_SESSION['is_student'] === true;
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educational Platform</title>
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
        }

        body.no-scroll {
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #3fd0a4;
            padding: 1.5rem 2rem;
            color: #333;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        header.scrolled {
            padding: 0.8rem 2rem;
            background-color: rgba(63, 208, 164, 0.95);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        header.scrolled .logo {
            font-size: 1.3rem;
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
            padding-top: 5rem;
        }

        body.no-scroll main {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            padding-top: 0;
        }

        /* Hero Section */
        .hero {
            padding: 2rem;
            margin-top: 1rem;
        }

        .hero-content {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
            align-items: center;
        }

        .keyhole-container {
            flex: 1;
            min-width: 300px;
            max-width: 50%;
        }

        .keyhole-image {
            background-color: #e0e0e0;
            height: 250px;
            border-radius: 8px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .keyhole-image::after {
            content: "";
            position: absolute;
            right: 50px;
            width: 100px;
            height: 100px;
            background-color: #444;
            border-radius: 50%;
        }

        .keyhole-image::before {
            content: "";
            position: absolute;
            right: 80px;
            width: 40px;
            height: 80px;
            background-color: #444;
            z-index: 1;
        }

        .keyhole-text {
            color: #333;
            font-weight: 600;
            max-width: 50%;
            text-align: left;
            margin-right: auto;
            padding-left: 2rem;
            z-index: 2;
        }

        .join-container {
            flex: 1;
            min-width: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .join-container h2 {
            position: relative;
            padding-bottom: 0.5rem;
            font-size: 1.8rem;
        }

        .join-container h2::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #3fd0a4;
        }

        .begin-btn {
            background-color: #3fd0a4;
            color: #333;
            border: none;
            padding: 0.8rem 2.5rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .begin-btn:hover {
            background-color: #2fb890;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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

        .error-container {
            display: none;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.5rem;
        }

        .error-message {
            color: #e53e3e;
            font-size: 0.85rem;
        }

        .forgot-password,
        .signup-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .forgot-password:hover,
        .signup-link:hover {
            text-decoration: underline;
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

        /* Subjects Section */
        .subjects {
            padding: 2rem;
            margin-top: 1rem;
        }

        .subjects h2 {
            position: relative;
            padding-bottom: 0.5rem;
            margin-bottom: 2rem;
            font-size: 1.5rem;
        }

        .subjects h2::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 200px;
            height: 2px;
            background-color: #3fd0a4;
        }

        .forms-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .form-section {
            width: 100%;
        }

        .form-section h4 {
            margin-bottom: 1rem;
            font-size: 1.2rem;
            color: #3fd0a4;
        }

        .selection-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .form-selection {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .form-selection h3 {
            font-size: 1.2rem;
        }

        .form-selection select {
            background-color: #3fd0a4;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .stream-filters {
            display: flex;
            gap: 0.5rem;
        }

        .stream-filter {
            background-color: #3fd0a4;
            color: #333;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .stream-filter:hover {
            background-color: #3fd0a4;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px #3fd0a4;
        }

        .stream-filter.active {
            background-color: #3fd0a4;
            box-shadow: 0 5px 15px #3fd0a4;

        }

        .subject-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.8rem;
            margin-bottom: 1.5rem;
        }

        .subject {
            background-color: #f8e6e6;
            color: #333;
            padding: 0.8rem;
            text-align: center;
            font-size: 0.9rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
        }

        .subject:hover {
            background-color: #3fd0a4;
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .subject img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }

        .heart {
            color: #ff4d4d;
        }

        /* Subject container animations */
        .hidden-section {
            display: none;
            opacity: 0;
            height: 0;
            overflow: hidden;
            transition: opacity 0.5s ease, height 0.5s ease;
        }

        .visible-section {
            display: block;
            opacity: 1;
            height: auto;
        }

        /* For screen readers */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .forms-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            header {
                padding: 1rem;
            }

            header.scrolled {
                padding: 0.6rem 1rem;
            }

            .hero-content {
                flex-direction: column;
            }

            .keyhole-container,
            .join-container {
                width: 100%;
            }

            .selection-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .stream-filters {
                margin-top: 1rem;
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

            .error-container {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .subject-grid {
                grid-template-columns: 1fr;
            }

            .keyhole-text {
                max-width: 70%;
                padding-left: 1rem;
            }

            .login-title {
                font-size: 1.5rem;
            }

            .form-control {
                padding: 0.7rem;
            }

            .footer-links-column {
                min-width: 100%;
            }
        }

        .keyhole-image {
            position: relative;
        }

        .profile-icon-link {
            position: absolute;
            right: 50px;
            width: 100px;
            height: 100px;
            background-color: #444;
            border-radius: 50%;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .profile-icon-link:hover {
            background-color: #3fd0a4;
            transform: scale(1.05);
        }

        .profile-icon {
            font-size: 50px;
        }

        /* Added for stream section visibility */
        .stream-section {
            transition: all 0.3s ease;
        }

        .streams-container {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
    </style>
</head>

<body>
    <header id="header">
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
        <section class="hero">
            <div class="hero-content">
                <div class="keyhole-container">
                    <div class="keyhole-image">
                        <div class="keyhole-text">
                            <p>WE PROVIDE THE BEST MATERIAL</p>
                        </div>
                        <?php if ($isLoggedIn): ?>
                            <?php if ($isInstructor): ?>
                                <a href="/capstone/PROFILE/INSTRUCTOR ( SURELY )/instructorProfile.php"
                                    class="profile-icon-link">
                                    <i class="fas fa-user-circle profile-icon" style="font-size: 70px"></i>
                                </a>
                            <?php elseif ($isStudent): ?>
                                <a href="/capstone/PROFILE/STUDENT ( PIKER )/studentProfile.php" class="profile-icon-link">
                                    <i class="fas fa-user-circle profile-icon" style="font-size: 70px"></i>
                                </a>
                            <?php elseif ($isAdmin): ?>
                                <a href="" class="profile-icon-link">
                                    <i class="fas fa-user-circle profile-icon" style="font-size: 70px"></i>
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="" class="profile-icon-link">
                                <i class="fas fa-user-circle profile-icon" style="font-size: 70px"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="join-container">
                    <?php if ($isLoggedIn): ?>
                        <h2>Your Learning</h2>
                        <?php if ($isInstructor): ?>
                            <a href="/capstone/INSTRUCTOR ( CHOW )/Quiz View.php" class="begin-btn">GO TO
                                DASHBOARD</a>
                        <?php elseif ($isStudent): ?>
                            <a href="/capstone/PROFILE/STUDENT ( PIKER )/studentDashboard.php" class="begin-btn">GO TO
                                DASHBOARD</a>
                        <?php elseif ($isAdmin): ?>
                            <a href="/capstone/ADMIN ( OSCAR )/adminDashboard.php" class="begin-btn">GO TO DASHBOARD</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <h2>JOIN US</h2>
                        <a href="signup.php" class="begin-btn">BEGIN</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <section class="subjects">
        <h2>SUBJECTS WE PROVIDE</h2>

        <div class="selection-container">
            <div class="form-selection">
                <h3>FORM</h3>
                <select id="formSelect">
                    <option value="all">All Forms</option>
                    <option value="1">FORM 1</option>
                    <option value="2">FORM 2</option>
                    <option value="3">FORM 3</option>
                    <option value="4">FORM 4</option>
                    <option value="5">FORM 5</option>
                </select>
            </div>

            <div class="stream-filters">
                <button id="all-subjects-btn" class="stream-filter">All Subjects</button>
                <button id="science-stream-btn" class="stream-filter active">Science Stream</button>
                <button id="art-stream-btn" class="stream-filter">Art Stream</button>
            </div>
        </div>

        <div class="forms-container">
            <!-- Left Column: Core Subjects -->
            <div class="form-section hidden-section" id="core-subjects-section">
                <h4>CORE SUBJECTS</h4>
                <div class="subject-grid">
                    <?php if ($isLoggedIn): ?>
                        <a href="subjects/english.php" class="subject">
                            <img src="images/english.jpg" alt="English Icon">
                            ENGLISH</a>
                        <a href="subjects/geography.php" class="subject">
                            <img src="images/geography.jpg" alt="Geography Image">
                            GEOGRAPHY</a>
                        <a href="subjects/malay.php" class="subject">
                            <img src="images/malay.jpg" alt="Malay Image">
                            MALAY</a>
                        <a href="subjects/mathematics.php" class="subject">
                            <img src="images/math.jpg" alt="Math Image">
                            MATHEMATICS</a>
                        <a href="subjects/science.php" class="subject">
                            <img src="images/science.jpg" alt="Science Image">
                            SCIENCE</a>
                        <a href="subjects/history.php" class="subject">
                            <img src="images/history.jpg" alt="History Image">
                            HISTORY</a>
                    <?php else: ?>
                        <a href="javascript:redirectToSignup()" class="subject">
                            <img src="images/english.jpg" alt="English Icon">
                            ENGLISH</a>
                        <a href="javascript:redirectToSignup()" class="subject">
                            <img src="images/geography.jpg" alt="Geography Image">
                            GEOGRAPHY</a>
                        <a href="javascript:redirectToSignup()" class="subject">
                            <img src="images/malay.jpg" alt="Malay Image">
                            MALAY</a>
                        <a href="javascript:redirectToSignup()" class="subject">
                            <img src="images/math.jpg" alt="Math Image">
                            MATHEMATICS</a>
                        <a href="javascript:redirectToSignup()" class="subject">
                            <img src="images/science.jpg" alt="Science Image">
                            SCIENCE</a>
                        <a href="javascript:redirectToSignup()" class="subject">
                            <img src="images/history.jpg" alt="History Image">
                            HISTORY</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Science and Art Streams -->
            <div class="form-section" id="streams-section">
                <div class="streams-container">
                    <!-- Science Stream Section - Initially Visible -->
                    <div class="stream-section visible-section" id="science-stream-section">
                        <h4>SCIENCE STREAM</h4>
                        <div class="subject-grid">
                            <?php if ($isLoggedIn): ?>
                                <a href="subjects/physics.php" class="subject">
                                    <img src="images/physics.jpg" alt="Physics Image">
                                    PHYSICS</a>
                                <a href="subjects/add-math.php" class="subject">
                                    <img src="images/addmaths.jpg" alt="Additional Math Image">
                                    ADD MATH</a>
                                <a href="subjects/chemistry.php" class="subject">
                                    <img src="images/chemistry.jpg" alt="Chemistry Image">
                                    CHEMISTRY</a>
                                <a href="subjects/biology.php" class="subject">
                                    <img src="images/biology.jpg" alt="Biology Image">
                                    BIOLOGY</a>
                            <?php else: ?>
                                <a href="javascript:redirectToSignup()" class="subject">
                                    <img src="images/physics.jpg" alt="Physics Image">
                                    PHYSICS</a>
                                <a href="javascript:redirectToSignup()" class="subject">
                                    <img src="images/addmaths.jpg" alt="Additional Math Image">
                                    ADD MATH</a>
                                <a href="javascript:redirectToSignup()" class="subject">
                                    <img src="images/chemistry.jpg" alt="Chemistry Image">
                                    CHEMISTRY</a>
                                <a href="javascript:redirectToSignup()" class="subject">
                                    <img src="images/biology.jpg" alt="Biology Image">
                                    BIOLOGY</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Art Stream Section - Initially Hidden -->
                    <div class="stream-section hidden-section" id="art-stream-section">
                        <h4>ART STREAM</h4>
                        <div class="subject-grid">
                            <?php if ($isLoggedIn): ?>
                                <a href="subjects/accounting.php" class="subject">
                                    <img src="images/accounting.png" alt="Accounting Image">
                                    ACCOUNTING</a>
                                <a href="subjects/business.php" class="subject">
                                    <img src="images/business.jpg" alt="Business Image">
                                    BUSINESS</a>
                                <a href="subjects/economy.php" class="subject">
                                    <img src="images/economy.jpg" alt="Economy Image">
                                    ECONOMY</a>
                                <a href="subjects/add-math-art.php" class="subject">
                                    <img src="images/addmaths.jpg" alt="Additional Math Image">
                                    ADD MATH</a>
                            <?php else: ?>
                                <a href="javascript:redirectToSignup()" class="subject">
                                    <img src="images/accounting.png" alt="Accounting Image">
                                    ACCOUNTING</a>
                                <a href="javascript:redirectToSignup()" class="subject">
                                    <img src="images/business.jpg" alt="Business Image">
                                    BUSINESS</a>
                                <a href="javascript:redirectToSignup()" class="subject">
                                    <img src="images/economy.jpg" alt="Economy Image">
                                    ECONOMY</a>
                                <a href="javascript:redirectToSignup()" class="subject">
                                    <img src="images/addmaths.jpg" alt="Additional Math Image">
                                    ADD MATH</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>


    <script>
        // JavaScript to handle the header shrinking on scroll
        window.addEventListener('scroll', function () {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Function to redirect non-logged in users to signup page
        function redirectToSignup() {
            window.location.href = 'signup.php';
        }

        // Function to toggle section visibility
        function toggleSections(showAllSubjects, showScienceStream, showArtStream) {
            const coreSection = document.getElementById('core-subjects-section');
            const scienceSection = document.getElementById('science-stream-section');
            const artSection = document.getElementById('art-stream-section');

            // Toggle core subjects section
            if (showAllSubjects) {
                coreSection.classList.add('visible-section');
                coreSection.classList.remove('hidden-section');
            } else {
                coreSection.classList.add('hidden-section');
                coreSection.classList.remove('visible-section');
            }

            // Toggle science stream section
            if (showScienceStream) {
                scienceSection.classList.add('visible-section');
                scienceSection.classList.remove('hidden-section');
            } else {
                scienceSection.classList.add('hidden-section');
                scienceSection.classList.remove('visible-section');
            }

            // Toggle art stream section
            if (showArtStream) {
                artSection.classList.add('visible-section');
                artSection.classList.remove('hidden-section');
            } else {
                artSection.classList.add('hidden-section');
                artSection.classList.remove('visible-section');
            }
        }

        // Filter buttons event listeners
        document.getElementById('all-subjects-btn').addEventListener('click', function () {
            toggleSections(true, true, true);
            updateActiveButton(this);
        });

        document.getElementById('science-stream-btn').addEventListener('click', function () {
            toggleSections(false, true, false);
            updateActiveButton(this);
        });

        document.getElementById('art-stream-btn').addEventListener('click', function () {
            toggleSections(false, false, true);
            updateActiveButton(this);
        });

        // Update active button style
        function updateActiveButton(clickedButton) {
            // Remove active class from all buttons
            const buttons = document.querySelectorAll('.stream-filter');
            buttons.forEach(button => {
                button.classList.remove('active');
            });

            // Add active class to the clicked button
            clickedButton.classList.add('active');
        }

        // Form selector event listener
        document.getElementById('formSelect').addEventListener('change', function () {
            const selectedValue = this.value;

            if (selectedValue === 'all') {
                // If "All Forms" is selected, respect the current stream filter
                const scienceBtn = document.getElementById('science-stream-btn');
                const artBtn = document.getElementById('art-stream-btn');
                const allBtn = document.getElementById('all-subjects-btn');

                if (scienceBtn.classList.contains('active')) {
                    toggleSections(false, true, false);
                } else if (artBtn.classList.contains('active')) {
                    toggleSections(false, false, true);
                } else if (allBtn.classList.contains('active')) {
                    toggleSections(true, true, true);
                }
            } else {
                // If a specific form is selected, show all subject sections
                toggleSections(true, true, true);
                updateActiveButton(document.getElementById('all-subjects-btn'));
            }
        });

        // Initialize with Science Stream visible by default
        document.addEventListener('DOMContentLoaded', function () {
            toggleSections(false, true, false);
        });
    </script>
</body>

</html>