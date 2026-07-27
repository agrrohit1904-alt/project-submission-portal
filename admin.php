<?php
require_once 'config.php';
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch all projects with user info
$stmt = $pdo->query("
    SELECT p.*, u.name as student_name, u.email as student_email 
    FROM projects p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.submitted_at DESC
");
$projects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SKIT Portal</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container" style="max-width: 1400px;">
        <header class="header">
            <h2>Admin Panel <span class="status-badge" style="margin-left: 1rem;">Administrator</span></h2>
            <div class="user-profile">
                <span><?= htmlspecialchars($_SESSION['name']) ?></span>
                <a href="logout.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </header>

        <div class="glass-card">
            <h3><i class="fa-solid fa-layer-group"></i> All Project Submissions</h3>
            <p>View and manage all projects submitted by SKIT students.</p>
            
            <div class="table-container" style="margin-top: 2rem;">
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Project Title</th>
                            <th>Submitted At</th>
                            <th>Links</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($projects) > 0): ?>
                            <?php foreach ($projects as $proj): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($proj['student_name']) ?></strong></td>
                                    <td><span style="color: var(--text-muted);"><?= htmlspecialchars($proj['student_email']) ?></span></td>
                                    <td><?= htmlspecialchars($proj['title']) ?></td>
                                    <td><?= date('M d, Y g:i A', strtotime($proj['submitted_at'])) ?></td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <?php if ($proj['github_link']): ?>
                                                <a href="<?= htmlspecialchars($proj['github_link']) ?>" target="_blank" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                                    <i class="fa-brands fa-github"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($proj['pdf_path']): ?>
                                                <a href="<?= htmlspecialchars($proj['pdf_path']) ?>" target="_blank" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                                    <i class="fa-solid fa-file-pdf"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                    <i class="fa-solid fa-box-open" style="font-size: 2rem; margin-bottom: 1rem;"></i><br>
                                    No projects have been submitted yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
