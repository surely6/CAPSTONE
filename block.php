<?php
if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["logged_in"]) ||
    !isset($_SESSION["is_student"]) ||
    !isset($_SESSION["is_instructor"]) ||
    !isset($_SESSION["is_admin"])
) {
    echo '<script>alert("Please Log In"); window.location="/capstone/index.php";</script>';
    exit();
}
?>