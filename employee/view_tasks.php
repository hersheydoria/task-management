<?php
session_start();

include '../includes/db.php';
include '../includes/auth.php';

// Check if the user is logged in and has the role 'employee'
requireRole('employee');

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Refresh the high_priority_tasks materialized view to ensure up-to-date data
try {
    $pdo->exec("REFRESH MATERIALIZED VIEW high_priority_tasks");
} catch (PDOException $e) {
    die("Error refreshing high-priority tasks view: " . $e->getMessage());
}

// Fetch tasks for the logged-in employee using `user_task_summary` function
$tasksStmt = $pdo->prepare("
    SELECT task_id, title, priority, status, deadline
    FROM user_task_summary(?)
");
$tasksStmt->execute([$user_id]);
$tasks = $tasksStmt->fetchAll();

// Fetch high-priority tasks for the logged-in employee from the `high_priority_tasks` materialized view
$highPriorityStmt = $pdo->prepare("
    SELECT task_id, title, description, deadline
    FROM high_priority_tasks
    WHERE assigned_to = (SELECT username FROM users WHERE id = ?)
");
$highPriorityStmt->execute([$user_id]);
$highPriorityTasks = $highPriorityStmt->fetchAll();



// Check if any tasks were found
$noTasksMessage = empty($tasks) ? "No tasks found." : null;
$noHighPriorityMessage = empty($highPriorityTasks) ? "No high-priority tasks found." : null;
?>

<?php include '../includes/header.php'; ?>

<style>
    .tasks {
        text-align: center;
    }

    table {
        margin: 0 auto; /* Center the table */
        width: 80%; /* Reduce the table width */
        border-collapse: collapse;
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    th, td {
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f4f4f4;
    }

    .divider {
        width: 80%;
        margin: 20px auto; /* Center the divider */
        border-top: 2px solid #ccc;
    }

    h2 {
        margin-top: 30px;
    }
</style>

<div class="tasks">
    <h2 style="font-family: 'Roboto Slab', serif; color: white"> <i class="fas fa-user-check"></i> Assigned Tasks</h2>

    <?php if ($noTasksMessage): ?>
        <p><?= htmlspecialchars($noTasksMessage) ?></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Deadline</th>
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
                        <td>
                            <a href="update_task.php?id=<?= $task['task_id'] ?>">Update Status</a> | 
                            <a href="comments.php?task_id=<?= $task['task_id'] ?>">Add/View Comments</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="divider"></div>

    <h2 style="font-family: 'Roboto Slab', serif; color: white"> <i class="fas fa-exclamation-circle"></i> High Priority Tasks</h2>

    <?php if ($noHighPriorityMessage): ?>
        <p><?= htmlspecialchars($noHighPriorityMessage) ?></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Deadline</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($highPriorityTasks as $task): ?>
                    <tr>
                        <td><?= htmlspecialchars($task['title']) ?></td>
                        <td><?= htmlspecialchars($task['description']) ?></td>
                        <td><?= htmlspecialchars($task['deadline']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
 
<?php include '../includes/footer.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
