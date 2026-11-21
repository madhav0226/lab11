<?php
/**
 * Lab 11: Session Termination Controller (Logout)
 * 
 * Safely unsets session variables, destroys server session file,
 * deletes session cookie, and redirects user to login page.
 */

require_once __DIR__ . '/config.php';

// Clear all session variables
$_SESSION = [];

// Delete the session cookie if present
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session on server
session_destroy();

// Redirect to login page with feedback notice
header('Location: login.php?msg=logged_out');
exit;
