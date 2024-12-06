<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Check if the user has 'manager' role
requireRole('manager');

// Fetch tasks from the database
$stmt = $pdo->prepare("
    SELECT *, 
           is_task_overdue(deadline) AS overdue 
    FROM task_details_view
");
$stmt->execute();
$tasks = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<div class="main-content">
    <div class="container">
        <h2 style="font-family: 'Roboto Slab', serif; color: white;"><i class="fas fa-tasks"></i>  All Tasks</h2>
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
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?= htmlspecialchars($task['title'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($task['description'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($task['assigned_to'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($task['created_by'] ?? 'N/A') ?></td>
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
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
