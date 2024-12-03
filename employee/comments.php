<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Check if the user is logged in and has the role 'employee'
requireRole('employee');

$user_id = $_SESSION['user_id'];
$pdo->exec("SET myapp.current_user_id = '$user_id'");

// Check if task ID is provided
if (!isset($_GET['task_id'])) {
    die('Task ID is required.');
}

$task_id = (int)$_GET['task_id'];

// Fetch task details
$stmt = $pdo->prepare("SELECT id, title FROM tasks WHERE id = ? AND assigned_to = ?");
$stmt->execute([$task_id, $user_id]);
$task = $stmt->fetch();

if (!$task) {
    die('Task not found or not assigned to you.');
}

// Add a new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = htmlspecialchars(trim($_POST['comment']));

    $stmt = $pdo->prepare("INSERT INTO comments (task_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$task_id, $user_id, $comment]);


    header("Location: comments.php?task_id=$task_id");
    exit;
}

// Fetch existing comments
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

    <!-- Comment Form -->
    <form method="POST">
        <textarea name="comment" rows="4" placeholder="Add your comment here..." required></textarea>
        <button type="submit">Add Comment</button>
    </form>

    <!-- Display Comments -->
    <h3>Existing Comments</h3>
    <ul>
        <?php foreach ($comments as $comment): ?>
            <li>
                <strong><?= htmlspecialchars($comment['username']) ?></strong> (<?= $comment['created_at'] ?>):
                <p><?= htmlspecialchars($comment['comment']) ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include '../includes/footer.php'; ?>
