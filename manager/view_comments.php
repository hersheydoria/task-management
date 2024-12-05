<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Check if the user has 'manager' role
requireRole('manager');

// Fetch task ID from the query string
if (!isset($_GET['task_id']) || !is_numeric($_GET['task_id'])) {
    echo "Invalid task ID.";
    exit;
}

$task_id = (int)$_GET['task_id'];

// Fetch task details
$taskStmt = $pdo->prepare("SELECT title FROM tasks WHERE id = ?");
$taskStmt->execute([$task_id]);
$task = $taskStmt->fetch();

if (!$task) {
    echo "Task with ID $task_id not found."; // Detailed error message
    exit;
}

// Fetch comments associated with the task
$commentsStmt = $pdo->prepare("SELECT c.comment, c.created_at, u.username 
                               FROM comments c 
                               JOIN users u ON c.user_id = u.id 
                               WHERE c.task_id = ?
                               ORDER BY c.created_at DESC");
$commentsStmt->execute([$task_id]);
$comments = $commentsStmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="comments">
    <h2>Comments for Task: <?= htmlspecialchars($task['title']) ?></h2>
    
    <?php if (empty($comments)): ?>
        <p>No comments yet.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($comments as $comment): ?>
                <li>
                    <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong> <?= htmlspecialchars($comment['comment']) ?></p>
                    <p><em>Posted on <?= htmlspecialchars($comment['created_at']) ?></em></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <a href="view_tasks.php">Back to Tasks</a>
</div>

<?php include '../includes/footer.php'; ?>
