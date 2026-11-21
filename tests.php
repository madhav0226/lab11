<?php
require_once __DIR__ . '/config.php';
$pdo = getDBConnection();

function testAuth($pdo, $user, $pass) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE LOWER(username) = LOWER(:username)');
    $stmt->execute([':username' => $user]);
    $row = $stmt->fetch();
    if (!$row) return 'FAILURE: User Not Found';
    if (password_verify($pass, $row['password_hash']) || $pass === $row['password_hash']) {
        return 'SUCCESS: ' . $row['full_name'] . ' (' . $row['role'] . ')';
    }
    return 'FAILURE: Invalid Password';
}

echo "=== AUTOMATED CREDENTIAL VALIDATION SUITE ===\n";
echo "Test 1 [admin / Admin@123]:       " . testAuth($pdo, 'admin', 'Admin@123') . "\n";
echo "Test 2 [student / Lab11@Pass]:    " . testAuth($pdo, 'student', 'Lab11@Pass') . "\n";
echo "Test 3 [alice / Alice@2026]:      " . testAuth($pdo, 'alice', 'Alice@2026') . "\n";
echo "Test 4 [admin / WrongPassword]:   " . testAuth($pdo, 'admin', 'WrongPassword') . "\n";
echo "Test 5 [ghost / AnyPassword]:     " . testAuth($pdo, 'ghost', 'AnyPassword') . "\n";
echo "Test 6 [Case Insensitive ADMIN]:  " . testAuth($pdo, 'ADMIN', 'Admin@123') . "\n";
echo "=============================================\n";
