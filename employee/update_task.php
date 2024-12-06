<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Check if the user is logged in and has the role 'employee'
requireRole('employee');

$user_id = $_SESSION['user_id'];
$pdo->exec("SET myapp.current_user_id = '$user_id'");

// Check if task ID is provided
if (!isset($_GET['id'])) {
    die('Task ID is required.');
}

$task_id = (int)$_GET['id'];

// Fetch task details
$stmt = $pdo->prepare("SELECT id, title, status FROM tasks WHERE id = ? AND assigned_to = ?");
$stmt->execute([$task_id, $user_id]);
$task = $stmt->fetch();

if (!$task) {
    die('Task not found or not assigned to you.');
}

// Update the task status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->execute([$status, $task_id]);

  
    // Redirect to the task list
    header('Location: view_tasks.php');
    exit;
}
?>

<?php include '../includes/header.php'; ?>

<div>
<h2 style="color: white; text-align: center; font-family: 'Roboto Slab', serif;"> <i class="fas fa-pencil-alt"></i> Update Task Status</h2>
    <form method="POST">
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="Not Started" <?= $task['status'] === 'Not Started' ? 'selected' : '' ?>>Not Started</option>
                <option value="In Progress" <?= $task['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="Completed" <?= $task['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
            </select>
        </div>
        <button type="submit">Update</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
