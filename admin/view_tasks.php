<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Check if the user has 'admin' role
requireRole('admin'); // Only admins can access this page

// Initialize filter parameters for task list
$whereClauses = [];
$params = [];

// Apply filters if provided
if (isset($_POST['priority']) && $_POST['priority'] !== '') {
    $whereClauses[] = "t.priority = ?";
    $params[] = $_POST['priority'];
}

if (isset($_POST['assigned_to']) && $_POST['assigned_to'] !== '') {
    $whereClauses[] = "t.assigned_to = ?";
    $params[] = $_POST['assigned_to'];
}

if (isset($_POST['deadline']) && $_POST['deadline'] !== '') {
    $whereClauses[] = "t.deadline = ?";
    $params[] = $_POST['deadline'];
}

// Fetch tasks
$query = "
    SELECT *
    FROM all_user_task_summary(NULL);
";

if (!empty($whereClauses)) {
    $query .= " AND " . implode(" AND ", $whereClauses);
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll();




// Fetch users for the "assigned to" filter
$usersStmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'employee'");
$usersStmt->execute();
$users = $usersStmt->fetchAll();

// Fetch task status summary for all users
$taskStatusSummaryStmt = $pdo->query("
    SELECT *
    FROM task_status_summary 
");
$taskStatusSummaries = $taskStatusSummaryStmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="admin-panel">
    <h2>View All Tasks</h2>
    
    <!-- Filter Form -->
    <form method="POST">
        <div class="form-group">
            <label for="priority">Priority</label>
            <select name="priority" id="priority">
                <option value="">All</option>
                <option value="Low" <?= isset($_POST['priority']) && $_POST['priority'] === 'Low' ? 'selected' : '' ?>>Low</option>
                <option value="Medium" <?= isset($_POST['priority']) && $_POST['priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="High" <?= isset($_POST['priority']) && $_POST['priority'] === 'Hgh' ? 'selected' : '' ?>>High</option>
            </select>
        </div>

        <div class="form-group">
            <label for="assigned_to">Assigned to</label>
            <select name="assigned_to" id="assigned_to">
                <option value="">All</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= isset($_POST['assigned_to']) && $_POST['assigned_to'] == $user['id'] ? 'selected' : '' ?>><?= htmlspecialchars($user['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="deadline">Deadline</label>
            <input type="date" name="deadline" id="deadline" value="<?= isset($_POST['deadline']) ? $_POST['deadline'] : '' ?>">
        </div>

        <button type="submit">Filter</button>
    </form>

    <!-- Task List -->
    <h3>All Tasks</h3>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Deadline</th>
                <th>Assigned To</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td><?= htmlspecialchars($task['priority']) ?></td>
                    <td><?= htmlspecialchars($task['status']) ?></td>
                    <td><?= htmlspecialchars($task['deadline']) ?></td>
                    <td><?= htmlspecialchars($task['assigned_to_name']) ?></td>
                    <td>
                        <a href="edit_task.php?id=<?= $task['task_id'] ?>">Edit</a> | 
                        <a href="delete_task.php?id=<?= $task['task_id'] ?>" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- Generate Report Button -->
    <form method="POST" action="reports.php">
                <button type="submit" class="report-button">Generate Report</button>
            </form>

    <!-- Task Status Summary -->
    <h3>Task Status Summary</h3>
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Total Tasks</th>
                <th>Not Started</th>
                <th>In Progress</th>
                <th>Completed</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($taskStatusSummaries as $summary): ?>
                <tr>
                    <td><?= htmlspecialchars($summary['username']) ?></td>
                    <td><?= htmlspecialchars($summary['total_tasks']) ?></td>
                    <td><?= htmlspecialchars($summary['not_started']) ?></td>
                    <td><?= htmlspecialchars($summary['in_progress']) ?></td>
                    <td><?= htmlspecialchars($summary['completed']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
