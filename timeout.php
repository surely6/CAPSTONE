<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only apply timeout to student accounts
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && 
    isset($_SESSION['is_student']) && $_SESSION['is_student'] === true) {
    
    // Timeout duration: 24 hours in seconds
    $timeout_duration = 86400;
    
    // Check if login time is set
    if (!isset($_SESSION['LOGIN_TIME'])) {
        // Set initial login time if not already set
        $_SESSION['LOGIN_TIME'] = time();
    }
    
    // Check if 24 hours have passed since login
    if ((time() - $_SESSION['LOGIN_TIME']) > $timeout_duration) {
        // Session has expired, redirect to logout.php
        header("Location: logout.php");
        exit();
    }
}
?>