<?php
/**
 * Lab 11: User Registration
 * 
 * Allows adding new authorized user credentials to the database
 * to demonstrate insertion, duplicate handling, and bcrypt hashing.
 */

require_once __DIR__ . '/config.php';

$error = '';
$username = '';
$fullName = '';
$email = '';
$role = 'Student';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username'] ?? '');
    $fullName   = trim($_POST['full_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';
    $role       = trim($_POST['role'] ?? 'Student');

    // Validation
    if ($username === '' || $fullName === '' || $email === '' || $password === '') {
        $error = "All fields are required.";
    } elseif (strlen($username) < 3) {
        $error = "Login name must be at least 3 characters long.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirmPass) {
        $error = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid email address.";
    } else {
        $pdo = getDBConnection();

        // Check if username already exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE LOWER(username) = LOWER(:username)");
        $checkStmt->execute([':username' => $username]);

        if ($checkStmt->fetch()) {
            $error = "The login name '" . htmlspecialchars($username) . "' is already taken. Please choose another.";
        } else {
            // Hash password with bcrypt
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // Insert into database
            $insertStmt = $pdo->prepare("INSERT INTO users (username, full_name, email, password_hash, role) 
                                         VALUES (:username, :full_name, :email, :password_hash, :role)");
            $insertStmt->execute([
                ':username'      => $username,
                ':full_name'     => $fullName,
                ':email'         => $email,
                ':password_hash' => $passwordHash,
                ':role'          => $role
            ]);

            // Redirect to login with success message
            header('Location: login.php?msg=registered&user=' . urlencode($username));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User - Lab 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="app-header">
            <span class="badge badge-info">Lab 11: Account Creation</span>
            <h1>Register New User</h1>
            <p class="subtitle">Add a new verified credential record to the database</p>
        </header>

        <main class="card">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <span class="alert-icon">✕</span>
                    <div><strong>Registration Error:</strong> <?= $error ?></div>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="username">Login Name / Desired Username *</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-control" 
                        placeholder="e.g., alex_chen" 
                        value="<?= htmlspecialchars($username) ?>" 
                        required
                    >
                    <div class="form-hint">Unique identifier used for server validation.</div>
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input 
                        type="text" 
                        id="full_name" 
                        name="full_name" 
                        class="form-control" 
                        placeholder="e.g., Alex Chen" 
                        value="<?= htmlspecialchars($fullName) ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="e.g., alex@lab11.local" 
                        value="<?= htmlspecialchars($email) ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="role">User Role</label>
                    <select id="role" name="role" class="form-control">
                        <option value="Student" <?= $role === 'Student' ? 'selected' : '' ?>>Student</option>
                        <option value="Faculty" <?= $role === 'Faculty' ? 'selected' : '' ?>>Faculty</option>
                        <option value="Developer" <?= $role === 'Developer' ? 'selected' : '' ?>>Developer</option>
                        <option value="Administrator" <?= $role === 'Administrator' ? 'selected' : '' ?>>Administrator</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Minimum 6 characters" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-control" 
                        placeholder="Re-enter your password" 
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Register &amp; Save to Database &rarr;
                </button>
            </form>

            <div class="card-links">
                Already registered? <a href="login.php">Back to Login</a> &bull; 
                <a href="view_users.php">View Database</a>
            </div>
        </main>

        <footer class="app-footer">
            <p>Web Programming Laboratory &bull; Lab 11 User Validation Application</p>
        </footer>
    </div>
</body>
</html>
