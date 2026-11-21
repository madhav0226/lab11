<?php
/**
 * Lab 11: Database Records Inspector
 * 
 * Allows students and evaluators to inspect the contents of the database
 * table to verify that users are genuinely stored and hashed on the server.
 */

require_once __DIR__ . '/config.php';

$pdo = getDBConnection();
$error = '';
$users = [];

try {
    $stmt = $pdo->query("SELECT id, username, full_name, email, password_hash, role, created_at FROM users ORDER BY id ASC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Users - Lab 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-wide">
        <header class="app-header">
            <span class="badge badge-info">Database Driver: <?= strtoupper(DB_TYPE) ?></span>
            <h1>Database Records Inspector</h1>
            <p class="subtitle">Live contents of the <code>users</code> authentication table</p>
        </header>

        <main class="card">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <span class="alert-icon">✕</span>
                    <div><strong>Database Query Error:</strong> <?= htmlspecialchars($error) ?></div>
                </div>
            <?php else: ?>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
                    <div>
                        <strong>Total Records Found:</strong> <?= count($users) ?> registered accounts
                    </div>
                    <div>
                        <a href="setup_database.php" class="btn btn-outline" style="padding:6px 12px; font-size:0.82rem;" onclick="return confirm('Reset database back to the 3 default sample users?');">
                            ↺ Reset / Re-seed Database
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Login Name</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Stored Password Hash (Bcrypt)</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <?php
                                $roleClass = 'role-user';
                                $roleLower = strtolower($u['role'] ?? '');
                                if (strpos($roleLower, 'admin') !== false) {
                                    $roleClass = 'role-admin';
                                } elseif (strpos($roleLower, 'student') !== false) {
                                    $roleClass = 'role-student';
                                } elseif (strpos($roleLower, 'facult') !== false) {
                                    $roleClass = 'role-faculty';
                                }
                                ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($u['id']) ?></strong></td>
                                    <td><code><?= htmlspecialchars($u['username']) ?></code></td>
                                    <td><?= htmlspecialchars($u['full_name']) ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><span class="role-badge <?= $roleClass ?>"><?= htmlspecialchars($u['role']) ?></span></td>
                                    <td title="<?= htmlspecialchars($u['password_hash']) ?>">
                                        <code style="font-size:0.75rem; color:#475569;"><?= htmlspecialchars(substr($u['password_hash'], 0, 18)) ?>...</code>
                                    </td>
                                    <td style="font-size:0.8rem; color:#6b7280;"><?= htmlspecialchars($u['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="info-box" style="margin-top:25px; background:#f8fafc; padding:15px; border-radius:8px; border:1px solid #e2e8f0;">
                <h4 style="margin-bottom:8px; font-size:0.9rem; color:#334155;">Default Credentials Cheatsheet:</h4>
                <p style="font-size:0.85rem; color:#64748b; margin-bottom:4px;">&bull; <code>admin</code> &rarr; <code>Admin@123</code> (Administrator)</p>
                <p style="font-size:0.85rem; color:#64748b; margin-bottom:4px;">&bull; <code>student</code> &rarr; <code>Lab11@Pass</code> (Student)</p>
                <p style="font-size:0.85rem; color:#64748b;">&bull; <code>alice</code> &rarr; <code>Alice@2026</code> (Faculty)</p>
            </div>

            <div class="btn-group" style="margin-top: 25px;">
                <a href="login.php" class="btn btn-primary">&larr; Return to Login Form</a>
                <a href="register.php" class="btn btn-outline">+ Register Another User</a>
            </div>
        </main>

        <footer class="app-footer">
            <p>Web Programming Laboratory &bull; Lab 11 User Validation Application</p>
        </footer>
    </div>
</body>
</html>
