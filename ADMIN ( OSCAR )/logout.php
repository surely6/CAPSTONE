<?php
session_start();

//Store user information before clearing session
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

if ($isLoggedIn) {
    // Get user ID and type
    $userId = $_SESSION['user_id'];
    $isStudent = isset($_SESSION['is_student']) && $_SESSION['is_student'] === true;
    $isInstructor = isset($_SESSION['is_instructor']) && $_SESSION['is_instructor'] === true;
    $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

    // Database connection
    $host = 'localhost';
    $dbname = 'capstone';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Log the logout in user_logs table based on user type
        if ($isStudent) {
            $logStmt = $pdo->prepare("INSERT INTO user_logs (student_id, datetime_of_log, isLogin) VALUES (?, NOW(), 0)");
            $logStmt->execute([$userId]);
        } elseif ($isInstructor) {
            $logStmt = $pdo->prepare("INSERT INTO user_logs (instructor_id, datetime_of_log, isLogin) VALUES (?, NOW(), 0)");
            $logStmt->execute([$userId]);
        } elseif ($isAdmin) {
            $logStmt = $pdo->prepare("INSERT INTO user_logs (admin_id, datetime_of_log, isLogin) VALUES (?, NOW(), 0)");
            $logStmt->execute([$userId]);
        }
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Clear session
$_SESSION = array();
session_destroy();

// Redirect
header("Location: /capstone/index.php");
exit();
?>