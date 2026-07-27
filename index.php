<?php
require_once 'config.php';
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKIT Project Submission Portal</title>
    <meta name="description" content="Secure portal for SKIT students to submit their academic projects.">
    <link rel="stylesheet" href="assets/style.css">
    <!-- Load FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="center-screen container">
        <div class="glass-card hero-text">
            <div style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h1>SKIT Project Portal</h1>
            <p>Welcome to the official project submission portal for SKIT. Please sign in using your <strong>@skit.ac.in</strong> email address to securely submit your project files and repository links.</p>
            
            <div style="margin-top: 2rem;">
                <a href="login.php" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 2rem;">
                    <i class="fa-brands fa-google"></i> Sign in with Google
                </a>
            </div>
        </div>
    </div>
</body>
</html>
