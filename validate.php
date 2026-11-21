<?php
/**
 * Lab 11: Server-Side Validation Controller
 * 
 * Receives the login name & password via HTTP POST, checks credentials
 * against the records available in the Database using PDO prepared statements.
 * Returns a successful login page if matched, or displays a failure message otherwise.
 */

require_once __DIR__ . '/config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Retrieve and sanitize inputs
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Check for empty inputs
if ($username === '' || $password === '') {
    // Redirect to failure page indicating missing credentials
    header('Location: login_failure.php?reason=empty_fields&username=' . urlencode($username));
    exit;
}

// Query database for the given username
$pdo = getDBConnection();

try {
    // Using PDO prepared statement with parameter binding to prevent SQL Injection
    $stmt = $pdo->prepare("SELECT id, username, full_name, email, password_hash, role, created_at 
                           FROM users 
                           WHERE LOWER(username) = LOWER(:username) 
                           LIMIT 1");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    $isAuthenticated = false;

    if ($user) {
        // Verify against cryptographic hash (bcrypt)
        if (password_verify($password, $user['password_hash'])) {
            $isAuthenticated = true;
        } 
        // Fallback for plain-text entries (e.g. manual lab testing in database)
        elseif ($password === $user['password_hash']) {
            $isAuthenticated = true;
        }
    }

    if ($isAuthenticated) {
        // Authentication succeeded!
        // Prevent session fixation attack by regenerating session ID
        session_regenerate_id(true);

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['username']   = $user['username'];
        $_SESSION['full_name']  = $user['full_name'];
        $_SESSION['email']      = $user['email'];
        $_SESSION['role']       = $user['role'];
        $_SESSION['login_time'] = time();

        // Redirect to successful login page
        header('Location: login_success.php');
        exit;
    } else {
        // Authentication failed: credentials do not match database record
        header('Location: login_failure.php?reason=invalid_credentials&username=' . urlencode($username));
        exit;
    }

} catch (PDOException $e) {
    // Database query error handling
    header('Location: login_failure.php?reason=db_error&error=' . urlencode($e->getMessage()));
    exit;
}
