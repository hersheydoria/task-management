<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Check if the user has 'admin' role 
requireRole('admin');

$user_id = $_SESSION['user_id'];
$pdo->exec("SET myapp.current_user_id = '$user_id'");

// Check if task_id is set and is a valid number
if (!isset($_GET['task_id']) || !is_numeric($_GET['task_id']) || empty($_GET['task_id'])) {
    echo "Invalid task ID.";
    exit;
}
$task_id = (int)$_GET['task_id'];
// Fetch task details from the database
$taskStmt = $pdo->prepare("SELECT title FROM tasks WHERE id = ?");
$taskStmt->execute([$task_id]);
$task = $taskStmt->fetch();

if (!$task) {
    echo "Task with ID $task_id not found."; // Detailed error message
    exit;
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the comment from the form
    $comment = htmlspecialchars(trim($_POST['comment']));

    // Get the current user's ID (admin)
    $user_id = $_SESSION['user_id'];

    // Insert the comment into the database
    $commentStmt = $pdo->prepare("INSERT INTO comments (task_id, user_id, comment) VALUES (?, ?, ?)");
    $commentStmt->execute([$task_id, $user_id, $comment]);

    // Redirect to the same page to show the new comment
    header("Location: view_comments.php?task_id=$task_id");
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
    <h2 style="font-family: 'Roboto Slab', serif;"> <i class="fas fa-comments"></i> COMMENTS FOR TASK: <?= htmlspecialchars($task['title']) ?></h2>

    <!-- Comment Form (for admins) -->
    <form method="POST">
        <textarea name="comment" rows="4" placeholder="Add your comment here..." required></textarea>
        <button type="submit">Add Comment</button>
    </form>

    <!-- Display Comments -->
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

    <a href="view_tasks.php" style="color: white;">Back to Tasks</a>
</div>

<?php include '../includes/footer.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
