<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Check if the user has 'manager' role
requireRole('manager');

// Get the current user's ID from the session
$currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

// If no user ID is found in the session, redirect or show an error
if (!$currentUserId) {
    die('Error: User is not logged in.');
}

// Fetch tasks created by the current manager and assigned to employees
$stmt = $pdo->prepare("
    SELECT 
        td.*, 
        is_task_overdue(td.deadline) AS overdue, 
        u.username AS created_by_name,
        assigned_user.username AS assigned_to_name
    FROM 
        task_details_view td
    JOIN 
        users u ON td.created_by_id = u.id  -- Use created_by_id for comparison
    LEFT JOIN 
        users assigned_user ON td.assigned_to = assigned_user.username  -- Keep comparing assigned_user.username
    WHERE 
        u.role = 'manager'
        AND td.created_by_id = :current_user_id  -- Ensure comparison is with the user ID
        AND assigned_user.role = 'employee';
");
$stmt->execute([':current_user_id' => $currentUserId]);
$tasks = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<div class="main-content">
    <div class="container">
        <h2 style="font-family: 'Roboto Slab', serif; color: white;"><i class="fas fa-tasks"></i>  My Tasks</h2>
        <div class="user-task-summary-admin-panel">
        <table class="user-task-summary-styled-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Assigned To</th>
                    <th>Created By</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Deadline</th>
                    <th>Overdue</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tasks)): ?>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['title'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($task['description'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($task['assigned_to_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($task['created_by_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($task['status'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($task['priority'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($task['deadline'] ?? 'N/A') ?></td>
                            <td><?= $task['overdue'] ? 'Yes' : 'No' ?></td>
                            <td>
                                <a href="edit_task.php?id=<?= htmlspecialchars($task['id'] ?? '') ?>" style="color:black">Edit</a> |
                                <a href="delete_task.php?id=<?= htmlspecialchars($task['id'] ?? '') ?>" onclick="return confirm('Are you sure you want to delete this task?');" style="color:black">Delete</a> |
                                <a href="view_comments.php?task_id=<?= htmlspecialchars($task['id'] ?? '') ?>" style="color:black">View Comments</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No tasks found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
