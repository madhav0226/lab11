<?php
/**
 * Lab 11: Application Router
 * Redirects authenticated users to the success dashboard,
 * and guests to the login page.
 */

require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    header('Location: login_success.php');
    exit;
} else {
    header('Location: login.php');
    exit;
}
