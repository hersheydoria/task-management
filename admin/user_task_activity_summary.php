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

<div class="admin-panel">
    <h2>User Task Activity Summary</h2>
    <table>
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

<?php include '../includes/footer.php'; ?>
