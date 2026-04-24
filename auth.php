<?php
/**
 * Centralized Auth & Session Management
 * Include on every page for session_start() + helpers
 */

session_start();

// Regenerate ID periodically for security
if (!isset($_SESSION['regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['regenerated'] = time();
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function get_user_data() {
    if (!is_logged_in()) return null;
    return [
        'user' => $_SESSION['user'] ?? '',
        'role' => $_SESSION['role'] ?? 'user',
        'user_id' => $_SESSION['user_id'] ?? 0
    ];
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

