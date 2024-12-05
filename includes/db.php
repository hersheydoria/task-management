<?php
$host = 'localhost';
$dbname = 'task_management';
$username = 'postgres';
$password = 'hershey';

try {
    // Use the PostgreSQL DSN instead of MySQL
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
