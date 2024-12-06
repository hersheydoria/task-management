<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Check if the user has the required role
requireRole('admin');

$user_id = $_SESSION['user_id']; // Get the logged-in user ID
$pdo->exec("SET myapp.current_user_id = '$user_id'");

// Fetch the task details for editing
if (isset($_GET['id'])) {
    $task_id = (int)$_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch();

    if (!$task) {
        die('Task not found.');
    }
}

// Update the task when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $assigned_to = (int)$_POST['assigned_to'];
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];

    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, assigned_to = ?, priority = ?, deadline = ? WHERE id = ?");
    $stmt->execute([$title, $description, $assigned_to, $priority, $deadline, $task_id]);

  
    // Redirect to the tasks list
    header('Location: view_tasks.php');
    exit;
}

// Fetch the list of users for the "Assigned To" dropdown
$usersStmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'employee'");
$usersStmt->execute();
$users = $usersStmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="edit-container">
<h2 style="color: white; font-family: 'Roboto Slab', serif;"><i class="fas fa-tasks"></i> Edit Tasks</h2>

    <form method="POST">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($task['title']) ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4" required><?= htmlspecialchars($task['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="assigned_to">Assigned To</label>
            <select name="assigned_to" id="assigned_to" required>
                <option value="">Select an employee</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= $task['assigned_to'] == $user['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="priority">Priority</label>
            <select name="priority" id="priority" required>
                <option value="Low" <?= $task['priority'] === 'Low' ? 'selected' : '' ?>>Low</option>
                <option value="Medium" <?= $task['priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="High" <?= $task['priority'] === 'High' ? 'selected' : '' ?>>High</option>
            </select>
        </div>

        <div class="form-group">
            <label for="deadline">Deadline</label>
            <input type="date" name="deadline" id="deadline" value="<?= $task['deadline'] ?>" required>
        </div>

        <button type="submit">Update Task</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
