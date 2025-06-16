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
    <title>Careers - Assestify</title>
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

        .careers-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .hero-section {
            text-align: center;
            margin-bottom: 4rem;
        }

        .careers-title {
            font-size: 3rem;
            color: #3fd0a4;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .careers-tagline {
            font-size: 1.2rem;
            color: #3fd0a4;
            font-weight: 400;
            margin-bottom: 2rem;
        }

        .careers-intro {
            max-width: 800px;
            margin: 0 auto;
            text-align: left;
            background-color: rgba(63, 208, 164, 0.1);
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #3fd0a4;
        }

        .section-title {
            font-size: 1.8rem;
            color: #3fd0a4;
            margin-bottom: 1.5rem;
            font-weight: 600;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 75px;
            height: 3px;
            background-color: #3fd0a4;
        }

        .benefits-section {
            margin-bottom: 4rem;
        }

        .benefit-card {
            background-color: #444;
            border-radius: 8px;
            padding: 1.5rem;
            height: 100%;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .benefit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-bottom: 3px solid #3fd0a4;
        }

        .benefit-icon {
            font-size: 2rem;
            color: #3fd0a4;
            margin-bottom: 1rem;
        }

        .benefit-title {
            color: #3fd0a4;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .positions-section {
            margin-bottom: 4rem;
        }

        .positions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        .positions-table th {
            background-color: #3fd0a4;
            color: #333;
            padding: 1rem;
            text-align: left;
        }

        .positions-table td {
            padding: 1rem;
            border-bottom: 1px solid #555;
        }

        .positions-table tr:hover {
            background-color: rgba(63, 208, 164, 0.1);
        }

        .looking-for-section {
            margin-bottom: 4rem;
        }

        .quality-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .quality-icon {
            color: #3fd0a4;
            font-size: 1.2rem;
            margin-right: 1rem;
            margin-top: 0.2rem;
        }

        .apply-section {
            margin-bottom: 4rem;
            background-color: #444;
            padding: 2rem;
            border-radius: 8px;
        }

        .step {
            display: flex;
            margin-bottom: 1.5rem;
        }

        .step-number {
            background-color: #3fd0a4;
            color: #333;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .contact-info {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .contact-info p {
            margin-bottom: 0px;
        }

        .contact-icon {
            color: #3fd0a4;
            margin-right: 0.5rem;
        }

        .mission-section {
            background-color: rgba(63, 208, 164, 0.1);
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
        }

        .mission-text {
            font-style: italic;
            margin-bottom: 1rem;
        }

        .heart {
            color: #ff4d4d;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                padding: 0.8rem 1rem;
            }

            .careers-title {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            .positions-table {
                display: block;
                overflow-x: auto;
            }
        }

        @media (max-width: 576px) {
            .careers-title {
                font-size: 2rem;
            }

            .careers-tagline {
                font-size: 1rem;
            }

            .benefit-card {
                margin-bottom: 1rem;
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
        <div class="careers-section">
            <!-- Hero Section (Top of Z) -->
            <div class="hero-section">
                <h1 class="careers-title">Careers at Assestify</h1>
                <p class="careers-tagline">Empower the Future of Learning</p>

                <div class="careers-intro">
                    <p>At Assestify, we're on a mission to reshape how Malaysian students prepare for their SPM — making
                        learning more personal, engaging, and effective.</p>
                    <p class="mb-0">We believe that every student deserves a learning path as unique as they are. If
                        you're passionate about education, innovation, and making a real impact, we want you on our
                        team.</p>
                </div>
            </div>

            <!-- Why Join Section (Left to Right in Z) -->
            <div class="benefits-section">
                <h2 class="section-title">Why Join Assestify?</h2>

                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="benefit-card">
                            <div class="benefit-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h3 class="benefit-title">Meaningful Work</h3>
                            <p>Help students unlock their true potential and achieve academic success.</p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="benefit-card">
                            <div class="benefit-icon">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <h3 class="benefit-title">Innovative Culture</h3>
                            <p>Collaborate on creative, tech-driven education solutions.</p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="benefit-card">
                            <div class="benefit-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="benefit-title">Growth Opportunities</h3>
                            <p>Learn, lead, and level up alongside a supportive, ambitious team.</p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="benefit-card">
                            <div class="benefit-icon">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                            <h3 class="benefit-title">Flexibility</h3>
                            <p>We embrace a work style that encourages balance, creativity, and continuous learning.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Open Positions Section (Diagonal in Z) -->
            <div class="positions-section">
                <h2 class="section-title">Open Positions</h2>

                <div class="table-responsive">
                    <table class="positions-table">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th>Type</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Content Creator (Education Focus)</td>
                                <td>Part-time / Freelance</td>
                                <td>Remote / Hybrid</td>
                            </tr>
                            <tr>
                                <td>Learning Designer (SPM Subjects)</td>
                                <td>Full-time</td>
                                <td>Remote / Kuala Lumpur</td>
                            </tr>
                            <tr>
                                <td>Front-End Developer</td>
                                <td>Full-time</td>
                                <td>Remote</td>
                            </tr>
                            <tr>
                                <td>Marketing Intern (Student Ambassador)</td>
                                <td>Internship</td>
                                <td>Remote</td>
                            </tr>
                            <tr>
                                <td>Educational Psychologist Advisor (Consultant)</td>
                                <td>Contract</td>
                                <td>Remote</td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-center"><em>(More roles coming soon!)</em></p>
                </div>
            </div>

            <!-- What We're Looking For Section (Left to Right in Z) -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="looking-for-section">
                        <h2 class="section-title">What We're Looking For</h2>

                        <div class="quality-item">
                            <div class="quality-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div>
                                <p>A genuine passion for education and student success.</p>
                            </div>
                        </div>

                        <div class="quality-item">
                            <div class="quality-icon">
                                <i class="fas fa-brain"></i>
                            </div>
                            <div>
                                <p>Creativity in solving problems and improving how students learn.</p>
                            </div>
                        </div>

                        <div class="quality-item">
                            <div class="quality-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <p>Team players who care deeply about making an impact.</p>
                            </div>
                        </div>

                        <div class="quality-item">
                            <div class="quality-icon">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <div>
                                <p>Willingness to experiment, iterate, and grow.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- How to Apply Section (Bottom Right of Z) -->
                <div class="col-lg-6">
                    <div class="apply-section">
                        <h2 class="section-title">How to Apply</h2>

                        <div class="step">
                            <div class="step-number">1</div>
                            <div>
                                <p>Send your resume and a short intro (tell us why you're excited about Assestify!)</p>
                            </div>
                        </div>

                        <div class="step">
                            <div class="step-number">2</div>
                            <div>
                                <p>Include any past work, portfolio, or relevant experiences if you have.</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="contact-info">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <p>Email us at: <a href="mailto:assestifyofficial@gmail.com"
                                            class="text-info">assestifyofficial@gmail.com</a></p>
                                </div>
                            </div>


                            <div class="contact-info">
                                <div class="contact-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div>
                                    <p>Subject line: Application for [Role Name]</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mission Section (Bottom of Z) -->
            <div class="mission-section mt-5">
                <h2 class="section-title mb-4">Our Mission</h2>

                <p class="mission-text">At Assestify, we prepare students for SPM by empowering them with personalized
                    learning experiences tailored to their strengths and needs.</p>

                <p class="mission-text">We envision a future where every student thrives — not by fitting into a system,
                    but by growing through a journey designed just for them.</p>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>