<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Only admin should access this page
requireRole('admin');

// Fetch all logs
$stmt = $pdo->prepare("
    SELECT 
        logs.id, 
        users.username, 
        logs.action, 
        TO_CHAR(logs.timestamp, 'YYYY-MM-DD HH24:MI:SS') AS formatted_timestamp,
        logs.details
    FROM logs
    JOIN users ON logs.user_id = users.id
    ORDER BY logs.timestamp DESC
");
$stmt->execute();
$logs = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="admin-panel">
    <h2>System Logs</h2>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Action</th>
                <th>Timestamp</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['username']) ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['formatted_timestamp']) ?></td>
                        <td><?= htmlspecialchars($log['details']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No logs found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
