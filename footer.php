<?php
// footer.php
?>
<!-- Footer Section -->
<footer>
    <div class="footer-content">
        <div class="footer-top">
            <div class="footer-logo">
                <div>ASSESTIFY</div>
                <small>CREATED BY 6 PEOPLE</small>
            </div>

            <div class="footer-links">
                <div class="footer-links-column">
                    <h4 class="footer-links-title">Company</h4>
                    <ul class="footer-link-list">
                        <li><a href="/capstone/about-us.php" class="footer-link">About Us</a></li>
                        <li><a href="/capstone/careers.php" class="footer-link">Careers</a></li>
                        <li><a href="/capstone/contact-us.php" class="footer-link">Contact Us</a></li>
                    </ul>
                </div>

                <div class="footer-links-column">
                    <h4 class="footer-links-title">Support</h4>
                    <ul class="footer-link-list">
                        <li><a href="/capstone/faq.php" class="footer-link">FAQ</a></li>
                        <!-- <li><a href="/capstone/feedback.php" class="footer-link">Feedback</a></li> -->
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-credit">
                made with <span class="heart">❤</span> in MALAYSIA
            </div>
            <div class="footer-bottom-links">
                <a href="/capstone/privacy-policy.php" class="footer-link">Privacy Policy</a>
                <a href="/capstone/terms.php" class="footer-link">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<!-- Footer Styles -->
<style>
    /* Footer Styles */
    footer {
        background-color: #3fd0a4;
        color: #333;
        padding: 1.5rem 2rem;
        margin-top: auto;
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
        padding-left: 0;
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

    /* Responsive Design for Footer */
    @media (max-width: 768px) {
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
        .footer-links-column {
            min-width: 100%;
        }
    }
</style>