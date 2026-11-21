<?php
/**
 * Lab 11: Login Failure Page
 * 
 * Displayed when user credential validation fails against the database.
 * Shows a clear failure message, diagnostics, and retry options.
 */

require_once __DIR__ . '/config.php';

$reason = $_GET['reason'] ?? 'invalid_credentials';
$attemptedUser = $_GET['username'] ?? '';
$customError = $_GET['error'] ?? '';

// Format failure explanation
$failureTitle = "Authentication Failed";
$failureMessage = "The login name or password you entered does not match any record in our database.";

if ($reason === 'empty_fields') {
    $failureTitle = "Incomplete Submission";
    $failureMessage = "Both Login Name and Password fields are required to authenticate.";
} elseif ($reason === 'db_error') {
    $failureTitle = "Database Processing Error";
    $failureMessage = "A database error occurred during verification: " . htmlspecialchars($customError);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Failed - Lab 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="app-header">
            <span class="badge badge-danger">Validation Status: Failed</span>
            <h1>Access Denied</h1>
            <p class="subtitle">Database credential verification returned no match</p>
        </header>

        <main class="card">
            <!-- Prominent Failure Banner -->
            <div class="result-banner banner-failure">
                <div class="result-icon icon-failure">✕</div>
                <h2 class="result-title title-failure"><?= htmlspecialchars($failureTitle) ?></h2>
                <p class="result-desc"><?= htmlspecialchars($failureMessage) ?></p>
            </div>

            <!-- Attempt Telemetry -->
            <h3 style="font-size: 1.05rem; margin-bottom: 12px; color: #1f2937;">Validation Audit Summary</h3>
            <div class="details-grid">
                <div class="detail-row">
                    <span class="detail-label">Attempted Login Name</span>
                    <span class="detail-value">
                        <?php if ($attemptedUser !== ''): ?>
                            <code><?= htmlspecialchars($attemptedUser) ?></code>
                        <?php else: ?>
                            <em>(Empty / Not Provided)</em>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Attempt Timestamp</span>
                    <span class="detail-value"><?= date('d M Y, h:i:s A') ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Database Match Result</span>
                    <span class="detail-value" style="color: #dc2626;">0 Matching Records</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Client IP Address</span>
                    <span class="detail-value"><?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1') ?></span>
                </div>
            </div>

            <!-- Security Diagnostic Tip -->
            <div class="diagnostic-box">
                <div class="diagnostic-title">💡 Security Note (OWASP Best Practice):</div>
                <ul>
                    <li>The system displays a generic <strong>"Invalid Login Name or Password"</strong> message instead of saying whether the user exists. This prevents <em>User Enumeration attacks</em>.</li>
                    <li>Passwords are compared using cryptographic hashes and prepared statements, ensuring safety against <em>SQL Injection</em>.</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="btn-group">
                <a href="login.php<?= $attemptedUser ? '?user=' . urlencode($attemptedUser) : '' ?>" class="btn btn-primary" style="flex: 1;">
                    &larr; Try Again
                </a>
                <a href="register.php" class="btn btn-outline" style="flex: 1;">
                    Register New Account &rarr;
                </a>
            </div>

            <div class="card-links">
                Forgot valid credentials? <a href="view_users.php">Check Available Database Users</a>
            </div>
        </main>

        <footer class="app-footer">
            <p>Web Programming Laboratory &bull; Lab 11 User Validation Application</p>
        </footer>
    </div>
</body>
</html>
