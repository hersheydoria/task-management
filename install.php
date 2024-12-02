<?php
// Database connection
$host = 'localhost';
$db = 'task_management';
$user = 'postgres';
$pass = 'hershey';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Users Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(20) NOT NULL CHECK (role IN ('admin', 'manager', 'employee')),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Create Tasks Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tasks (
            id SERIAL PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            description TEXT,
            assigned_to INT REFERENCES users(id) ON DELETE SET NULL,
            created_by INT REFERENCES users(id),
            status VARCHAR(20) DEFAULT 'Not Started' CHECK (status IN ('Not Started', 'In Progress', 'Completed')),
            priority VARCHAR(20) DEFAULT 'Medium' CHECK (priority IN ('Low', 'Medium', 'High')),
            deadline DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Create Comments Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS comments (
            id SERIAL PRIMARY KEY,
            task_id INT REFERENCES tasks(id) ON DELETE CASCADE,
            user_id INT REFERENCES users(id),
            comment TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Insert Admin User
    $password = password_hash("admin123", PASSWORD_DEFAULT);
    $pdo->exec("
        INSERT INTO users (username, password, role)
        VALUES ('admin', '$password', 'admin')
        ON CONFLICT DO NOTHING;
    ");

    echo "Database setup completed! Admin user created (Username: admin, Password: admin123).";
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>
