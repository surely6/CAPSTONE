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
    <title>Terms of Service - Assestify</title>
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

        /* Terms of Service Styles */
        .terms-section {
            background-color: #444;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .terms-title {
            color: #3fd0a4;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .terms-title::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background-color: #3fd0a4;
        }

        .terms-section h2 {
            color: #3fd0a4;
            margin: 1.5rem 0 1rem;
            font-size: 1.5rem;
        }

        .terms-section h3 {
            color: #fff;
            margin: 1.2rem 0 0.8rem;
            font-size: 1.2rem;
        }

        .terms-section p {
            margin-bottom: 1rem;
        }

        .terms-section ul,
        .terms-section ol {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }

        .terms-section li {
            margin-bottom: 0.5rem;
        }

        .terms-section .highlight {
            background-color: rgba(63, 208, 164, 0.1);
            padding: 1rem;
            border-left: 3px solid #3fd0a4;
            margin: 1rem 0;
        }

        .terms-section .last-updated {
            font-style: italic;
            color: #aaa;
            margin-top: 2rem;
            font-size: 0.9rem;
        }

        .terms-section a {
            color: #3fd0a4;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .terms-section a:hover {
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

            .terms-section {
                padding: 1.5rem;
            }

            .terms-title {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            .terms-title {
                font-size: 1.5rem;
            }

            .terms-section h2 {
                font-size: 1.3rem;
            }

            .terms-section {
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
        <section class="terms-section">
            <h1 class="terms-title">Terms of Service</h1>

            <p>Last Updated: <?php echo date("F d, Y"); ?></p>

            <p>Welcome to Assestify. Please read these Terms of Service carefully before using the Assestify website and
                educational platform operated by Assestify Education.</p>

            <p>Your access to and use of the Service is conditioned on your acceptance of and compliance with these
                Terms. These Terms apply to all visitors, users, and others who access or use the Service.</p>

            <div class="highlight">
                <p>By accessing or using the Service, you agree to be bound by these Terms. If you disagree with any
                    part of the terms, then you may not access the Service.</p>
            </div>

            <h2>1. Accounts</h2>

            <p>When you create an account with us, you must provide accurate, complete, and current information at all
                times.</p>

            <p>You are responsible for safeguarding the password that you use to access the Service and for any
                activities or actions under your password, whether your password is with our Service or a third-party
                service.</p>

            <p>You agree not to disclose your password to any third party. You must notify us immediately upon becoming
                aware of any breach of security or unauthorized use of your account.</p>

            <h3>Account Types</h3>
            <p>Assestify offers different types of accounts:</p>
            <ul>
                <li><strong>Student Accounts:</strong> For individual learners accessing educational content</li>
                <li><strong>Instructor Accounts:</strong> For educators providing educational content</li>
                <li><strong>Administrator Accounts:</strong> For platform management</li>
            </ul>

            <p>Each account type has specific permissions and responsibilities as outlined in our platform
                documentation.</p>

            <h2>2. Content and Intellectual Property Rights</h2>

            <p>Our Service allows you to access educational content, including text, graphics, and interactive materials
                ("Content"). The Content is owned by Assestify and/or its licensors and is protected by copyright,
                trademark, and other intellectual property laws.</p>

            <h3>Content Usage</h3>
            <p>You are granted a limited, non-exclusive, non-transferable license to access and use the Content for
                personal, non-commercial educational purposes. You may not:</p>
            <ul>
                <li>Modify, copy, distribute, transmit, display, perform, reproduce, publish, license, create derivative
                    works from, transfer, or sell any Content</li>
                <li>Use any Content for commercial purposes without our express written permission</li>
                <li>Attempt to decompile or reverse engineer any software contained on the Service</li>
                <li>Remove any copyright or other proprietary notations from the Content</li>
                <li>Transfer the Content to another person or "mirror" the Content on any other server</li>
            </ul>

            <h3>User-Generated Content</h3>
            <p>If you are an instructor or have permission to upload content, you retain ownership of your original
                content. By uploading content to our platform, you grant Assestify a worldwide, non-exclusive,
                royalty-free license to use, reproduce, adapt, publish, and distribute such content on our platform for
                educational purposes.</p>

            <h2>3. Service Usage and Conduct</h2>

            <p>You may use our Service only for lawful purposes and in accordance with these Terms. You agree not to use
                the Service:</p>
            <ul>
                <li>In any way that violates any applicable national or international law or regulation</li>
                <li>To transmit, or procure the sending of, any advertising or promotional material, including any "junk
                    mail", "chain letter", "spam", or any other similar solicitation</li>
                <li>To impersonate or attempt to impersonate another user, person, or entity</li>
                <li>To engage in any other conduct that restricts or inhibits anyone's use or enjoyment of the Service
                </li>
            </ul>

            <h3>Academic Integrity</h3>
            <div class="highlight">
                <p>Assestify is committed to academic integrity. Users must not:</p>
                <ul>
                    <li>Share answers to assessments or quizzes</li>
                    <li>Engage in any form of cheating or plagiarism</li>
                    <li>Attempt to manipulate assessment results</li>
                    <li>Share account credentials with others</li>
                </ul>
                <p>Violations of academic integrity may result in account suspension or termination.</p>
            </div>

            <h2>4. Subscription and Payments</h2>

            <p>Some aspects of the Service may be offered on a subscription basis. By selecting a subscription plan, you
                agree to pay the subscription fees as described at the time of your selection.</p>

            <h3>Free Services</h3>
            <p>Assestify currently offers core features including pathway setup, learning materials, and quizzes
                completely free. Premium features may be added in the future.</p>

            <h3>Payment Terms</h3>
            <p>If premium features are introduced:</p>
            <ul>
                <li>Payments will be charged on the date you sign up for a subscription and will cover the use of that
                    service for the period specified</li>
                <li>All fees are exclusive of all taxes, levies, or duties imposed by taxing authorities</li>
                <li>You are responsible for all charges incurred under your account</li>
            </ul>

            <h2>5. Termination</h2>

            <p>We may terminate or suspend your account immediately, without prior notice or liability, for any reason
                whatsoever, including without limitation if you breach the Terms.</p>

            <p>Upon termination, your right to use the Service will immediately cease. If you wish to terminate your
                account, you may simply discontinue using the Service or contact us to request account deletion.</p>

            <h2>6. Limitation of Liability</h2>

            <p>In no event shall Assestify, nor its directors, employees, partners, agents, suppliers, or affiliates, be
                liable for any indirect, incidental, special, consequential or punitive damages, including without
                limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from:</p>
            <ul>
                <li>Your access to or use of or inability to access or use the Service</li>
                <li>Any conduct or content of any third party on the Service</li>
                <li>Any content obtained from the Service</li>
                <li>Unauthorized access, use or alteration of your transmissions or content</li>
            </ul>

            <h2>7. Disclaimer</h2>

            <p>Your use of the Service is at your sole risk. The Service is provided on an "AS IS" and "AS AVAILABLE"
                basis. The Service is provided without warranties of any kind, whether express or implied, including,
                but not limited to, implied warranties of merchantability, fitness for a particular purpose,
                non-infringement or course of performance.</p>

            <h2>8. Governing Law</h2>

            <p>These Terms shall be governed and construed in accordance with the laws of Malaysia, without regard to
                its conflict of law provisions.</p>

            <p>Our failure to enforce any right or provision of these Terms will not be considered a waiver of those
                rights. If any provision of these Terms is held to be invalid or unenforceable by a court, the remaining
                provisions of these Terms will remain in effect.</p>

            <h2>9. Changes to Terms</h2>

            <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision
                is material, we will try to provide at least 30 days' notice prior to any new terms taking effect. What
                constitutes a material change will be determined at our sole discretion.</p>

            <p>By continuing to access or use our Service after those revisions become effective, you agree to be bound
                by the revised terms. If you do not agree to the new terms, please stop using the Service.</p>

            <h2>10. Contact Us</h2>

            <p>If you have any questions about these Terms, please contact us at:</p>
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