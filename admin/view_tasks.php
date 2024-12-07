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
    // Filter by assigned_to (user ID)
    $whereClauses[] = "t.assigned_to = ?";  // Use assigned_to for filtering by user ID
    $params[] = $_POST['assigned_to'];  // Pass the user ID selected from the form
}

if (isset($_POST['deadline']) && $_POST['deadline'] !== '') {
    $whereClauses[] = "t.deadline = ?";
    $params[] = $_POST['deadline'];
}

// Fetch tasks with created_by_name
$query = "
    SELECT *
    FROM all_user_task_summary(NULL) t
";

if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll();

// Fetch users for the "assigned to" filter
$usersStmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'employee'");
$usersStmt->execute();
$users = $usersStmt->fetchAll();

// Fetch task status summary for all users
$taskStatusSummaryStmt = $pdo->query("SELECT * FROM task_status_summary");
$taskStatusSummaries = $taskStatusSummaryStmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<div class="container">
<h2 style="color: white; font-family: 'Roboto Slab', serif;"> <i class="fas fa-tasks"></i> View All Tasks</h2>
<div class="admin-panel" style="margin-top: 10px;">
    <!-- Filter Form -->
    <form method="POST">
        <div class="form-group">
            <label for="priority">Priority</label>
            <select name="priority" id="priority">
                <option value="" >All</option>
                <option value="Low" <?= isset($_POST['priority']) && $_POST['priority'] === 'Low' ? 'selected' : '' ?> >Low</option>
                <option value="Medium" <?= isset($_POST['priority']) && $_POST['priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="High" <?= isset($_POST['priority']) && $_POST['priority'] === 'High' ? 'selected' : '' ?>>High</option>
            </select>
        </div>

        <div class="form-group">
            <label for="assigned_to">Assigned to</label>
            <select name="assigned_to" id="assigned_to">
            <option value="">All</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>" <?= isset($_POST['assigned_to']) && $_POST['assigned_to'] == $user['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user['username']) ?>
                </option>
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
    <div style="overflow-x: auto; margin: 20px 0;"> <!-- Added container for better responsiveness -->
    <table style="width: 60%; margin: auto; border-collapse: collapse; text-align: center;"> <!-- Adjusted width and centered table -->
        <thead>
            <tr>
                <th>Title</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Deadline</th>
                <th>Assigned To</th>
                <th>Created By</th> <!-- Added "Created By" column -->
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
                    <td><?= htmlspecialchars($task['created_by_name']) ?></td> <!-- Display creator's name -->
                    <td>
                        <a href="edit_task.php?id=<?= $task['task_id'] ?>">Edit</a> | 
                        <a href="delete_task.php?id=<?= $task['task_id'] ?>" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                        <a href="view_comments.php?task_id=<?= htmlspecialchars($task['task_id'] ?? '') ?>">View Comments</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
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
</div>
<?php include '../includes/footer.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
