<?php
require_once 'config.php';
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Check for existing submission
$stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id = ?");
$stmt->execute([$user_id]);
$project = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$project) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $github_link = trim($_POST['github_link']);
    
    // Handle PDF upload
    $pdf_path = null;
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['pdf_file']['tmp_name'];
        $file_name = $_FILES['pdf_file']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if ($file_ext === 'pdf') {
            $new_file_name = uniqid('proj_') . '.pdf';
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            
            $dest = $upload_dir . $new_file_name;
            if (move_uploaded_file($file_tmp, $dest)) {
                $pdf_path = $dest;
            } else {
                $message = "Error uploading PDF file.";
            }
        } else {
            $message = "Only PDF files are allowed.";
        }
    }

    if (empty($title) || empty($description)) {
        $message = "Title and Description are required.";
    } elseif (empty($message)) {
        // Insert project
        $stmt = $pdo->prepare("INSERT INTO projects (user_id, title, description, github_link, pdf_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $description, $github_link, $pdf_path]);
        $message = "Project submitted successfully!";
        // Refresh project data
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $project = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - SKIT Portal</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h2>Student Dashboard</h2>
            <div class="user-profile">
                <span><?= htmlspecialchars($_SESSION['name']) ?></span>
                <a href="logout.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </header>

        <div class="dashboard-grid <?= $project ? '' : 'two-cols' ?>">
            
            <?php if ($project): ?>
                <div class="glass-card">
                    <h2 style="color: var(--success);"><i class="fa-solid fa-circle-check"></i> Project Submitted Successfully</h2>
                    <hr style="border: 0; border-top: 1px solid var(--card-border); margin: 1.5rem 0;">
                    
                    <h3><?= htmlspecialchars($project['title']) ?></h3>
                    <p style="margin-top: 1rem;"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
                    
                    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                        <?php if ($project['github_link']): ?>
                            <a href="<?= htmlspecialchars($project['github_link']) ?>" target="_blank" class="btn btn-outline">
                                <i class="fa-brands fa-github"></i> View Repository
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($project['pdf_path']): ?>
                            <a href="<?= htmlspecialchars($project['pdf_path']) ?>" target="_blank" class="btn btn-primary">
                                <i class="fa-solid fa-file-pdf"></i> View PDF
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="glass-card" style="grid-column: 1 / -1; max-width: 800px; margin: 0 auto; width: 100%;">
                    <h2>Submit Your Project</h2>
                    <p>Fill out the details below to submit your academic project.</p>
                    
                    <?php if ($message): ?>
                        <div style="background: rgba(239,68,68,0.1); color: var(--danger); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid rgba(239,68,68,0.2);">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="title">Project Title *</label>
                            <input type="text" id="title" name="title" class="form-control" required placeholder="Enter the full title of your project">
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Project Description *</label>
                            <textarea id="description" name="description" class="form-control" required placeholder="Describe your project, features, and tech stack..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="github_link">GitHub Repository Link</label>
                            <input type="url" id="github_link" name="github_link" class="form-control" placeholder="https://github.com/username/repo">
                        </div>
                        
                        <div class="form-group">
                            <label for="pdf_file">Project Report (PDF)</label>
                            <input type="file" id="pdf_file" name="pdf_file" accept=".pdf" class="form-control" style="padding: 0.5rem; background: rgba(15,23,42,0.4);">
                        </div>
                        
                        <div style="margin-top: 2rem;">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Submit Project
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</body>
</html>
