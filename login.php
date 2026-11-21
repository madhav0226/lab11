<?php
/**
 * Lab 11: User Login Page
 * 
 * Interface for the user to submit login name and password to the server.
 */

require_once __DIR__ . '/config.php';

// If user is already logged in, redirect to success page
if (isLoggedIn()) {
    header('Location: login_success.php');
    exit;
}

$error = $_GET['error'] ?? '';
$msg = $_GET['msg'] ?? '';
$prefillUser = htmlspecialchars($_GET['user'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login - Lab 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="app-header">
            <span class="badge badge-info">Lab 11: Web Authentication</span>
            <h1>User Validation System</h1>
            <p class="subtitle">Secure database credential verification using PHP &amp; PDO</p>
        </header>

        <main class="card">
            <!-- Informational & Status Alerts -->
            <?php if ($msg === 'logged_out'): ?>
                <div class="alert alert-info">
                    <span class="alert-icon">ℹ</span>
                    <div><strong>Session Ended:</strong> You have been logged out successfully.</div>
                </div>
            <?php elseif ($msg === 'registered'): ?>
                <div class="alert alert-success">
                    <span class="alert-icon">✓</span>
                    <div><strong>Registration Successful!</strong> Your account was saved to the database. You can now log in.</div>
                </div>
            <?php endif; ?>

            <?php if ($error === 'invalid_credentials'): ?>
                <div class="alert alert-danger">
                    <span class="alert-icon">✕</span>
                    <div><strong>Login Failed:</strong> Invalid login name or password. Please verify your credentials and try again.</div>
                </div>
            <?php elseif ($error === 'empty_fields'): ?>
                <div class="alert alert-warning">
                    <span class="alert-icon">⚠</span>
                    <div><strong>Missing Fields:</strong> Please enter both your login name and password.</div>
                </div>
            <?php elseif ($error === 'unauthorized'): ?>
                <div class="alert alert-warning">
                    <span class="alert-icon">⚠</span>
                    <div><strong>Access Restricted:</strong> Please log in to view the requested page.</div>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="validate.php" method="POST" autocomplete="off" id="loginForm">
                <div class="form-group">
                    <label for="username">Login Name / Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-control" 
                        placeholder="Enter your login name (e.g., admin)" 
                        value="<?= $prefillUser ?>"
                        required 
                        autofocus
                    >
                    <div class="form-hint">Case-insensitive database match.</div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Enter your account password" 
                            required
                        >
                        <button type="button" class="toggle-password-btn" id="togglePasswordBtn" title="Toggle visibility">👁 Show</button>
                    </div>
                    <div class="form-hint">Stored securely in database using Bcrypt cryptographic hash.</div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                    Submit &amp; Validate Credentials &rarr;
                </button>
            </form>

            <!-- Quick-Fill Demo Accounts -->
            <div class="demo-accounts">
                <div class="demo-title">⚡ Quick-Fill Demo Database Accounts (Click to test):</div>
                <div class="demo-chips">
                    <button type="button" class="demo-chip" onclick="fillCredentials('admin', 'Admin@123')">
                        <strong>admin</strong> (Admin@123)
                    </button>
                    <button type="button" class="demo-chip" onclick="fillCredentials('student', 'Lab11@Pass')">
                        <strong>student</strong> (Lab11@Pass)
                    </button>
                    <button type="button" class="demo-chip" onclick="fillCredentials('alice', 'Alice@2026')">
                        <strong>alice</strong> (Alice@2026)
                    </button>
                    <button type="button" class="demo-chip" style="color:#b91c1c; border-color:#fca5a5;" onclick="fillCredentials('wrong_user', 'BadPass!99')">
                        <strong>invalid</strong> (Test Failure)
                    </button>
                </div>
            </div>

            <div class="card-links">
                Don't have an account? <a href="register.php">Register New User</a> &bull; 
                <a href="view_users.php">View Database</a>
            </div>
        </main>

        <footer class="app-footer">
            <p>Web Programming Laboratory &bull; Lab 11 User Validation Application</p>
        </footer>
    </div>

    <script>
        // Toggle password visibility
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');

        toggleBtn.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.textContent = '🔒 Hide';
            } else {
                passwordInput.type = 'password';
                toggleBtn.textContent = '👁 Show';
            }
        });

        // Quick fill credentials helper
        function fillCredentials(user, pass) {
            document.getElementById('username').value = user;
            document.getElementById('password').value = pass;
            document.getElementById('password').focus();
        }
    </script>
</body>
</html>
