<?php
/**
 * Lab 11: Database Configuration & Connection Utility
 * 
 * Supports both SQLite (zero-config, works immediately with no setup)
 * and MySQL (for XAMPP/WAMP/MySQL 8.0 server).
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuration options
// Change DB_TYPE to 'mysql' if you wish to use a local MySQL server
define('DB_TYPE', 'sqlite'); // Options: 'sqlite' or 'mysql'

// SQLite Configuration
define('SQLITE_FILE', __DIR__ . DIRECTORY_SEPARATOR . 'users.db');

// MySQL Configuration (Used when DB_TYPE is 'mysql')
define('MYSQL_HOST', 'localhost');
define('MYSQL_PORT', '3306');
define('MYSQL_DB',   'lab11_auth');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '');

/**
 * Returns a PDO database connection instance.
 * Automatically initializes database and default users if not already present.
 * 
 * @return PDO
 */
function getDBConnection(): PDO {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    try {
        if (DB_TYPE === 'sqlite') {
            $isNewDatabase = !file_exists(SQLITE_FILE);
            $pdo = new PDO('sqlite:' . SQLITE_FILE);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // If newly created or table missing, auto-initialize
            initDatabase($pdo, 'sqlite');
        } else {
            // MySQL Connection
            $dsn = "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DB . ";charset=utf8mb4";
            $pdo = new PDO($dsn, MYSQL_USER, MYSQL_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
    } catch (PDOException $e) {
        // Friendly error message for students/evaluators
        die('<div style="font-family:sans-serif;max-width:600px;margin:50px auto;padding:25px;border-left:5px solid #ef4444;background:#fee2e2;border-radius:8px;">' .
            '<h2 style="color:#b91c1c;margin-top:0;">Database Connection Error</h2>' .
            '<p style="color:#7f1d1d;"><strong>Details:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>' .
            '<p style="color:#991b1b;font-size:0.9rem;">Tip: If using MySQL, make sure MySQL Server is running and the database <code>' . MYSQL_DB . '</code> exists. Alternatively, set <code>DB_TYPE = \'sqlite\'</code> in <code>config.php</code> for zero-setup execution.</p>' .
            '</div>');
    }

    return $pdo;
}

/**
 * Creates the users table and seeds sample accounts if they do not exist.
 * 
 * @param PDO $pdo
 * @param string $driver
 */
function initDatabase(PDO $pdo, string $driver = 'sqlite'): void {
    // Check if table exists
    if ($driver === 'sqlite') {
        $tableQuery = "SELECT name FROM sqlite_master WHERE type='table' AND name='users'";
    } else {
        $tableQuery = "SHOW TABLES LIKE 'users'";
    }

    $stmt = $pdo->query($tableQuery);
    $tableExists = $stmt->fetchColumn() !== false;

    if (!$tableExists) {
        // Create table
        if ($driver === 'sqlite') {
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                full_name TEXT NOT NULL,
                email TEXT NOT NULL,
                password_hash TEXT NOT NULL,
                role TEXT DEFAULT 'User',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");
        } else {
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                full_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                role VARCHAR(20) DEFAULT 'User',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        // Seed initial default accounts
        seedDefaultUsers($pdo);
    }
}

/**
 * Seed initial sample users for quick laboratory demonstrations.
 * 
 * @param PDO $pdo
 */
function seedDefaultUsers(PDO $pdo): void {
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

    $insertStmt = $pdo->prepare("INSERT OR IGNORE INTO users (username, full_name, email, password_hash, role) 
                                 VALUES (:username, :full_name, :email, :password_hash, :role)");

    // For MySQL compatibility if OR IGNORE is not supported directly in older versions:
    if (DB_TYPE === 'mysql') {
        $insertStmt = $pdo->prepare("INSERT IGNORE INTO users (username, full_name, email, password_hash, role) 
                                     VALUES (:username, :full_name, :email, :password_hash, :role)");
    }

    foreach ($defaultUsers as $user) {
        $insertStmt->execute([
            ':username' => $user['username'],
            ':full_name' => $user['full_name'],
            ':email' => $user['email'],
            ':password_hash' => password_hash($user['password'], PASSWORD_BCRYPT),
            ':role' => $user['role']
        ]);
    }
}

/**
 * Helper to check if a user is currently logged in.
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['username']);
}

/**
 * Helper to require authentication, redirecting to login if unauthenticated.
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: login.php?error=unauthorized');
        exit;
    }
}
