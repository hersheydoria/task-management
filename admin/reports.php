<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Ensure the user has admin role
requireRole('admin');

// Fetch tasks with the applied filters
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

$query = "SELECT t.title, t.description, t.deadline, t.priority, u.username AS assigned_to_name
          FROM tasks t
          LEFT JOIN users u ON t.assigned_to = u.id";

if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll();

// Generate CSV report if data is available
if (!empty($tasks)) {
    $filename = "tasks_report_" . date("Y-m-d") . ".csv";
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $output = fopen("php://output", "w");

    // Write CSV column headers
    fputcsv($output, ['Title', 'Description', 'Assigned To', 'Priority', 'Deadline']);

    // Write task data to CSV
    foreach ($tasks as $task) {
        fputcsv($output, [
            $task['title'],
            $task['description'],
            $task['assigned_to_name'],
            $task['priority'],
            $task['deadline']
        ]);
    }

    fclose($output);
    exit; // Ensure no further output after CSV is generated
} else {
    echo "No tasks found for the selected filters.";
    exit;
}
?>

<?php include '../includes/header.php'; ?>

<!-- Admin Panel - Report Generation Section -->
<div class="admin-panel">
    <h2>Generate Task Report</h2>
    <form method="POST">
        <label for="priority">Priority</label>
        <select name="priority" id="priority">
            <option value="">--Select Priority--</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
        </select>

        <label for="assigned_to">Assigned To</label>
        <select name="assigned_to" id="assigned_to">
            <option value="">--Select User--</option>
            <!-- Fetch and display users dynamically for selection -->
            <?php
            $userStmt = $pdo->prepare("SELECT id, username FROM users WHERE role != 'admin'");
            $userStmt->execute();
            $users = $userStmt->fetchAll();
            foreach ($users as $user) {
                echo "<option value='{$user['id']}'>{$user['username']}</option>";
            }
            ?>
        </select>

        <label for="deadline">Deadline</label>
        <input type="date" name="deadline" id="deadline">

        <button type="submit">Generate Report</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
