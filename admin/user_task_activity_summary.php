<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Ensure only admins can access this page
requireRole('admin');

// Fetch data from the view
$stmt = $pdo->query("SELECT * FROM user_task_activity_summary");
$summary = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<!-- Admin Panel for Task Activity Summary -->
<div class="user-task-summary-admin-panel">
    <!-- Fixed Header -->
    <div class="user-task-summary-admin-panel-header">
        <h2><i class="fas fa-file-alt"></i> User Task Activity Summary</h2>
    </div>

    <!-- Scrollable Table Container with Specific Class for Styling -->
    <div class="user-task-summary-table-container">
        <table class="user-task-summary-styled-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Tasks Assigned</th>
                    <th>Tasks Created</th>
                    <th>Actions Logged</th>
                    <th>Last Action Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($summary as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td><?= htmlspecialchars($row['tasks_assigned_count']) ?></td>
                        <td><?= htmlspecialchars($row['tasks_created_count']) ?></td>
                        <td><?= htmlspecialchars($row['actions_count']) ?></td>
                        <td><?= htmlspecialchars($row['last_action_timestamp']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
