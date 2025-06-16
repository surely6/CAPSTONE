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
    <title>FAQ - Assestify</title>
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

        /* FAQ Section */
        .faq-section {
            padding: 2rem;
            margin-top: 2rem;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .faq-title {
            position: relative;
            padding-bottom: 0.5rem;
            margin-bottom: 2rem;
            font-size: 1.8rem;
            color: #3fd0a4;
        }

        .faq-title::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background-color: #3fd0a4;
        }

        .faq-item {
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .faq-question {
            width: 100%;
            background: none;
            border: none;
            text-align: left;
            padding: 1rem 0;
            color: #3fd0a4;
            font-size: 1.1rem;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            color: #2fb890;
        }

        .faq-question:focus {
            outline: none;
        }

        .faq-question i {
            transition: transform 0.3s ease;
        }

        .faq-question[aria-expanded="true"] i {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 0 0 1.5rem 0;
            color: #f0f0f0;
            line-height: 1.6;
        }

        .faq-answer ul {
            padding-left: 1.5rem;
            margin-top: 0.5rem;
        }

        .faq-answer li {
            margin-bottom: 0.5rem;
        }

        /* Footer */
        footer {
            background-color: #3fd0a4;
            color: #333;
            padding: 1.5rem 2rem;
            margin-top: 3rem;
        }

        .footer-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .footer-top {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 2rem;
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

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .footer-links-column {
            min-width: 150px;
        }

        .footer-links-title {
            font-weight: 600;
            margin-bottom: 0.8rem;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .footer-link-list {
            list-style: none;
            padding: 0;
        }

        .footer-link-list li {
            margin-bottom: 0.5rem;
        }

        .footer-link {
            color: #333;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .footer-link:hover {
            color: #fff;
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .footer-bottom-links {
            display: flex;
            gap: 1.5rem;
        }

        .heart {
            color: #ff4d4d;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                padding: 1rem;
            }

            header.scrolled {
                padding: 0.6rem 1rem;
            }

            .faq-section {
                padding: 1.5rem;
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
            .faq-title {
                font-size: 1.5rem;
            }

            .faq-question {
                font-size: 1rem;
            }

            .footer-links-column {
                min-width: 100%;
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
        <section class="faq-section">
            <h2 class="faq-title">FAQ</h2>

            <div class="accordion" id="faqAccordion">
                <!-- FAQ Item 1 -->
                <div class="faq-item">
                    <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq1"
                        aria-expanded="false" aria-controls="faq1">
                        What is Assestify?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="faq1" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="faq-answer">
                            Assestify is an online learning platform built for Malaysian students preparing for SPM from
                            Form 1 to Form 5. It offers a personalized learning path based on your strengths and
                            preferences.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item">
                    <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq2"
                        aria-expanded="false" aria-controls="faq2">
                        How does Assestify personalize my learning?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="faq2" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="faq-answer">
                            After completing a short learning style questionnaire (Visual, Auditory, Read/Write), the
                            system recommends materials and study methods that best match how you
                            learn.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="faq-item">
                    <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq3"
                        aria-expanded="false" aria-controls="faq3">
                        Who is this platform for?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="faq3" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="faq-answer">
                            Assestify is primarily designed for secondary school students (Form 1–5) preparing for SPM
                            exams. However, any student who wants to improve their learning habits is welcome.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="faq-item">
                    <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq4"
                        aria-expanded="false" aria-controls="faq4">
                        What subjects are covered?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="faq4" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="faq-answer">
                            <p>We currently support core SPM subjects such as:</p>
                            <ul>
                                <li>Mathematics</li>
                                <li>Bahasa Melayu</li>
                                <li>English</li>
                                <li>Science</li>
                                <li>Sejarah</li>
                                <li>And more coming soon!</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="faq-item">
                    <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq5"
                        aria-expanded="false" aria-controls="faq5">
                        Is Assestify free to use?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="faq5" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="faq-answer">
                            Yes! Our core features including pathway setup, learning materials, and quizzes are
                            completely free. Premium features may be added in the future.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 6 -->
                <div class="faq-item">
                    <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq6"
                        aria-expanded="false" aria-controls="faq6">
                        Can I track my progress?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="faq6" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="faq-answer">
                            Yes, once you've started your learning pathway, you can monitor your completed modules, set
                            goals, and see how well you're progressing over time.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 7 -->
                <div class="faq-item">
                    <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq7"
                        aria-expanded="false" aria-controls="faq7">
                        How do I reset or change my learning style?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="faq7" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="faq-answer">
                            You can revisit the Learning Style Questionnaire from your profile settings and retake it
                            anytime to update your preferences.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 8 -->
                <div class="faq-item">
                    <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq8"
                        aria-expanded="false" aria-controls="faq8">
                        Is my data secure on Assestify?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="faq8" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="faq-answer">
                            Absolutely. We use industry-standard encryption and follow best practices to keep your data
                            private and secure.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 9 -->
                <div class="faq-item">
                    <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq9"
                        aria-expanded="false" aria-controls="faq9">
                        Can I access Assestify on mobile?
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="faq9" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="faq-answer">
                            Yes! Assestify is mobile-friendly and works smoothly on most smartphones and tablets.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- <footer>
            <div class="footer-content">
                <div class="footer-top">
                    <div class="footer-logo">
                        <div>ASSESTIFY</div>
                        <small>CREATED BY 6 PEOPLE</small>
                    </div>

                    <div class="footer-links">
                        <div class="footer-links-column">
                            <h4 class="footer-links-title">Quick Access</h4>
                            <ul class="footer-link-list">
                                <li><a href="subjects/index.php" class="footer-link">All Subjects</a></li>
                                <li><a href="levels/lower-form.php" class="footer-link">Lower Form</a></li>
                                <li><a href="levels/upper-form.php" class="footer-link">Upper Form</a></li>
                                <li><a href="levels/science-stream.php" class="footer-link">Science Stream</a></li>
                                <li><a href="levels/art-stream.php" class="footer-link">Art Stream</a></li>
                            </ul>
                        </div>

                        <div class="footer-links-column">
                            <h4 class="footer-links-title">Company</h4>
                            <ul class="footer-link-list">
                                <li><a href="about-us.php" class="footer-link">About Us</a></li>
                                <li><a href="careers.php" class="footer-link">Careers</a></li>
                                <li><a href="contact-us.php" class="footer-link">Contact Us</a></li>
                            </ul>
                        </div>

                        <div class="footer-links-column">
                            <h4 class="footer-links-title">Support</h4>
                            <ul class="footer-link-list">
                                <li><a href="faq.php" class="footer-link">FAQ</a></li>
                                <li><a href="feedback.php" class="footer-link">Feedback</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <div class="footer-credit">
                        made with <span class="heart">❤</span> in MALAYSIA
                    </div>
                    <div class="footer-bottom-links">
                        <a href="privacy-policy.php" class="footer-link">Privacy Policy</a>
                        <a href="terms.php" class="footer-link">Terms of Service</a>
                    </div>
                </div>
            </div>
        </footer> -->

    <?php include("footer.php"); ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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

        // Add active class to FAQ questions when expanded
        document.addEventListener('DOMContentLoaded', function () {
            const faqButtons = document.querySelectorAll('.faq-question');

            faqButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const expanded = this.getAttribute('aria-expanded') === 'false';
                    this.setAttribute('aria-expanded', !expanded);
                });
            });
        });
    </script>
</body>

</html>