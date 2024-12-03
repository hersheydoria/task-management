<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Ensure the user has admin role
requireRole('admin');

// Fetch task status summary from the `task_status_summary` view
$statusSummaryStmt = $pdo->prepare("SELECT assigned_to, total_tasks, not_started, in_progress, completed FROM task_status_summary");
$statusSummaryStmt->execute();
$statusSummary = $statusSummaryStmt->fetchAll();

// Fetch tasks with applied filters
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
        // Format the deadline date properly
        if (empty($task['deadline'])) {
            $deadline = 'No Deadline';  // Placeholder for empty deadlines
        } else {
            $deadline = date("Y-m-d", strtotime($task['deadline']));  // Format to Y-m-d (YYYY-MM-DD)
        }
        
        // Write row to CSV
        fputcsv($output, [
            $task['title'],
            $task['description'],
            $task['assigned_to_name'],
            $task['priority'],
            $deadline
        ]);
    }

    // Write status summary to CSV
    fputcsv($output, []); // Add a blank row for separation
    fputcsv($output, ['Task Status Summary']); // Add a summary header row
    fputcsv($output, ['Assigned To', 'Total Tasks', 'Not Started', 'In Progress', 'Completed']); // Add summary column headers
    foreach ($statusSummary as $summary) {
        fputcsv($output, [
            $summary['assigned_to'],
            $summary['total_tasks'],
            $summary['not_started'],
            $summary['in_progress'],
            $summary['completed']
        ]);
    }

    fclose($output);
    exit; // Ensure no further output after CSV is generated
} else {
    echo "No tasks found for the selected filters.";
    exit;
}
?>
