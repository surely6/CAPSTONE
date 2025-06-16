<?php
session_start();
include('connect.php');
include('font.php');
include("../../block.php");


$message = "";

// Check if instructor is logged in
//if (!isset($_SESSION['instructor_id'])) {
//    header("Location: login.php"); // Redirect to login page if not logged in
//    exit();
//}

// $_SESSION['instructor_id'] = 'I01'; 
$instructorID = $_SESSION['user_id'];

if (!isset($_SESSION['user_id'])) {
    die("Instructor ID is not set in the session.");
}


// Fetch instructor information
$query = "SELECT * FROM instructors WHERE instructor_id = '$instructorID'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching instructor data: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    $instructor = mysqli_fetch_assoc($result);
    $instructorEmail = $instructor['instructor_email'] ?? 'Email not found';
    $instructorName = $instructor['instructor_name'] ?? 'Name not found';
    $instructorPassword = $instructor['instructor_password'] ?? '';
    $profileImage = $instructor['profile_pic_url'] ?? 'default.jpg';
} else {
    $instructorEmail = 'Email not found';
    $instructorName = 'Name not found';
}

if (isset($_POST['update_profile'])) {
    $newName = trim($_POST['name']);
    $newEmail = trim($_POST['email']);
    $profilePicURL = NULL; // Initialize profile picture URL

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $targetDir = "uploads/";
        $fileName = uniqid() . "_" . basename($_FILES["profile_image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Create uploads directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $allowedTypes = array("jpg", "jpeg", "png", "gif");
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFilePath)) {
                $profilePicURL = $targetFilePath;
            } else {
                $message = "<script>alert('Sorry, there was an error uploading your file. Check permissions for the `uploads/` directory.');</script>";
            }
        } else {
            $message = "<script>alert('Only JPG, JPEG, PNG & GIF files are allowed.');</script>";
        }
    }

    if ($profilePicURL !== null) {
        $updateQuery = "UPDATE instructors SET instructor_name = '$newName', instructor_email = '$newEmail', profile_pic_url = '$profilePicURL' WHERE instructor_id = '$instructorID'";
    } else {
        // Keep the old image if not uploading a new one
        $updateQuery = "UPDATE instructors SET instructor_name = '$newName', instructor_email = '$newEmail', profile_pic_url = '$profileImage' WHERE instructor_id = '$instructorID'";
    }

    if (mysqli_query($conn, $updateQuery)) {
        $message = "<script>alert('Profile updated successfully.');</script>";
        header("Location: profile.php");
        exit();
    } else {
        $message = "<script>alert('Error updating profile: " . mysqli_error($conn) . "');</script>";
    }
}


if (isset($_POST['update_password'])) {
    $currentPassword = trim($_POST['current_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);
    // Fetch the hashed password from the database
    $query = "SELECT instructor_password FROM instructors WHERE instructor_id = '$instructorID'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $instructorPassword = $row['instructor_password'];
        if (empty($currentPassword)) {
            echo "<script>alert('Current password is required.');</script>";
        } elseif (!password_verify($currentPassword, $instructorPassword)) {
            echo "<script>alert('Incorrect current password. Please try again.');</script>";
        } elseif (empty($newPassword) || empty($confirmPassword)) {
            echo "<script>alert('New password and confirmation are required.');</script>";
        } elseif ($newPassword !== $confirmPassword) {
            echo "<script>alert('New password and confirm password do not match.');</script>";
        } elseif (strlen($newPassword) < 8) {
            echo "<script>alert('Password must be at least 8 characters long.');</script>";
        } elseif (!preg_match('/[A-Z]/', $newPassword)) {
            echo "<script>alert('Password must contain at least one uppercase letter.');</script>";
        } elseif (!preg_match('/[a-z]/', $newPassword)) {
            echo "<script>alert('Password must contain at least one lowercase letter.');</script>";
        } elseif (!preg_match('/[0-9]/', $newPassword)) {
            echo "<script>alert('Password must contain at least one number.');</script>";
        } elseif (!preg_match('/[\W]/', $newPassword)) {
            echo "<script>alert('Password must contain at least one special character.');</script>";
        } else {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updatePasswordQuery = "UPDATE instructors SET instructor_password = '$hashedPassword' WHERE instructor_id = '$instructorID'";
            if (mysqli_query($conn, $updatePasswordQuery)) {
                $message = "<script>alert('Password changed successfully.');</script>";
                header("Location: profile.php");
                exit();
            } else {
                $message = "<script>alert('Error changing password: " . mysqli_error($conn) . "');</script>";
            }
        }
    } else {
        $message = "<script>alert('Error fetching password from the database.');</script>";
    }
}
?>

<!DOCTYPE html>

<head>
    <link href="style.css" rel="stylesheet">
    <link href="header-format.css" rel="stylesheet">
    <title>INSTRUCTOR MANAGE PROFILE PAGE</title>
</head>

<body>
    <?php echo $message; ?>
    <header>
        <ul>
            <li id="profileDropdown">
                <img src="icon/profile.png" alt="Profile Icon" id="profileIcon"
                    style="width: 50px; height: 50px; cursor: pointer; align-items: center; display: flex; justify-content: center; margin-left: 10px; margin-top: 10px;">
                <div id="profileMenu" class="dropdown-content">
                    <a href="profile.php">My Profile</a>
                    <!-- <a href="#">Summary</a> -->
                    <a href="system_feedback.php">Feedback</a>
                    <a href="../../logout.php">Logout</a>
                </div>
            </li>
            <li class="options"><a href="">CREATE LEARNING MATERIAL</a></li>
            <li class="options"><a href="">CREATE QUIZ</a></li>
            <li class="options"><a href="">DRAFT</a></li>
            <li id="logo" style="margin-left: 65px;"><a
                    href="/capstone/INSTRUCTOR ( CHOW )/Learning Material View.php">ASSESTIFY</a></li>
        </ul>
    </header>
    <style>
        .manage-account-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
        }

        .manage-account-sidebar {
            width: 200px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-right: 20px;
        }

        .manage-account-sidebar h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
            color: var(--dark-grey);
        }

        .manage-account-sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .manage-account-sidebar li {
            margin: 0;
            cursor: pointer;
            color: var(--dark-grey);
            padding: 0px 12px;
            text-align: left;
            display: block;
            float: none;
            width: 200px;
            height: 50px;
        }

        .manage-account-sidebar li.active {
            font-weight: bold;
            color: var(--green);
        }

        .content {
            flex: 0 0 70%;
            max-width: 800px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-left: 20px;
            margin-right: 20px;
        }

        .tab-pane {
            margin-bottom: 20px;
        }


        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            background-color: var(--dark-grey);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            color: #6c757d;
            margin-right: 20px;
            overflow: hidden;
            position: relative;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            position: absolute;
            top: 0;
            left: 0;
        }

        .profile-info h1 {
            margin: 0 0 10px 0;
            color: var(--grey);
            position: relative;
            display: inline-block;
        }

        .profile-info h1::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -5px;
            width: 100%;
            height: 2px;
            background-color: var(--gray);
        }

        .profile-info p {
            margin: 5px 0;
            color: var(--grey);
        }

        .form-group {
            margin-bottom: 5px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: var(--dark-grey);
        }

        .form-control {
            width: 90%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            background-color: var(--light-grey);
            color: var(--dark-grey);
            margin: 10px 0;
        }

        .buttons-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            margin: 0 5px;
        }

        .btn-primary {
            background-color: var(--blue);
            color: white;

        }

        .btn-primary:hover {
            background-color: #0a6ad1;
        }

        .btn-secondary {
            background-color: var(--red);
            color: white;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background-color: #a14241;
        }

        .tab-content>.tab-pane {
            display: none;
        }

        .tab-content>.active {
            display: block;
        }

        @media screen and (max-width: 768px) {
            .manage-account-container {
                flex-direction: column;
                padding: 10px;
            }

            .manage-account-sidebar {
                width: 100%;
                margin-right: 0;
                margin-bottom: 20px;
            }

            .manage-account-sidebar ul {
                flex-direction: row;
                gap: 10px;
                justify-content: space-between;
            }

            .manage-account-sidebar li {
                width: auto;
                padding: 5px 10px;
                font-size: 16px;
            }

            .content {
                flex: 0 0 100%;
                margin-left: 0;
                margin-right: 0;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-avatar {
                width: 60px;
                height: 60px;
            }

            .form-control {
                width: 100%;
            }

            .buttons-container {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                margin: 5px 0;
            }
        }

        @media screen and (max-width: 480px) {
            .manage-account-container {
                flex-direction: column;
                padding: 5px;
            }

            .manage-account-sidebar {
                width: 100%;
                margin-bottom: 20px;
            }

            .manage-account-sidebar ul {
                flex-direction: column;
                gap: 10px;
                justify-content: flex-start;
            }

            .manage-account-sidebar li {
                width: 100%;
                padding: 10px 15px;
                font-size: 14px;
            }

            .content {
                flex: 0 0 100%;
                margin-left: 0;
                margin-right: 0;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-avatar {
                width: 50px;
                height: 50px;
            }

            .form-control {
                width: 100%;
                padding: 8px;
            }

            .buttons-container {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                margin: 5px 0;
                padding: 12px 0;
            }
        }
    </style>
    <main>
        <div class="manage-account-container">
            <div class="manage-account-sidebar">
                <h3>Manage Account</h3>
                <ul>
                    <li class="active" data-tab="account-details">Account Details</li>
                    <li data-tab="change-password">Change Password</li>
                </ul>
            </div>

            <div class="content">
                <div class="tab-content">
                    <!-- Account Details Tab -->
                    <div class="tab-pane active" id="account-details">
                        <div class="profile-header">
                            <div class="profile-avatar">
                                <?php
                                if (!empty($profileImage) && file_exists($profileImage)) {
                                    echo '<img src="' . htmlspecialchars($profileImage) . '" alt="Profile Image">';
                                } else {
                                    echo '<div class="profile-initial">' . substr($instructorName ?? 'U', 0, 1) . '</div>';
                                }
                                ?>
                            </div>
                            <div class="profile-info">
                                <h1><?php echo $instructorName; ?></h1>
                                <p><strong>ID: </strong><?php echo $instructorID; ?></p>
                                <p><strong>Email: </strong><?php echo $instructorEmail; ?></p>
                                <p><strong>Role: </strong>Instructor</p>
                            </div>
                        </div>

                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="profile_image">Profile Image</label>
                                <input type="file" class="form-control" id="profile_image" name="profile_image"
                                    accept="image/*"><br>
                                <small>Upload a profile image (JPG or PNG)</small>
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?php echo htmlspecialchars($instructorName); ?>" autocomplete="name">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($instructorEmail); ?>" autocomplete="email">
                            </div>
                            <div class="buttons-container">
                                <a href="profile.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" name="update_profile" class="btn btn-primary">Update
                                    Profile</button>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane" id="change-password">
                        <form action="" method="POST" autocomplete="off" onsubmit="return validatePasswordChange()">
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" class="form-control" id="current_password"
                                    name="current_password" autocomplete="current-password" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                    autocomplete="new-password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,}"
                                    title="Must contain at least one number, one uppercase, one lowercase letter, one special character, and at least 8 characters"
                                    required>
                                <small class="text-muted">Password requirements: 8+ characters with uppercase,
                                    lowercase, number, and special character</small>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password"
                                    name="confirm_password" autocomplete="new-password" required>
                            </div>
                            <div class="buttons-container">
                                <a href="profile.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" name="update_password" class="btn btn-primary">Change
                                    Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Profile dropdown menu
        document.getElementById('profileIcon').addEventListener('click', function (event) {
            event.stopPropagation();
            document.getElementById('profileMenu').classList.toggle('active');
        });

        // Close dropdown when clicking elsewhere
        window.addEventListener('click', function (event) {
            const profileMenu = document.getElementById('profileMenu');
            if (!document.getElementById('profileDropdown').contains(event.target)) {
                profileMenu.classList.remove('active');
            }
        });

        // Tab navigation
        document.querySelectorAll('.manage-account-sidebar li').forEach(function (tab) {
            tab.addEventListener('click', function () {
                document.querySelectorAll('.manage-account-sidebar li').forEach(function (t) {
                    t.classList.remove('active');
                });
                document.querySelectorAll('.tab-pane').forEach(function (pane) {
                    pane.classList.remove('active');
                });

                this.classList.add('active');
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        function validatePasswordChange() {
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            if (!currentPassword) {
                alert('Please enter your current password');
                return false;
            }
            if (newPassword !== confirmPassword) {
                alert('New password and confirmation do not match');
                return false;
            }
            const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,}$/;
            if (!strongRegex.test(newPassword)) {
                alert('Password must be at least 8 characters with uppercase, lowercase, number, and special character');
                return false;
            }
            return true;
        }
    </script>
</body>