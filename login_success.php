<?php
/**
 * Lab 11: Successful Login Page
 * 
 * Returned when login name and password match database records.
 * Displays user profile, authorization details, and active session telemetry.
 */

require_once __DIR__ . '/config.php';

// Ensure user is authenticated
requireLogin();

$username   = $_SESSION['username'] ?? 'User';
$fullName   = $_SESSION['full_name'] ?? 'Authorized User';
$email      = $_SESSION['email'] ?? 'N/A';
$role       = $_SESSION['role'] ?? 'Student';
$userId     = $_SESSION['user_id'] ?? 0;
$loginTime  = isset($_SESSION['login_time']) ? date('d M Y, h:i:s A', $_SESSION['login_time']) : date('d M Y, h:i:s A');
$sessionId  = session_id();

// Role badge class
$roleClass = 'role-user';
$roleLower = strtolower($role);
if (strpos($roleLower, 'admin') !== false) {
    $roleClass = 'role-admin';
} elseif (strpos($roleLower, 'student') !== false) {
    $roleClass = 'role-student';
} elseif (strpos($roleLower, 'facult') !== false) {
    $roleClass = 'role-faculty';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Successful - Lab 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="app-header">
            <span class="badge badge-success">Status: Authenticated</span>
            <h1>Validation Successful</h1>
            <p class="subtitle">Credentials matched verified records in the database</p>
        </header>

        <main class="card">
            <!-- Celebratory Banner -->
            <div class="result-banner banner-success">
                <div class="result-icon icon-success">✓</div>
                <h2 class="result-title title-success">Welcome back, <?= htmlspecialchars($fullName) ?>!</h2>
                <p class="result-desc">Your login credentials were successfully validated against the database.</p>
            </div>

            <!-- Profile & Session Details Card -->
            <h3 style="font-size: 1.05rem; margin-bottom: 12px; color: #1f2937;">Authenticated Account Details</h3>
            <div class="details-grid">
                <div class="detail-row">
                    <span class="detail-label">Database User ID</span>
                    <span class="detail-value">#<?= htmlspecialchars($userId) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Login Name (Username)</span>
                    <span class="detail-value"><code><?= htmlspecialchars($username) ?></code></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Full Name</span>
                    <span class="detail-value"><?= htmlspecialchars($fullName) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email Address</span>
                    <span class="detail-value"><?= htmlspecialchars($email) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Assigned Role</span>
                    <span class="detail-value"><span class="role-badge <?= $roleClass ?>"><?= htmlspecialchars($role) ?></span></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Login Timestamp</span>
                    <span class="detail-value"><?= htmlspecialchars($loginTime) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Active Session ID</span>
                    <span class="detail-value" style="font-family: monospace; font-size: 0.8rem;"><?= htmlspecialchars(substr($sessionId, 0, 16)) ?>...</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Validation Mechanism</span>
                    <span class="detail-value" style="color: #059669;">PDO Prepared Statement + Bcrypt Hash</span>
                </div>
            </div>

            <!-- Action Controls -->
            <div class="btn-group">
                <a href="logout.php" class="btn btn-danger" style="flex: 1;">
                    Logout of Session &rarr;
                </a>
                <a href="view_users.php" class="btn btn-outline" style="flex: 1;">
                    View Database Records
                </a>
            </div>

            <div class="card-links">
                Need another account? <a href="register.php">Register New User</a>
            </div>
        </main>

        <footer class="app-footer">
            <p>Web Programming Laboratory &bull; Lab 11 User Validation Application</p>
        </footer>
    </div>
</body>
</html>
