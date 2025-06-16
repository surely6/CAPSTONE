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
    <title>About Us - Educational Platform</title>
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

        .about-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .about-header {
            margin-bottom: 3rem;
        }

        .about-title {
            font-size: 3rem;
            color: #3fd0a4;
            margin-top: 9rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .about-tagline {
            font-size: 1.2rem;
            color: #3fd0a4;
            font-weight: 400;
        }

        .mission-section {
            margin-bottom: 4rem;
        }

        .mission-title {
            font-size: 2rem;
            color: #3fd0a4;
            margin-top: 3rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .mission-text {
            color: #3fd0a4;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .creators-section {
            margin-bottom: 3rem;
        }

        .creators-title {
            font-size: 2rem;
            color: #3fd0a4;
            margin-bottom: 2rem;
            font-weight: 600;
            position: relative;
            display: inline-block;
        }

        .creators-title::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #3fd0a4;
        }

        .creator-card {
            background-color: #444;
            border-radius: 8px;
            padding: 1.5rem;
            height: 100%;
            transition: all 0.3s ease;
            text-align: center;
        }

        .creator-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px #3fd0a4;
            background-color: #555;
        }

        .creator-name {
            color: #3fd0a4;
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .creator-role {
            color: #ccc;
            font-size: 0.9rem;
        }

        .heart {
            color: #ff4d4d;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                padding: 0.8rem 1rem;
            }

            .about-title {
                font-size: 2.5rem;
            }

            .mission-title,
            .creators-title {
                font-size: 1.8rem;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .about-title {
                font-size: 2rem;
            }

            .about-tagline {
                font-size: 1rem;
            }

            .mission-title,
            .creators-title {
                font-size: 1.5rem;
            }

            .mission-text {
                font-size: 1rem;
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
        <div class="about-section">
            <div class="row">
                <div class="col-lg-6">
                    <div class="about-header">
                        <h1 class="about-title">About Assestify</h1>
                        <p class="about-tagline">Your Future, Your Way, with Assestify.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mission-section">
                        <h2 class="mission-title">Our Mission</h2>
                        <p class="mission-text">
                            At Assestify is to prepare SPM students for excellence by providing a learning experience
                            tailored to their unique styles and goals.
                        </p>
                        <p class="mission-text">
                            We believe that every student deserves a personalized journey — where learning is flexible,
                            empowering, and built around their strengths.
                        </p>
                        <p class="mission-text">
                            At Assestify, we are committed to helping students ace their SPM and unlock their fullest
                            potential through customized pathways, adaptive resources, and continuous support.
                        </p>
                    </div>
                </div>
            </div>

            <div class="creators-section">
                <h2 class="creators-title">Creator</h2>
                <div class="row g-4 mt-3">
                    <div class="col-md-4">
                        <div class="creator-card">
                            <h3 class="creator-name">Daniel Chow Shi Jie</h3>
                            <p class="creator-role">Developer</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="creator-card">
                            <h3 class="creator-name">Daniel Ling Chee Kian</h3>
                            <p class="creator-role">Developer</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="creator-card">
                            <h3 class="creator-name">Mah Pik Er</h3>
                            <p class="creator-role">Developer</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="creator-card">
                            <h3 class="creator-name">Ong Shire Li</h3>
                            <p class="creator-role">Developer</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="creator-card">
                            <h3 class="creator-name">Oscar Nge Yan Hen</h3>
                            <p class="creator-role">Developer</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="creator-card">
                            <h3 class="creator-name">Wong Kit Hoong</h3>
                            <p class="creator-role">Developer</p>
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