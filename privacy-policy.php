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
    <title>Privacy Policy - Assestify</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            padding: 6rem 2rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* Privacy Policy Styles */
        .privacy-section {
            background-color: #444;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .privacy-title {
            color: #3fd0a4;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .privacy-title::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background-color: #3fd0a4;
        }

        .privacy-section h2 {
            color: #3fd0a4;
            margin: 1.5rem 0 1rem;
            font-size: 1.5rem;
        }

        .privacy-section h3 {
            color: #fff;
            margin: 1.2rem 0 0.8rem;
            font-size: 1.2rem;
        }

        .privacy-section p {
            margin-bottom: 1rem;
        }

        .privacy-section ul,
        .privacy-section ol {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }

        .privacy-section li {
            margin-bottom: 0.5rem;
        }

        .privacy-section .highlight {
            background-color: rgba(63, 208, 164, 0.1);
            padding: 1rem;
            border-left: 3px solid #3fd0a4;
            margin: 1rem 0;
        }

        .privacy-section .last-updated {
            font-style: italic;
            color: #aaa;
            margin-top: 2rem;
            font-size: 0.9rem;
        }

        .privacy-section a {
            color: #3fd0a4;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .privacy-section a:hover {
            color: #2fb890;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                padding: 1rem;
            }

            header.scrolled {
                padding: 0.6rem 1rem;
            }

            main {
                padding: 5rem 1rem 1rem;
            }

            .privacy-section {
                padding: 1.5rem;
            }

            .privacy-title {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            .privacy-title {
                font-size: 1.5rem;
            }

            .privacy-section h2 {
                font-size: 1.3rem;
            }

            .privacy-section {
                padding: 1.2rem;
            }
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
        <section class="privacy-section">
            <h1 class="privacy-title">Privacy Policy</h1>

            <p>Last Updated: <?php echo date("F d, Y"); ?></p>

            <p>Welcome to Assestify. We are committed to protecting your personal information and your right to privacy.
                This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you
                visit our website and use our services.</p>

            <div class="highlight">
                <p>Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy,
                    please do not access the site.</p>
            </div>

            <h2>1. Information We Collect</h2>

            <h3>Personal Information</h3>
            <p>We may collect personal information that you voluntarily provide to us when you register on our platform,
                express interest in obtaining information about us or our products and services, or otherwise contact
                us. The personal information we collect may include:</p>
            <ul>
                <li>Name</li>
                <li>Email address</li>
                <li>Educational information</li>
                <li>Learning preferences</li>
                <li>Performance data</li>
            </ul>

            <h3>Usage Data</h3>
            <p>We automatically collect certain information when you visit, use, or navigate our platform. This
                information does not reveal your specific identity but may include:</p>
            <ul>
                <li>Time spent on pages</li>
                <li>Login and logout times</li>
            </ul>

            <h2>2. How We Use Your Information</h2>
            <p>We use the information we collect for various purposes, including:</p>
            <ul>
                <li>Providing, personalizing, and improving our services</li>
                <li>Creating and maintaining your account</li>
                <li>Responding to your inquiries and support requests</li>
                <li>Sending administrative information</li>
                <li>Developing new products and services</li>
                <li>Analyzing usage patterns to improve user experience</li>
                <li>Protecting our services and users</li>
            </ul>

            <h2>3. Data Security</h2>
            <p>We implement appropriate technical and organizational security measures to protect your personal
                information. These measures include:</p>

            <h3>Password Security</h3>
            <div class="highlight">
                <p>Your password is never stored in plain text. We use industry-standard password hashing algorithms to
                    securely store your password. This means:</p>
                <ul>
                    <li>Even our staff cannot see your actual password</li>
                    <li>Each password is salted and hashed to protect against rainbow table attacks</li>
                    <li>In the unlikely event of a data breach, your actual password remains secure</li>
                </ul>
            </div>

            <h3>Session Security</h3>
            <p>We implement secure session management practices to protect your account while you're logged in:</p>
            <ul>
                <li>Sessions are encrypted</li>
                <li>Automatic timeout after 24 hours of inactivity for student accounts</li>
                <li>Session regeneration upon login to prevent session fixation attacks</li>
                <li>All login and logout activities are logged for security monitoring</li>
            </ul>

            <h3>Data Encryption</h3>
            <p>We use secure, encrypted connections (HTTPS/SSL) to protect data transmitted between your browser and our
                servers.</p>

            <h2>4. Data Retention</h2>
            <p>We will retain your personal information only for as long as necessary to fulfill the purposes outlined
                in this Privacy Policy, unless a longer retention period is required or permitted by law.</p>

            <h2>5. Your Data Rights</h2>
            <p>Depending on your location, you may have certain rights regarding your personal information, including:
            </p>
            <ul>
                <li>Right to access your personal data</li>
                <li>Right to correct inaccurate data</li>
                <li>Right to request deletion of your data</li>
                <li>Right to restrict or object to our processing of your data</li>
                <li>Right to data portability</li>
            </ul>
            <p>To exercise these rights, please contact us using the information provided in the "Contact Us" section.
            </p>

            <h2>6. Children's Privacy</h2>
            <p>Our services are intended for educational use by students of all ages, including those under 18. We
                comply with applicable laws regarding the protection of children's data. Parents or guardians can
                review, delete, or refuse further collection of their child's information by contacting us.</p>

            <h2>7. Third-Party Services</h2>
            <p>Our platform may contain links to third-party websites or services that are not owned or controlled by
                Assestify. We have no control over and assume no responsibility for the content, privacy policies, or
                practices of any third-party sites or services.</p>

            <h2>8. Changes to This Privacy Policy</h2>
            <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new
                Privacy Policy on this page and updating the "Last Updated" date.</p>

            <h2>9. Contact Us</h2>
            <p>If you have any questions about this Privacy Policy, please contact us at:</p>
            <p>Email: <a href="mailto:assestifyofficial@gmail.com">assestifyofficial@gmail.com</a><br>
                Address: Assestify Pacific Center, Jalan Teknologi 5, 57000 Kuala Lumpur, Malaysia</p>

            <p class="last-updated">This document was last updated on <?php echo date("F d, Y"); ?></p>
        </section>
    </main>

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
    </script>
</body>

</html>