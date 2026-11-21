<?php
/**
 * Lab 11: Database Setup and Reset Utility
 * Can be run from terminal via: php setup_database.php
 * Or accessed via browser: http://localhost:8000/setup_database.php
 */

require_once __DIR__ . '/config.php';

$pdo = getDBConnection();
$message = '';
$error = '';

try {
    if (DB_TYPE === 'sqlite') {
        $pdo->exec("DROP TABLE IF EXISTS users");
        $pdo->exec("CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            full_name TEXT NOT NULL,
            email TEXT NOT NULL,
            password_hash TEXT NOT NULL,
            role TEXT DEFAULT 'Student',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    } else {
        $pdo->exec("DROP TABLE IF EXISTS users");
        $pdo->exec("CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role VARCHAR(30) DEFAULT 'Student',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    // Default users with verifiable bcrypt hashes
    $defaultUsers = [
        [
            'username' => 'admin',
            'full_name' => 'System Administrator',
            'email' => 'admin@lab11.local',
            'password' => 'Admin@123',
            'role' => 'Administrator'
        ],
        [
            'username' => 'student',
            'full_name' => 'John Doe',
            'email' => 'student@lab11.local',
            'password' => 'Lab11@Pass',
            'role' => 'Student'
        ],
        [
            'username' => 'alice',
            'full_name' => 'Alice Johnson',
            'email' => 'alice@lab11.local',
            'password' => 'Alice@2026',
            'role' => 'Faculty'
        ]
    ];

    $insertStmt = $pdo->prepare("INSERT INTO users (username, full_name, email, password_hash, role) 
                                 VALUES (:username, :full_name, :email, :password_hash, :role)");

    foreach ($defaultUsers as $u) {
        $insertStmt->execute([
            ':username' => $u['username'],
            ':full_name' => $u['full_name'],
            ':email' => $u['email'],
            ':password_hash' => password_hash($u['password'], PASSWORD_BCRYPT),
            ':role' => $u['role']
        ]);
    }

    $message = "Database successfully initialized and seeded with 3 sample users!";
} catch (Exception $e) {
    $error = "Database initialization failed: " . $e->getMessage();
}

// If invoked via CLI
if (php_sapi_name() === 'cli') {
    if ($error) {
        echo "[ERROR] $error\n";
        exit(1);
    }
    echo "[SUCCESS] $message\n";
    echo "Default Credentials:\n";
    echo "  1. Username: admin    | Password: Admin@123\n";
    echo "  2. Username: student  | Password: Lab11@Pass\n";
    echo "  3. Username: alice    | Password: Alice@2026\n";
    exit(0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup & Reset - Lab 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="app-header">
            <span class="badge badge-info">Lab 11: Database Management</span>
            <h1>Database Initialization</h1>
            <p class="subtitle">Reset and pre-seed user authentication table</p>
        </header>

        <main class="card">
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <span class="alert-icon">✓</span>
                    <div>
                        <strong>Success:</strong> <?= htmlspecialchars($message) ?>
                    </div>
                </div>

                <div class="info-box">
                    <h3>Pre-seeded Accounts for Demonstration:</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Login Name (Username)</th>
                                <th>Password</th>
                                <th>Full Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="role-badge role-admin">Administrator</span></td>
                                <td><code>admin</code></td>
                                <td><code>Admin@123</code></td>
                                <td>System Administrator</td>
                            </tr>
                            <tr>
                                <td><span class="role-badge role-student">Student</span></td>
                                <td><code>student</code></td>
                                <td><code>Lab11@Pass</code></td>
                                <td>John Doe</td>
                            </tr>
                            <tr>
                                <td><span class="role-badge role-faculty">Faculty</span></td>
                                <td><code>alice</code></td>
                                <td><code>Alice@2026</code></td>
                                <td>Alice Johnson</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="btn-group" style="margin-top: 25px;">
                    <a href="login.php" class="btn btn-primary">Go to Login Page &rarr;</a>
                    <a href="view_users.php" class="btn btn-outline">View Database Records</a>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <span class="alert-icon">✕</span>
                    <div>
                        <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                    </div>
                </div>
                <div class="btn-group" style="margin-top: 20px;">
                    <a href="login.php" class="btn btn-secondary">&larr; Return to Login</a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
