<?php
include '../includes/db.php';
session_start();

$user_id = $_SESSION['user_id']; // Get the logged-in user ID
$pdo->exec("SET myapp.current_user_id = '$user_id'");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and fetch form data
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $assigned_to = (int) $_POST['assigned_to'];
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];
    $created_by = $_SESSION['user_id'];

    // Prepare the query to insert the task into the database
    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, assigned_to, created_by, deadline, priority) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $assigned_to, $created_by, $deadline, $priority]);

    // Redirect to the dashboard after creating the task
    header('Location: ../dashboard.php');
    exit; // Ensure no further code is executed after the redirect
}

// Fetch all users to assign tasks to
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'employee'");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="form-container">
    <h2>Create New Task</h2>

    <form method="POST" action="create_task.php">
        <div class="form-group">
            <label for="title">Task Title</label>
            <input type="text" name="title" id="title" placeholder="Task title" required>
        </div>

        <div class="form-group">
            <label for="description">Task Description</label>
            <textarea name="description" id="description" placeholder="Task description" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="assigned_to">Assign to</label>
            <select name="assigned_to" id="assigned_to" required>
                <option value="">Select an employee</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="deadline">Deadline</label>
            <input type="date" name="deadline" id="deadline" required>
        </div>

        <div class="form-group">
            <label for="priority">Priority</label>
            <select name="priority" id="priority" required>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>
        </div>

        <button type="submit">Create Task</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
