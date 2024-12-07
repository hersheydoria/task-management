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
        logs.user_id, -- Include user_id
        users.username, 
        logs.action, 
        TO_CHAR(logs.timestamp, 'YYYY-MM-DD HH24:MI:SS') AS formatted_timestamp,
        logs.details, 
        logs.table_name, -- Include table_name
        logs.affected_columns -- Include affected_columns
    FROM logs
    JOIN users ON logs.user_id = users.id
    ORDER BY logs.timestamp DESC
");
$stmt->execute();
$logs = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<div class="container">
<h2 style="color: white; font-family: 'Roboto Slab', serif;"><i class="fas fa-book"></i> System Logs</h2>
<div class="view-logs-admin-panel">
    <!-- Scrollable Table Container with Specific Class for Styling -->
    <div class="view-logs-table-container">
        <table class="view-logs-styled-table">
            <thead>
                <tr>
                    <th>User ID</th> <!-- New header for user_id -->
                    <th>Username</th>
                    <th>Action</th>
                    <th>Timestamp</th>
                    <th>Details</th>
                    <th>Table Name</th> <!-- New header for table_name -->
                    <th>Affected Columns</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="user-id"><?= htmlspecialchars($log['user_id']) ?></td> <!-- Display user_id -->
                            <td class="username"><?= htmlspecialchars($log['username']) ?></td>
                            <td class="action"><?= htmlspecialchars($log['action']) ?></td>
                            <td class="timestamp"><?= htmlspecialchars($log['formatted_timestamp']) ?></td>
                            <td class="details"><?= htmlspecialchars($log['details']) ?></td>
                            <td class="table-name"><?= htmlspecialchars($log['table_name']) ?></td> <!-- Display table_name -->
                            <td class="affected-columns"><?= htmlspecialchars($log['affected_columns']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No logs found.</td> <!-- Update colspan to match the number of columns -->
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
<?php include '../includes/footer.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
