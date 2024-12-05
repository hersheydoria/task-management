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

    <div class="view-logs-admin-panel">
        <!-- Fixed Header -->
        <div class="view-logs-admin-panel-header">
            <h2><i class="fas fa-book"></i> System Logs</h2>
        </div>

        <!-- Scrollable Table Container with Specific Class for Styling -->
        <div class="view-logs-table-container">
            <table class="view-logs-styled-table">
                <thead >
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
                                <td class="username"><?= htmlspecialchars($log['username']) ?></td>
                                <td class="action"><?= htmlspecialchars($log['action']) ?></td>
                                <td class="timestamp"><?= htmlspecialchars($log['formatted_timestamp']) ?></td>
                                <td class="details"><?= htmlspecialchars($log['details']) ?></td>
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
    </div>

    <?php include '../includes/footer.php'; ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
