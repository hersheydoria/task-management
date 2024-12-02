<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Check if the user has 'admin' role
requireRole('admin'); // Only admins can access this page

// Fetch tasks from the database (with optional filtering)
$whereClauses = [];
$params = [];

if (isset($_POST['priority']) && $_POST['priority'] !== '') {
    $whereClauses[] = "priority = ?";
    $params[] = $_POST['priority'];
}

if (isset($_POST['assigned_to']) && $_POST['assigned_to'] !== '') {
    $whereClauses[] = "assigned_to = ?";
    $params[] = $_POST['assigned_to'];
}

if (isset($_POST['deadline']) && $_POST['deadline'] !== '') {
    $whereClauses[] = "deadline = ?";
    $params[] = $_POST['deadline'];
}

// Build query with filters
$query = "SELECT t.id, t.title, t.description, t.assigned_to, t.created_by, t.deadline, t.priority, u.username AS assigned_to_name 
          FROM tasks t 
          LEFT JOIN users u ON t.assigned_to = u.id";

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
                <option value="low" <?= isset($_POST['priority']) && $_POST['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                <option value="medium" <?= isset($_POST['priority']) && $_POST['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                <option value="high" <?= isset($_POST['priority']) && $_POST['priority'] === 'high' ? 'selected' : '' ?>>High</option>
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
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Assigned To</th>
                <th>Priority</th>
                <th>Deadline</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td><?= htmlspecialchars($task['description']) ?></td>
                    <td><?= htmlspecialchars($task['assigned_to_name']) ?></td>
                    <td><?= htmlspecialchars($task['priority']) ?></td>
                    <td><?= htmlspecialchars($task['deadline']) ?></td>
                    <td>
                        <a href="edit_task.php?id=<?= $task['id'] ?>">Edit</a> | 
                        <a href="delete_task.php?id=<?= $task['id'] ?>" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Generate Report Button -->
    <form method="POST" action="reports.php">
        <button type="submit" class="report-button">Generate Report</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
